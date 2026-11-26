<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo">
    <meta name="description" content="{{ $__env->yieldContent('meta_description', 'Project City Build is a creative Minecraft community for city builders and players.') }}">
    <meta name="theme-color" content="#030712">
    <meta name="apple-mobile-web-app-title" content="PCB">
    @stack('meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('static/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('static/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('static/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('static/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('static/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('static/favicon/favicon.ico') }}">
    <link rel="canonical" href="{{ request()->url() }}">

    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="{{ $__env->yieldContent('title', 'Project City Build') }}">
    <meta property="og:description" content="{{ $__env->yieldContent('meta_description', 'Project City Build is a creative Minecraft community for city builders and players.') }}">
    <meta property="og:image" content="{{ Vite::asset('resources/images/hero.webp') }}">
    <meta property="og:image:alt" content="Project City Build, a creative Minecraft community">
    <meta property="og:site_name" content="Project City Build">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@PCB_Minecraft">
    <meta name="twitter:title" content="{{ $__env->yieldContent('title', 'Project City Build') }}">
    <meta name="twitter:description" content="{{ $__env->yieldContent('meta_description', 'Project City Build is a creative Minecraft community for city builders and players.') }}">
    <meta name="twitter:image" content="{{ Vite::asset('resources/images/hero.webp') }}">
    <meta name="twitter:image:alt" content="Project City Build, a creative Minecraft community">

    <title>{{ $__env->yieldContent('title', 'Project City Build') }}</title>

    @vite([
        'resources/css/front.css',
        'resources/js/front/front.ts',
    ])

    @stack('head')
</head>
<body class="bg-gray-950">

<div id="app">
    @yield('body')
</div>

@stack('end')

</body>
</html>
