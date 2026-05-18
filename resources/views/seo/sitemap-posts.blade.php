<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset 
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
>
    {{-- 게시글 리스트 --}}
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
</urlset>
