<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- 홈 --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- 관광지 목록 --}}
    <url>
        <loc>{{ url('/tour') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- 카테고리 피드 (공지 카테고리 제외) --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ url('/?category=' . $category->slug) }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- 법적 페이지 --}}
    <url>
        <loc>{{ url('/terms') }}</loc>
        <lastmod>2026-04-12T00:00:00+09:00</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ url('/privacy') }}</loc>
        <lastmod>2026-04-12T00:00:00+09:00</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
</urlset>
