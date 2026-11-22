@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'rounded-panel border border-red-200 bg-red-50/70 px-4 py-3']) }} role="alert">
        <div class="flex gap-2.5">
            <x-lucide-triangle-alert class="mt-0.5 text-red-700" />
            <div class="min-w-0">
                <p class="text-[13px] font-semibold text-red-900">Periksa kembali isian berikut</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-4 text-[13px] leading-6 text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
