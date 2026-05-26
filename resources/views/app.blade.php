<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="naver-site-verification" content="b1763caa574f418b81b82307627d9afa1e13483a" />

        <title inertia>{{ config('app.name', 'ttip') }}</title>

        @php
            $component   = $page['component'] ?? '';
            $post        = $page['props']['post']       ?? null;
            $spot        = $page['props']['spot']       ?? null;
            $restaurant  = $page['props']['restaurant'] ?? null;

            $siteUrl    = config('app.url');
            $siteName   = 'ttip';
            $defaultImg = asset('images/og-default.png');

            // ── 관광지 상세 페이지 ──────────────────────────────────────────
            if ($spot) {
                $displayTitle = ($spot['title'] ?? '관광지') . ' - 대구 관광 | ttip';
                $rawOverview  = $spot['overview'] ?? '';
                $cleanOverview = strip_tags(preg_replace('/&nbsp;/', ' ', $rawOverview));
                $description  = $cleanOverview
                    ? mb_substr($cleanOverview, 0, 120) . (mb_strlen($cleanOverview) > 120 ? '...' : '')
                    : ($spot['title'] ?? '') . ' - 대구 관광지 정보를 ttip에서 확인하세요.';
                $image        = $spot['image'] ?? $spot['thumbnail'] ?? $defaultImg;
                $url          = $siteUrl . '/tour/' . ($spot['contentId'] ?? '');
                $ogType       = 'article';
                $keywords     = implode(', ', array_filter([
                    $spot['title'] ?? '', '대구 관광', '대구 여행', '대구 관광지',
                    $spot['addr1'] ? explode(' ', $spot['addr1'])[1] ?? '' : '',
                    'ttip', '대구',
                ]));
                $jsonLd = [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'TouristAttraction',
                    'name'        => $spot['title'] ?? '',
                    'description' => $description,
                    'url'         => $url,
                    'image'       => $image ?: $defaultImg,
                    'address'     => [
                        '@type'           => 'PostalAddress',
                        'streetAddress'   => ($spot['addr1'] ?? '') . ' ' . ($spot['addr2'] ?? ''),
                        'addressLocality' => '대구광역시',
                        'addressCountry'  => 'KR',
                    ],
                    'telephone'   => $spot['tel'] ?? null,
                    'geo'         => ($spot['mapX'] && $spot['mapY']) ? [
                        '@type'     => 'GeoCoordinates',
                        'longitude' => $spot['mapX'],
                        'latitude'  => $spot['mapY'],
                    ] : null,
                ];

            // ── 관광지 목록 페이지 ──────────────────────────────────────────
            } elseif (str_starts_with($component, 'Tour/')) {
                $displayTitle = '대구 관광지 | ttip';
                $description  = '대구의 숨겨진 명소부터 인기 관광지까지, ttip에서 한눈에 확인하세요.';
                $image        = $defaultImg;
                $url          = $siteUrl . '/tour';
                $ogType       = 'website';
                $keywords     = '대구 관광, 대구 여행, 대구 관광지 추천, 대구 명소, 대구광역시 여행, ttip';
                $jsonLd       = null;

            // ── 맛집 상세 페이지 ────────────────────────────────────────────
            } elseif ($restaurant) {
                $displayTitle = ($restaurant['title'] ?? '맛집') . ' - 대구 맛집 | ttip';
                $description  = ($restaurant['overview'] ?? null)
                    ? mb_substr(strip_tags($restaurant['overview']), 0, 120) . '...'
                    : ($restaurant['title'] ?? '') . ' - 대구 맛집 정보를 ttip에서 확인하세요.';
                $image        = $restaurant['image'] ?? $defaultImg;
                $url          = $siteUrl . '/restaurants/' . ($restaurant['contentId'] ?? '');
                $ogType       = 'article';
                $keywords     = implode(', ', array_filter([
                    $restaurant['title'] ?? '', '대구 맛집', '대구 맛집 추천', '대구 음식점',
                    $restaurant['category'] ?? '', 'ttip', '대구',
                ]));
                $jsonLd = [
                    '@context'    => 'https://schema.org',
                    '@type'       => 'Restaurant',
                    'name'        => $restaurant['title'] ?? '',
                    'description' => $description,
                    'url'         => $url,
                    'image'       => $image ?: $defaultImg,
                    'address'     => [
                        '@type'           => 'PostalAddress',
                        'streetAddress'   => $restaurant['address'] ?? '',
                        'addressLocality' => '대구광역시',
                        'addressCountry'  => 'KR',
                    ],
                    'telephone'   => $restaurant['tel'] ?? null,
                    'servesCuisine' => $restaurant['category'] ?? null,
                    'geo'         => ($restaurant['mapX'] && $restaurant['mapY']) ? [
                        '@type'     => 'GeoCoordinates',
                        'longitude' => $restaurant['mapX'],
                        'latitude'  => $restaurant['mapY'],
                    ] : null,
                ];

            // ── 맛집 목록 페이지 ────────────────────────────────────────────
            } elseif (str_starts_with($component, 'Restaurants/')) {
                $displayTitle = '대구 맛집 | ttip';
                $description  = '대구 한식, 카페, 양식 등 다양한 맛집 정보를 ttip에서 확인하세요.';
                $image        = $defaultImg;
                $url          = $siteUrl . '/restaurants';
                $ogType       = 'website';
                $keywords     = '대구 맛집, 대구 음식점, 대구 맛집 추천, 대구 카페, 대구 한식, 대구 맛집 지도, ttip';
                $jsonLd       = null;

            // ── 문화행사 목록 페이지 ────────────────────────────────────────
            } elseif (str_starts_with($component, 'Events/')) {
                $displayTitle = '대구 문화행사 | ttip';
                $description  = '대구 공연, 전시, 축제 등 다양한 문화행사 일정을 ttip에서 확인하세요.';
                $image        = $defaultImg;
                $url          = $siteUrl . '/events';
                $ogType       = 'website';
                $keywords     = '대구 문화행사, 대구 공연, 대구 전시, 대구 축제, 대구 행사, 대구 문화, ttip';
                $jsonLd       = null;

            // ── 커뮤니티 포스트 상세 ────────────────────────────────────────
            } elseif ($post) {
                $displayTitle = ($post['title'] ?? '') . ' - ttip';
                $summary      = $post['summary'] ?? null;
                $description  = $summary
                    ? (is_array($summary) ? implode(' ', $summary) : $summary)
                    : '대구 관광지, 맛집, 문화행사 정보와 지역 커뮤니티. 대구의 매력을 ttip에서 발견하세요.';
                $image        = $post['card_image_url'] ?? $defaultImg;
                $url          = $siteUrl . '/posts/' . ($post['id'] ?? '');
                $ogType       = 'article';
                $postKeywords = [];
                if (isset($post['tags']) && is_array($post['tags'])) {
                    $postKeywords = $post['tags'];
                }
                if (isset($post['category']['name'])) {
                    $postKeywords[] = $post['category']['name'];
                }
                $keywords = implode(', ', array_filter(
                    array_merge($postKeywords, ['ttip', '대구', '정보공유', '커뮤니티'])
                ));
                $jsonLd = [
                    '@context'         => 'https://schema.org',
                    '@type'            => 'Article',
                    'headline'         => $post['title'] ?? '',
                    'description'      => $description,
                    'url'              => $url,
                    'image'            => $image,
                    'datePublished'    => $post['created_at'] ?? '',
                    'dateModified'     => $post['updated_at'] ?? '',
                    'author'           => ['@type' => 'Person', 'name' => $post['user']['name'] ?? 'ttip'],
                    'publisher'        => ['@type' => 'Organization', 'name' => $siteName, 'url' => $siteUrl],
                ];

            // ── 기본 (홈 등) ────────────────────────────────────────────────
            } else {
                $displayTitle = 'ttip - 대구를 더 잘 알고 싶다면';
                $description  = '대구 관광지, 맛집, 문화 행사 정보와 대구 시민 커뮤니티. 대구의 모든 것, ttip에서 확인하세요.';
                $image        = $defaultImg;
                $url          = url()->current();
                $ogType       = 'website';
                $keywords     = '대구, 대구 관광, 대구 맛집, 대구 여행, 대구 커뮤니티, ttip';
                $jsonLd       = null;
            }

            $twitterCard = ($image && $image !== $defaultImg) ? 'summary_large_image' : 'summary';
        @endphp

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ $url }}">

        <!-- Meta Tags -->
        <meta name="description" content="{{ $description }}">
        <meta name="keywords" content="{{ $keywords }}">
        <meta name="author" content="ttip Team">
        <meta name="robots" content="index, follow">

        <!-- Open Graph -->
        <meta property="og:type"        content="{{ $ogType }}">
        <meta property="og:site_name"   content="{{ $siteName }}">
        <meta property="og:url"         content="{{ $url }}">
        <meta property="og:title"       content="{{ $displayTitle }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image"       content="{{ $image }}">
        <meta property="og:locale"      content="ko_KR">

        <!-- Twitter Card -->
        <meta property="twitter:card"        content="{{ $twitterCard }}">
        <meta property="twitter:url"         content="{{ $url }}">
        <meta property="twitter:title"       content="{{ $displayTitle }}">
        <meta property="twitter:description" content="{{ $description }}">
        <meta property="twitter:image"       content="{{ $image }}">

        <!-- JSON-LD 구조화 데이터 -->
        @if($jsonLd)
        <script type="application/ld+json">
            {!! json_encode(array_filter($jsonLd, fn($v) => $v !== null), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
        @endif

        <!-- Kakao SDK -->
        <script src="https://t1.kakaocdn.net/kakao_js_sdk/2.7.2/kakao.min.js"
                integrity="sha384-TiCUE00h649CAMonG018J2ujOgDKW/kVWlChEuu4jK2vxfAAD0eZxzCKakxg55G4"
                crossorigin="anonymous"></script>
        <script>
            if (window.Kakao && !window.Kakao.isInitialized()) {
                window.Kakao.init('{{ env('KAKAO_JS_KEY') }}');
            }
        </script>

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
