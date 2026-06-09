@extends ("layouts.guest")
@section ("title", "Pelayanan Desa Dukuhtengah")
@section ("content")
    {{-- SECTION 1: HERO --}}
    <section
        id="beranda"
        class="relative bg-gradient-to-b from-[#d8e7ff] from-[6%] via-[#ddeaff] via-[40%] to-white overflow-hidden"
    >
        <div class="max-w-[1440px] mx-auto px-6 lg:px-[80px]">
            {{-- Hero Text --}}
            <div
                class="pt-[114px] pb-10"
                style="padding-top: 60px; padding-bottom: 60px"
            >
                <div class="max-w-[723px]">
                    <h1
                        class="text-4xl sm:text-5xl lg:text-[72px] font-extrabold lg:leading-[72px] mb-[23px]"
                    >
                        <span class="text-[#111827] block"
                            >Urus Administrasi</span
                        >
                        <span class="text-[#2563eb] block"
                            >Tanpa Perlu Antre</span
                        >
                    </h1>
                    <p class="text-base lg:text-[20px] font-normal text-[#6b7280] leading-[28px]">Platform pelayanan mandiri untuk warga desa Dukuhtengah.<br class="hidden lg:block" />
                    Ajukan surat, pantau progres, dan terima dokumen secara digital kapan saja.</p>
                </div>
            </div>
            {{-- Feature Cards --}}
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-16"
                style="padding-bottom: 50px; padding-top: 40px"
            >
                {{-- Kinerja Optimal --}}
                <div
                    class="w-full px-10 py-8 bg-white rounded-3xl outline outline-1 outline-offset-[-1px] outline-gray-100 flex flex-col justify-start items-start gap-2.5 hover:bg-[#FFF5E5] hover:outline-[#FFF5E5] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group"
                >
                    <div
                        class="self-stretch flex flex-col justify-center items-start gap-8"
                    >
                        <div
                            class="w-16 h-16 bg-yellow-50 rounded-2xl flex justify-center items-center group-hover:bg-white transition-all duration-300"
                        >
                            <svg class="w-6 h-6 text-amber-500 group-hover:scale-110 group-hover:rotate-[15deg] transition-all duration-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                        <div
                            class="self-stretch flex flex-col justify-center items-start gap-7"
                        >
                            <div
                                class="self-stretch text-gray-900 text-xl font-bold font-['Plus_Jakarta_Sans'] leading-7"
                            >
                                Kinerja Optimal
                            </div>
                            <div
                                class="self-stretch text-gray-500 text-base font-medium font-['Plus_Jakarta_Sans'] leading-6"
                            >
                                Sistem dirancang untuk memberikan pelayanan yang
                                stabil dan konsisten.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kemudahan Akses --}}
                <div
                    class="w-full px-10 py-8 bg-white rounded-3xl outline outline-1 outline-offset-[-1px] outline-gray-100 flex flex-col justify-start items-start gap-2.5 hover:bg-[#E2FFF5] hover:outline-[#E2FFF5] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group"
                >
                    <div
                        class="self-stretch flex flex-col justify-center items-start gap-8"
                    >
                        <div
                            class="w-16 h-16 bg-emerald-50 rounded-2xl flex justify-center items-center group-hover:bg-white transition-all duration-300"
                        >
                            <svg class="w-6 h-6 text-emerald-500 group-hover:scale-110 group-hover:rotate-[15deg] transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div
                            class="self-stretch flex flex-col justify-center items-start gap-7"
                        >
                            <div
                                class="self-stretch text-gray-900 text-xl font-bold font-['Plus_Jakarta_Sans'] leading-7"
                            >
                                Kemudahan Akses
                            </div>
                            <div
                                class="self-stretch text-gray-500 text-base font-medium font-['Plus_Jakarta_Sans'] leading-6"
                            >
                                Layanan dapat digunakan kapan saja dan di mana
                                saja tanpa antre
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pantauan Real-Time --}}
                <div
                    class="w-full px-10 py-8 bg-white rounded-3xl outline outline-1 outline-offset-[-1px] outline-gray-100 flex flex-col justify-start items-start gap-2.5 hover:bg-[#E0EDFF] hover:outline-[#E0EDFF] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group"
                >
                    <div
                        class="self-stretch flex flex-col justify-center items-start gap-8"
                    >
                        <div
                            class="w-16 h-16 bg-blue-50 rounded-2xl flex justify-center items-center group-hover:bg-white transition-all duration-300"
                        >
                            <svg class="w-6 h-6 text-blue-500 group-hover:scale-110 group-hover:rotate-[15deg] transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div
                            class="self-stretch flex flex-col justify-center items-start gap-7"
                        >
                            <div
                                class="self-stretch text-gray-900 text-xl font-bold font-['Plus_Jakarta_Sans'] leading-7"
                            >
                                Pantauan Real-Time
                            </div>
                            <div
                                class="self-stretch text-gray-500 text-base font-medium font-['Plus_Jakarta_Sans'] leading-6"
                            >
                                Pantau setiap proses secara langsung dengan
                                mudah, cepat, dan akurat.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- SECTION 2: LAYANAN PUBLIK DIGITAL --}}
    @if ($layanan->isNotEmpty())
        <section id="layanan" class="bg-white py-12 lg:py-16">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-[102px]">
                {{-- Section Header --}}
                <div class="text-center mb-10">
                    <h2
                        class="text-3xl lg:text-[48px] font-extrabold leading-tight lg:leading-[72px]"
                    >
                        <span class="text-black">Layanan Publik </span>
                        <span class="text-[#2563eb]">Digital</span>
                    </h2>
                    <p class="text-[16px] font-normal text-[#6b7280] leading-[28px] mt-1">Akses semua kebutuhan administrasi desa dalam satu genggaman</p>
                </div>

                {{-- Grid Layanan DINAMIS dari database --}}
                <div
                    class="grid grid-cols-2 justify-items-center gap-x-4 gap-y-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 lg:gap-x-5 lg:gap-y-7"
                >
                    @foreach ($layanan as $item)
                        <a
                            href="{{ route('login') }}"
                            class="flex w-full max-w-[128px] flex-col items-center group cursor-pointer rounded-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2563eb] focus-visible:ring-offset-4 sm:max-w-[136px] lg:max-w-[148px]"
                        >
                            <div
                                class="flex h-[124px] w-[124px] items-center justify-center rounded-[11px] bg-white shadow-sm transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-md sm:h-[132px] sm:w-[132px] lg:h-[144px] lg:w-[144px]"
                            >
                                @if ($item->icon_url)
                                    <img
                                        src="{{ $item->icon_url }}"
                                        alt="{{ $item->nama_layanan }}"
                                        class="h-16 w-16 object-contain transition-transform duration-300 group-hover:scale-105 sm:h-[72px] sm:w-[72px] lg:h-[82px] lg:w-[82px]"
                                    />
                                @else
                                    <div
                                        class="flex h-[78px] w-[78px] items-center justify-center rounded-3xl bg-[#e0edff] sm:h-[82px] sm:w-[82px] lg:h-[90px] lg:w-[90px]"
                                    >
                                        <svg class="h-8 w-8 text-[#2563eb] lg:h-9 lg:w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <p class="mt-2 w-full text-center text-[12px] font-medium leading-5 text-black sm:text-[13px] lg:text-[13px]">{{ $item->nama_layanan }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- SECTION 3: PANDUAN PENGGUNAAN --}}
    <section id="panduan" class="bg-white py-12 lg:py-16">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-[115px]">
            {{-- Section Header --}}
            <div class="text-center mb-10">
                <h2
                    class="text-3xl lg:text-[48px] font-extrabold leading-tight lg:leading-[72px]"
                >
                    <span class="text-[#111827]">Panduan Penggunaan </span>
                    <span class="text-[#2563eb]">Layanan</span>
                </h2>
                <p class="text-[16px] font-normal text-[#6b7280] leading-[28px] mt-1">Ikuti langkah-langkah sederhana di bawah ini untuk mengurus dokumen administrasi Anda secara mandiri.</p>
            </div>

            @php
                $steps = [
                    [
                        "no" => "01",
                        "title" => "Daftar Akun",
                        "desc" =>
                            "Warga yang belum terdaftar wajib melakukan registrasi akun menggunakan nama dan nomor WhatsApp aktif.",
                        "svg" =>
                            "M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z",
                    ],
                    [
                        "no" => "02",
                        "title" => "Masuk Portal",
                        "desc" =>
                            "Gunakan nama dan kata sandi yang telah didaftarkan untuk masuk ke dalam sistem pelayanan mandiri.",
                        "svg" =>
                            "M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9",
                    ],
                    [
                        "no" => "03",
                        "title" => "Pilih Jenis Surat",
                        "desc" =>
                            'Klik tombol "Ajukan Baru" dan pilih jenis dokumen yang dibutuhkan.',
                        "svg" =>
                            "M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z",
                    ],
                    [
                        "no" => "04",
                        "title" => "Lengkapi Formulir",
                        "desc" =>
                            "Isi formulir data diri dengan teliti. Pastikan tujuan pembuatan surat sudah benar.",
                        "svg" =>
                            "M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10",
                    ],
                    [
                        "no" => "05",
                        "title" => "Pantau Progres",
                        "desc" => 'Cek status permohonan Anda secara berkala di menu "Status".',
                        "svg" =>
                            "M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z",
                    ],
                    [
                        "no" => "06",
                        "title" => "Terima Dokumen",
                        "desc" =>
                            'Setelah status menjadi "Selesai", dokumen digital dapat diunduh langsung.',
                        "svg" =>
                            "M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3",
                    ],
                ];
            @endphp

            {{-- Grid 2 kolom sesuai Figma --}}
            <div
                class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                style="column-gap: 181px"
            >
                @foreach ($steps as $step)
                    <div
                        class="bg-white rounded-[11px] p-0 h-[131px] flex items-center px-[18px] shadow-sm hover:shadow-md transition-shadow duration-300"
                    >
                        <div class="flex gap-[35px] items-start w-full">
                            <div
                                class="bg-[#2563eb] w-[70px] h-[70px] rounded-[16px] flex items-center justify-center shrink-0"
                            >
                                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    @foreach (explode(" M", $step["svg"]) as $i => $path)
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="{{ $i > 0 ? 'M' : '' }}{{ $path }}"
                                        />
                                    @endforeach
                                </svg>
                            </div>
                            <div class="flex flex-col gap-[3px] flex-1">
                                <h4
                                    class="text-[16px] font-extrabold text-[#1f2937]"
                                >
                                    <span
                                        class="text-[#2563eb]"
                                        >{{ $step["no"] }}</span
                                    >
                                    <span
                                        class="ml-2"
                                        >{{ $step["title"] }}</span
                                    >
                                </h4>
                                <p class="text-[14px] font-medium text-[#6b7280] leading-[26px]">{{ $step["desc"] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- SECTION 4: LAYANAN PENGADUAN WARGA --}}
    <section id="aduan" class="bg-white py-12 lg:py-16">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-20">
            <div
                class="flex flex-col lg:flex-row gap-[39px] items-start justify-center"
            >
                {{-- Left: Text --}}
                <div
                    class="w-full lg:w-[489px] shrink-0 flex flex-col justify-center"
                >
                    <h2
                        class="text-3xl lg:text-[48px] font-extrabold leading-tight lg:leading-[72px] mb-4"
                    >
                        <span class="text-[#111827]">Layanan </span>
                        <span class="text-[#2563eb]">Pengaduan Warga</span>
                    </h2>
                    <p class="text-[16px] font-normal text-[#6b7280] leading-[28px]">Kami berkomitmen untuk terus meningkatkan kualitas pelayanan publik dan keterbukaan pemerintahan desa. Melalui layanan ini, masyarakat dapat dengan mudah menyampaikan aspirasi, saran, kritik, maupun keluhan secara langsung agar pelayanan dan tata kelola desa menjadi lebih baik.</p>
                </div>

                {{-- Right: Form --}}
                <div
                    class="w-full lg:w-[749px] bg-white rounded-[40px] shadow-[0px_0px_9.8px_0px_rgba(0,0,0,0.09)] px-[54px] py-[39px]"
                >
                    @if (session("aduan_success"))
                        <div
                            class="mb-6 rounded-[18px] border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
                        >
                            {{
                                session(
                                    "aduan_success",
                                )
                            }}
                        </div>
                    @endif

                    @if ($errors->aduanForm->any())
                        <div
                            class="mb-6 rounded-[18px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600"
                        >
                            Mohon periksa kembali data aduan yang Anda isi.
                        </div>
                    @endif

                    <form
                        action="{{ route('aduan.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-[29px]"
                    >
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-[33px]">
                            <div class="flex flex-col gap-[17px]">
                                <label class="text-[16px] font-bold text-black"
                                    >Nama Depan</label
                                >
                                <input
                                    type="text"
                                    name="nama_depan"
                                    value="{{ old('nama_depan') }}"
                                    placeholder="Masukkan nama depan"
                                    class="w-full bg-[#f8f8f8] border border-[#e1e1e1] rounded-[18px] h-[51px] px-[14px] text-[12px] placeholder-black/30 focus:ring-primary focus:border-primary"
                                />
                                @error ("nama_depan", "aduanForm")
                                    <p class="text-[12px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-[17px]">
                                <label class="text-[16px] font-bold text-black"
                                    >Nama Belakang</label
                                >
                                <input
                                    type="text"
                                    name="nama_belakang"
                                    value="{{ old('nama_belakang') }}"
                                    placeholder="Masukkan nama belakang"
                                    class="w-full bg-[#f8f8f8] border border-[#e1e1e1] rounded-[18px] h-[51px] px-[14px] text-[12px] placeholder-black/30 focus:ring-primary focus:border-primary"
                                />
                                @error ("nama_belakang", "aduanForm")
                                    <p class="text-[12px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex flex-col gap-[17px]">
                            <label class="text-[16px] font-bold text-black"
                                >Email</label
                            >
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan alamat email"
                                class="w-full bg-[#f8f8f8] border border-[#e1e1e1] rounded-[18px] h-[51px] px-[14px] text-[12px] placeholder-black/30 focus:ring-primary focus:border-primary"
                            />
                            @error ("email", "aduanForm")
                                <p class="text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-[17px]">
                            <label class="text-[16px] font-bold text-black"
                                >Laporkan Aduan</label
                            >
                            <textarea
                                name="isi_aduan"
                                rows="4"
                                placeholder="Tuliskan laporan anda"
                                class="w-full bg-[#f8f8f8] border border-[#e1e1e1] rounded-[11px] px-[14px] py-3 text-[12px] placeholder-black/30 resize-none focus:ring-primary focus:border-primary"
                                >{{
                                    old(
                                        "isi_aduan",
                                    )
                                }}</textarea
                            >
                            @error ("isi_aduan", "aduanForm")
                                <p class="text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-[17px]">
                            <label class="text-[16px] font-bold text-black"
                                >Lampiran</label
                            >
                            <div
                                class="relative w-full bg-[#f8f8f8] border border-dashed border-[#c5c5c5] rounded-[18px] px-[14px] py-5 flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-primary/50 hover:bg-[#f0f4ff] transition-all duration-200"
                                onclick="
                                    document
                                        .getElementById('lampiran-input')
                                        .click()
                                "
                            >
                                <input
                                    type="file"
                                    id="lampiran-input"
                                    name="lampiran"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="hidden"
                                    onchange="updateFileName(this)"
                                />
                                <svg class="w-8 h-8 text-black/25" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                <p id="lampiran-text" class="text-[12px] text-black/40 text-center">
                                    Klik untuk memilih file<br />
                                    <span class="text-[11px] text-black/30"
                                        >Format: JPG, PNG, PDF — Maks 2MB</span
                                    >
                                </p>
                            </div>
                            @error ("lampiran", "aduanForm")
                                <p class="text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-center pt-2">
                            <button
                                id="btn-kirim-aduan"
                                type="submit"
                                disabled
                                class="bg-[#a8c3ff] border border-[#8bafff] text-white font-semibold text-[14px] h-[54px] px-20 rounded-[27px] transition w-full sm:w-[543px] opacity-50 cursor-not-allowed"
                            >
                                Kirim Aduan
                            </button>
                        </div>
                    </form>

                    <script>
                        function updateFileName(input) {
                            const textEl =
                                document.getElementById("lampiran-text");
                            if (input.files && input.files.length > 0) {
                                textEl.innerHTML =
                                    '<span class="text-black/70 font-medium">' +
                                    input.files[0].name +
                                    "</span>";
                            } else {
                                textEl.innerHTML =
                                    'Klik untuk memilih file<br><span class="text-[11px] text-black/30">Format: JPG, PNG, PDF — Maks 2MB</span>';
                            }
                            toggleAduanButton();
                        }

                        (function () {
                            const namaDepan = document.querySelector(
                                'input[name="nama_depan"]',
                            );
                            const namaBelakang = document.querySelector(
                                'input[name="nama_belakang"]',
                            );
                            const email = document.querySelector(
                                'input[name="email"]',
                            );
                            const isiAduan = document.querySelector(
                                'textarea[name="isi_aduan"]',
                            );
                            const btn =
                                document.getElementById("btn-kirim-aduan");

                            function toggleBtn() {
                                const filled =
                                    namaDepan.value.trim() !== "" &&
                                    namaBelakang.value.trim() !== "" &&
                                    email.value.trim() !== "" &&
                                    isiAduan.value.trim() !== "";

                                if (filled) {
                                    btn.disabled = false;
                                    btn.style.backgroundColor = "#2563eb";
                                    btn.style.borderColor = "#2563eb";
                                    btn.style.opacity = "1";
                                    btn.style.cursor = "pointer";
                                } else {
                                    btn.disabled = true;
                                    btn.style.backgroundColor = "#a8c3ff";
                                    btn.style.borderColor = "#8bafff";
                                    btn.style.opacity = "0.5";
                                    btn.style.cursor = "not-allowed";
                                }
                            }

                            // Expose for file input callback
                            window.toggleAduanButton = toggleBtn;

                            [namaDepan, namaBelakang, email, isiAduan].forEach(
                                function (el) {
                                    el.addEventListener("input", toggleBtn);
                                },
                            );

                            // Run on page load (for old() values)
                            toggleBtn();
                        })();
                    </script>
                </div>
            </div>
        </div>
    </section>
    {{-- SECTION 5: CTA KENDALA --}}
    <section class="py-10 lg:py-14 bg-white">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-[157px]">
            <div
                class="bg-[#2563eb] rounded-[40px] px-8 lg:px-[66px] py-[60px] flex flex-col lg:flex-row items-center gap-[99px]"
            >
                <div class="text-white w-full lg:w-[662px]">
                    <h3
                        class="text-2xl lg:text-[36px] font-bold leading-tight mb-[17px]"
                    >
                        Mengalami Kendala?
                    </h3>
                    <p class="text-[16px] font-medium opacity-90 leading-normal">Tim pelayanan desa siap membantu Anda menyelesaikan kendala administrasi dengan cepat dan tepat.</p>
                </div>
                <a
                    href="https://wa.me/6281329164778?text=Halo%2C%20saya%20ingin%20bertanya."
                    target="_blank"
                    class="bg-white text-[#111827] font-extrabold text-[16px] px-[40px] py-[20px] rounded-[24px] flex items-center gap-3 hover:bg-gray-100 transition shrink-0"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

@endsection
