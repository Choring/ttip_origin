<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * 회원 리스트 출력 (권한, 밴 상태 등 확인용)
     */
    public function index(Request $request)
    {
        $users = User::with('tier')->latest()->paginate(15);
        
        return Inertia::render('Admin/Users/Index', [
            'users' => $users
        ]);
    }

    /**
     * 회원의 밴(이용 정지) 상태를 토글(적용/해제)합니다.
     */
    public function toggleBan(User $user)
    {
        // 최고 관리자는 누구도 밴할 수 없도록 보안 처리
        if ($user->role === 'master') {
            return back()->with('error', '최고 관리자(master)는 정지할 수 없습니다.');
        }

        $user->is_banned = !$user->is_banned;
        $user->save();

        $statusMessage = $user->is_banned ? '접속 정지' : '정지 해제';
        return back()->with('success', "선택한 회원이 {$statusMessage} 처리되었습니다.");
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'tiers' => \App\Models\Tier::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'current_points' => 'required|integer|min:0',
            'tier_id' => 'nullable|exists:tiers,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'current_points' => $validated['current_points'],
            'tier_id' => $validated['tier_id'] ?? null,
            'is_banned' => false,
        ]);

        return redirect()->route('admin.users.index')->with('success', '새로운 회원이 성공적으로 등록되었습니다.');
    }

    public function edit(User $user)
    {
        // 최고 관리자는 수정 불가 보호
        if ($user->role === 'master') {
            abort(403, '마스터 계정 정보는 보호됩니다.');
        }

        return Inertia::render('Admin/Users/Edit', [
            'editUser' => $user,
            'tiers' => \App\Models\Tier::all()
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'master') {
            abort(403, '마스터 계정 정보는 보호됩니다.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'current_points' => 'required|integer|min:0',
            'tier_id' => 'nullable|exists:tiers,id',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->role = $validated['role'];
        $user->current_points = $validated['current_points'];
        $user->tier_id = $validated['tier_id'] ?? null;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', '회원 정보가 성공적으로 수정되었습니다.');
    }
}
