<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'ttip') }}</title>

        <!-- Meta Tags -->
        <meta name="description" content="ttip - 당신의 일상에 특별한 팁을 더하는 커뮤니티 공간입니다. 유용한 정보와 즐거운 이야기를 나누어보세요.">
        <meta name="keywords" content="ttip, 팁, 정보공유, 커뮤니티, 도파스테이션, 일상꿀팁">
        <meta name="author" content="ttip Team">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://dopastation.com/">
        <meta property="og:title" content="ttip - 특별한 팁이 가득한 공간">
        <meta property="og:description" content="당신의 일상에 특별한 팁을 더하는 커뮤니티 공간, ttip입니다.">
        <meta property="og:image" content="https://dopastation.com/og-image.png">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="https://dopastation.com/">
        <meta property="twitter:title" content="ttip - 특별한 팁이 가득한 공간">
        <meta property="twitter:description" content="당신의 일상에 특별한 팁을 더하는 커뮤니티 공간, ttip입니다.">
        <meta property="twitter:image" content="https://dopastation.com/og-image.png">

        <!-- Google AdSense -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8069193438069319"
             crossorigin="anonymous"></script>

        <!-- Favicon -->
        <link rel="shortcut icon" href="/favicon.ico">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
