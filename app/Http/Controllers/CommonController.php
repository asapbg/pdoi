<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Http\Requests\PageFileUploadRequest;
use App\Models\Event;
use App\Models\File;
use App\Models\MenuSection;
use App\Models\Page;
use App\Models\PdoiApplication;
use App\Models\PdoiResponseSubject;
use App\Models\RzsSection;
use App\Models\User;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;

class CommonController extends Controller
{

    /**
     * @params entityId required
     *         model required
     *         booleanType required (Ex: active, status)
     *         status required
     * Toggle Model's active database field
     * @param Request $request
     */
    public function toggleBoolean(Request $request)
    {
        if (
            !$request->filled('entityId')
            || !$request->filled('model')
            || !$request->filled('booleanType')
            || !$request->filled('status')
        ) {
            return back();
        }
        $entityId = $request->get('entityId');
        $booleanType = $request->get('booleanType');
        $model = "\App\Models\\".$request->get('model');
        if (!class_exists($model)) {
            $model = "\App\\".$request->get('model'); //Spatie\Permission\Models
            if (!class_exists($model)) {
                $model = "Spatie\Permission\Models\\".$request->get('model');
                if (!class_exists($model)) {
                    return back();
                }
            }
        }
        $status = $request->get('status');

        $entity = $model::find($entityId);

        if( $request->get('model') === 'User' ) {
            $entity->status = $status ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
            $entity->$booleanType = $status;
        } else{
            if( $request->get('model') === 'Role' && !$status) {
                $entity->syncPermissions([]);
            }
            $entity->$booleanType = $status;
        }

        if( $request->get('model') === 'ModelSection' ) {
            //Clear menu cache
            foreach (config('available_languages') as $locale) {
                Cache::forget('menu_'.$locale['code']);
            }
        }

        $entity->save();
    }

    /**
     * @params entityId required
     *         model required
     *         permission required
     *         status required
     * Toggle Model's permissions
     * @param Request $request
     */
    public function togglePermissions(Request $request)
    {
        if (
            !$request->filled('entityId')
            || !$request->filled('model')
            || !$request->filled('permission')
            || !$request->filled('status')
        ) {
            return back();
        }
        $entityId = request()->get('entityId');
        $permission = request()->get('permission');
        $model = "\App\Models\\".request()->get('model');
        if (!class_exists($model)) {
            $model = "\App\\".request()->get('model');
            if (!class_exists($model)) {
                return back();
            }
        }
        $status = request()->get('status');
        $entity = $model::find($entityId);

        if ($status == 0) {
            $entity->revokePermissionTo($permission);
        }
        else {
            $entity->givePermissionTo($permission);
        }
    }

    /**
     * Fix the primary key sequence for a given table
     *
     * @param $table
     */
    public static function fixSequence($table)
    {
        $primary_key_info = DB::select(DB::raw("SELECT a.attname AS name, format_type(a.atttypid, a.atttypmod) AS type FROM pg_class AS c JOIN pg_index AS i ON c.oid = i.indrelid AND i.indisprimary JOIN pg_attribute AS a ON c.oid = a.attrelid AND a.attnum = ANY(i.indkey) WHERE c.oid = '" . $table . "'::regclass"));
        $primary_key_type = 'number';
        $primary_key_name = 'id';
        if (array_key_exists('0', $primary_key_info)) {
            $primary_key_type = $primary_key_info[0]->type;
            $primary_key_name = $primary_key_info[0]->name;
        }
        if (strpos($primary_key_type, 'character') === false) {
            $max_id = DB::table($table)->max($primary_key_name);
            $next_id = $max_id + 1;
            $sequence_key_name = $table . '_' . $primary_key_name . '_seq';
            DB::statement("ALTER SEQUENCE $sequence_key_name RESTART WITH $next_id");
        }
    }

    public function modalPdoiSubjects(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $canSelect = (boolean)$request->input('select');
        $multipleSelect = (boolean)$request->input('multiple');
        $subjects = PdoiResponseSubject::getTree($request->all());
        $oldBootstrap = $request->input('admin') && $request->input('admin'); //ugly way to fix design for bootstrap
        return view('partials.pdoi_tree.tree', compact('subjects', 'canSelect', 'multipleSelect', 'oldBootstrap'));
    }

    public function downloadFile(Request $request, File $file)
    {
        $user = $request->user();
        $application = $file->code_object == File::CODE_OBJ_APPLICATION ? $file->application : $file->event->application;

        if( !$user->can('download', $file) ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        if (Storage::disk('local')->has($file->path)) {
            return Storage::disk('local')->download($file->path, $file->filename);
        } else {
            return back()->with('warning', __('custom.record_not_found'));
        }
    }

    public function setCookie(Request $request): \Illuminate\Http\JsonResponse
    {
        if( $request->filled('value') && $request->filled('name') ) {
            Session::put($request->input('name'), (int)$request->input('value'));
        }
        return response()->json(['ok'], 200);
    }

    public function resetVisualOptions(Request $request): \Illuminate\Http\JsonResponse
    {
        Session::put('vo_font_percent', 100);
        Session::put('vo_high_contrast', 0);
        return response()->json(['ok'], 200);
    }

    /**
     * Admin Upload file by set object, type object
     * @param PageFileUploadRequest $request
     * @param $objectId
     * @param $typeObject
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function uploadFile(PageFileUploadRequest $request, $objectId, $typeObject) {
        try {
            $validated = $request->validated();
            $fileNameToStore = round(microtime(true)).'.'.$validated['file']->getClientOriginalExtension();
            // Upload File
            $validated['file']->storeAs(File::PUBLIC_UPLOAD_DIR, $fileNameToStore, 'public_uploads');
            $item = new File([
                'id_object' => $objectId,
                'code_object' => $typeObject,
                'filename' => $fileNameToStore,
                'content_type' => $validated['file']->getClientMimeType() != 'application/octet-stream' ? $validated['file']->getClientMimeType() : $validated['file']->getMimeType(),
                'path' => File::PUBLIC_UPLOAD_DIR.$fileNameToStore,
                'description' => $validated['description'],
                'user_reg' => $request->user()->id,
            ]);
            $item->save();

            $route = match ((int)$typeObject) {
                File::CODE_OBJ_PAGE => route('admin.page.edit', Page::find($objectId)) . '#ct-files',
                File::CODE_OBJ_MENU_SECTION => route('admin.menu_section.edit', MenuSection::find($objectId)) . '#ct-files',
                default => '',
            };
            return redirect($route)->with('success', 'Файлът/файловте са качени успешно');
        } catch (\Exception $e) {
            logError('Upload file', $e->getMessage());
            return back()->with(['danger' => 'Възникна грешка. Презаредете страницата и опитайте отново.']);
        }
    }

    /**
     * Download public file
     * @param Request $request
     * @param File $file
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \League\Flysystem\FilesystemException
     */
    public function downloadPageFile(Request $request, File $file)
    {
        if( $file->code_object != File::CODE_OBJ_MENU_SECTION && $file->code_object != File::CODE_OBJ_PAGE ) {
            return back()->with('warning', __('custom.record_not_found'));
        }

        if (Storage::disk('public_uploads')->has($file->path)) {
            return Storage::disk('public_uploads')->download($file->path, $file->filename);
        } else {
            return back()->with('warning', __('custom.record_not_found'));
        }
    }

    /**
     * Delete public file
     * @param Request $request
     * @param File $file
     * @return bool|RedirectResponse
     * @throws FilesystemException
     */
    public function deleteFile(Request $request, File $file)
    {
        $user = $request->user();
        if( !$user->can('delete', $file) ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $route = match ((int)$file->code_object) {
            File::CODE_OBJ_PAGE => route('admin.page.edit', Page::find($file->id_object)) . '#ct-files',
            File::CODE_OBJ_MENU_SECTION => route('admin.menu_section.edit', MenuSection::find($file->id_object)) . '#ct-files',
            default => '',
        };
        $file->delete();
        if (Storage::disk('public_uploads')->has($file->path)) {
            Storage::disk('public_uploads')->delete($file->path, $file->filename);
        }
        return redirect($route)->with('success', 'Файлът е изтрит успешно');
    }

    public function callbackRegisterEvent(Request $request)
    {
        $appId = (int)$request->input('application_id');
        $event = (int)$request->input('event');
        //TODO create middleware for this request
        $application = PdoiApplication::find((int)$appId);
        if( $application ) {
            try {
                //Sometimes document is registered, but we fail to update application.
                // In this case cron will try to register application and status will be DS_ALREADY_RECEIVED.
                // We need to update application in our platform and set notification as send
                // to continue application process
                if( ($event == ApplicationEventsEnum::SEND_TO_SEOS->value && $application->status == PdoiApplicationStatusesEnum::RECEIVED->value)
                    || ($event == ApplicationEventsEnum::APPROVE_BY_SEOS->value && $application->status == PdoiApplicationStatusesEnum::REGISTRATION_TO_SUBJECT->value) ) {
                    $appService = new ApplicationService($application);
                    $appService->registerEvent($event);
                }
                echo '200';
            } catch (\Exception $e){
                Log::error('Callback SEOS error :'.$e);
                echo '500';
            }
        } else {
            echo '404';
        }
    }

    public function getSubjectContactInfo(Request $request)
    {
        $id = $request->get('s');
        $item = PdoiResponseSubject::find((int)$id);
        if( !$item ) {
            echo '<p class="text-danger">'.__('messages.record_not_found').'</p>';
        }

        $contacts = User::IsActive()->IsContactVisible()->where('administrative_unit', '=', $item->id)->get();
        return view('front.partials.contact_person', compact('contacts'));
    }
}
