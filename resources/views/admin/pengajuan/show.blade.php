@extends ("layouts.admin")
@section ("title", "Tinjau Pengajuan")

@section ("content")
    @php
        $statusColors = [
            "menunggu" => "border-amber-200 bg-amber-50 text-amber-600",
            "diproses" => "border-blue-200 bg-blue-50 text-blue-600",
            "disetujui" => "border-emerald-200 bg-emerald-50 text-emerald-600",
            "ditolak" => "border-red-200 bg-red-50 text-red-500",
        ];
    @endphp
    <div class="space-y-6">
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"
        >
            <div class="flex items-start gap-3">
                <a
                    href="{{ route('admin.pengajuan.index') }}"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-[#D8E2F1] bg-white text-[#64748B] transition hover:border-primary/30 hover:text-primary"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div>
                    <h1
                        class="text-2xl font-extrabold text-[#0F172A] lg:text-[30px]"
                    >
                        Tinjau Pengajuan
                    </h1>
                    <p class="mt-1 text-sm text-[#64748B]">Diajukan {{ $permohonan->created_at->translatedFormat("d M Y, H:i") }} WIB oleh {{ $permohonan->user->nama_lengkap ?? "-" }}.</p>
                </div>
            </div>
            <span
                class="inline-flex shrink-0 items-center self-start rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] {{ $statusColors[$permohonan->status] ?? 'border-slate-200 bg-slate-50 text-slate-600' }}"
            >
                {{
                    ucfirst(
                        $permohonan->status,
                    )
                }}
            </span>
        </div>

        @if (session("success"))
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700"
            >
                {{
                    session(
                        "success",
                    )
                }}
            </div>
        @endif

        @if ($identityFieldsUsed->isNotEmpty())
            <div
                class="rounded-[28px] border border-[#D8E2F1] bg-white p-5 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.18)] lg:p-7"
            >
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#DBEAFE] text-primary"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#0F172A]">
                            Identitas Pemohon
                        </h2>
                        <p class="text-sm text-[#64748B]">Hanya field identitas yang memang dipakai pada pengajuan ini.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ($identityFieldsUsed as $field)
                        <div
                            class="rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-3.5"
                        >
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#94A3B8]">{{ $field["label"] }}</p>
                            <p class="mt-1.5 text-sm font-medium text-[#0F172A]">{{ $field["value"] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($additionalSections->isNotEmpty())
            <div
                class="rounded-[28px] border border-[#D8E2F1] bg-white p-5 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.18)] lg:p-7"
            >
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#E0F2FE] text-[#0284C7]"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7.5 4.5h9A2.25 2.25 0 0118.75 6.75v10.5A2.25 2.25 0 0116.5 19.5h-9A2.25 2.25 0 015.25 17.25V6.75A2.25 2.25 0 017.5 4.5z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#0F172A]">
                            Data Formulir Tambahan
                        </h2>
                        <p class="text-sm text-[#64748B]">Field berikut memang diisi khusus saat pengajuan dan tidak berasal dari profil.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @foreach ($additionalSections as $section)
                        <div>
                            <h3
                                class="mb-3 text-sm font-bold uppercase tracking-[0.16em] text-[#64748B]"
                            >
                                {{ $section["title"] }}
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach ($section["fields"] as $field)
                                    <div
                                        class="{{ $field['is_textarea'] ? 'md:col-span-2' : '' }} rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 py-3.5"
                                    >
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#94A3B8]">{{ $field["label"] }}</p>
                                        <p class="mt-1.5 whitespace-pre-line text-sm font-medium text-[#0F172A]">{{ $field["value"] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($lampiranCards->isNotEmpty())
            <div
                class="rounded-[28px] border border-[#D8E2F1] bg-white p-5 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.18)] lg:p-7"
            >
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#FFF7ED] text-[#F97316]"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-[#0F172A]">
                            Lampiran Persyaratan Dokumen
                        </h2>
                        <p class="text-sm text-[#64748B]">Hanya dokumen yang benar-benar diunggah dalam pengajuan ini.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($lampiranCards as $card)
                        <button
                            type="button"
                            onclick="openDocumentPreview(@js($card['label']), @js($card['preview_url']), @js($card['preview_type']), @js($card['download_url']), @js($card['filename']))"
                            class="group flex min-h-[184px] w-full flex-col items-center justify-center rounded-[24px] border border-[#D8E2F1] bg-white px-5 py-6 text-center transition hover:border-primary/35 hover:shadow-[0_18px_40px_-28px_rgba(37,99,235,0.35)]"
                        >
                            @if ($card["is_image"])
                                <div class="overflow-hidden rounded-[20px] border border-[#D8E2F1] bg-[#F8FAFC]">
                                    <img src="{{ $card['preview_url'] }}" alt="{{ $card['label'] }}" class="h-28 w-28 object-cover transition duration-200 group-hover:scale-[1.02]" />
                                </div>
                            @else
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-[22px] bg-[#DBEAFE] text-primary transition group-hover:bg-[#BFDBFE]"
                                >
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                            @endif
                            <p class="mt-4 line-clamp-2 text-base font-bold text-[#0F172A]">{{ $card["label"] }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-[#94A3B8]">{{ $card["section"] }}</p>
                            <span
                                class="mt-4 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600"
                            >
                                {{ $card["status"] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($permohonan->status === "ditolak" && $permohonan->catatan)
            <div
                class="rounded-[28px] border border-red-200 bg-white p-5 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.18)] lg:p-7"
            >
                <div
                    class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4"
                >
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-red-500">Alasan Penolakan</p>
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $permohonan->catatan }}</p>
                </div>
            </div>
        @endif

        @if ($permohonan->status === "menunggu" || $permohonan->status === "diproses")
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="openRejectModal()"
                    class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-white px-6 py-3 text-sm font-semibold text-red-500 transition hover:bg-red-50"
                >
                    Tolak Dokumen
                </button>
                <form
                    method="POST"
                    action="{{ route('admin.pengajuan.approve', $permohonan) }}"
                >
                    @csrf
                    @method ("PATCH")
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-[0_16px_40px_-20px_rgba(37,99,235,0.55)] transition hover:bg-primary-hover sm:w-auto"
                    >
                        Dokumen Valid
                    </button>
                </form>
            </div>
        @endif
    </div>
    <div
        id="rejectModal"
        class="fixed inset-0 z-[70] hidden items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-slate-950/40 backdrop-blur-sm"
            onclick="closeRejectModal()"
        ></div>
        <div
            class="relative w-full max-w-lg rounded-[28px] border border-[#D8E2F1] bg-white p-6 shadow-2xl sm:p-7"
        >
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-[#0F172A]">
                        Tolak Pengajuan
                    </h2>
                    <p class="mt-1 text-sm text-[#64748B]">Tuliskan alasan penolakan agar pemohon dapat memperbaiki dokumen dengan tepat.</p>
                </div>
                <button
                    type="button"
                    onclick="closeRejectModal()"
                    class="rounded-full p-2 text-[#94A3B8] transition hover:bg-slate-100 hover:text-[#334155]"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div
                class="mb-5 rounded-2xl border border-[#BFDBFE] bg-[#EFF6FF] px-4 py-3 text-sm text-[#1D4ED8]"
            >
                Pastikan alasan penolakan spesifik. Contoh: foto KTP tidak
                terbaca, nomor dokumen tidak sesuai, atau lampiran belum
                lengkap.
            </div>

            <form
                method="POST"
                action="{{ route('admin.pengajuan.reject', $permohonan) }}"
                class="space-y-4"
            >
                @csrf
                @method ("PATCH")
                <div>
                    <label
                        for="alasan_tolak"
                        class="mb-2 block text-sm font-semibold text-[#0F172A]"
                        >Alasan Penolakan</label
                    >
                    <textarea
                        id="alasan_tolak"
                        name="alasan_tolak"
                        rows="5"
                        required
                        placeholder="Contoh: Foto KTP tidak terbaca dengan jelas, silakan unggah ulang dokumen dengan pencahayaan yang lebih baik."
                        class="w-full rounded-2xl border border-[#D8E2F1] bg-[#F8FAFC] px-4 py-3 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/15"
                        >{{
                            old(
                                "alasan_tolak",
                            )
                        }}</textarea
                    >
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        onclick="closeRejectModal()"
                        class="inline-flex items-center justify-center rounded-2xl border border-[#D8E2F1] bg-white px-5 py-3 text-sm font-semibold text-[#475569] transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-red-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-600"
                    >
                        Tolak Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div
        id="documentPreviewModal"
        class="fixed inset-0 z-[75] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
    >
        <button
            type="button"
            class="absolute inset-0"
            aria-label="Tutup pratinjau"
            onclick="closeDocumentPreview()"
        ></button>
        <div
            class="relative w-full max-w-5xl rounded-[28px] border border-slate-700 bg-white p-4 sm:p-5"
        >
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <h2
                        id="documentPreviewTitle"
                        class="text-lg font-bold text-[#0F172A]"
                    >
                        Pratinjau Lampiran
                    </h2>
                    <p id="documentPreviewFilename" class="mt-1 text-sm text-[#64748B]"></p>
                </div>
                <button
                    type="button"
                    onclick="closeDocumentPreview()"
                    class="rounded-full p-2 text-[#94A3B8] transition hover:bg-slate-100 hover:text-[#334155]"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div
                class="overflow-hidden rounded-3xl border border-[#D8E2F1] bg-[#F8FAFC]"
            >
                <img
                    id="documentPreviewImage"
                    src=""
                    alt="Pratinjau lampiran"
                    class="hidden max-h-[72vh] w-full object-contain"
                />
                <iframe
                    id="documentPreviewFrame"
                    src="about:blank"
                    class="hidden h-[72vh] w-full bg-white"
                ></iframe>
                <div
                    id="documentPreviewFileState"
                    class="hidden min-h-[380px] flex-col items-center justify-center gap-4 px-6 py-10 text-center"
                >
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-[22px] bg-[#DBEAFE] text-primary"
                    >
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-[#0F172A]">Dokumen tidak mendukung pratinjau langsung</p>
                        <p class="mt-2 text-sm text-[#64748B]">Buka file di tab baru atau unduh dokumen untuk melihat isi lengkapnya.</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a
                    id="documentPreviewOpenLink"
                    href="#"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-[#D8E2F1] bg-white px-5 py-3 text-sm font-semibold text-[#334155] transition hover:border-primary/30 hover:text-primary"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H18m0 0v4.5M18 6l-7.5 7.5M6 10.5v7.125A1.875 1.875 0 007.875 19.5H15" />
                    </svg>
                    Buka File
                </a>
                <a
                    id="documentPreviewDownloadLink"
                    href="#"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-hover"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4.5-4.5M12 15l-4.5-4.5M4.5 19.5h15" />
                    </svg>
                    Unduh
                </a>
            </div>
        </div>
    </div>
    <script>
        function openRejectModal() {
            const modal = document.getElementById("rejectModal");
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.style.overflow = "hidden";
        }

        function closeRejectModal() {
            const modal = document.getElementById("rejectModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow = "";
        }

        function setPreviewActionState(element, url) {
            if (!url) {
                element.setAttribute("href", "#");
                element.classList.add("pointer-events-none", "opacity-50");
                return;
            }

            element.setAttribute("href", url);
            element.classList.remove("pointer-events-none", "opacity-50");
        }

        function openDocumentPreview(title, url, type, downloadUrl, filename) {
            const modal = document.getElementById("documentPreviewModal");
            const image = document.getElementById("documentPreviewImage");
            const frame = document.getElementById("documentPreviewFrame");
            const fileState = document.getElementById(
                "documentPreviewFileState",
            );
            const openLink = document.getElementById("documentPreviewOpenLink");
            const downloadLink = document.getElementById(
                "documentPreviewDownloadLink",
            );

            document.getElementById("documentPreviewTitle").textContent = title;
            document.getElementById("documentPreviewFilename").textContent =
                filename || "";

            image.classList.add("hidden");
            frame.classList.add("hidden");
            fileState.classList.add("hidden");
            image.src = "";
            frame.src = "about:blank";

            if (type === "image" && url) {
                image.src = url;
                image.classList.remove("hidden");
            } else if (type === "pdf" && url) {
                frame.src = url;
                frame.classList.remove("hidden");
            } else {
                fileState.classList.remove("hidden");
                fileState.classList.add("flex");
            }

            setPreviewActionState(openLink, url);
            setPreviewActionState(downloadLink, downloadUrl);

            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.style.overflow = "hidden";
        }

        function closeDocumentPreview() {
            const modal = document.getElementById("documentPreviewModal");
            const image = document.getElementById("documentPreviewImage");
            const frame = document.getElementById("documentPreviewFrame");
            const fileState = document.getElementById(
                "documentPreviewFileState",
            );

            image.src = "";
            frame.src = "about:blank";
            image.classList.add("hidden");
            frame.classList.add("hidden");
            fileState.classList.add("hidden");
            fileState.classList.remove("flex");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow = "";
        }
    </script>
@endsection





