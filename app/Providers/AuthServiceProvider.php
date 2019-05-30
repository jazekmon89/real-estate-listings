<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth\Guard as Guard;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Auth\CustomUserProvider as CustomUserProvider;

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

    public function register() {}

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('custom', function($app, $config) {
    
            return new CustomUserProvider($app['hash'], $config['model']);
        });
    }
}
