<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class RegisterVerificationController extends Controller
{
    /**
     * 이메일로 OTP 인증코드 발송
     */
    public function sendCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower($request->email);

        // 이미 가입된 이메일 체크
        if (User::where('email', $email)->exists()) {
            return response()->json(['message' => '이미 사용 중인 이메일입니다.'], 422);
        }

        // 발송 횟수 제한 (1분에 3회)
        $rateLimitKey = 'otp_send:' . $email;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json(['message' => "잠시 후 다시 시도해주세요. ({$seconds}초 후)"], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        // 6자리 코드 생성 후 10분간 캐시 저장
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("otp:{$email}", $code, now()->addMinutes(10));

        // 이메일 발송
        Mail::send([], [], function ($message) use ($email, $code) {
            $message->to($email)
                ->subject('[ttip] 이메일 인증코드')
                ->html("
                    <div style='font-family:sans-serif;max-width:480px;margin:0 auto;padding:32px;'>
                        <h2 style='color:#FF6B00;margin-bottom:8px;'>ttip 이메일 인증</h2>
                        <p style='color:#555;margin-bottom:24px;'>아래 인증코드를 입력해주세요. (10분 이내 유효)</p>
                        <div style='background:#f4f4f4;border-radius:12px;padding:24px;text-align:center;'>
                            <span style='font-size:36px;font-weight:900;letter-spacing:8px;color:#111;'>{$code}</span>
                        </div>
                        <p style='color:#999;font-size:12px;margin-top:24px;'>본인이 요청하지 않은 경우 이 메일을 무시하세요.</p>
                    </div>
                ");
        });

        return response()->json(['message' => '인증코드가 발송되었습니다.']);
    }

    /**
     * OTP 코드 검증
     */
    public function verifyCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $email = strtolower($request->email);
        $code  = $request->code;

        $cached = Cache::get("otp:{$email}");

        if (!$cached) {
            return response()->json(['message' => '인증코드가 만료되었습니다. 다시 발송해주세요.'], 422);
        }

        if ($cached !== $code) {
            return response()->json(['message' => '인증코드가 올바르지 않습니다.'], 422);
        }

        // 인증 완료 표시 (30분 유지 — 가입 완료 전까지)
        Cache::forget("otp:{$email}");
        Cache::put("otp_verified:{$email}", true, now()->addMinutes(30));

        return response()->json(['message' => '이메일 인증이 완료되었습니다.']);
    }

    /**
     * 닉네임 중복 체크
     */
    public function checkNickname(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|min:2|max:20',
        ]);

        $exists = User::where('name', $request->name)->exists();

        return response()->json([
            'available' => !$exists,
            'message'   => $exists ? '이미 사용 중인 닉네임입니다.' : '사용 가능한 닉네임입니다.',
        ]);
    }
}
