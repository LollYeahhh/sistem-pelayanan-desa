<footer class="bg-footer text-white">
    <div
        class="max-w-[1440px] mx-auto px-5 sm:px-8 lg:px-[121px] py-10 sm:py-12"
    >
        {{-- Desktop Layout --}}
        <div class="hidden lg:flex flex-row gap-[168px]">
            {{-- Info Desa --}}
            <div class="flex items-start gap-5">
                <img
                    src="{{ asset('images/logo-tegal.png') }}"
                    alt="Logo"
                    class="h-[72px] w-[58px] object-contain shrink-0"
                />
                <div>
                    <h3 class="text-base font-extrabold leading-7 mb-4">
                        Pemerintah Desa Dukuhtengah
                    </h3>
                    <p class="text-sm font-medium leading-7 text-white/90">Desa Dukuhtengah, Kecamatan Bojong,<br />
                    Kabupaten Tegal,<br />
                    Provinsi Jawa Tengah, 52465</p>
                </div>
            </div>

            {{-- Hubungi Kami --}}
            <div>
                <h3 class="text-base font-extrabold leading-7 mb-6">
                    Hubungi Kami
                </h3>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span class="text-sm font-medium">08xxxxxxxxxx</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="text-sm font-medium"
                            >iniemaildesa@desa.id</span
                        >
                    </div>
                </div>
            </div>

            {{-- Kunjungi Juga --}}
            <div>
                <h3 class="text-base font-extrabold leading-7 mb-6">
                    Kunjungi Juga
                </h3>
                <a
                    href="https://dukuhtengah.digitaldesa.id/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-block whitespace-nowrap text-sm font-medium hover:underline"
                    >Website Desa Dukuhtengah</a
                >
            </div>
        </div>

        {{-- Mobile Layout --}}
        <div class="flex flex-col lg:hidden">
            {{-- Info Desa — selalu tampil, tanpa dropdown --}}
            <div class="flex items-start gap-4 pb-5 border-b border-white/10">
                <img
                    src="{{ asset('images/logo-tegal.png') }}"
                    alt="Logo"
                    class="h-12 w-10 object-contain shrink-0"
                />
                <div>
                    <h3 class="text-sm font-extrabold leading-6 mb-2">
                        Pemerintah Desa Dukuhtengah
                    </h3>
                    <p class="text-xs font-medium leading-6 text-white/90">Desa Dukuhtengah, Kecamatan Bojong,<br />
                    Kabupaten Tegal,<br />
                    Provinsi Jawa Tengah, 52465</p>
                </div>
            </div>

            {{-- Hubungi Kami — dropdown --}}
            <details class="border-b border-white/10 group">
                <summary
                    class="flex w-full cursor-pointer list-none items-center justify-between py-4 text-left [&::-webkit-details-marker]:hidden"
                >
                    <span class="text-sm font-extrabold">Hubungi Kami</span>
                    <svg
                        class="w-4 h-4 text-white/60 shrink-0 transition-transform duration-300 group-open:rotate-180"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pb-4 flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span class="text-xs font-medium">08xxxxxxxxxx</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="text-xs font-medium break-all"
                            >iniemaildesa@desa.id</span
                        >
                    </div>
                </div>
            </details>

            {{-- Kunjungi Juga — dropdown --}}
            <details class="border-b border-white/10 group">
                <summary
                    class="flex w-full cursor-pointer list-none items-center justify-between py-4 text-left [&::-webkit-details-marker]:hidden"
                >
                    <span class="text-sm font-extrabold">Kunjungi Juga</span>
                    <svg
                        class="w-4 h-4 text-white/60 shrink-0 transition-transform duration-300 group-open:rotate-180"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pb-4">
                    <a
                        href="https://dukuhtengah.digitaldesa.id/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-block whitespace-nowrap text-xs font-medium hover:underline"
                        >Website Desa Dukuhtengah</a
                    >
                </div>
            </details>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="border-t border-white/10 py-3 sm:py-4 px-5">
        <p class="text-center text-[11px] sm:text-xs font-medium text-white/60 tracking-tight">Copyright @ 2026 Pemerintah Desa Dukuhtengah, All right reserved.</p>
    </div>
</footer>
