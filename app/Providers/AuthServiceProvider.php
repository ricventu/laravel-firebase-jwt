<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

use App\User;
use Firebase\JWT\JWT;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Auth::viaRequest('token', function ($request) {
            $token = $request->get('token') ?? $request->bearerToken();
             if ($token) {
                 try {
                     $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
                     return User::find($credentials->sub);
                 } catch (ExpiredException $e) {
                     //Provided token is expired.
                 } catch (Exception $e) {
                     //An error while decoding token.
                 }
             }
         });
 
    }
}
