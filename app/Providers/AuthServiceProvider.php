<?php

namespace App\Providers;

use App\Models\PdoiResponseSubject;
use App\Policies\PdoiResponseSubjectPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        PdoiResponseSubject::class => PdoiResponseSubjectPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        //TODO fix me This is not good when using policy with additional model clause
//        Gate::before(function ($user, $ability) {
//            return $user->hasRole('service_user') ? true : null;
//        });
    }
}
