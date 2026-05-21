<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => 'required|in:post,comment',
            'reportable_id'   => 'required|integer',
            'reason'          => 'required|in:spam,abuse,obscene,illegal,other',
            'description'     => 'nullable|string|max:500',
        ]);

        // 실제 모델 존재 확인
        $model = match($validated['reportable_type']) {
            'post'    => Post::findOrFail($validated['reportable_id']),
            'comment' => Comment::findOrFail($validated['reportable_id']),
        };

        $user = auth()->user();

        // 자기 자신의 콘텐츠 신고 방지
        if ($model->user_id === $user->id) {
            return response()->json(['message' => '본인의 콘텐츠는 신고할 수 없습니다.'], 422);
        }

        // 중복 신고 방지
        $exists = Report::where('user_id', $user->id)
            ->where('reportable_type', $model::class)
            ->where('reportable_id', $model->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => '이미 신고한 콘텐츠입니다.'], 422);
        }

        Report::create([
            'user_id'          => $user->id,
            'reportable_type'  => $model::class,
            'reportable_id'    => $model->id,
            'reason'           => $validated['reason'],
            'description'      => $validated['description'] ?? null,
        ]);

        return response()->json(['message' => '신고가 접수되었습니다. 검토 후 조치하겠습니다.']);
    }
}
