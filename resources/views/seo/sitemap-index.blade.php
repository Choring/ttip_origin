<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- 메인, 카테고리, 법적 고지 페이지 -->
    <sitemap>
        <loc>{{ url('/sitemap-main.xml') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </sitemap>

    <!-- 관광지 -->
    <sitemap>
        <loc>{{ url('/sitemap-spots.xml') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </sitemap>

    <!-- 게시글 페이징 (1000개 단위) -->
    @for ($i = 1; $i <= max(1, $pages); $i++)
    <sitemap>
        <loc>{{ url('/sitemap-posts-' . $i . '.xml') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </sitemap>
    @endfor
</sitemapindex>
