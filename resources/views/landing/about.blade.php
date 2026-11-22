@php
    $boundaries = [
        [
            'number' => '01',
            'title' => 'Tuban Gedog Fabric',
            'summary' => 'Cotton cultivation and manual processing through to handwoven Gedog fabric.',
            'activities' => [
                'Soil and growing-medium preparation',
                'Cotton-plant maintenance',
                'Harvesting, sun drying and natural storage',
                'Post-harvest hand ginning and carding',
                'Hand spinning and hand weaving',
            ],
        ],
        [
            'number' => '02',
            'title' => 'Tuban Gedog Batik Fabric',
            'summary' => 'Hand-drawn batik and the dyeing activities listed for Tuban Gedog fabric.',
            'activities' => [
                'Hand-drawn batik: wax, lighting and stove gas',
                'Blue batik dyeing: transport to Temanggung, boiling gas, Strobilanthes cussia paste and sun drying',
                'Brown batik dyeing: mahogany sawdust, firewood and sun drying',
                'Other batik colours: transport to DIY, plant materials, firewood and sun drying',
            ],
        ],
        [
            'number' => '03',
            'title' => 'Pisano Handwoven Fabric (ATBM)',
            'summary' => 'Cotton cultivation, machine-assisted yarn preparation and ATBM hand weaving, including the listed transport legs.',
            'activities' => [
                'Soil and growing-medium preparation',
                'Cotton-plant maintenance',
                'Harvesting, sun drying and natural storage',
                'Post-harvest transport, machine ginning and electric carding',
                'Yarn-to-fabric production: sliver transport, factory spinning, ATBM weaving in Karangasem, Klaten, and return transport to Yogya',
            ],
        ],
        [
            'number' => '04',
            'title' => 'Pisano Handwoven Batik Fabric (ATBM)',
            'summary' => 'Hand-drawn batik and the listed blue and white dyeing stages for Pisano ATBM fabric.',
            'activities' => [
                'Hand-drawn batik: transport to Tancep/DIY, wax, lighting and stove gas',
                'Blue batik dyeing: transport to Temanggung, boiling gas, Strobilanthes cussia paste and sun drying',
                'White batik dyeing: transport to DIY, leaves, bark, roots or flowers, firewood and sun drying',
            ],
        ],
    ];

@endphp

<x-landing.layouts.app
    title="About Sekar Kawung | Understanding the Footprint Behind Every Fabric"
    description="Learn how Sekar Kawung explains textile production boundaries, activity data, emission factors, and product carbon footprints."
    hero-background="bg-white"
>
    <x-slot:hero>
        <section class="mx-auto grid w-full max-w-[1200px] items-center gap-10 px-4 pb-20 pt-14 text-pine-900 sm:px-6 lg:grid-cols-[minmax(0,688px)_minmax(0,448px)] lg:gap-16 lg:px-0 lg:pb-24 lg:pt-[88px]" aria-labelledby="about-heading">
            <div>
                <p class="text-base font-bold leading-6 tracking-[0.08em]">ABOUT SEKAR KAWUNG</p>
                <h1 id="about-heading" class="mt-6 max-w-[688px] text-4xl font-bold leading-tight tracking-[-0.02em] sm:text-5xl sm:leading-[1.2]">
                    Understanding the Footprint Behind Every Fabric
                </h1>
                <div class="mt-6 space-y-[18px] text-base leading-[30px] lg:text-justify">
                    <p>The Sekar Kawung Carbon Calculator helps users estimate greenhouse gas emissions associated with textile production, from seed cultivation to the finished fabric within a defined production boundary.</p>
                    <p>By recording activity data, material use, and energy consumption, users can better understand how different production activities contribute to a product’s carbon footprint. The results provide a starting point for reviewing resource use and identifying opportunities for improvement.</p>
                </div>
            </div>
            <img
                src="{{ asset('images/landing/about-environment.png') }}"
                alt="Illustration of the Earth with buildings, trees, and renewable energy"
                width="1254"
                height="1254"
                class="mx-auto h-auto w-full max-w-[448px] object-contain"
                fetchpriority="high"
            >
        </section>
    </x-slot:hero>

    <section class="bg-mist-100 py-8 text-pine-900" aria-labelledby="report-heading">
        <div class="mx-auto grid w-full max-w-[1200px] gap-10 px-4 sm:px-6 lg:grid-cols-[600px_520px] lg:gap-20 lg:px-0">
            <div class="space-y-6">
                <h2 id="report-heading" class="text-3xl font-bold leading-tight tracking-[-0.02em] sm:text-[40px] sm:leading-[48px]">What Is a Product Carbon Footprint Report?</h2>
                <p class="text-lg leading-[30px] lg:text-justify">A Product Carbon Footprint Report summarizes the estimated greenhouse gas emissions associated with a product within a defined production boundary. It explains what was assessed, which activities were included, and how the results were calculated.</p>
                <p class="text-lg leading-[30px] lg:text-justify">Results are expressed in carbon dioxide equivalents (CO₂e) and should be read alongside the product quantity, reporting period, data sources, and assumptions used in the assessment.</p>
            </div>
            <div>
                <h3 class="text-2xl font-bold leading-8">What the Report Covers</h3>
                <ul class="mt-6 space-y-4 text-lg leading-[30px]">
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Product and assessed quantity</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Production boundary and included activities</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Activity data and emission factors</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Total estimated emissions and activity breakdown</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Data sources, assumptions, and limitations</span></li>
                </ul>
                <p class="mt-6 text-base leading-6">Emissions per unit of product should only be shown when the assessed output quantity and unit are defined.</p>
            </div>
        </div>
    </section>

    <section class="bg-white pb-20 pt-8 text-pine-900" aria-labelledby="boundaries-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <h2 id="boundaries-heading" class="text-3xl font-bold leading-tight tracking-[-0.02em] sm:text-[40px] sm:leading-[48px]">Four Production Boundaries</h2>
            <p class="mt-5 text-lg leading-[30px]">A production boundary defines which activities are included in the carbon footprint calculation and where the assessment begins and ends. The calculator covers four production pathways, each with its own set of activities.</p>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                @foreach ($boundaries as $boundary)
                    <article class="min-h-[560px] rounded-2xl border border-pine-900 bg-mist-100 p-6 sm:p-8">
                        <p class="text-base font-bold leading-6">{{ $boundary['number'] }}</p>
                        <h3 class="mt-3 text-2xl font-bold leading-8">{{ $boundary['title'] }}</h3>
                        <p class="mt-3 text-base leading-6">From Seed to Finished Fabric</p>
                        <p class="mt-5 text-lg leading-[30px]">{{ $boundary['summary'] }}</p>
                        <h4 class="mt-5 text-base font-bold leading-6">Included Activities</h4>
                        <ul class="mt-3 space-y-2 text-lg leading-[30px]">
                            @foreach ($boundary['activities'] as $activity)
                                <li class="flex gap-3"><span aria-hidden="true">•</span><span>{{ $activity }}</span></li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

</x-landing.layouts.app>
