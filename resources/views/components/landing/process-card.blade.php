@props(['title', 'body'])

<article {{ $attributes->merge(['class' => 'flex flex-col gap-2 self-stretch rounded-2xl bg-foam-50 p-6']) }}>
    <h3 class="text-lg font-bold leading-7 tracking-tight text-pine-900">{{ $title }}</h3>
    <p class="text-[15px] leading-6 tracking-normal text-ink-soft">{{ $body }}</p>
</article>
