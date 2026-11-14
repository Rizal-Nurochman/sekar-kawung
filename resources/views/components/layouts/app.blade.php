@props([
    'title' => 'Sekar Kawung - Sustainable Materials - Katalok & Kalkulator Jejak Karbon Tekstil',
    'description' => 'Katalok material tekstil ramah lingkungan dan kalkulator jejak karbon untuk industri tekstil berkelanjutan.',
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
    <meta property="og:image" content="{{ asset('images/about-kapas.webp') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ asset('images/about-kapas.webp') }}">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @fonts

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $head ?? '' }}
</head>
<body class="flex min-h-screen flex-col bg-gray-50 font-sans text-slate-800 antialiased selection:bg-sage-500 selection:text-white">

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-sage-600 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Langsung ke konten
    </a>

    {{-- Header --}}
    <header x-data="navigation" class="sticky top-0 z-40 border-b border-gray-100 bg-white/90 shadow-sm backdrop-blur-md transition-all duration-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-3">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo Sekar Kawung" width="200" height="200" class="size-12 rounded-lg bg-white object-contain">
                    <span class="block">
                        <span class="block text-base font-bold tracking-tight text-slate-900 sm:text-xl">Sekar Kawung</span>
                        <span class="-mt-1 block text-[10px] font-semibold uppercase tracking-widest text-slate-400">Sustainable Materials</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-8 md:flex" aria-label="Navigasi utama">
                    <a href="#katalog" class="text-sm font-medium text-slate-600 transition-colors hover:text-sage-600">Katalog Produk</a>
                    <a href="#kalkulator" class="text-sm font-medium text-slate-600 transition-colors hover:text-sage-600">Kalkulator Karbon</a>
                    <a href="#tentang" class="text-sm font-medium text-slate-600 transition-colors hover:text-sage-600">Tentang Kami</a>
                    <a href="#kontak" class="text-sm font-medium text-slate-600 transition-colors hover:text-sage-600">Kontak</a>
                </nav>

                <div class="hidden items-center gap-4 md:flex">
                    <x-ui.button href="#kalkulator" variant="primary" weight="medium">
                        <i class="fa-solid fa-calculator" aria-hidden="true"></i>
                        <span>Hitung Emisi</span>
                    </x-ui.button>
                </div>

                <div class="flex items-center md:hidden">
                    <x-ui.button variant="ghost" type="button" id="mobile-menu-btn" x-on:click="toggleNav()" x-bind:aria-expanded="navOpen" x-bind:aria-label="navOpen ? 'Tutup menu' : 'Buka menu'" aria-controls="mobile-menu">
                        <i class="fa-solid fa-bars text-2xl" aria-hidden="true"></i>
                    </x-ui.button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" x-bind:class="{ hidden: !navOpen }" class="hidden border-b border-gray-100 bg-white px-4 pb-6 pt-2 md:hidden">
            <nav class="space-y-3" aria-label="Navigasi seluler" x-on:click="closeNav()">
                <a href="#katalog" class="block py-2 text-base font-medium text-slate-600 hover:text-sage-600">Katalog Produk</a>
                <a href="#kalkulator" class="block py-2 text-base font-medium text-slate-600 hover:text-sage-600">Kalkulator Karbon</a>
                <a href="#tentang" class="block py-2 text-base font-medium text-slate-600 hover:text-sage-600">Tentang Kami</a>
                <a href="#kontak" class="block py-2 text-base font-medium text-slate-600 hover:text-sage-600">Kontak</a>
                <x-ui.button href="#kalkulator" variant="primary" full-width>
                    <span>Hitung Emisi Sekarang</span>
                </x-ui.button>
            </nav>
        </div>
    </header>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer id="kontak" class="mt-auto border-t border-slate-800 bg-slate-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                <div class="md:col-span-1">
                    <div class="mb-4 flex items-center gap-3">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo Sekar Kawung" width="200" height="200" class="size-12 shrink-0 rounded-lg bg-white object-contain">
                        <span class="text-lg font-bold">Sekar Kawung - Sustainable Materials</span>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400">
                        Inovasi katalok dan alat kalkulasi karbon terkini untuk percepatan industri tekstil hijau yang ramah lingkungan.
                    </p>
                </div>

                <div>
                    <h2 class="mb-4 text-sm font-bold text-white">Navigasi</h2>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="#katalog" class="transition-colors hover:text-sage-400">Katalog Tekstil</a></li>
                        <li><a href="#kalkulator" class="transition-colors hover:text-sage-400">Kalkulator Karbon</a></li>
                        <li><a href="#tentang" class="transition-colors hover:text-sage-400">Tentang Kami</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="mb-4 text-sm font-bold text-white">Kontak Industri</h2>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-sage-400" aria-hidden="true"></i>
                            <span>Kontak belum tersedia</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-sage-400" aria-hidden="true"></i>
                            <span>Nomor kontak belum tersedia</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-sage-400" aria-hidden="true"></i>
                            <span>Desa Kerek, Tuban</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h2 class="mb-4 text-sm font-bold text-white">Ikuti Kami</h2>
                    <div class="flex gap-3">
                        <a href="#" class="flex size-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 transition-all hover:bg-sage-600 hover:text-white" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in text-sm" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="flex size-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 transition-all hover:bg-sage-600 hover:text-white" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-sm" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="flex size-9 items-center justify-center rounded-xl bg-slate-800 text-slate-300 transition-all hover:bg-sage-600 hover:text-white" aria-label="Twitter">
                            <i class="fa-brands fa-twitter text-sm" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-12 border-t border-slate-800 pt-8 text-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Sekar Kawung - Sustainable Materials. Hak Cipta Dilindungi Undang-Undang. Prototipe Web Industri Tekstil.</p>
            </div>
        </div>
    </footer>

    {{ $scripts ?? '' }}
</body>
</html>