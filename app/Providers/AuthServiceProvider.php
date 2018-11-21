<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // $gate->define('isAdmin', function($user){
        //     return $user->type == 'admin';
        // });

        // $gate->define('isUser', function($user){
        //     return $user->type == 'user';
        // });

        // $gate->define('isOrganization', function($user){
        //     return $user->type == 'organization';
        // });

        // $gate->define('isMyAccount', function($user, $profileUser){
        //     return $user->type === $profileUser -> id;
        // });

        Gate::define('isAdmin',function($user){
            return $user->type === 'admin';
        });
        Gate::define('isAuthor',function($user){
            return $user->type === 'author';
        });
        Gate::define('isUser',function($user){
            return $user->type === 'user';
        });

        Passport::routes();

        //
    }
}
