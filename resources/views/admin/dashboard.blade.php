@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

{{-- Page Title --}}
<h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark mb-6">Dashboard Utama</h1>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">

    {{-- Layanan Aktif --}}
    <div class="bg-white rounded-2xl border border-gray-light p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
        </div>
        <p class="text-[11px] font-semibold text-gray tracking-wider uppercase mb-1">Layanan Aktif</p>
        <p class="text-[28px] lg:text-[32px] font-extrabold text-dark leading-tight">{{ $stats['layanan_aktif'] }}</p>
    </div>

    {{-- Pengajuan Hari Ini --}}
    <div class="bg-white rounded-2xl border border-gray-light p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
            <span class="text-[11px] font-semibold text-orange-500 bg-orange-50 px-2 py-0.5 rounded-full">Total</span>
        </div>
        <p class="text-[11px] font-semibold text-gray tracking-wider uppercase mb-1">Pengajuan Hari Ini</p>
        <p class="text-[28px] lg:text-[32px] font-extrabold text-dark leading-tight">{{ $stats['pengajuan_baru'] }}</p>
    </div>

    {{-- Warga Terdaftar --}}
    <div class="bg-white rounded-2xl border border-gray-light p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-semibold text-green-500 bg-green-50 px-2 py-0.5 rounded-full">Total</span>
        </div>
        <p class="text-[11px] font-semibold text-gray tracking-wider uppercase mb-1">Warga Terdaftar</p>
        <p class="text-[28px] lg:text-[32px] font-extrabold text-dark leading-tight">{{ number_format($stats['warga_terdaftar']) }}</p>
    </div>

    {{-- Arsip Dokumen --}}
    <div class="bg-white rounded-2xl border border-gray-light p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
        </div>
        <p class="text-[11px] font-semibold text-gray tracking-wider uppercase mb-1">Arsip Dokumen</p>
        <p class="text-[28px] lg:text-[32px] font-extrabold text-dark leading-tight">{{ number_format($stats['arsip_dokumen']) }}</p>
    </div>
</div>

{{-- CHART + PENGAJUAN TERBARU --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6 mb-8">

    {{-- Chart Statistik --}}
    <div class="bg-white rounded-2xl border border-gray-light p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-dark">Statistik Pengajuan Bulanan</h2>
            <select class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-dark focus:ring-primary focus:border-primary">
                <option>Semua</option>
                <option>Menunggu</option>
                <option>Diproses</option>
                <option>Selesai</option>
            </select>
        </div>
        <div class="h-[280px]">
            <canvas id="chartPengajuan"></canvas>
        </div>
    </div>

    {{-- Pengajuan Terbaru (sidebar) --}}
    <div class="bg-white rounded-2xl border border-gray-light p-6">
        <h2 class="text-lg font-bold text-dark mb-5">Pengajuan Terbaru</h2>

        <div class="space-y-4">
            @forelse($pengajuanTerbaru->take(3) as $p)
            <div class="flex items-center gap-3">
                {{-- Avatar Inisial --}}
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-primary">
                        {{ strtoupper(substr($p->user->nama_lengkap ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $p->user->nama_lengkap ?? 'U')[1] ?? '', 0, 1)) }}
                    </span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-dark truncate">{{ $p->user->nama_lengkap ?? '-' }}</p>
                    <p class="text-xs text-gray truncate">{{ $p->layanan->nama_layanan ?? '-' }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <p class="text-sm text-gray">Belum ada pengajuan.</p>
            </div>
            @endforelse
        </div>

        @if($pengajuanTerbaru->count() > 0)
        <div class="mt-6 pt-4 border-t border-gray-100">
            <a href="#" class="text-sm font-semibold text-primary hover:underline">Lihat Semua Pengajuan</a>
        </div>
        @endif
    </div>
</div>

{{-- TABEL 5 PENGAJUAN TERBARU --}}
<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
    <div class="flex items-center justify-between p-6 pb-4">
        <h2 class="text-base font-bold text-dark">5 Pengajuan Terbaru</h2>
        <a href="#" class="text-sm font-semibold text-primary hover:underline">Lihat Semua</a>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-t border-gray-100">
                    <th class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3">Pemohon</th>
                    <th class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3">Layanan</th>
                    <th class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3">Tanggal</th>
                    <th class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3">Status</th>
                    <th class="px-6 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanTerbaru as $p)
                <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-dark">{{ $p->user->nama_lengkap ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray">{{ $p->layanan->nama_layanan ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray">{{ $p->created_at->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block text-[11px] font-bold px-3 py-1 rounded-full {{ $p->status_badge['color'] }}">
                            {{ $p->status_badge['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="text-gray-400 hover:text-primary">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray">Belum ada data pengajuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="sm:hidden px-4 pb-4 space-y-3">
        @forelse($pengajuanTerbaru as $p)
        <div class="border border-gray-100 rounded-xl p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-bold text-dark">{{ $p->user->nama_lengkap ?? '-' }}</p>
                <span class="text-[11px] font-bold px-3 py-1 rounded-full {{ $p->status_badge['color'] }}">
                    {{ $p->status_badge['label'] }}
                </span>
            </div>
            <p class="text-xs text-gray mb-1">{{ $p->layanan->nama_layanan ?? '-' }}</p>
            <p class="text-xs text-gray">{{ $p->created_at->translatedFormat('d M Y') }}</p>
        </div>
        @empty
        <p class="text-center text-sm text-gray py-8">Belum ada data pengajuan.</p>
        @endforelse
    </div>
</div>

{{-- CHART.JS SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartPengajuan').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.3)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.02)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($bulanLabels),
            datasets: [{
                label: 'Pengajuan',
                data: @json($chartValues),
                backgroundColor: function(context) {
                    const index = context.dataIndex;
                    const value = context.dataset.data[index];
                    const max = Math.max(...context.dataset.data);
                    return value === max ? '#2563EB' : '#BFDBFE';
                },
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: 40,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleFont: { family: 'Plus Jakarta Sans', weight: '600' },
                    bodyFont: { family: 'Plus Jakarta Sans' },
                    cornerRadius: 8,
                    padding: 12,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9', drawBorder: false },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        color: '#94A3B8',
                    },
                    border: { display: false },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                        color: '#94A3B8',
                    },
                    border: { display: false },
                }
            }
        }
    });
});
</script>

@endsection