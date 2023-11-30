<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        $this->setBreadcrumbsTitle(__('auth.forgot_password'));
        return $this->view('auth.passwords.email');
    }
    
    public function confirmPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if( $validator->fails() ) {
            return back()->with('danger', __('custom.check_for_errors'))->withInput()->withErrors($validator->errors()->all());
        }

        $validated = $validator->validated();
        $user = User::where('email', '=', $validated['email'])->first();

        if( $user ) {
            $user->password = Hash::make($validated['password']);
            $user->pass_last_change = Carbon::now();
            $user->pass_is_new = 1;
            $user->save();

            $route = $user->user_type == User::USER_TYPE_EXTERNAL ? 'login' : 'login.admin';
            return redirect(route($route))->with('success', __('Паролата е сменена успешно'));
        }

        return back()->with('danger', __('custom.system_error'))->withInput()->withErrors($validator->errors());
    }
}
