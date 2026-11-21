<x-landing.layouts.app :title="$section . ' | Segera Hadir | Sekar Kawung'">
    <x-slot:hero>
        <section class="mx-auto w-full max-w-[1200px] px-4 pb-20 pt-14 sm:px-6 lg:px-0">
            <p class="text-sm font-semibold tracking-normal text-pine-900">Segera Hadir</p>
            <h1 class="mt-2 max-w-2xl text-3xl font-bold leading-tight tracking-tight text-black sm:text-5xl">{{ $section }}</h1>
            <p class="mt-4 max-w-xl text-lg leading-8 text-ink-soft">Halaman {{ $section }} sedang disiapkan. Silakan kembali ke beranda.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-pine-900 px-6 py-3 text-base font-semibold leading-6 text-white transition-colors hover:bg-pine-950">Kembali ke Beranda</a>
        </section>
    </x-slot:hero>
</x-landing.layouts.app>
