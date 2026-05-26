@props(['status'])
@php
    $class = in_array($status, ['delivered', 'completed', 'confirmed', 'received'], true)
        ? 'ok'
        : (in_array($status, ['failed', 'cancelled'], true) ? 'danger' : 'warn');
@endphp
<span class="badge {{ $class }}">{{ \App\Support\ArabicLabels::status($status) }}</span>
