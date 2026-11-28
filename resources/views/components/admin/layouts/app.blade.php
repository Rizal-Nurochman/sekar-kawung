@props([
    'title' => 'Admin - Sekar Kawung',
    'heading' => null,
    'subheading' => null,
])

@php
    // Konfigurasi is temporarily hidden from the navigation; its routes stay
    // registered so the screens remain reachable by URL.
    $navigation = [
        'Kerja' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'layout-dashboard'],
            ['label' => 'Kalkulator', 'route' => 'admin.calculator.edit', 'active' => 'admin.calculator.*', 'icon' => 'calculator'],
            ['label' => 'Laporan', 'route' => 'admin.reports.index', 'active' => 'admin.reports.*', 'icon' => 'file-text'],
        ],
    ];

    $navItems = collect($navigation)->flatten(1);
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $head ?? '' }}
</head>
<body class="min-h-screen bg-foam-50 font-sans text-slate-800 antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-control focus:bg-pine-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Langsung ke konten
    </a>

    <div class="flex min-h-screen">
        <aside class="sticky top-0 hidden h-dvh w-60 shrink-0 flex-col bg-pine-900 lg:flex" aria-label="Navigasi admin">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-5 pb-6 pt-7">
                <img src="{{ asset('images/landing/hero-mark.png') }}" alt="" aria-hidden="true" class="size-8 shrink-0 object-contain">
                <span class="min-w-0">
                    <span class="block text-[15px] font-bold leading-5 tracking-tight text-white">Sekar Kawung</span>
                    <span class="block text-[11px] leading-4 text-sage-muted">Ruang kerja admin</span>
                </span>
            </a>

            <nav class="flex-1 overflow-y-auto px-3" aria-label="Menu admin">
                @foreach ($navigation as $group => $items)
                    <div @class(['mb-6' => ! $loop->last])>
                        <p class="px-2.5 pb-1.5 text-[11px] font-semibold tracking-wide text-sage-muted/70">{{ $group }}</p>
                        <ul class="space-y-0.5">
                            @foreach ($items as $item)
                                @php($isActive = request()->routeIs($item['active']))
                                <li>
                                    <a
                                        href="{{ route($item['route']) }}"
                                        @if ($isActive) aria-current="page" @endif
                                        @class([
                                            'relative flex items-center gap-2.5 rounded-control px-2.5 py-2 text-[13.5px] font-medium transition-colors',
                                            'bg-white/[0.09] text-white' => $isActive,
                                            'text-footer-link/85 hover:bg-white/[0.05] hover:text-white' => ! $isActive,
                                        ])
                                    >
                                        @if ($isActive)
                                            <span class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-sage-pale" aria-hidden="true"></span>
                                        @endif
                                        <x-dynamic-component :component="'lucide-'.$item['icon']" @class(['size-[18px] shrink-0', 'text-sage-pale' => $isActive, 'text-sage-muted/70' => ! $isActive]) />
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-3">
                <div class="flex items-center gap-2.5 rounded-control px-2 py-1.5">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-white/10 text-[13px] font-semibold text-white" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-semibold leading-5 text-white">{{ auth()->user()?->name }}</p>
                        <p class="truncate text-[11px] leading-4 text-sage-muted">{{ auth()->user()?->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex size-8 items-center justify-center rounded-control text-sage-muted transition-colors hover:bg-white/10 hover:text-white" title="Keluar" aria-label="Keluar">
                            <x-lucide-log-out class="size-[18px]" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-line bg-foam-50/95 backdrop-blur">
                <div class="mx-auto w-full max-w-6xl px-4 sm:px-6">
                    <div class="flex items-center gap-2 pt-3 lg:hidden">
                        <a href="{{ route('admin.dashboard') }}" class="shrink-0">
                            <img src="{{ asset('images/landing/nav-logo.png') }}" alt="Sekar Kawung" class="h-7 w-auto object-contain">
                        </a>
                        <span class="rounded-badge bg-pine-900/8 px-1.5 py-0.5 text-[11px] font-semibold leading-4 text-ink-muted">Admin</span>
                        <form method="POST" action="{{ route('admin.logout') }}" class="ml-auto">
                            @csrf
                            <button type="submit" class="rounded-control px-2.5 py-1.5 text-[13px] font-semibold text-pine-900 hover:bg-pine-900/[0.06]">Keluar</button>
                        </form>
                    </div>

                    <div class="flex flex-wrap items-end justify-between gap-x-4 gap-y-2 pb-3 pt-3 lg:pt-5">
                        <div class="min-w-0">
                            @isset($breadcrumbs)
                                <nav aria-label="Breadcrumb" class="mb-1 flex items-center gap-1.5 text-xs text-ink-muted">
                                    {{ $breadcrumbs }}
                                </nav>
                            @endisset
                            <h1 class="truncate text-[21px] font-bold leading-7 tracking-tight text-pine-900">{{ $heading ?? $title }}</h1>
                            @isset($subheading)
                                <p class="mt-1 truncate text-[13.5px] leading-5 text-ink-muted">{{ $subheading }}</p>
                            @endisset
                        </div>

                        @isset($actions)
                            <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
                        @endisset
                    </div>

                    <nav class="flex gap-1 overflow-x-auto lg:hidden" aria-label="Menu admin seluler">
                        @foreach ($navItems as $item)
                            @php($isActive = request()->routeIs($item['active']))
                            <a
                                href="{{ route($item['route']) }}"
                                @if ($isActive) aria-current="page" @endif
                                @class([
                                    'flex shrink-0 items-center gap-1.5 border-b-2 px-2.5 pb-2 pt-1 text-[13px] font-medium transition-colors',
                                    'border-pine-900 text-pine-900' => $isActive,
                                    'border-transparent text-ink-muted hover:text-pine-900' => ! $isActive,
                                ])
                            >
                                <x-dynamic-component :component="'lucide-'.$item['icon']" class="size-4" />
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </header>

            <main id="main" class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:px-6 lg:py-8">
                @if (session('status'))
                    <div class="mb-5 flex items-start gap-2.5 rounded-panel border border-sage-500/30 bg-white px-4 py-3" role="status">
                        <x-lucide-circle-check class="mt-0.5 text-sage-600" />
                        <p class="text-[13.5px] leading-6 text-pine-900">{{ session('status') }}</p>
                    </div>
                @endif

                @if ($errors->has('general'))
                    <div class="mb-5 flex items-start gap-2.5 rounded-panel border border-red-200 bg-red-50/70 px-4 py-3" role="alert">
                        <x-lucide-circle-alert class="mt-0.5 text-red-700" />
                        <p class="text-[13.5px] leading-6 text-red-800">{{ $errors->first('general') }}</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    {{ $scripts ?? '' }}
</body>
</html>
