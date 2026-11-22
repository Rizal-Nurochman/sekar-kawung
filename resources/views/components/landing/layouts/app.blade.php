@props([
    'title' => 'Sekar Kawung - Sustainable Materials',
    'description' => 'Merawat biodiversitas melalui budaya untuk mencapai ekonomi lestari.',
])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ asset('images/landing/emissions-factory.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ asset('images/landing/emissions-factory.png') }}">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $head ?? '' }}
</head>
<body class="flex min-h-screen flex-col bg-foam-50 font-sans text-slate-800 antialiased selection:bg-pine-900 selection:text-white">

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-pine-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Langsung ke konten
    </a>

    <div class="sticky top-3 z-40 bg-transparent px-4 sm:top-4 sm:px-6">
        <header x-data="navigation" class="relative mx-auto w-full max-w-[1200px]">
            <nav class="flex items-center justify-between rounded-full border border-white/65 bg-white/70 px-4 py-2 shadow-[0_10px_28px_0_rgba(0,53,36,0.16)] backdrop-blur-[14px] sm:px-6" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center" aria-label="Sekar Kawung beranda">
                    <img src="{{ asset('images/landing/nav-logo.png') }}" alt="Logo Sekar Kawung" width="500" height="222" class="block h-10 w-auto object-contain sm:h-12">
                </a>

                <div class="hidden items-center gap-10 text-base leading-6 text-pine-900 md:flex">
                    <a href="{{ route('home') }}" class="tracking-normal transition-colors hover:text-sage-600 {{ request()->routeIs('home') ? 'font-bold' : 'font-normal' }}">Home</a>
                    <a href="{{ route('about') }}" class="tracking-normal transition-colors hover:text-sage-600 {{ request()->routeIs('about') ? 'font-bold' : 'font-normal' }}">About</a>
                    <a href="{{ route('methodology') }}" class="tracking-normal transition-colors hover:text-sage-600 {{ request()->routeIs('methodology') ? 'font-bold' : 'font-normal' }}">Methodology</a>
                    <a href="{{ route('calculator') }}" class="tracking-normal transition-colors hover:text-sage-600 {{ request()->routeIs('calculator') ? 'font-bold' : 'font-normal' }}">Public Calculator</a>
                </div>

                <div class="flex items-center md:hidden">
                    <button type="button" x-on:click="toggleNav()" x-bind:aria-expanded="navOpen" x-bind:aria-label="navOpen ? 'Tutup menu' : 'Buka menu'" aria-controls="landing-mobile-menu" class="flex size-10 items-center justify-center rounded-full text-pine-900">
                        <svg x-show="!navOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg x-show="navOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </nav>

            <div id="landing-mobile-menu" x-bind:class="{ hidden: !navOpen }" class="absolute inset-x-0 top-full mt-2 hidden md:hidden">
                <nav class="mt-2 space-y-1 rounded-3xl border border-white/65 bg-white/95 px-4 py-4 shadow-lg backdrop-blur-[14px]" aria-label="Navigasi seluler" x-on:click="closeNav()">
                    <a href="{{ route('home') }}" class="block rounded-xl px-3 py-2 text-base font-medium text-pine-900 hover:bg-mist-100">Home</a>
                    <a href="{{ route('about') }}" class="block rounded-xl px-3 py-2 text-base text-pine-900 hover:bg-mist-100">About</a>
                    <a href="{{ route('methodology') }}" class="block rounded-xl px-3 py-2 text-base text-pine-900 hover:bg-mist-100">Methodology</a>
                    <a href="{{ route('calculator') }}" class="block rounded-xl px-3 py-2 text-base text-pine-900 hover:bg-mist-100">Public Calculator</a>
                </nav>
            </div>
        </header>
    </div>

    <div class="bg-mist-100 -mt-[84px] pt-[84px] sm:-mt-[92px] sm:pt-[92px]">
        {{ $hero ?? '' }}
    </div>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-pine-900 text-white">
        <div class="mx-auto w-full max-w-[1200px] px-4 pb-12 pt-16 sm:px-6 lg:px-0">
            <div class="flex flex-col gap-12 lg:flex-row lg:gap-12">
                <div class="flex-1">
                    <img src="{{ asset('images/landing/footer-logo.png') }}" alt="Logo Sekar Kawung" width="1882" height="836" class="h-10 w-auto object-contain">
                    <p class="mt-4 max-w-[360px] text-[15px] leading-6 text-sage-muted">
                        Inovasi katalog dan alat kalkulasi karbon terkini untuk percepatan industri tekstil hijau yang ramah lingkungan.
                    </p>
                </div>

                <div class="w-full sm:w-40">
                    <h2 class="text-sm font-semibold tracking-normal text-sage-pale">Navigation</h2>
                    <ul class="mt-4 space-y-3 text-[15px] leading-6 tracking-normal text-footer-link">
                        <li><a href="{{ route('home') }}" class="transition-colors hover:text-white">Home</a></li>
                        <li><a href="{{ route('calculator') }}" class="transition-colors hover:text-white">Public Calculator</a></li>
                        <li><a href="{{ route('methodology') }}" class="transition-colors hover:text-white">Methodology</a></li>
                        <li><a href="{{ route('about') }}" class="transition-colors hover:text-white">About</a></li>
                    </ul>
                </div>

                <div class="w-full sm:w-70">
                    <h2 class="text-sm font-semibold tracking-normal text-sage-pale">Contact</h2>
                    <ul class="mt-4 space-y-4 text-[15px] leading-6 tracking-normal text-footer-link">
                        <li class="flex items-center gap-3">
                            <x-fas-envelope class="h-5 w-5 shrink-0" />
                            <span>sekarkawung@gmail.com</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-fas-phone class="h-5 w-5 shrink-0" />
                            <span>0812345678910</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-fas-location-dot class="h-5 w-5 shrink-0" />
                            <span>Desa Kerek, Tuban</span>
                        </li>
                    </ul>
                </div>

                <div class="w-full sm:w-55">
                    <h2 class="text-sm font-semibold tracking-normal text-sage-pale">Follow Us</h2>
                    <ul class="mt-4 space-y-4 text-[15px] leading-6 tracking-normal text-footer-link">
                        <li class="flex items-center gap-3">
                            <x-fab-instagram class="h-5 w-5 shrink-0" />
                            <span>sekarkawung_id</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-fab-linkedin-in class="h-5 w-5 shrink-0" />
                            <span>sekarkawung_id</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-fab-x-twitter class="h-5 w-5 shrink-0" />
                            <span>sekarkawung_id</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 border-t border-white/25 pt-6 text-center text-xs font-medium leading-5 tracking-normal text-sage-muted">
                <p>&copy; {{ date('Y') }} Sekar Kawung - Sustainable Materials. Hak Cipta Dilindungi Undang-Undang. Prototipe Web Industri Tekstil.</p>
            </div>
        </div>
    </footer>

    {{ $scripts ?? '' }}
</body>
</html>
