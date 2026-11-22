<div class="space-y-4">
    <x-admin.ui.field label="Subproses" name="subprocess_id" required>
        <select id="subprocess_id" name="subprocess_id" required class="control" @error('subprocess_id') aria-invalid="true" aria-describedby="subprocess_id-error" @enderror>
            <option value="">Pilih subproses</option>
            @foreach ($subprocesses as $subprocess)
                <option value="{{ $subprocess->id }}" @selected((string) old('subprocess_id', $activity->subprocess_id ?? '') === (string) $subprocess->id)>{{ $subprocess->description }}</option>
            @endforeach
        </select>
    </x-admin.ui.field>

    <x-admin.ui.field label="Deskripsi" name="description" required hint="Contoh: Pengairan artifisial (pompa diesel)">
        <input id="description" name="description" type="text" value="{{ old('description', $activity->description ?? '') }}" required maxlength="255" class="control" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>
    </x-admin.ui.field>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-admin.ui.field label="Satuan aktivitas" name="activity_unit_id" hint="Opsional">
            <select id="activity_unit_id" name="activity_unit_id" class="control" @error('activity_unit_id') aria-invalid="true" aria-describedby="activity_unit_id-error" @enderror>
                <option value="">Tanpa satuan</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" @selected((string) old('activity_unit_id', $activity->activity_unit_id ?? '') === (string) $unit->id)>{{ $unit->unit_name }}</option>
                @endforeach
            </select>
        </x-admin.ui.field>

        <x-admin.ui.field label="Faktor emisi" name="emission_factor_id" hint="Opsional">
            <select id="emission_factor_id" name="emission_factor_id" class="control" @error('emission_factor_id') aria-invalid="true" aria-describedby="emission_factor_id-error" @enderror>
                <option value="">Tanpa faktor emisi</option>
                @foreach ($factors as $factor)
                    <option value="{{ $factor->id }}" @selected((string) old('emission_factor_id', $activity->emission_factor_id ?? '') === (string) $factor->id)>{{ $factor->unit_name }} ({{ $factor->ef_value }})</option>
                @endforeach
            </select>
        </x-admin.ui.field>
    </div>

    <p class="rounded-control bg-sage-50 px-3 py-2 text-xs leading-5 text-ink-soft">
        Aktivitas tanpa faktor emisi dan tanpa opsi dianggap langkah manual bercitra 0 kg CO2e, misalnya pertenunan tangan.
    </p>

    <x-admin.ui.field label="Urutan" name="sort_order" required>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $activity->sort_order ?? 0) }}" required class="control w-32 tabular-nums" @error('sort_order') aria-invalid="true" aria-describedby="sort_order-error" @enderror>
    </x-admin.ui.field>
</div>
