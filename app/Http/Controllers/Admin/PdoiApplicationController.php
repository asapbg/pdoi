<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PdoiApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdoiApplicationController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $filter = $this->filters($request);
        $requestFilter = $request->all();
        $items = null;
        if( isset($requestFilter['search']) ) {
            $paginate = $filter['paginate'] ?? PdoiApplication::PAGINATE;
            $items = PdoiApplication::with(['responseSubject', 'responseSubject.translations'])
                ->FilterBy($requestFilter)
                ->ByUserSubjects()
                ->paginate($paginate);
        }
        return $this->view('admin.applications.index', compact('items', 'filter'));
    }

    public function show(Request $request, int $id = 0): \Illuminate\View\View
    {
        $item = PdoiApplication::with(['files', 'responseSubject', 'responseSubject.translations',
            'categories', 'categories.translations', 'profileType', 'profileType.translations', 'country',
            'country.translations', 'area', 'area.translations', 'municipality', 'municipality.translations',
            'settlement', 'settlement.translations'])
            ->find((int)$id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $user = auth()->user();
        if( !$user->canAny(['update', 'view'], $item) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $categories = Category::optionsList();

        return $this->view('admin.applications.view', compact('item', 'categories'));
    }

    public function addCategory(Request $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        if( !$request->filled('categories') ) {
            return back()->with('danger', __('custom.category_not_selected'))->withInput();
        }
        $item = PdoiApplication::find((int)$request->input('id'));
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $user = auth()->user();
        if( !$user->canAny(['update'], $item) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $item->categories()->attach($request->input('categories'));

        return redirect(route('admin.application.view', ['item' => $item->id]))
            ->with('success', __('custom.success_update'));
    }

    public function removeCategory(Request $request)
    {
        if( !$request->filled('category') ) {
            return response()->json(['error' => 1, 'message' => __('custom.category_not_selected')]);
        }
        $item = PdoiApplication::find((int)$request->input('id'));
        if( !$item ) {
            return response()->json(['error' => 1, 'message' => __('custom.record_not_found')]);
        }
        $user = auth()->user();
        if( !$user->canAny(['update'], $item) ){
            return response()->json(['error' => 1, 'message' => __('messages.unauthorized')]);
        }

        $item->categories()->detach($request->input('category'));

        return response()->json(['success' => 1, Response::HTTP_OK]);
    }

    private function filters($request): array
    {
        return array(
            'period' => array(
                'type' => 'select',
                'options' => optionsTimePeriod(true,'', __('custom.period')),
                'default' => '',
                'value' => $request->input('period'),
                'col' => 'col-md-3'
            ),
            'formDate' => array(
                'type' => 'datepicker',
                'value' => $request->input('formDate'),
                'placeholder' => __('custom.begin_date'),
                'col' => 'col-md-2'
            ),
            'toDate' => array(
                'type' => 'datepicker',
                'value' => $request->input('toDate'),
                'placeholder' => __('custom.end_date'),
                'col' => 'col-md-2'
            ),
            'status' => array(
                'type' => 'select',
                'options' => optionsApplicationStatus(true, '', __('custom.status')),
                'default' => '',
                'value' => $request->input('status'),
                'col' => 'col-md-4'
            ),
            'applicationUri' => array(
                'type' => 'text',
                'placeholder' => __('custom.reg_number'),
                'value' => $request->input('applicationUri'),
                'col' => 'col-md-4'
            ),
            'category' => array(
                'type' => 'select',
                'options' => optionsFromModel(Category::optionsList(), true,'', trans_choice('custom.categories',1)),
                'default' => '',
                'value' => $request->input('status'),
                'col' => 'col-md-4'
            ),
            'text' => array(
                    'type' => 'text',
                    'placeholder' => __('validation.attributes.text'),
                    'value' => $request->input('text')
            ),
            'subjects' => array(
                'type' => 'subjects',
                'multiple' => false,
                'options' => optionsFromModel(PdoiApplication::optionsList(), true, '', trans_choice('custom.pdoi_response_subjects', 1)),
                'value' => $request->input('subjects'),
                'default' => '',
                'placeholder' => trans_choice('custom.pdoi_response_subjects',1),
                'col' => 'col-md-9'
            )
        );
    }
}
