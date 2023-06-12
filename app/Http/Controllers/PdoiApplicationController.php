<?php

namespace App\Http\Controllers;

use App\Http\Requests\PdoiApplicationApplyRequest;
use App\Models\Country;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\PdoiApplication;
use App\Models\PdoiResponseSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PdoiApplicationController extends Controller
{
    public function create(Request $request)
    {
        $user = User::with(['country', 'area', 'municipality', 'settlement'])->find((int)$request->user()->id);

        if( !$user || $user->cannot('create', PdoiApplication::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $title = __('front.application.title.apply');

        $data=[];
        if( !$user->country ) {
            $data['countries'] = Country::optionsList();
        }
        if( !$user->area ) {
            $data['areas'] = EkatteArea::optionsList();
        }
        if( !$user->municipality ) {
            $data['municipality'] = EkatteMunicipality::optionsList();
        }
        if( !$user->settlement ) {
            $data['settlements'] = EkatteSettlement::optionsList();
        }

        $rzs = PdoiResponseSubject::optionsList();
        return view('front.application.apply', compact('data', 'user', 'rzs', 'title'));
    }

    public function store(Request $request) {

        $user = $request->user();
        if( !$user || $user->cannot('create', PdoiApplication::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        $applicationRequest = new PdoiApplicationApplyRequest();
        $validator = Validator::make($request->all(), $applicationRequest->rules());

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
        }

        try {
            $validated = $validator->validated();
            try {
                $this->updateProfile($validated);
            return response()->json(['applicationInfo' => $validated], 200);
                return response()->json(['applicationInfo' => []], 200);
            } catch (\Exception $e) {
                logError('Save application', $e->getMessage());
                return response()->json(['errors' => __('custom.system_error')], 200);
            }

        } catch (\Exception $e) {
            logError('Apply application (front)', $e->getMessage());
            return back()->withInput()->with('danger', __('custom.system_error'));
        }
    }

    private function updateProfile($validatedFields) {
        $fields = User::prepareModelFields($validatedFields, true);
        $user = auth()->user();
        $user->fill($fields);
        $user->save();
    }
}
