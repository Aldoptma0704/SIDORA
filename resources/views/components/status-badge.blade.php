@props([
    'status' => 'draft',
])

@php
    $statusLower = strtolower(trim($status));
    
    switch ($statusLower) {
        case 'disetujui':
        case 'selesai':
        case 'aktif':
            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $icon = 'fas fa-check-circle';
            $label = 'Disetujui';
            break;

        case 'ditolak':
        case 'batal':
            $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
            $icon = 'fas fa-times-circle';
            $label = 'Ditolak';
            break;

        case 'menunggu':
        case 'pending':
        case 'proses':
            $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
            $icon = 'fas fa-clock';
            $label = 'Menunggu';
            break;

        case 'dikirim':
            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
            $icon = 'fas fa-paper-plane';
            $label = 'Terkirim';
            break;

        default:
            $badgeClass = 'bg-slate-50 text-slate-700 border-slate-200';
            $icon = 'fas fa-file-alt';
            $label = ucfirst($status);
            break;
    }
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
    <i class="{{ $icon }} text-[10px]"></i>
    <span>{{ $slot->isNotEmpty() ? $slot : $label }}</span>
</span>
