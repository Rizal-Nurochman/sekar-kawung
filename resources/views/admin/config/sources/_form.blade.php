<div class="space-y-4">
    <x-admin.ui.field label="Judul" name="title" required hint="Nama publikasi atau lembaga penerbit">
        <input id="title" name="title" type="text" value="{{ old('title', $source->title ?? '') }}" required maxlength="255" class="control" @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
    </x-admin.ui.field>

    <x-admin.ui.field label="Tautan referensi" name="link_reference" hint="Opsional">
        <input id="link_reference" name="link_reference" type="url" value="{{ old('link_reference', $source->link_reference ?? '') }}" maxlength="2048" placeholder="https://" class="control" @error('link_reference') aria-invalid="true" aria-describedby="link_reference-error" @enderror>
    </x-admin.ui.field>
</div>
