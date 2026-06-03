<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled(config('app.name', 'Laravel')) ? config('app.name', 'Laravel').' - '.($title ?? null) : $title }}
</title>

<link rel="icon" href="/logo.png" type="image/png" />

@if(Route::is(['home', 'about', 'contact', 'edukasi', 'product', 'opportunity']))
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

<link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/lightbox.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

@fonts
@endif

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance