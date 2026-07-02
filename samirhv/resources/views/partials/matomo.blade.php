{{-- Matomo Analytics (self-hosted em a.blue3.cloud). Só injeta o rastreador
     quando MATOMO_ENABLED=true E MATOMO_SITE_ID definido — ver config/services.php
     e a seção Matomo do .env. Assíncrono, não bloqueia o render. --}}
@php
    $matomoUrl = rtrim((string) config('services.matomo.url'), '/') . '/';
    $matomoSiteId = config('services.matomo.site_id');
    $matomoCookieDomain = config('services.matomo.cookie_domain');
@endphp
@if (config('services.matomo.enabled') && $matomoSiteId)
    <!-- Matomo -->
    <script>
      var _paq = window._paq = window._paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      @if ($matomoCookieDomain)
      _paq.push(["setCookieDomain", "{{ $matomoCookieDomain }}"]);
      @endif
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="{{ $matomoUrl }}";
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '{{ $matomoSiteId }}']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <noscript><p><img referrerpolicy="no-referrer-when-downgrade" src="{{ $matomoUrl }}matomo.php?idsite={{ $matomoSiteId }}&amp;rec=1" style="border:0;" alt="" /></p></noscript>
    <!-- End Matomo Code -->
@endif
