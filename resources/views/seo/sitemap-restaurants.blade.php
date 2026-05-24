<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
>
    @foreach($restaurants as $restaurant)
    <url>
        <loc>{{ url('/restaurants/' . $restaurant->content_id) }}</loc>
        <lastmod>{{ $restaurant->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        @if(!empty($restaurant->image))
        <image:image>
            <image:loc>{{ $restaurant->image }}</image:loc>
            @if(!empty($restaurant->title))
            <image:title>{{ htmlspecialchars($restaurant->title) }}</image:title>
            @endif
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
