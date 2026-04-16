<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
>
    {{-- 홈 --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
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

    {{-- 게시글 (일반 타입만, 최근 500개) --}}
    @foreach($posts as $post)
    <url>
        <loc>{{ route('posts.show', $post->id) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
        @if($post->card_image_url)
        <image:image>
            <image:loc>{{ $post->card_image_url }}</image:loc>
            <image:title>{{ htmlspecialchars($post->title) }}</image:title>
        </image:image>
        @endif
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
