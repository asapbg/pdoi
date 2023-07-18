<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @if(isset($baseMap) && is_array($baseMap))
        <sitemap>
            <loc>{{ route('sitemap.sub') }}</loc>
            <lastmod>{{ \Carbon\Carbon::parse($baseMap[0]->last_mod)->toIso8601String() }}</lastmod>
        </sitemap>
    @endif
    @if(isset($subMaps) && is_array($subMaps) && sizeof($subMaps))
        @foreach($subMaps as $item)
        <sitemap>
            <loc>{{ route('sitemap.sub', ['subject_id' => $item->id]) }}</loc>
            <lastmod>{{ \Carbon\Carbon::parse($item->last_mod)->toIso8601String() }}</lastmod>
        </sitemap>
        @endforeach
    @endif
</sitemapindex>
