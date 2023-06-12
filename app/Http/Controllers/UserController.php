<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileStoreRequest;
use App\Models\Country;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\ProfileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $title = __('front.profile.title.my_profile');
        $user = $request->user();

        if( $request->isMethod('put') ) {

            $profileRequest = new ProfileStoreRequest();
            $validator = Validator::make($request->all(), $profileRequest->rules());
            if($validator->fails()) {
                return back()->withInput()->withErrors($validator->errors())->with('danger', __('custom.check_for_errors'));
            }

            try {
                $validated = $this->replaceRequestFields($validator->validated());
                $user->fill($validated);
                $user->save();
                session()->flash('success', __('custom.success_update'));

            } catch (\Exception $e) {
                logError('Edit profile (front)', $e->getMessage());
                return back()->withInput()->with('danger', __('custom.system_error'));
            }
        }

        $profileTypes = ProfileType::optionsList();
        $countries = Country::optionsList();
        $areas = EkatteArea::optionsList();
        $municipalities = EkatteMunicipality::optionsList();
        $settlements = EkatteSettlement::optionsList();
        return view('front.edit_profile', compact('title', 'user', 'profileTypes'
            , 'countries', 'areas', 'municipalities', 'settlements'));
    }

    private function replaceRequestFields($fields = []){
        if (isset($fields['country'])) {
            $fields['country_id'] = $fields['country'];
            unset($fields['country']);
        }

        foreach (['area', 'municipality', 'settlement'] as $f) {
            if (isset($f)) {
                $fields['ekatte_'.$f.'_id'] = $fields[$f];
                unset($fields[$f]);
            }
        }

        return $fields;
    }
}