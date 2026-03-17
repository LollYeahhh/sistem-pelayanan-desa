@extends ("layouts.rt")
@section ("title", "Ajukan Layanan RT")

@section ("content")
    <div class="space-y-8">
        <div class="flex items-center justify-center gap-0">
            <div class="flex items-center gap-2">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-white shadow-md shadow-primary/30"
                >
                    1
                </div>
                <span
                    class="hidden text-sm font-semibold text-primary sm:inline"
                    >Pilih Layanan</span
                >
            </div>
            <div class="mx-2 h-[2px] w-12 bg-gray-200 lg:w-20"></div>
            <div class="flex items-center gap-2">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-[#94A3B8]"
                >
                    2
                </div>
                <span
                    class="hidden text-sm font-medium text-[#94A3B8] sm:inline"
                    >Lengkapi Dokumen</span
                >
            </div>
            <div class="mx-2 h-[2px] w-12 bg-gray-200 lg:w-20"></div>
            <div class="flex items-center gap-2">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-[#94A3B8]"
                >
                    3
                </div>
                <span
                    class="hidden text-sm font-medium text-[#94A3B8] sm:inline"
                    >Konfirmasi</span
                >
            </div>
        </div>

        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-[#0F172A]">
                Ajukan Permohonan Layanan
            </h1>
            <p class="mt-2 text-sm text-[#64748B]">Pilih jenis layanan yang ingin Anda ajukan sebagai RT.</p>
        </div>

        <div
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
        >
            @forelse ($layanan as $item)
                <a
                    href="{{ route('rt.ajukan.form', $item) }}"
                    class="group rounded-[20px] bg-white p-5 text-center shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)] transition hover:-translate-y-1 hover:shadow-[0_24px_40px_-28px_rgba(37,99,235,0.35)]"
                >
                    <div
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-[#EAF2FF]"
                    >
                        @if ($item->icon_path)
                            <img
                                src="{{ asset('images/layanan/' . $item->icon_path) }}"
                                alt="{{ $item->nama_layanan }}"
                                class="h-14 w-14 object-contain"
                            />
                        @else
                            <svg class="h-10 w-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        @endif
                    </div>
                    <p class="mt-4 text-sm font-semibold leading-6 text-[#111827]">
                        {{ $item->nama_layanan }}
                    </p>
                </a>
            @empty
                <div
                    class="col-span-full rounded-[20px] bg-white px-6 py-12 text-center text-sm text-[#64748B] shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]"
                >
                    Belum ada layanan aktif yang tersedia.
                </div>
            @endforelse
        </div>
    </div>
@endsection
