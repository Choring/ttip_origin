<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이메일 인증 - ttip</title>
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
                            <p style="font-size:52px;text-align:center;margin:0 0 20px 0;">📧</p>

                            <!-- 제목 -->
                            <h1 style="font-size:22px;font-weight:800;color:#111827;text-align:center;margin:0 0 14px 0;line-height:1.4;">
                                이메일 인증을 완료해 주세요!
                            </h1>

                            <!-- 설명 -->
                            <p style="font-size:14px;color:#6b7280;text-align:center;line-height:1.8;margin:0 0 32px 0;">
                                ttip에 가입해 주셔서 감사합니다.<br>
                                아래 버튼을 클릭하면 이메일 인증이 완료되고<br>
                                ttip의 모든 기능을 이용하실 수 있어요.
                            </p>

                            <!-- 인증 버튼 -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                        <a href="{{ $url }}"
                                           style="display:inline-block;background:#FF6B2C;color:#ffffff;text-decoration:none;font-size:16px;font-weight:800;padding:16px 48px;border-radius:50px;letter-spacing:-0.3px;">
                                            이메일 인증하기 →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- 만료 안내 -->
                            <p style="font-size:12px;color:#9ca3af;text-align:center;margin:0 0 28px 0;">
                                이 버튼은 <strong style="color:#6b7280;">60분</strong> 후 만료됩니다.
                            </p>

                            <!-- 구분선 -->
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:0 0 24px 0;">

                            <!-- fallback 링크 -->
                            <p style="font-size:12px;color:#9ca3af;text-align:center;line-height:1.7;margin:0;">
                                버튼이 클릭되지 않으면 아래 링크를 브라우저에 직접 붙여넣으세요.<br>
                                <a href="{{ $url }}" style="color:#FF6B2C;word-break:break-all;">{{ $url }}</a>
                            </p>

                        </td>
                    </tr>

                    <!-- 푸터 -->
                    <tr>
                        <td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6;">
                            <p style="font-size:12px;color:#9ca3af;line-height:1.7;margin:0 0 6px 0;">
                                본인이 가입 요청을 하지 않았다면 이 메일을 무시해 주세요.<br>
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
