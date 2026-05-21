<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::latest();

        if ($request->status && in_array($request->status, ['pending', 'answered'])) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('name',    'like', "%{$request->search}%")
                  ->orWhere('email',   'like', "%{$request->search}%");
            });
        }

        $inquiries = $query->paginate(20)->appends($request->query());
        $pendingCount = Inquiry::where('status', 'pending')->count();

        return Inertia::render('Admin/Inquiry/Index', [
            'inquiries'    => $inquiries,
            'pendingCount' => $pendingCount,
            'filters'      => $request->only(['status', 'search']),
        ]);
    }

    public function show(Inquiry $inquiry)
    {
        return Inertia::render('Admin/Inquiry/Show', [
            'inquiry' => $inquiry->load('answeredBy'),
        ]);
    }

    public function answer(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'answer' => 'required|string|max:3000',
        ]);

        $inquiry->update([
            'answer'      => $request->answer,
            'status'      => 'answered',
            'answered_at' => now(),
            'answered_by' => Auth::id(),
        ]);

        Cache::forget('pending_inquiry_count');

        return redirect()->route('admin.inquiries.show', $inquiry->id)
            ->with('success', '답변이 저장되었습니다.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')
            ->with('success', '문의가 삭제되었습니다.');
    }
}
