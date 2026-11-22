<div class="space-y-4">
    <x-admin.ui.field label="Deskripsi" name="description" required hint="Contoh: Sepeda Motor, Mobil-Bensin, Truk">
        <input id="description" name="description" type="text" value="{{ old('description', $option->description ?? '') }}" required maxlength="255" class="control" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>
    </x-admin.ui.field>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-admin.ui.field label="Satuan aktivitas" name="activity_unit_id" hint="Opsional">
            <select id="activity_unit_id" name="activity_unit_id" class="control" @error('activity_unit_id') aria-invalid="true" aria-describedby="activity_unit_id-error" @enderror>
                <option value="">Tanpa satuan</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" @selected((string) old('activity_unit_id', $option->activity_unit_id ?? '') === (string) $unit->id)>{{ $unit->unit_name }}</option>
                @endforeach
            </select>
        </x-admin.ui.field>

        <x-admin.ui.field label="Faktor emisi" name="emission_factor_id" hint="Opsional, kosong berarti 0 kg CO2e">
            <select id="emission_factor_id" name="emission_factor_id" class="control" @error('emission_factor_id') aria-invalid="true" aria-describedby="emission_factor_id-error" @enderror>
                <option value="">Tanpa faktor emisi</option>
                @foreach ($factors as $factor)
                    <option value="{{ $factor->id }}" @selected((string) old('emission_factor_id', $option->emission_factor_id ?? '') === (string) $factor->id)>{{ $factor->unit_name }} ({{ $factor->ef_value }})</option>
                @endforeach
            </select>
        </x-admin.ui.field>
    </div>
</div>
