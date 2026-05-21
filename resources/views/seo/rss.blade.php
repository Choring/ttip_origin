<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>{{ $siteName }}</title>
    <link>{{ $siteUrl }}</link>
    <description>{{ $description }}</description>
    <language>ko</language>
    <lastBuildDate>{{ $buildDate }}</lastBuildDate>
    <atom:link href="{{ $siteUrl }}/rss.xml" rel="self" type="application/rss+xml" />

    @foreach ($posts as $post)
    <item>
      <title><![CDATA[{{ $post->title }}]]></title>
      <link>{{ $siteUrl }}/posts/{{ $post->id }}</link>
      <guid isPermaLink="true">{{ $siteUrl }}/posts/{{ $post->id }}</guid>
      <pubDate>{{ $post->created_at->toRfc2822String() }}</pubDate>
      @if ($post->category)
      <category><![CDATA[{{ $post->category->name }}]]></category>
      @endif
      @if ($post->user)
      <author>{{ $post->user->name }}</author>
      @endif
      <description><![CDATA[
        @if (is_array($post->summary) && !empty($post->summary))
          {{ implode(' ', $post->summary) }}
        @elseif (is_string($post->summary) && $post->summary)
          {{ $post->summary }}
        @else
          {{ mb_substr(strip_tags($post->content ?? ''), 0, 200) }}
        @endif
      ]]></description>
    </item>
    @endforeach

  </channel>
</rss>
