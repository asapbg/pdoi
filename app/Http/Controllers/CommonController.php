<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\PdoiResponseSubject;
use App\Models\RzsSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

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
}
