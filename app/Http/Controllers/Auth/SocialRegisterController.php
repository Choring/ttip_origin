<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SocialRegisterController extends Controller
{
    /**
     * 추가 정보 입력 페이지 표시
     */
    public function create(): Response|RedirectResponse
    {
        $socialData = session('kakao_auth_data');

        if (!$socialData) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/SocialRegister', [
            'socialData' => $socialData,
        ]);
    }

    /**
     * 추가 정보 입력 후 가입 처리
     */
    public function store(Request $request): RedirectResponse
    {
        $socialData = session('kakao_auth_data');

        if (!$socialData) {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'kakao_id' => $socialData['kakao_id'],
            'avatar' => $socialData['avatar'],
            'password' => null, // 소셜 가입은 비밀번호 없이 생성
            // 이메일을 직접 입력받았으므로 인증 절차를 거치게 함 (email_verified_at 미설정)
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 세션 데이터 삭제
        session()->forget('kakao_auth_data');

        return redirect(route('verification.notice'));
    }
}
