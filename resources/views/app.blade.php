<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'RSMD Mobile') }}</title>

    <!-- PWA Primary Meta Tags -->
    <meta name="theme-color" content="#0d9488">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RSMD Mobile">
    <link rel="apple-touch-icon" href="/icon_rsmd.png">
    <link rel="manifest" href="/manifest.webmanifest">

    <!-- Fonts & Icons -->
    <link rel="icon" type="image/png" href="/icon_rsmd.png">

    @vite(['resources/js/app.js', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-base-200">
    @inertia
</body>
</html>
