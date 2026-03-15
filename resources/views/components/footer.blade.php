{{-- Footer - sesuai desain Figma --}}
<footer class="bg-footer text-white">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-[121px] py-[72px]">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-[168px]">

            {{-- Info Desa --}}
            <div class="flex items-start gap-7">
                <img src="{{ asset('images/logo-tegal.png') }}" alt="Logo" class="h-[94px] w-[76px] object-contain shrink-0">
                <div>
                    <h3 class="text-[20px] font-extrabold leading-7 mb-6">Pemerintah Desa Dukuhtengah</h3>
                    <p class="text-base font-medium leading-7 text-white/90">
                        Desa Dukuhtengah, Kecamatan Bojong,<br>
                        Kabupaten Tegal,<br>
                        Provinsi Jawa Tengah, 52465
                    </p>
                </div>
            </div>

            {{-- Hubungi Kami --}}
            <div>
                <h3 class="text-[20px] font-extrabold leading-7 mb-8">Hubungi Kami</h3>
                <div class="flex flex-col gap-5">
                    <div class="flex items-center gap-4">
                        <svg class="w-6 h-6 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span class="text-base font-medium">08xxxxxxxxxx</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <svg class="w-6 h-6 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="text-base font-medium">iniemaildesa@desa.id</span>
                    </div>
                </div>
            </div>

            {{-- Kunjungi Juga --}}
            <div>
                <h3 class="text-[20px] font-extrabold leading-7 mb-9">Kunjungi Juga</h3>
                <a href="#" class="text-base font-medium hover:underline">Website Desa Dukuhtengah</a>
            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="border-t border-white/10 py-4">
        <p class="text-center text-sm font-medium text-white/60 tracking-tight">
            Copyright @ 2026 Pemerintah Desa Dukuhtengah , All right reserved.
        </p>
    </div>
</footer>