<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
>
    @foreach($spots as $spot)
    <url>
        <loc>{{ url('/tour/' . $spot->content_id) }}</loc>
        <lastmod>{{ $spot->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        @if(!empty($spot->image))
        <image:image>
            <image:loc>{{ $spot->image }}</image:loc>
            @if(!empty($spot->title))
            <image:title>{{ htmlspecialchars($spot->title) }}</image:title>
            @endif
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
