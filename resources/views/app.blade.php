<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'ttip') }}</title>

        @php
            $post = $page['props']['post'] ?? null;
            $title = isset($post['title']) ? $post['title'] : null;
            $displayTitle = $title ? "$title - ttip" : config('app.name', 'ttip');
            
            $summary = isset($post['summary']) ? $post['summary'] : null;
            $description = $summary ? (is_array($summary) ? implode(' ', $summary) : $summary) : 'ttip - 당신의 일상에 특별한 팁을 더하는 커뮤니티 공간입니다.';
            
            $image = isset($post['card_image_url']) ? $post['card_image_url'] : asset('og-image.png');
            $url = isset($post['id']) ? route('posts.show', $post['id']) : url()->current();
        @endphp

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ $url }}">

        <!-- Meta Tags -->
        <meta name="description" content="{{ $description }}">
        <meta name="keywords" content="ttip, 팁, 정보공유, 커뮤니티, 도파스테이션, 일상꿀팁">
        <meta name="author" content="ttip Team">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ $post ? 'article' : 'website' }}">
        <meta property="og:url" content="{{ $url }}">
        <meta property="og:title" content="{{ $displayTitle }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image" content="{{ $image }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="{{ isset($post['card_image_url']) ? 'summary_large_image' : 'summary' }}">
        <meta property="twitter:url" content="{{ $url }}">
        <meta property="twitter:title" content="{{ $displayTitle }}">
        <meta property="twitter:description" content="{{ $description }}">
        <meta property="twitter:image" content="{{ $image }}">

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
