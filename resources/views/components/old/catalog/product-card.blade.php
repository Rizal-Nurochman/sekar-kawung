@props(['product'])

<x-old.ui.card
    as="article"
    tone="white"
    padding="none"
    rounded="2xl"
    class="card-hover flex cursor-pointer flex-col justify-between overflow-hidden"
    tabindex="0"
    role="button"
    aria-label="Lihat detail {{ $product['name'] }}"
    {{ $attributes }}
>
    <div>
        <div class="relative h-48 overflow-hidden bg-slate-100">
            <img
                src="{{ asset(ltrim($product['image'], '/')) }}"
                alt="{{ $product['name'] }}"
                loading="lazy"
                decoding="async"
                width="600"
                height="400"
                class="h-full w-full object-cover transition-transform duration-500 hover:scale-105"
            >

            <span class="absolute left-3 top-3 rounded-lg border border-gray-200 bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-sage-800 backdrop-blur-md">
                {{ $product['categoryName'] }}
            </span>
        </div>

        <div class="p-5">
            <div class="mb-1 flex items-center gap-1.5 text-xs text-slate-400">
                <i class="fa-solid fa-industry text-sage-600" aria-hidden="true"></i>
                <span class="font-medium text-slate-500">{{ $product['company']['name'] }}</span>
            </div>

            <h4 class="line-clamp-1 text-lg font-bold text-slate-900">{{ $product['name'] }}</h4>
            <p class="mt-1 line-clamp-1 text-xs text-slate-400">{{ $product['spec'] }}</p>
            <p class="mt-3 line-clamp-2 text-xs text-slate-600">{{ $product['desc'] }}</p>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between border-t border-gray-50 p-5 pt-4">
        <div>
            <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Jejak Karbon</span>
            <span class="text-sm font-extrabold text-sage-700">
                {{ $product['emission'] }}
                <span class="text-xs font-normal text-slate-500">kg CO₂e/kg</span>
            </span>
        </div>

        <span class="flex items-center gap-1 text-xs font-bold text-sage-600 hover:text-sage-700">
            <span>Detail &amp; Keberlanjutan</span>
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </span>
    </div>
</x-old.ui.card>