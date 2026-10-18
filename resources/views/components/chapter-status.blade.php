@props(['chapter'])
@php
$labels = [
    'draft' => 'Draft',
    'pending' => 'Menunggu Review',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
];
@endphp
<span {{ $attributes->merge(['class' => 'badge badge-' . $chapter->status]) }}>{{ $labels[$chapter->status] ?? $chapter->status }}</span>
