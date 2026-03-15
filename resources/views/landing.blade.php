@extends('layouts.guest')

@section('title', 'Pelayanan Desa Dukuhtengah')

@section('content')

{{-- ============================================ --}}
{{-- SECTION 1: HERO --}}
{{-- ============================================ --}}
<section id="beranda" class="relative bg-gradient-to-b from-[#D8E7FF] via-[#DDEAFF] to-white overflow-hidden">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-20">

        {{-- Hero Text --}}
        <div class="pt-24 lg:pt-32 pb-8">
            <div class="max-w-[723px]">
                <h1 class="text-4xl sm:text-5xl lg:text-[72px] font-extrabold leading-tight lg:leading-[72px] mb-6 opacity-0 animate-fade-in-up">
                    <span class="text-dark">Urus Administrasi</span><br>
                    <span class="text-primary">Tanpa Perlu Antre</span>
                </h1>
                <p class="text-base lg:text-[20px] font-normal text-gray leading-7 opacity-0 animate-fade-in-up animation-delay-200">
                    Platform pelayanan mandiri untuk warga desa Dukuhtengah.<br class="hidden lg:block">
                    Ajukan surat, pantau progres, dan terima dokumen secara digital kapan saja.
                </p>
            </div>
        </div>

        {{-- Feature Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 pb-16 lg:pb-24">

            {{-- Kinerja Optimal --}}
            <div class="bg-white border border-gray-light rounded-3xl px-8 lg:px-10 py-8 opacity-0 animate-fade-in-up animation-delay-200">
                <div class="bg-[#FFF5E5] w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                    <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <h3 class="text-[20px] font-bold text-dark leading-7 mb-7">Kinerja Optimal</h3>
                <p class="text-base font-medium text-gray leading-6">Sistem dirancang untuk memberikan pelayanan yang stabil dan konsisten.</p>
            </div>

            {{-- Kemudahan Akses --}}
            <div class="bg-white border border-gray-light rounded-3xl px-8 lg:px-10 py-8 opacity-0 animate-fade-in-up animation-delay-300">
                <div class="bg-[#E2FFF5] w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <h3 class="text-[20px] font-bold text-dark leading-7 mb-7">Kemudahan Akses</h3>
                <p class="text-base font-medium text-gray leading-6">Layanan dapat digunakan kapan saja dan di mana saja tanpa antre</p>
            </div>

            {{-- Pantauan Real-Time --}}
            <div class="bg-white border border-gray-light rounded-3xl px-8 lg:px-10 py-8 opacity-0 animate-fade-in-up animation-delay-400">
                <div class="bg-[#E0EDFF] w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-[20px] font-bold text-dark leading-7 mb-7">Pantauan Real-Time</h3>
                <p class="text-base font-medium text-gray leading-6">Pantau setiap proses secara langsung dengan mudah, cepat, dan akurat.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- SECTION 2: LAYANAN PUBLIK DIGITAL --}}
{{-- (Dinamis dari database - dikelola Admin) --}}
{{-- ============================================ --}}
<section id="layanan" class="py-16 lg:py-24">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-[102px]">

        {{-- Section Header --}}
        <div class="text-center mb-10">
            <h2 class="text-3xl lg:text-[48px] font-extrabold leading-tight lg:leading-[72px]">
                <span class="text-dark">Layanan Publik </span>
                <span class="text-primary">Digital</span>
            </h2>
            <p class="text-base font-normal text-gray leading-7 mt-1">
                Akses semua kebutuhan administrasi desa dalam satu genggaman
            </p>
        </div>

        {{-- Grid Layanan DINAMIS dari database --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 lg:gap-8">
            @forelse($layanan as $item)
            <div class="flex flex-col items-center group cursor-pointer">
                <div class="bg-white rounded-xl w-[140px] h-[140px] lg:w-[158px] lg:h-[158px] flex items-center justify-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    @if($item->icon_path)
                        <img src="{{ asset('images/layanan/' . $item->icon_path) }}"
                             alt="{{ $item->nama_layanan }}"
                             class="w-20 h-20 lg:w-24 lg:h-24 object-contain group-hover:scale-110 transition-transform duration-300">
                    @else
                        {{-- Placeholder jika Admin belum upload icon --}}
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-blue-50 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <p class="text-sm font-medium text-black text-center mt-2 leading-5 w-[140px] lg:w-[158px]">{{ $item->nama_layanan }}</p>
            </div>
            @empty
            <div class="col-span-full text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <p class="text-gray text-base">Belum ada layanan yang tersedia saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- SECTION 3: PANDUAN PENGGUNAAN LAYANAN --}}
{{-- ============================================ --}}
<section id="panduan" class="py-16 lg:py-24">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-[115px]">

        {{-- Section Header --}}
        <div class="text-center mb-10">
            <h2 class="text-3xl lg:text-[48px] font-extrabold leading-tight lg:leading-[72px]">
                <span class="text-dark">Panduan Penggunaan </span>
                <span class="text-primary">Layanan</span>
            </h2>
            <p class="text-base font-normal text-gray leading-7 mt-1">
                Ikuti langkah-langkah sederhana di bawah ini untuk mengurus dokumen administrasi<br class="hidden lg:block">
                Anda secara mandiri.
            </p>
        </div>

        {{-- Steps Grid --}}
        @php
            $steps = [
                ['no' => '01', 'title' => 'Daftar Akun', 'desc' => 'Warga yang belum terdaftar wajib melakukan registrasi akun menggunakan nama dan nomor WhatsApp aktif.', 'svg' => 'M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z'],
                ['no' => '02', 'title' => 'Masuk Portal', 'desc' => 'Gunakan nama dan kata sandi yang telah didaftarkan untuk masuk ke dalam sistem pelayanan mandiri.', 'svg' => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9'],
                ['no' => '03', 'title' => 'Pilih Jenis Surat', 'desc' => 'Klik tombol "Ajukan Baru" dan pilih jenis dokumen yang dibutuhkan.', 'svg' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                ['no' => '04', 'title' => 'Lengkapi Formulir', 'desc' => 'Isi formulir data diri dengan teliti. Pastikan tujuan pembuatan surat sudah benar.', 'svg' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10'],
                ['no' => '05', 'title' => 'Pantau Progres', 'desc' => 'Cek status permohonan Anda secara berkala di menu "Status".', 'svg' => 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ['no' => '06', 'title' => 'Terima Dokumen', 'desc' => 'Setelah status menjadi "Selesai", dokumen digital dapat diunduh langsung.', 'svg' => 'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3'],
            ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($steps as $step)
            <div class="bg-white rounded-xl p-5 lg:p-6 flex gap-6 lg:gap-9 items-start hover:shadow-md transition-shadow duration-300">
                <div class="bg-primary w-[60px] h-[60px] lg:w-[70px] lg:h-[70px] rounded-2xl flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        @foreach(explode(' M', $step['svg']) as $i => $path)
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $i > 0 ? 'M' : '' }}{{ $path }}" />
                        @endforeach
                    </svg>
                </div>
                <div>
                    <h4 class="text-base font-extrabold text-dark mb-1">
                        <span class="text-primary">{{ $step['no'] }}</span>
                        <span class="ml-2">{{ $step['title'] }}</span>
                    </h4>
                    <p class="text-sm font-medium text-gray leading-[26px]">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- SECTION 4: LAYANAN PENGADUAN WARGA --}}
{{-- ============================================ --}}
<section id="aduan" class="py-16 lg:py-24">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-20">
        <div class="flex flex-col lg:flex-row gap-10 items-start">

            {{-- Left: Text --}}
            <div class="w-full lg:w-[489px] shrink-0">
                <h2 class="text-3xl lg:text-[48px] font-extrabold leading-tight lg:leading-[72px] mb-4">
                    <span class="text-dark">Layanan </span>
                    <span class="text-primary">Pengaduan Warga</span>
                </h2>
                <p class="text-base font-normal text-gray leading-7">
                    Kami berkomitmen untuk terus meningkatkan kualitas pelayanan publik dan keterbukaan pemerintahan desa. Melalui layanan ini, masyarakat dapat dengan mudah menyampaikan aspirasi, saran, kritik, maupun keluhan secara langsung agar pelayanan dan tata kelola desa menjadi lebih baik.
                </p>
            </div>

            {{-- Right: Form --}}
            <div class="w-full lg:flex-1 bg-white rounded-[40px] shadow-[0_0_9.8px_rgba(0,0,0,0.09)] px-8 lg:px-[54px] py-10">
                <form action="#" method="POST" class="space-y-7">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8">
                        <div>
                            <label class="block text-base font-bold text-black mb-4">Nama Depan</label>
                            <input type="text" placeholder="Masukkan nama depan" class="w-full bg-[#F8F8F8] border border-[#E1E1E1] rounded-[18px] px-4 py-3 text-sm placeholder-black/30 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-base font-bold text-black mb-4">Nama Belakang</label>
                            <input type="text" placeholder="Masukkan nama belakang" class="w-full bg-[#F8F8F8] border border-[#E1E1E1] rounded-[18px] px-4 py-3 text-sm placeholder-black/30 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-base font-bold text-black mb-4">Email</label>
                        <input type="email" placeholder="Masukkan alamat email" class="w-full bg-[#F8F8F8] border border-[#E1E1E1] rounded-[18px] px-4 py-3 text-sm placeholder-black/30 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-base font-bold text-black mb-4">Laporkan Aduan</label>
                        <textarea rows="4" placeholder="Tuliskan laporan anda" class="w-full bg-[#F8F8F8] border border-[#E1E1E1] rounded-xl px-4 py-3 text-sm placeholder-black/30 resize-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div>
                        <label class="block text-base font-bold text-black mb-4">Lampiran</label>
                        <div class="relative">
                            <input type="file" class="w-full bg-[#F8F8F8] border border-[#E1E1E1] rounded-[18px] px-12 py-3 text-sm text-black/30 file:hidden">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-6 h-6 text-black/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex justify-center pt-2">
                        <button type="submit" class="bg-primary text-white font-semibold text-sm px-20 py-4 rounded-full hover:bg-primary-hover transition w-full sm:w-auto">Kirim Aduan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- SECTION 5: CTA KENDALA --}}
{{-- ============================================ --}}
<section class="py-10 lg:py-16">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-[157px]">
        <div class="bg-primary rounded-[40px] px-8 lg:px-[66px] py-12 lg:py-[60px] flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="text-white max-w-[662px]">
                <h3 class="text-2xl lg:text-[36px] font-bold leading-tight mb-4">Mengalami Kendala?</h3>
                <p class="text-base font-medium opacity-90">Tim pelayanan desa siap membantu Anda menyelesaikan kendala administrasi dengan cepat dan tepat.</p>
            </div>
            <a href="https://wa.me/628xxxxxxxxxx" target="_blank"
            class="bg-white text-dark font-extrabold text-base px-10 py-5 rounded-3xl flex items-center gap-3 hover:bg-gray-100 transition shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
                Hubungi Kami
            </a>
        </div>
    </div>
</section>

@endsection