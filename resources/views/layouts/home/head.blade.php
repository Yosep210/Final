<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    @include('partials.home.topbar')
    {{ $slot }}
    @include('partials.home.footer')
    @include('partials.home.script')
    @fluxScripts
</body>

</html>