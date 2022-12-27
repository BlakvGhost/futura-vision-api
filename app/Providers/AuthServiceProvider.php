<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Post' => 'App\Policies\DashPolicy',
        'App\Models\Technology' => 'App\Policies\DashPolicy',
        'App\Models\Team' => 'App\Policies\DashPolicy',
        'App\Models\Project' => 'App\Policies\DashPolicy',
        'App\Models\Service' => 'App\Policies\DashPolicy',
        'App\Models\Exploit' => 'App\Policies\DashPolicy',
        'App\Models\Partner' => 'App\Policies\DashPolicy',
        'App\Models\Customer' => 'App\Policies\DashPolicy',
        'App\Models\Category' => 'App\Policies\DashPolicy',
        'App\Models\Contact' => 'App\Policies\GuestPolicy',
        'App\Models\Blog' => 'App\Policies\GuestPolicy',
        'App\Models\Forum' => 'App\Policies\GuestPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('comment', function (User $user) {
            return $user->role !== 'get';
        });
        Gate::define('onlyAdmin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
