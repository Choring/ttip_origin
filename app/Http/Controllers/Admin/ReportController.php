<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'reportable'])
            ->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $type = $request->type === 'post'
                ? \App\Models\Post::class
                : \App\Models\Comment::class;
            $query->where('reportable_type', $type);
        }

        $reports = $query->paginate(20)->appends($request->query());

        // reportable 정보를 프론트가 사용하기 편하게 정제
        $reports->getCollection()->transform(function ($report) {
            $reportable = $report->reportable;
            $report->reportable_label = $report->reportable_type === \App\Models\Post::class
                ? 'post' : 'comment';
            $report->reportable_title = $reportable
                ? (isset($reportable->title) ? $reportable->title : mb_strimwidth($reportable->content, 0, 50, '...'))
                : '(삭제된 콘텐츠)';
            $report->reportable_link = $reportable
                ? ($report->reportable_label === 'post'
                    ? route('posts.show', $reportable->id)
                    : route('posts.show', $reportable->post_id))
                : null;
            $report->reason_label  = \App\Models\Report::reasonLabel($report->reason);
            $report->status_label  = \App\Models\Report::statusLabel($report->status);
            return $report;
        });

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
            'filters' => $request->only('status', 'type'),
        ]);
    }

    public function resolve(Request $request, Report $report)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        // 신고 대상 콘텐츠 블라인드 처리
        $reportable = $report->reportable;
        if ($reportable) {
            $reportable->update(['is_hidden' => true]);
        }

        $report->update([
            'status'     => 'resolved',
            'admin_note' => $request->admin_note,
        ]);

        $label = $report->reportable_type === \App\Models\Post::class ? '게시글' : '댓글';
        return back()->with('success', "신고를 처리 완료했습니다. 해당 {$label}이 블라인드 처리되었습니다.");
    }

    public function dismiss(Request $request, Report $report)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $report->update([
            'status'     => 'dismissed',
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', '신고를 기각 처리했습니다.');
    }
}
