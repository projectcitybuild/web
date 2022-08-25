<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo">
    <meta name="description" content="@yield('meta_description')">
    <meta name="theme-color" content="#524641">
    <meta name="apple-mobile-web-app-title" content="PCB">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('assets/images/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/images/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon/favicon-16x16.png') }}">

    <meta property="og:url" content="https://projectcitybuild.com">
    <meta property="og:title" content="@yield('meta_title', 'Project City Build')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:site_name" content="Project City Build">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">

    <meta name="twitter:card" content="@yield('meta_description')">
    <meta name="twitter:site" content="@PCB_Minecraft">


    <title>@yield('title', 'Project City Build')</title>

    <link rel="stylesheet" href="{{ mix('assets/css/app-v2.css') }}">
    <link rel="icon" type="type/x-icon" href="{{ asset('assets/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="https://i.imgur.com/g1OfIGT.png"/>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <script src="https://kit.fontawesome.com/a0425475c4.js" crossorigin="anonymous"></script>

    @vite(['resources/js/app.ts', 'resources/sass/front/front.scss'])

    @stack('head')

    @env(['staging', 'production'])
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-2747125-5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-2747125-5');
    </script>
    @endenv
</head>
<body>

<x-navbar />

<div id="app">
    @yield('body')
    @include('front.components.footer')
</div>

@stack('end')

</body>
</html>
