@php
    $selected = $selectedSchemeId ?? null;
@endphp

<form
    method="POST"
    action="{{ route('calculator.start') }}"
    class="flex items-center gap-2"
    x-data="{
        changed(event) {
            if (!confirm('Ganti skema? Isian yang sudah masuk akan dihapus.')) {
                event.target.value = '{{ $selected }}';
                return;
            }
            event.target.form.submit();
        },
    }"
>
    @csrf
    <label for="scheme-picker" class="whitespace-nowrap text-[13px] font-medium text-ink-muted">Skema</label>
    <select
        id="scheme-picker"
        name="scheme_id"
        x-on:change="changed($event)"
        class="control h-10"
    >
        <option value="" @selected($selected === null)>Semua skema</option>
        @foreach ($schemes as $scheme)
            <option value="{{ $scheme->id }}" @selected((int) $selected === (int) $scheme->id)>{{ $scheme->description }}</option>
        @endforeach
    </select>
</form>