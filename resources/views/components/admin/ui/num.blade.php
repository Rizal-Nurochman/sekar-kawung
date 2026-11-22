@props([
    'value',
    'decimals' => 4,
])

{{ \App\Support\Number::emission($value, $decimals) }}
