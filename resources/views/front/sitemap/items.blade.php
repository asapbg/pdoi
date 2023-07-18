<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @if(sizeof($items))
        @foreach($items as $item)
        <url>
            <loc>{{ $item['url'] }}</loc>
            @if(isset($item['last_mod']) && !empty($item['last_mod']))
                <lastmod>{{ \Carbon\Carbon::parse($item['last_mod'])->toIso8601String() }}</lastmod>
            @endif
        </url>
        @endforeach
    @endif
</urlset>
