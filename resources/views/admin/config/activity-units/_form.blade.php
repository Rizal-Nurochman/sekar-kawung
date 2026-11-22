<x-admin.ui.field label="Nama satuan" name="unit_name" required hint="Contoh: kWh, liter/diesel, kg pupuk agregat">
    <input id="unit_name" name="unit_name" type="text" value="{{ old('unit_name', $unit->unit_name ?? '') }}" required maxlength="255" class="control" @error('unit_name') aria-invalid="true" aria-describedby="unit_name-error" @enderror>
</x-admin.ui.field>
