<div class="space-y-4">
    <x-admin.ui.field label="Nama satuan" name="unit_name" required hint="Satuan yang dikalikan dengan nilai, contoh: liter/diesel">
        <input id="unit_name" name="unit_name" type="text" value="{{ old('unit_name', $factor->unit_name ?? '') }}" required maxlength="255" class="control" @error('unit_name') aria-invalid="true" aria-describedby="unit_name-error" @enderror>
    </x-admin.ui.field>

    <x-admin.ui.field label="Nilai faktor emisi" name="ef_value" required hint="kg CO2e per satuan">
        <input id="ef_value" name="ef_value" type="number" step="any" min="0" value="{{ old('ef_value', $factor->ef_value ?? '') }}" required class="control w-48 tabular-nums" @error('ef_value') aria-invalid="true" aria-describedby="ef_value-error" @enderror>
    </x-admin.ui.field>

    <x-admin.ui.field label="Sumber" name="source_id" hint="Opsional, tapi disarankan agar laporan punya sitasi">
        <select id="source_id" name="source_id" class="control" @error('source_id') aria-invalid="true" aria-describedby="source_id-error" @enderror>
            <option value="">Tanpa sumber</option>
            @foreach ($sources as $source)
                <option value="{{ $source->id }}" @selected((string) old('source_id', $factor->source_id ?? '') === (string) $source->id)>{{ $source->title }}</option>
            @endforeach
        </select>
    </x-admin.ui.field>
</div>
