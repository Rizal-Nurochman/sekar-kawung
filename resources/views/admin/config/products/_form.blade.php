<div class="space-y-4">
    <x-admin.ui.field label="Skema" name="scheme_id" required>
        <select id="scheme_id" name="scheme_id" required class="control" @error('scheme_id') aria-invalid="true" aria-describedby="scheme_id-error" @enderror>
            <option value="">Pilih skema</option>
            @foreach ($schemes as $scheme)
                <option value="{{ $scheme->id }}" @selected((string) old('scheme_id', $product->scheme_id ?? '') === (string) $scheme->id)>{{ $scheme->description }}</option>
            @endforeach
        </select>
    </x-admin.ui.field>

    <x-admin.ui.field label="Deskripsi" name="description" required hint="Contoh: Produk Produksi dari Biji Sampai Jadi Kain">
        <input id="description" name="description" type="text" value="{{ old('description', $product->description ?? '') }}" required maxlength="255" class="control" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>
    </x-admin.ui.field>

    <x-admin.ui.field label="Urutan" name="sort_order" required hint="Kalkulator menggabungkan tahap dengan urutan lebih kecil atau sama">
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $product->sort_order ?? 0) }}" required class="control w-32 tabular-nums" @error('sort_order') aria-invalid="true" aria-describedby="sort_order-error" @enderror>
    </x-admin.ui.field>
</div>
