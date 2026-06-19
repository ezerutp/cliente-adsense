@php
    $siteSettings = $siteSettings ?? \App\Models\SiteSetting::current();
    $ageGate = $ageGate ?? \App\Models\AgeGateSetting::current()->toModalContent();
@endphp

<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', $siteSettings->site_subtitle)">
    <title>@yield('title', $siteSettings->site_title.' | '.$siteSettings->brandName())</title>
    <style>
        {!! $siteSettings->inlineCssVariableBlock() !!}
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="classic-public-page font-sans antialiased">
    <x-classic-header :site-settings="$siteSettings" />

    @yield('content')

    <x-classic-footer :site-settings="$siteSettings" />
    <x-age-confirmation-modal :content="$ageGate" />
    @stack('overlays')
</body>
</html>
