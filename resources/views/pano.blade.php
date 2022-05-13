<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <meta name="panoapp-root" content="{{ resolve(\Pano\Pano::class)->currentApp()->getAppUrl() }}">



  <link href="{{ mix('app.css', 'vendor/pano') }}" rel="stylesheet">
  <script src="{{ mix('app.js', 'vendor/pano') }}" async></script>

        {{-- <link rel="preconnect" href="{{asset('')}}"> --}}

    {{-- <link rel="search" type="application/opensearchdescription+xml" title="Batzo" href="/opensearch.xml"> --}}
    {{-- <meta name="theme-color" content="rgb(0, 0, 0)" media="(prefers-color-scheme: dark)"> --}}


    @if (!empty($pano->currentApp()->headView))
        @include($pano->currentApp()->headView)
    @endif

<body>
    <div id="pano-app"></div>
</body>

</html>
