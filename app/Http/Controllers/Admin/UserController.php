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
}
