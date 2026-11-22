@php
    $concepts = [
        ['title' => 'Activity Data', 'description' => 'The measured amount of a material, fuel, electricity, or other resource used in a production activity.'],
        ['title' => 'Emission Factors', 'description' => 'Values that relate an amount of activity or resource use to its associated greenhouse gas emissions.'],
        ['title' => 'Carbon Dioxide Equivalents', 'description' => 'A common unit used to express the warming effect of different greenhouse gases relative to carbon dioxide.'],
        ['title' => 'Activity Contributions', 'description' => 'A breakdown showing how much each included activity contributes to the estimated product footprint.'],
    ];

    $inputs = [
        ['number' => '01', 'title' => 'Activity Data', 'description' => 'The quantity of a resource used in an included production activity, recorded for a consistent assessment period and production quantity.'],
        ['number' => '02', 'title' => 'Emission Factor', 'description' => 'A value that expresses greenhouse gas emissions per unit of activity. Its source, unit, geographical relevance, and reference year should be documented.'],
        ['number' => '03', 'title' => 'Estimated Emissions', 'description' => 'The calculated contribution of an activity, expressed in carbon dioxide equivalents (CO₂e) when the selected factor represents combined greenhouse gas emissions.'],
    ];

    $examples = [
        ['title' => 'Electricity Use', 'description' => 'Multiply electricity consumption by an electricity emission factor that is appropriate for the assessment location and period.', 'equation' => 'Electricity Used (kWh) × Emission Factor (kg CO₂e/kWh) = Emissions (kg CO₂e)', 'icon' => 'electricity'],
        ['title' => 'Material Use', 'description' => 'Multiply the quantity of a material by an emission factor that matches the material and the process represented by that factor.', 'equation' => 'Material Used (kg) × Emission Factor (kg CO₂e/kg) = Emissions (kg CO₂e)', 'icon' => 'material'],
    ];

    $factorDetails = [
        'Source title and author or organization',
        'Publication year',
        'Material or activity represented',
        'Factor value and unit',
        'Geographical and process coverage',
        'Original source link',
        'Relevant assumptions',
    ];
@endphp

<x-landing.layouts.app
    title="Methodology | Sekar Kawung"
    description="How the Sekar Kawung Carbon Calculator estimates textile production emissions using activity data and emission factors."
    hero-background="bg-white"
>
    <x-slot:hero>
        <section class="mx-auto w-full max-w-[1200px] px-4 pb-9 pt-16 text-pine-900 sm:px-6 lg:px-0" aria-labelledby="methodology-heading">
            <h1 id="methodology-heading" class="text-4xl font-bold leading-tight tracking-[-0.02em] sm:text-5xl sm:leading-[58px]">Methodology</h1>
            <p class="mt-5 text-lg leading-[30px] lg:text-justify">The Sekar Kawung Carbon Calculator uses activity data to estimate greenhouse gas emissions within a defined textile production boundary. Material and energy use are linked to relevant emission factors to calculate the contribution of each included activity. The assessment follows the production activities recorded for the selected boundary. Results should be interpreted alongside the data sources, units, and assumptions used.</p>
        </section>
    </x-slot:hero>

    <section class="bg-white pb-9 text-pine-900" aria-labelledby="footprints-heading">
        <div class="mx-auto w-full max-w-[1200px] border-t border-mist-100 px-4 pt-9 sm:px-6 lg:px-0">
            <h2 id="footprints-heading" class="text-3xl font-bold leading-tight tracking-[-0.02em] sm:text-[40px] sm:leading-[48px]">Understanding Carbon Emissions and Product Footprints</h2>
            <p class="mt-5 text-lg leading-[30px] lg:text-justify">Greenhouse gas emissions include carbon dioxide (CO₂), methane (CH₄), and nitrous oxide (N₂O), which contribute to climate change. A product carbon footprint brings these emissions together across the activities included in the assessment boundary. The gases are expressed in carbon dioxide equivalents (CO₂e), allowing their different warming effects to be represented using a common unit.</p>

            <div class="mt-9 grid gap-7 sm:grid-cols-2 sm:gap-x-12">
                @foreach ($concepts as $concept)
                    <div class="rounded-2xl border border-mist-100 px-4 pb-4">
                        <div class="h-px bg-mist-100"></div>
                        <h3 class="mt-4 text-[22px] font-bold leading-[30px]">{{ $concept['title'] }}</h3>
                        <p class="mt-4 text-lg leading-[30px]">{{ $concept['description'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-9 rounded-xl border border-pine-900 bg-mist-100 px-6 py-4 sm:px-8">
                <p class="text-center text-2xl font-bold leading-9 sm:text-[26px]">PCF = Σ (ADᵢ × EFᵢ)</p>
                <p class="mt-4 text-center text-lg font-bold leading-8 sm:text-[26px] sm:leading-9">Product Carbon Footprint = Activity Data × Emission Factor</p>
                <p class="mt-4 text-base leading-6">Activity units must match the units used by the emission factor. Total emissions are calculated by adding the included contributions without double counting.</p>
            </div>

            <h3 class="mt-9 text-[32px] font-bold leading-10 tracking-[-0.02em]">Understanding the Inputs</h3>
            <dl class="mt-9 border-t border-mist-100">
                @foreach ($inputs as $input)
                    <div class="grid gap-3 border-b border-mist-100 py-6 sm:grid-cols-[30px_244px_minmax(0,1fr)] sm:gap-8">
                        <span class="flex size-[30px] items-center justify-center rounded-lg bg-pine-900 text-lg font-bold leading-[30px] text-mist-100" aria-hidden="true">{{ $input['number'] }}</span>
                        <dt class="text-[22px] font-bold leading-[30px]">{{ $input['title'] }}</dt>
                        <dd class="text-lg leading-[30px] lg:text-justify">{{ $input['description'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="bg-white py-9 text-pine-900" aria-labelledby="examples-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <h2 id="examples-heading" class="text-[32px] font-bold leading-10 tracking-[-0.02em]">Calculation Examples</h2>
            <div class="mt-6 space-y-6">
                @foreach ($examples as $example)
                    <article class="flex flex-col gap-6 rounded-xl bg-mist-100 p-6 sm:flex-row sm:p-8">
                        <div class="relative h-11 w-12 shrink-0" aria-hidden="true">
                            @if ($example['icon'] === 'electricity')
                                <img src="{{ asset('images/landing/methodology-lightning.svg') }}" alt="" width="22" height="34" class="ml-2 mt-1">
                            @else
                                <img src="{{ asset('images/landing/methodology-layer-top.svg') }}" alt="" width="32" height="18" class="absolute left-1 top-1">
                                <img src="{{ asset('images/landing/methodology-layer-middle.svg') }}" alt="" width="32" height="10" class="absolute left-1 top-[18px]">
                                <img src="{{ asset('images/landing/methodology-layer-middle.svg') }}" alt="" width="32" height="10" class="absolute left-1 top-6">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-2xl font-bold leading-8">{{ $example['title'] }}</h3>
                            <p class="mt-3 text-lg leading-[30px]">{{ $example['description'] }}</p>
                            <p class="mt-3 text-lg font-bold leading-[30px]">{{ $example['equation'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-9 text-pine-900" aria-labelledby="factors-heading">
        <div class="mx-auto grid w-full max-w-[1200px] gap-8 px-4 sm:px-6 lg:grid-cols-[600px_minmax(0,1fr)] lg:gap-16 lg:px-0">
            <div>
                <h2 id="factors-heading" class="text-[32px] font-bold leading-10 tracking-[-0.02em]">Emission Factors and Data Sources</h2>
                <p class="mt-5 text-lg leading-[30px]">Emission factors should be selected for their relevance to the material, energy source, process, location, and assessment period. Research papers, technical reports, and documented datasets can provide supporting information.</p>
                <p class="mt-5 text-lg leading-[30px]">Each factor used in the calculation should be traceable to its original source. Differences in source boundaries and units should be reviewed before applying a factor.</p>
            </div>
            <div class="rounded-xl bg-mist-100 p-7">
                <h3 class="text-2xl font-bold leading-8">For Each Factor, Record:</h3>
                <ul class="mt-3 space-y-3 text-base leading-6">
                    @foreach ($factorDetails as $detail)
                        <li class="flex gap-2"><span aria-hidden="true">•</span><span>{{ $detail }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section id="references" class="bg-white pb-[72px] pt-9 text-pine-900" aria-labelledby="references-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <h2 id="references-heading" class="text-[32px] font-bold leading-10 tracking-[-0.02em]">Research and References</h2>
            <p class="mt-6 text-lg leading-[30px]">Explore the research and source documents supporting the calculation methodology and emission factors.</p>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                @foreach (range(1, 4) as $number)
                    <article class="flex min-h-[300px] flex-col gap-4 rounded-xl border border-mist-100 bg-white p-6">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full bg-mist-100 px-[10px] py-1 text-base font-bold leading-6">Reference to be added</span>
                            <span class="text-base font-bold leading-6">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-[22px] font-bold leading-[30px]">[Full Paper or Report Title]</h3>
                        <p class="text-base leading-6">[Author / Organization] · [Year]</p>
                        <p class="text-base leading-6">[Brief explanation of how this source supports the methodology or an emission factor.]</p>
                        <div class="mt-auto text-base leading-6">
                            <p class="font-bold opacity-65">View Original Source ↗</p>
                            <p class="mt-1 opacity-80">Source link pending</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6 text-right">
                <button type="button" disabled title="References are not available yet" class="inline-flex items-center gap-2 rounded-xl bg-pine-900 px-[33px] py-[15px] text-base font-bold leading-5 text-white">View All References <img src="{{ asset('images/landing/methodology-arrow.svg') }}" alt="" width="16" height="16"></button>
            </div>
        </div>
    </section>
</x-landing.layouts.app>
