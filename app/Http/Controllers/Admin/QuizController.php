<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = QuizQuestion::latest();

        if ($request->search) {
            $query->where('dialect', 'like', "%{$request->search}%")
                  ->orWhere('answer', 'like', "%{$request->search}%");
        }

        $questions = $query->paginate(20)->appends($request->query());

        return Inertia::render('Admin/Quiz/Index', [
            'questions' => $questions,
            'filters'   => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Quiz/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dialect'     => 'required|string|max:100',
            'answer'      => 'required|string|max:100',
            'wrong1'      => 'required|string|max:100',
            'wrong2'      => 'required|string|max:100',
            'explanation' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        QuizQuestion::create($validated);

        return redirect()->route('admin.quiz.index')
            ->with('success', '퀴즈 문제가 등록되었습니다.');
    }

    public function edit(QuizQuestion $quiz)
    {
        return Inertia::render('Admin/Quiz/Edit', [
            'question' => $quiz,
        ]);
    }

    public function update(Request $request, QuizQuestion $quiz)
    {
        $validated = $request->validate([
            'dialect'     => 'required|string|max:100',
            'answer'      => 'required|string|max:100',
            'wrong1'      => 'required|string|max:100',
            'wrong2'      => 'required|string|max:100',
            'explanation' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $quiz->update($validated);

        return redirect()->route('admin.quiz.index')
            ->with('success', '퀴즈 문제가 수정되었습니다.');
    }

    public function destroy(QuizQuestion $quiz)
    {
        $quiz->delete();

        return back()->with('success', '퀴즈 문제가 삭제되었습니다.');
    }

    public function toggleActive(QuizQuestion $quiz)
    {
        $quiz->update(['is_active' => !$quiz->is_active]);

        return back()->with('success', $quiz->is_active ? '문제가 활성화되었습니다.' : '문제가 비활성화되었습니다.');
    }
}
