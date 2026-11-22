<x-admin.layouts.app title="Ubah Aktivitas - Konfigurasi" heading="Ubah aktivitas" subheading="{{ $activity->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Aktivitas', 'url' => route('admin.config.activities.index')],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.activities.update', $activity)" method="PUT" :back-route="route('admin.config.activities.index')" submit-label="Simpan perubahan">
        <x-admin.ui.panel title="Detail aktivitas">
            @include('admin.config.activities._form')
        </x-admin.ui.panel>

        <x-admin.ui.panel title="Opsi aktivitas" description="Aktivitas dengan opsi memakai faktor emisi dari opsi yang dipilih.">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-[13px] text-ink-muted">
                    {{ $activity->options->count() }} opsi terpasang.
                </p>
                <x-admin.ui.button :href="route('admin.config.activity-options.edit', $activity)" size="sm">
                    <x-lucide-list-checks />
                    Atur opsi
                </x-admin.ui.button>
            </div>
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
