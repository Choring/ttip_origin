<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>문의 답변 - ttip</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:'Apple SD Gothic Neo','Noto Sans KR',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);max-width:560px;width:100%;">

                    <!-- 헤더 -->
                    <tr>
                        <td style="background:#FF6B2C;padding:32px 40px;text-align:center;">
                            <div style="color:#ffffff;font-size:30px;font-weight:900;letter-spacing:-1px;line-height:1;">ttip</div>
                            <div style="color:rgba(255,255,255,0.85);font-size:13px;margin-top:6px;">세상을 바꾸는 작은 팁</div>
                        </td>
                    </tr>

                    <!-- 본문 -->
                    <tr>
                        <td style="padding:40px 40px 20px 40px;">

                            <!-- 이모지 -->
                            <p style="font-size:52px;text-align:center;margin:0 0 20px 0;">✅</p>

                            <!-- 제목 -->
                            <h1 style="font-size:22px;font-weight:800;color:#111827;text-align:center;margin:0 0 10px 0;line-height:1.4;">
                                문의 답변이 등록되었습니다
                            </h1>
                            <p style="font-size:14px;color:#6b7280;text-align:center;margin:0 0 32px 0;">
                                안녕하세요, <strong style="color:#111827;">{{ $inquiry->name }}</strong>님!<br>
                                문의하신 내용에 대한 답변을 아래에서 확인하세요.
                            </p>

                            <!-- 원래 문의 내용 -->
                            <div style="background:#f9fafb;border-radius:12px;padding:20px 24px;margin-bottom:20px;border-left:4px solid #e5e7eb;">
                                <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 8px 0;">문의 제목</p>
                                <p style="font-size:15px;font-weight:700;color:#374151;margin:0;">{{ $inquiry->subject }}</p>
                            </div>

                            <!-- 답변 내용 -->
                            <div style="background:#eff6ff;border-radius:12px;padding:20px 24px;margin-bottom:28px;border-left:4px solid #6366f1;">
                                <p style="font-size:11px;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 12px 0;">ttip 운영팀 답변</p>
                                <p style="font-size:14px;color:#374151;line-height:1.8;margin:0;white-space:pre-line;">{{ $inquiry->answer }}</p>
                            </div>

                            <!-- 답변 일시 -->
                            <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0 0 28px 0;">
                                답변 등록일: {{ $inquiry->answered_at?->format('Y년 m월 d일 H:i') }}
                            </p>

                            <!-- 구분선 -->
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px 0;">

                            <!-- 추가 문의 안내 -->
                            <p style="font-size:13px;color:#6b7280;text-align:center;line-height:1.8;margin:0;">
                                추가로 궁금한 점이 있으시면 언제든지<br>
                                <a href="{{ url('/inquiry') }}" style="color:#FF6B2C;font-weight:700;text-decoration:none;">문의하기 페이지</a>를 통해 문의해 주세요.
                            </p>

                        </td>
                    </tr>

                    <!-- 푸터 -->
                    <tr>
                        <td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;">
                            <p style="font-size:12px;color:#9ca3af;line-height:1.7;margin:0 0 6px 0;">
                                본인이 문의하지 않으셨다면 이 메일을 무시해 주세요.<br>
                                문의: <a href="mailto:skyhonor201@gmail.com" style="color:#FF6B2C;text-decoration:none;">skyhonor201@gmail.com</a>
                            </p>
                            <p style="font-size:11px;color:#d1d5db;margin:0;">
                                © {{ date('Y') }} ttip. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
