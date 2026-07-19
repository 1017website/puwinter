@if($frontend['google_tag_manager_id'])
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $frontend['google_tag_manager_id'] }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
@endif
@if($frontend['meta_pixel_id'])
<noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id={{ $frontend['meta_pixel_id'] }}&ev=PageView&noscript=1"></noscript>
@endif
