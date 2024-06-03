<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryMethodsEnum;
use App\Library\EAuthentication;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class EAuthController extends Controller
{
    private string $homeRouteName = 'home';
    private string $adminRouteName = 'admin.home';
    //required field to register new user
    private array $newUserRequiredFields = ['name', 'legal_form', 'identity_number', 'email'];

    /**
     * Starts a eAuth process by open and submit form automatically to Identity provider
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
     */
    public function login(Request $request, $source = ''): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $eAuth = new EAuthentication();
        return $eAuth->spLoginPage($source);
    }

    /**
     * Integration call this method to send request response to us
     * @param Request $request
     * @param string $source You can use this to detect user type or login form... by sending
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    public function loginCallback(Request $request, $source = ''): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $eAuth = new EAuthentication();
        Log::channel('info')->info('eAuth SAMLResponse: '. $request->input('SAMLResponse'));
        $userInfo = $eAuth->userData($request->input('SAMLResponse'));

        //Identity number is required
        if( !isset($userInfo['legal_form']) || !isset($userInfo['identity_number'])
            || is_null($userInfo['legal_form']) || is_null($userInfo['identity_number']) ) {
            return $this->showMessage($this->homeRouteName, __('eauth.identity_number_is_missing'));
        }
        //First check if identity already exist - if yes sign
        $existUser = User::where(function ($q) use($userInfo){
            $q->where('person_identity', '=', $userInfo['identity_number'])
                ->orWhere('company_identity', '=', $userInfo['identity_number']);
        })->first();

        if($existUser) {
            return $this->redirectExistingUser($existUser);
        }

        //Email is optional
        if( isset($userInfo['email']) && !empty($userInfo['email']) ) {
            //Second check if email and exist - if yes sign
            $existUser = User::where('email', '=', $userInfo['email'])->where(function ($q) use($userInfo){
                $q->where('person_identity', '=', $userInfo['identity_number'])
                    ->orWhere('company_identity', '=', $userInfo['identity_number']);
            })->first();
            if($existUser) {
                return $this->redirectExistingUser($existUser);
            }
        }

        $userInfo['email'] = null;
        //Check if all required fields are filled
        $missingFields = false;
        foreach ($this->newUserRequiredFields as $f) {
            if( !isset($userInfo[$f]) || empty($userInfo[$f]) ) {
                $missingFields = true;
            }
        }

        if( $missingFields ) {
            return view('eauth.create_user', compact('userInfo'));
        }

        return $this->saveNewUser($userInfo);
    }

    /**
     * When user not exist, and we receive all required data
     * or after user submit missing data, we use this method to create new user in db
     * @param $data
     * @return \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    private function saveNewUser($data): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();
        try {
            $data['legal_form'] = $data['legal_form'] == 'person' ? User::USER_TYPE_PERSON : User::USER_TYPE_COMPANY;
            $user = new User([
                'password' => Hash::make(Str::random(8)),
                'user_type' => User::USER_TYPE_EXTERNAL,
                'names' => $data['name'],
                'lang' => getLocaleId(app()->getLocale()),
                'username' => $data['email'],
                'email' => $data['email'],
                'legal_form' => $data['legal_form'],
                'delivery_method' => DeliveryMethodsEnum::EMAIL->value,
                'phone' => $data['phone'] ?? null,
                'status' => User::STATUS_ACTIVE,
                'status_date' => Carbon::now(),
                'active' => 1,
                'email_verified_at' => Carbon::now(),
                'last_login_at' => Carbon::now()
            ]);

            if( $user ) {
                if( $data['legal_form'] == User::USER_TYPE_PERSON ) {
                    $user->person_identity = $data['identity_number'];
                } else {
                    $user->company_identity = $data['identity_number'];
                }
                $user->save();
            }

            //assign role
            $role = Role::where('name', User::EXTERNAL_USER_DEFAULT_ROLE)->first();
            if( $role ) {
                $user->assignRole($role);
            }

            $user->refresh();
            Auth::login($user);

            \Illuminate\Support\Facades\Session::put('user_last_login', $user->last_login_at);
            $sessionLifetime = Settings::where('name', '=', Settings::SESSION_LIMIT_KEY)->first();
            \Illuminate\Support\Facades\Session::put('user_session_time_limit', $sessionLifetime ? $sessionLifetime->value : 10);

            DB::commit();
            return redirect(route($this->homeRouteName));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('['.Carbon::now().'] : eAuth Integration save user error. Data ('.json_encode($data).'). Error: '.$e->getMessage());
            return $this->showMessage($this->homeRouteName, __('eauth.unknown_error'));
        }
    }

    /**
     * When user not exist and api do not return user email we need to ask user about his email address.
     * After validation, we create new user
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createUserSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users', 'max:255'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['nullable', 'string', 'min:3', 'max:50'],
            'legal_form' => ['required', 'string', Rule::in('person', 'company')],
            'identity_number' => ['required', 'string', 'max:20']
        ]);

        if( $validator->fails() ) {
            $userInfo = $request->all();
            $validationErrors = $validator->errors()->toArray();
            return view('eauth.create_user', compact('userInfo', 'validationErrors'))->with('danger', __('custom.check_for_errors'));
        }

        $validated = $validator->validated();

        return $this->saveNewUser($validated);
    }

    /**
     * We need a public metadata page as service provider for the integration
     * This link page is part of auth request message
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function spMetadata($callback_source = ''): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $eAuth = new EAuthentication();
        return $eAuth->spMetadata($callback_source);
    }

    /**
     * When user exist we sign him and redirect to his role home page
     * @param User $user
     * @return \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    private function redirectExistingUser(User $user): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $redirectRoute = $user->user_type == User::USER_TYPE_EXTERNAL ? $this->homeRouteName : $this->adminRouteName;
        //Check user status and return message if status not allow login
        if( in_array($user->status, [User::STATUS_INACTIVE, User::STATUS_BLOCKED, User::STATUS_REG_IN_PROCESS]) || !$user->active ) {
            $userStatuses = User::getUserStatuses();
            if( !$user->active ) {
                return $this->showMessage($redirectRoute, __('auth.account_not_active'));
            }

            if( $user->status == User::STATUS_REG_IN_PROCESS ) {
                return $this->showMessage($redirectRoute, __('auth.account_email_not_validated'));
            }
            return $this->showMessage($redirectRoute, __('auth.account_restricted', ['status' => $userStatuses[$user->status]]));
        }

        Auth::login($user);
        $user->last_login_at = Carbon::now();
        $user->save();

        //session timer
        \Illuminate\Support\Facades\Session::put('user_last_login', $user->last_login_at);
        $sessionLifetime = Settings::where('name', '=', Settings::SESSION_LIMIT_KEY)->first();
        \Illuminate\Support\Facades\Session::put('user_session_time_limit', $sessionLifetime ? $sessionLifetime->value : 10);

        return redirect(route($redirectRoute));
    }

    private function showMessage(string $route, string $msg, string $type = 'error'): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $typeMsg = match ($type) {
            default => 'danger',
        };
        return redirect(route($route))->with($typeMsg, $msg);
    }

    public function logout()
    {
        echo 'logout';
    }
}
