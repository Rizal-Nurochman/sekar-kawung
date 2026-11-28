<div class="space-y-4">
    <x-admin.ui.field label="Boundary" name="boundary_id" required>
        <select id="boundary_id" name="boundary_id" required class="control" @error('boundary_id') aria-invalid="true" aria-describedby="boundary_id-error" @enderror>
            <option value="">Pilih boundary</option>
            @foreach ($boundaries as $boundary)
                <option value="{{ $boundary->id }}" @selected((string) old('boundary_id', $process->boundary_id ?? '') === (string) $boundary->id)>{{ $boundary->description }}</option>
            @endforeach
        </select>
    </x-admin.ui.field>

    <x-admin.ui.field label="Deskripsi" name="description" required hint="Contoh: Proses Membatik tulis tangan">
        <input id="description" name="description" type="text" value="{{ old('description', $process->description ?? '') }}" required maxlength="255" class="control" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>
    </x-admin.ui.field>

    <x-admin.ui.field label="Urutan" name="sort_order" required hint="Menentukan urutan tahap di kalkulator">
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $process->sort_order ?? 0) }}" required class="control w-32 tabular-nums" @error('sort_order') aria-invalid="true" aria-describedby="sort_order-error" @enderror>
    </x-admin.ui.field>
</div>
