<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // ── 기초 ──────────────────────────────────────────────────────
            [
                'dialect'     => '가가 가가?',
                'answer'      => '그 아이가 그 아이야?',
                'wrong1'      => '거기 가도 돼?',
                'wrong2'      => '가방 가져가?',
                'explanation' => '"가"는 "그 아이(걔)"를 뜻하는 경상도 말이에요.',
            ],
            [
                'dialect'     => '니 밥 묵었나?',
                'answer'      => '너 밥 먹었어?',
                'wrong1'      => '밥 줄까?',
                'wrong2'      => '밥이 맛있나?',
                'explanation' => '"묵다"는 경상도에서 "먹다"를 뜻해요.',
            ],
            [
                'dialect'     => '와 이카노',
                'answer'      => '왜 이래',
                'wrong1'      => '왜 왔어',
                'wrong2'      => '어디 가',
                'explanation' => '"와"는 "왜", "이카다"는 "이러다"의 경상도 말이에요.',
            ],
            [
                'dialect'     => '머하노?',
                'answer'      => '뭐 해?',
                'wrong1'      => '뭐 먹어?',
                'wrong2'      => '어디 가?',
                'explanation' => '"머"는 "뭐", "하노"는 "해?"의 경상도 말이에요.',
            ],
            [
                'dialect'     => '마 됐다',
                'answer'      => '이제 됐어 / 그만해',
                'wrong1'      => '맞아 됐어',
                'wrong2'      => '많이 됐다',
                'explanation' => '"마"는 경상도에서 "이제", "그만" 이라는 뜻이에요.',
            ],
            [
                'dialect'     => '아이가',
                'answer'      => '아니잖아 / 아니야',
                'wrong1'      => '아이가 왔다',
                'wrong2'      => '맞잖아',
                'explanation' => '"아이가"는 경상도에서 "아니잖아"라는 뜻이에요.',
            ],
            [
                'dialect'     => '그기 어딘데?',
                'answer'      => '거기가 어딘데?',
                'wrong1'      => '그게 뭔데?',
                'wrong2'      => '그기가 어때?',
                'explanation' => '"그기"는 경상도에서 "거기"를 뜻해요.',
            ],
            [
                'dialect'     => '뭐라카노?',
                'answer'      => '뭐라고 했어?',
                'wrong1'      => '뭐 먹었어?',
                'wrong2'      => '뭐가 좋아?',
                'explanation' => '"카다"는 경상도에서 "하다"를 뜻해요. "뭐라 하노"가 변형된 말이에요.',
            ],
            [
                'dialect'     => '억수로 덥다',
                'answer'      => '엄청 더워',
                'wrong1'      => '약간 더워',
                'wrong2'      => '조금 시원해',
                'explanation' => '"억수로"는 경상도에서 "매우", "엄청"을 뜻해요.',
            ],
            [
                'dialect'     => '니는 어데 사노?',
                'answer'      => '너는 어디 살아?',
                'wrong1'      => '너는 뭐 먹어?',
                'wrong2'      => '너는 어디 가?',
                'explanation' => '"어데"는 "어디", "사노"는 "살아?"의 경상도 말이에요.',
            ],
            // ── 중급 ──────────────────────────────────────────────────────
            [
                'dialect'     => '빨리 오이소',
                'answer'      => '빨리 오세요',
                'wrong1'      => '빨리 가세요',
                'wrong2'      => '천천히 오세요',
                'explanation' => '"오이소"는 경상도에서 "오세요"의 높임말이에요.',
            ],
            [
                'dialect'     => '고맙심더',
                'answer'      => '감사합니다',
                'wrong1'      => '미안합니다',
                'wrong2'      => '잘 먹겠습니다',
                'explanation' => '"고맙심더"는 경상도 사투리로 "감사합니다"예요.',
            ],
            [
                'dialect'     => '우짜노',
                'answer'      => '어떡해 / 어쩌지',
                'wrong1'      => '왜 그래',
                'wrong2'      => '뭐 하지',
                'explanation' => '"우짜다"는 "어쩌다", "어떡하다"의 경상도 말이에요.',
            ],
            [
                'dialect'     => '씨이 맞다',
                'answer'      => '정말 맞아',
                'wrong1'      => '조금 맞아',
                'wrong2'      => '하나도 안 맞아',
                'explanation' => '"씨이"는 경상도에서 강조의 의미로 쓰이는 감탄사예요.',
            ],
            [
                'dialect'     => '인자 가자',
                'answer'      => '이제 가자',
                'wrong1'      => '나중에 가자',
                'wrong2'      => '빨리 가자',
                'explanation' => '"인자"는 경상도에서 "이제"를 뜻해요.',
            ],
            [
                'dialect'     => '저거 머꼬?',
                'answer'      => '저거 뭐야?',
                'wrong1'      => '저거 비싸?',
                'wrong2'      => '저거 먹어?',
                'explanation' => '"머꼬"는 "뭐야?", "뭐고?"의 경상도 말이에요.',
            ],
            [
                'dialect'     => '문 잠갔나?',
                'answer'      => '문 잠갔어?',
                'wrong1'      => '문 열었어?',
                'wrong2'      => '문 고쳤어?',
                'explanation' => '경상도에서는 어미 "-나?"로 의문문을 만들어요.',
            ],
            [
                'dialect'     => '한 마 해봐라',
                'answer'      => '한번 해봐',
                'wrong1'      => '많이 해봐',
                'wrong2'      => '그냥 해',
                'explanation' => '"마"는 여기서 "번"과 비슷한 의미로 쓰였어요.',
            ],
            [
                'dialect'     => '아 몰랐나?',
                'answer'      => '몰랐어?',
                'wrong1'      => '알았어?',
                'wrong2'      => '알고 싶어?',
                'explanation' => '경상도에서 "아"는 문장 앞에 붙는 감탄사예요.',
            ],
            [
                'dialect'     => '거 참 맞다 아이가',
                'answer'      => '그거 정말 맞잖아',
                'wrong1'      => '그거 틀리잖아',
                'wrong2'      => '그거 모르잖아',
                'explanation' => '"맞다 아이가"는 "맞잖아"라는 뜻의 경상도 표현이에요.',
            ],
            // ── 고급 ──────────────────────────────────────────────────────
            [
                'dialect'     => '니 오데 갔다 왔노?',
                'answer'      => '너 어디 갔다 왔어?',
                'wrong1'      => '너 언제 왔어?',
                'wrong2'      => '너 왜 갔어?',
                'explanation' => '"오데"는 "어디"의 경상도 말이에요.',
            ],
            [
                'dialect'     => '밥 묵고 가이소',
                'answer'      => '밥 먹고 가세요',
                'wrong1'      => '밥 사고 가세요',
                'wrong2'      => '밥 주고 가세요',
                'explanation' => '"묵고"는 "먹고", "가이소"는 "가세요"의 경상도 말이에요.',
            ],
            [
                'dialect'     => '이거 얼매고?',
                'answer'      => '이거 얼마예요?',
                'wrong1'      => '이거 뭐예요?',
                'wrong2'      => '이거 어때요?',
                'explanation' => '"얼매"는 경상도에서 "얼마"를 뜻해요.',
            ],
            [
                'dialect'     => '우리 집에 한 번 오이소',
                'answer'      => '우리 집에 한 번 오세요',
                'wrong1'      => '우리 집에 가세요',
                'wrong2'      => '우리 집에 살아요',
                'explanation' => '"오이소"는 경상도 높임말로 "오세요"예요.',
            ],
            [
                'dialect'     => '그 사람 참 무섭다 아이가',
                'answer'      => '그 사람 정말 무섭잖아',
                'wrong1'      => '그 사람 안 무섭잖아',
                'wrong2'      => '그 사람 멋있잖아',
                'explanation' => '"아이가"는 "~잖아"라는 뜻으로 자주 쓰여요.',
            ],
            [
                'dialect'     => '억수로 맛있다 아이가',
                'answer'      => '엄청 맛있잖아',
                'wrong1'      => '조금 맛있잖아',
                'wrong2'      => '별로 맛없잖아',
                'explanation' => '"억수로"(엄청) + "맛있다 아이가"(맛있잖아)의 조합이에요.',
            ],
            [
                'dialect'     => '와 그카노?',
                'answer'      => '왜 그래?',
                'wrong1'      => '어디 가?',
                'wrong2'      => '뭐 해?',
                'explanation' => '"그카다"는 "그러다"의 경상도 말이에요.',
            ],
            [
                'dialect'     => '니 오늘 머 입었노?',
                'answer'      => '너 오늘 뭐 입었어?',
                'wrong1'      => '너 오늘 뭐 먹었어?',
                'wrong2'      => '너 오늘 어디 갔어?',
                'explanation' => '"머"는 "뭐", "-었노"는 "-었어?"의 경상도 어미예요.',
            ],
            [
                'dialect'     => '저 사람 좀 이상하다 카더라',
                'answer'      => '저 사람 좀 이상하다고 하더라',
                'wrong1'      => '저 사람 좀 이상하게 생겼더라',
                'wrong2'      => '저 사람 좀 이상한 척 하더라',
                'explanation' => '"카더라"는 경상도에서 "하더라"를 뜻해요.',
            ],
            [
                'dialect'     => '행님, 한 잔 하입시다',
                'answer'      => '형, 한 잔 합시다',
                'wrong1'      => '형, 밥 먹읍시다',
                'wrong2'      => '형, 가봅시다',
                'explanation' => '"행님"은 "형님"의 경상도 말, "하입시다"는 "합시다"예요.',
            ],
        ];

        foreach ($questions as $question) {
            DB::table('quiz_questions')->insert([
                ...$question,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
