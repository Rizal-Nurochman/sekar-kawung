<div class="space-y-4">
    <x-admin.ui.field label="Proses" name="process_id" required>
        <select id="process_id" name="process_id" required class="control" @error('process_id') aria-invalid="true" aria-describedby="process_id-error" @enderror>
            <option value="">Pilih proses</option>
            @foreach ($processes as $process)
                <option value="{{ $process->id }}" @selected((string) old('process_id', $subprocess->process_id ?? '') === (string) $process->id)>{{ $process->description }}</option>
            @endforeach
        </select>
    </x-admin.ui.field>

    <x-admin.ui.field label="Deskripsi" name="description" required hint="Contoh: Proses Membatik tulis tangan">
        <input id="description" name="description" type="text" value="{{ old('description', $subprocess->description ?? '') }}" required maxlength="255" class="control" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>
    </x-admin.ui.field>

    <x-admin.ui.field label="Urutan" name="sort_order" required hint="Menentukan urutan tahap di kalkulator">
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $subprocess->sort_order ?? 0) }}" required class="control w-32 tabular-nums" @error('sort_order') aria-invalid="true" aria-describedby="sort_order-error" @enderror>
    </x-admin.ui.field>
</div>
