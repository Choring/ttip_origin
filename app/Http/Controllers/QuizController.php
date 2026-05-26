<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\QuizQuestion;

class QuizController extends Controller
{
    public function index()
    {
        // 랜덤 10문제 선정
        $questions = QuizQuestion::where('is_active', true)
            ->inRandomOrder()
            ->limit(10)
            ->get(['id', 'dialect', 'answer', 'wrong1', 'wrong2', 'explanation'])
            ->map(function ($q) {
                // 보기 3개를 랜덤 순서로 섞기
                $choices = [$q->answer, $q->wrong1, $q->wrong2];
                shuffle($choices);
                return [
                    'id'          => $q->id,
                    'dialect'     => $q->dialect,
                    'choices'     => $choices,
                    'answer'      => $q->answer,
                    'explanation' => $q->explanation,
                ];
            });

        return Inertia::render('Quiz/Index', [
            'questions' => $questions,
        ]);
    }
}
