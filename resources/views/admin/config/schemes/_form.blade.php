<div class="space-y-4">
    <x-admin.ui.field label="Deskripsi" name="description" required hint="Contoh: Gedog Tuban">
        <input id="description" name="description" type="text" value="{{ old('description', $scheme->description ?? '') }}" required maxlength="255" class="control" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>
    </x-admin.ui.field>

    <x-admin.ui.field label="Urutan" name="sort_order" required hint="Angka kecil tampil lebih dulu">
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $scheme->sort_order ?? 0) }}" required class="control w-32 tabular-nums" @error('sort_order') aria-invalid="true" aria-describedby="sort_order-error" @enderror>
    </x-admin.ui.field>
</div>
