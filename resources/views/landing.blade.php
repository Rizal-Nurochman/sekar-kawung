<x-landing.layouts.app>
    <x-slot:hero>
        <section class="mx-auto grid w-full max-w-[1103px] items-center gap-8 px-4 pb-20 pt-14 sm:px-6 lg:min-h-[688px] lg:grid-cols-[253px_818px] lg:items-start lg:justify-center lg:gap-[32px] lg:px-0 lg:pb-0 lg:pt-[149px]">
            <img
                src="{{ asset('images/landing/hero-mark.png') }}"
                alt="Motif Sekar Kawung"
                width="640"
                height="836"
                class="mx-auto h-auto w-44 object-contain sm:w-52 lg:mx-0 lg:h-[278px] lg:w-[253px]"
                fetchpriority="high"
            >
            <div class="lg:w-[818px]">
                <img
                    src="{{ asset('images/landing/hero-wordmark.png') }}"
                    alt="Sekar Kawung"
                    width="1024"
                    height="177"
                    class="h-auto w-72 object-contain object-bottom object-left sm:w-[408px] lg:h-[87px] lg:w-[408px]"
                    fetchpriority="high"
                >
                <p class="mt-4 max-w-[65ch] text-xl leading-8 tracking-normal text-black sm:text-2xl">
                    Merawat biodiversitas melalui budaya untuk mencapai ekonomi lestari.
                </p>
                <div class="mt-6">
                    <a
                        href="{{ route('calculator') }}"
                        class="inline-flex items-center justify-center rounded-full bg-pine-900 px-6 py-3 text-base font-semibold leading-6 tracking-normal text-white transition-colors hover:bg-pine-950"
                    >
                        Start Carbon Calculator
                    </a>
                </div>
            </div>
        </section>
    </x-slot:hero>

    {{-- Emissions --}}
    <section class="bg-pine-900 py-16 sm:py-24" aria-labelledby="emissions-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <div class="grid items-start gap-10 lg:grid-cols-[536px_1fr] lg:gap-16">
                <div>
                    <h2 id="emissions-heading" class="text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl sm:leading-[1.15]">
                        What Are Carbon Emissions and Carbon Footprints?
                    </h2>
                    <p class="mt-6 max-w-[65ch] text-lg leading-8 text-sage-muted">
                        Carbon emissions commonly refer to carbon dioxide (CO₂) released through activities such as burning fuel and using energy. A carbon footprint measures the total greenhouse gas emissions (including CO₂, methane (CH₄), and nitrous oxide (N₂O)) generated directly and indirectly by a person, organization, or activity. It is expressed in carbon dioxide equivalents (CO₂e). Understanding these emissions helps identify opportunities to reduce environmental impact.
                    </p>
                </div>
                <img
                    src="{{ asset('images/landing/emissions-factory.png') }}"
                    alt="Pabrik dengan latar ladang hijau"
                    width="1200"
                    height="660"
                    loading="lazy"
                    decoding="async"
                    class="h-auto w-full rounded-2xl object-cover"
                >
            </div>

            <div class="pt-16 sm:pt-24">
                <h3 class="text-2xl font-bold leading-8 tracking-tight text-white">Production Process</h3>
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($processCards as $card)
                        <x-landing.process-card :title="$card['title']" :body="$card['body']" />
                    @endforeach
                </div>
                <p class="mt-6 text-right">
                    <a href="{{ route('about') }}" class="inline-flex items-center gap-1 text-[15px] font-semibold leading-5 text-sage-pale transition-colors hover:text-white">Read More <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 translate-y-px" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" /></svg></a>
                </p>
            </div>
        </div>
    </section>

    {{-- Methodology --}}
    <section class="bg-white py-16 sm:py-24" aria-labelledby="methodology-heading">
        <div class="mx-auto grid w-full max-w-[1200px] items-start gap-10 px-4 sm:px-6 lg:grid-cols-[600px_1fr] lg:gap-16 lg:px-0">
            <img
                src="{{ asset('images/landing/methodology-factory.png') }}"
                alt="Pabrik dengan latar ladang hijau"
                width="1200"
                height="660"
                loading="lazy"
                decoding="async"
                class="h-auto w-full rounded-2xl object-cover"
            >
            <div>
                <h2 id="methodology-heading" class="text-3xl font-bold leading-tight tracking-tight text-pine-900 sm:text-4xl sm:leading-[1.15]">Methodology</h2>
                <p class="mt-6 max-w-[65ch] text-lg leading-8 text-ink-soft">
                    We apply a shared life cycle inventory across seed to fabric and seed to batik boundaries, using published emission factors and field data from Tuban. Totals are expressed in carbon dioxide equivalents (CO₂e) so hotspots can be compared across stages.
                </p>
                <div class="mt-4 rounded-xl border border-pine-900 bg-mist-100 px-4 py-4 text-pine-900">
                    <p class="text-center text-xl font-bold leading-8">PCF = Σ (ADᵢ × EFᵢ)</p>
                    <p class="mt-2 text-center text-base font-bold leading-7">Product Carbon Footprint = Activity Data × Emission Factor</p>
                    <p class="mt-3 text-sm leading-6">Activity units must match the units used by the emission factor. Total emissions are calculated by adding the included contributions without double counting.</p>
                </div>
                <p class="mt-6">
                    <a href="{{ route('methodology') }}" class="inline-flex items-center gap-1 text-[15px] font-semibold leading-5 text-sage-600 transition-colors hover:text-pine-900">Read More <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 translate-y-px" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" /></svg></a>
                </p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-white pb-16 sm:pb-24" aria-labelledby="cta-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <div class="relative flex flex-col gap-6 overflow-clip rounded-3xl bg-pine-900 px-6 py-10 sm:px-12 lg:flex-row lg:items-center lg:justify-between">
                <img
                    src="{{ asset('images/landing/plant-silhouette.svg') }}"
                    alt=""
                    aria-hidden="true"
                    class="pointer-events-none absolute right-10 top-3 hidden w-56 opacity-90 lg:block"
                >
                <div class="relative">
                    <h2 id="cta-heading" class="text-3xl font-semibold leading-tight tracking-tight text-white sm:text-[32px] sm:leading-10">Mulai Kenali Jejak Karbon Anda.</h2>
                    <p class="mt-3 max-w-[60ch] text-base leading-7 text-sage-muted">Siapkan data aktivitas Anda dan mulai perhitungan Emisi Jejak Karbon Anda!</p>
                </div>
                <a
                    href="{{ route('calculator') }}"
                    class="relative inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-foam-50 px-8 py-4 text-base font-semibold leading-6 text-pine-900 transition-colors hover:bg-white"
                >
                    Mulai Menghitung <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 translate-y-px" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>
        </div>
    </section>
</x-landing.layouts.app>
