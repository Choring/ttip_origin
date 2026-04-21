<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers()
                ->symbols();
        });

        // 전체 페이지: 1분에 120회 초과 시 차단
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip())->response(function () {
                abort(429, '너무 많은 요청입니다. 잠시 후 다시 시도해주세요.');
            });
        });

        // 로그인/회원가입: 1분에 10회 초과 시 차단
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // 글쓰기/댓글: 1분에 20회 초과 시 차단
        RateLimiter::for('write', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        // Kakao Socialite Provider 등록
        \Illuminate\Support\Facades\Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('kakao', \SocialiteProviders\Kakao\KakaoProvider::class);
        });
    }
}
