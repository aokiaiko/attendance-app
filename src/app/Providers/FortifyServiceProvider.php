<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\LogoutResponse;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);

        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::verifyEmailView(function () {
        return view('auth.verify-email');
        });

        Fortify::authenticateUsing(function (Request $request) {

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません。'],
                ]);
            }

            if (! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません。'],
                ]);
            }

            if ($request->login_type === 'admin' && $user->role !== 'admin') {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません。'],
                ]);
            }

            if ($request->login_type === 'user' && $user->role !== 'user') {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません。'],
                ]);
            }

            return $user;

        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email.$request->ip());
        });
    }
}
