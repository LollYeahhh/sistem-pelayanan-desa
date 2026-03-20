@extends ("layouts.warga")
@section ("title", "Ajukan Layanan")
@section ("page-title", "Ajukan Layanan")

@section ("content")
    {{-- Stepper --}}
    <div class="flex items-center justify-center gap-0 mb-8">
        {{-- Step 1 --}}
        <div class="flex items-center gap-2">
            <div
                class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold shadow-md shadow-primary/30"
            >
                1
            </div>
            <span class="text-sm font-semibold text-primary hidden sm:inline"
                >Pilih Layanan</span
            >
        </div>
        <div class="w-12 lg:w-20 h-[2px] bg-gray-200 mx-2"></div>
        {{-- Step 2 --}}
        <div class="flex items-center gap-2">
            <div
                class="w-10 h-10 rounded-full bg-gray-200 text-[#94a3b8] flex items-center justify-center text-sm font-bold"
            >
                2
            </div>
            <span class="text-sm font-medium text-[#94a3b8] hidden sm:inline"
                >Lengkapi Dokumen</span
            >
        </div>
        <div class="w-12 lg:w-20 h-[2px] bg-gray-200 mx-2"></div>
        {{-- Step 3 --}}
        <div class="flex items-center gap-2">
            <div
                class="w-10 h-10 rounded-full bg-gray-200 text-[#94a3b8] flex items-center justify-center text-sm font-bold"
            >
                3
            </div>
            <span class="text-sm font-medium text-[#94a3b8] hidden sm:inline"
                >Konfirmasi</span
            >
        </div>
    </div>
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-[#0f172a] mb-2">
            Pilih Jenis Layanan
        </h1>
        <p class="text-sm text-[#64748b]">Pilih layanan surat yang ingin Anda ajukan</p>
    </div>
    {{-- Service Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
        @forelse ($layanan as $item)
            <a
                href="{{ route('warga.ajukan.form', $item) }}"
                class="group bg-white rounded-2xl border border-gray-100 p-6 flex flex-col items-center text-center hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 hover:-translate-y-1 transition-all duration-300"
            >
                <div
                    class="w-20 h-20 rounded-2xl bg-blue-50 flex items-center justify-center mb-4 group-hover:bg-primary/10 transition"
                >
                    @if ($item->icon_path)
                        <img
                            src="{{ asset('images/layanan/' . $item->icon_path) }}"
                            alt="{{ $item->nama_layanan }}"
                            class="w-12 h-12 object-contain group-hover:scale-110 transition-transform duration-300"
                        />
                    @else
                        <svg class="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    @endif
                </div>
                <p class="text-sm font-semibold text-[#0f172a] leading-tight capitalize">{{ $item->nama_layanan }}</p>
            </a>
        @empty
            <div class="col-span-full text-center py-16">
                <div
                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                    <svg class="w-8 h-8 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <p class="text-sm text-[#64748b]">Belum ada layanan yang tersedia saat ini.</p>
            </div>
        @endforelse
    </div>

@endsection
