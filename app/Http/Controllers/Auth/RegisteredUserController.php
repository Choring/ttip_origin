<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:20', 'unique:'.User::class.',name'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.min'    => '닉네임은 최소 2글자 이상이어야 합니다.',
            'name.max'    => '닉네임은 최대 20글자까지 가능합니다.',
            'name.unique' => '이미 사용 중인 닉네임입니다.',
            'email.unique' => '이미 사용 중인 이메일입니다.',
        ]);

        // 이메일 인증 완료 여부 확인
        $email = strtolower($request->email);
        if (!Cache::get("otp_verified:{$email}")) {
            return back()->withErrors(['email' => '이메일 인증을 먼저 완료해주세요.']);
        }

        $user = User::create([
            'name'              => $request->name,
            'email'             => $email,
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
            'terms_agreed_at'   => now(),
            'privacy_agreed_at' => now(),
            'agreed_ip'         => $request->ip(),
        ]);

        // 인증 캐시 제거
        Cache::forget("otp_verified:{$email}");

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home'));
    }
}
