<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="pano-config" content="{{ json_encode($panoConfig) }}">
@if (false &&app()->isLocal())
{{
    Vite::useHotFile('vendor/pano/hot')
    // ->useBuildDirectory(base_path('/vendor/gchaumont/pano/public/build'))
    // ->useManifestFilename(base_path('vendor/gchaumont/pano/build/manifest.json'))
    ->withEntryPoints([
    'resources/js/app.js',
], 'vendor/pano/build')
}}
@else
    @vite(['resources/js/app.js'] , '/vendor/pano/build')
@endif
{{--
<link rel="preconnect" href="{{asset('')}}"> --}}
{{--
<link rel="search" type="application/opensearchdescription+xml" title="Batzo" href="/opensearch.xml"> --}}
{{--
<meta name="theme-color" content="rgb(0, 0, 0)" media="(prefers-color-scheme: dark)"> --}}
@foreach ($pano->getHeadViews() as $view)
    @include($view)
@endforeach

<body>
    <div id="pano-app" />
</body>

</html>