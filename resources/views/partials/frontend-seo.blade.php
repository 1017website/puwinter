@php
    $seoTitle = $frontend['seo_title'];
    $seoDescription = $frontend['seo_description'];
    $canonicalUrl = $frontend['seo_canonical_url'] ?: url()->current();
    $ogTitle = $frontend['seo_og_title'] ?: $seoTitle;
    $ogDescription = $frontend['seo_og_description'] ?: $seoDescription;
    $ogImage = $frontend['seo_og_image'] ?: asset('images/logo.png');
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
@if($frontend['seo_keywords'])<meta name="keywords" content="{{ $frontend['seo_keywords'] }}">@endif
<meta name="robots" content="{{ $frontend['seo_robots'] }}">
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="Puwinter">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'EducationalOrganization',
    'name' => 'Puwinter',
    'url' => $canonicalUrl,
    'logo' => asset('images/logo.png'),
    'description' => $seoDescription,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
