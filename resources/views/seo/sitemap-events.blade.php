<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
>
    @foreach($events as $event)
    <url>
        <loc>{{ url('/events/' . $event->event_seq) }}</loc>
        <lastmod>{{ $event->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
        @if(!empty($event->image))
        <image:image>
            <image:loc>{{ $event->image }}</image:loc>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
