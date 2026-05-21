<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InquiryController extends Controller
{
    public function create()
    {
        return Inertia::render('Inquiry/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:50',
            'email'   => 'required|email|max:100',
            'subject' => 'required|string|max:100',
            'content' => 'required|string|max:2000',
        ]);

        Inquiry::create($request->only('name', 'email', 'subject', 'content'));

        return redirect()->route('inquiry.create')
            ->with('success', '문의가 접수되었습니다. 입력하신 이메일로 답변 드리겠습니다.');
    }
}
