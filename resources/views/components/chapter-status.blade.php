@props(['chapter'])
@php
$labels = [
    'draft' => 'Draf',
    'pending' => 'Menunggu Review',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
];
@endphp
<span {{ $attributes->merge(['class' => 'badge badge-' . $chapter->status]) }}>{{ $labels[$chapter->status] ?? $chapter->status }}</span>
