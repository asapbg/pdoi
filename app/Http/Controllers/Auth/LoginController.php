<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Adldap\Laravel\Facades\Adldap;
use App\Models\User;
use Auth;
use DB;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $maxAttempts = 5;
    protected $decayMinutes = 1; // minutes to lockout

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Attempt to log in a user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws ValidationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function login(Request $request)
    {
        $guard = $request->filled('guard') ? $request->offsetGet('guard') : config('auth')['defaults']['guard'];
        if (empty($guard) || !isset(config('auth')['guards'][$guard])) {
            throw ValidationException::withMessages([
                'error' => [sprintf('auth.no_guard_found', $guard)],
            ]);
        }
        $provider = config('auth')['guards'][$guard]['provider'];
        $model = config('auth')['providers'][$provider]['model'];

        $this->validateLogin($request);

        $username = $request->offsetGet('username');
        $fieldType = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // First check if the user is active or
        // has entered a password after the account was created
        $user = $model::where('user_type', User::USER_TYPE_EXTERNAL)
            ->where(function ($q) use ($username){
                $q->where('username', $username)
                    ->orWhere('email', $username);
            })->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'error' => [trans('auth.no_user_found', ['username' => $username])],
            ]);
        }

        if ($this->hasTooManyLoginAttempts($request)) {
            $user->status = User::STATUS_BLOCKED;
            $user->save();
            $this->fireLockoutEvent($request);

            throw ValidationException::withMessages([
                'error' => [trans('auth.user_blocked')],
            ]);
            //return $this->sendLockoutResponse($request);
        }

        if ($user->status == User::STATUS_REG_IN_PROCESS) {
            throw ValidationException::withMessages([
                'error' => [trans('auth.verify_email')],
            ]);
        }

//        if (!$user->pass_is_new) {
//            $this->incrementLoginAttempts($request);
//            throw ValidationException::withMessages([
//                'error' => [trans('auth.password_not_changed')],
//            ]);
//        }
        if ($user->status != User::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'error' => [trans('auth.status_blocked')],
            ]);
        }

        if (\Auth::guard($guard)->attempt([
            $fieldType => $username,
            'password' => $request->offsetGet('password')
        ], $request->filled('remember'))) {

            $user = \Auth::guard($guard)->user();
            \Auth::guard($guard)->login($user);

            $user->last_login_at = Carbon::now();
            $user->save();

            \Auth::logoutOtherDevices(request('password'));

            return redirect()->intended($this->redirectPath());

        } else {

            $this->incrementLoginAttempts($request);

            $message = trans('auth.failed');

            if ($this->limiter()->attempts($this->throttleKey($request)) == $this->maxAttempts) {
                $message .= [trans('auth.last_attempt')];
            }
            throw ValidationException::withMessages([
                'error' => [$message],
            ]);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = currentUser();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($user) {
            activity('users')
                ->causedBy($user)
                ->performedOn($user)
                ->log("user_logout");
        }

        return $this->loggedOut($request) ?: redirect($this->redirectTo);
    }

}
