<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class KakaoController extends Controller
{
    /**
     * 카카오 로그인 페이지로 리다이렉트
     */
    public function redirectToKakao()
    {
        return Socialite::driver('kakao')->redirect();
    }

    /**
     * 카카오로부터의 콜백 처리
     */
    public function handleKakaoCallback()
    {
        try {
            $driver = Socialite::driver('kakao');

            // 로컬 개발 환경에서 SSL 인증서 오류 해결을 위해 검증 생략
            if (app()->environment('local')) {
                $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }

            $kakaoUser = $driver->user();
        } catch (\Exception $e) {
            Log::error('Kakao Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', '카카오 로그인 중 오류가 발생했습니다.');
        }

        // 1. 카카오 ID로 사용자 확인
        $user = User::where('kakao_id', $kakaoUser->getId())->first();

        if ($user) {
            // 이미 가입된 카카오 사용자: 정보 업데이트 후 로그인
            $user->update([
                'name' => $kakaoUser->getName() ?? $kakaoUser->getNickname() ?? $user->name,
                'avatar' => $kakaoUser->getAvatar(),
            ]);
            
            Auth::login($user);
            return redirect()->intended(route('home', absolute: false));
        }

        // 2. 이메일 정보가 있는 경우 기존 사용자 확인 (계정 연동)
        if ($kakaoUser->getEmail()) {
            $user = User::where('email', $kakaoUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'kakao_id' => $kakaoUser->getId(),
                    'avatar' => $kakaoUser->getAvatar(),
                ]);
                
                Auth::login($user);
                return redirect()->intended(route('home', absolute: false));
            }
        }

        // 3. 신규 사용자 또는 이메일이 없는 경우 가입 단계로 리다이렉트
        session()->put('kakao_auth_data', [
            'kakao_id' => (string) $kakaoUser->getId(),
            'name' => $kakaoUser->getName() ?? $kakaoUser->getNickname() ?? '',
            'email' => $kakaoUser->getEmail(),
            'avatar' => $kakaoUser->getAvatar(),
        ]);

        return redirect()->route('kakao.register');
    }
}
