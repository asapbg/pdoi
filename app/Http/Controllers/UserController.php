<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileStoreRequest;
use App\Models\Country;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\ProfileType;
use App\Models\User;
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
                $validated = User::prepareModelFields($validator->validated());
                if( isset($validated['legal_form']) && $validated['legal_form'] ) {
                    if( $validated['legal_form'] == User::USER_TYPE_PERSON ) {
                        $validated['company_identity'] = null;
                    } else {
                        $validated['person_identity'] = null;
                    }
                } else{
                    $validated['person_identity'] = null;
                    $validated['company_identity'] = null;
                }

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
}
