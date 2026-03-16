@extends ("layouts.admin")
@section ("title", "Profil " . $user->nama_lengkap)
@section ("content")
    <div class="flex items-start gap-3 mb-6">
        <a
            href="{{ route('admin.warga.index') }}"
            class="mt-1 w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition shrink-0"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        </a>
        <div>
            <h1 class="text-xl lg:text-[24px] font-extrabold text-dark">
                Profil Pengguna
            </h1>
            <p class="text-sm text-gray mt-1">Informasi detail identitas warga yang terdaftar.</p>
        </div>
    </div>
    @if (session("success"))
        <div
            id="flash-msg"
            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center justify-between"
        >
            <span>{{
                session(
                    "success",
                )
            }}</span>
            <button
                onclick="this.parentElement.remove()"
                class="text-green-500"
            >
                &times;
            </button>
        </div>
    @endif
    {{-- Profile Card --}}
    <div class="bg-white rounded-2xl border border-gray-light p-6 mb-6">
        <div class="flex items-center gap-4">
            <div
                class="w-[96px] h-[96px] rounded-full bg-primary/10 flex items-center justify-center text-2xl font-bold text-primary shrink-0"
            >
                {{
                    strtoupper(
                        substr($user->nama_lengkap, 0, 1),
                    )
                }}{{
                    strtoupper(
                        substr(explode(" ", $user->nama_lengkap)[1] ?? "", 0, 1),
                    )
                }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-dark">
                    {{ $user->nama_lengkap }}
                </h2>
                <p class="text-sm text-gray">{{ $user->email }}</p>
                <div class="flex gap-2 mt-2">
                    <span
                        class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-600"
                        >{{
                            $user->role === "rt"
                                    ? "Ketua RT"
                                    : "Warga Tetap"
                        }}</span
                    >
                    <span
                        class="text-xs font-bold px-3 py-1 rounded-full 
    {{ $user->status_akun === 'aktif' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}"
                    >
                        {{
                            $user->status_akun === "aktif"
                                    ? "Aktif"
                                    : "Nonaktif"
                        }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    {{-- Detail Data --}}
    <div class="bg-white rounded-2xl border border-gray-light p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
            @php
                $fields = [
                    "Nama Lengkap" => $user->nama_lengkap,
                    "NIK" => $user->nik,
                    "Tempat Lahir" => $user->tempat_lahir ?? "-",
                    "Tanggal Lahir" => $user->tanggal_lahir
                        ? date("d/m/Y", strtotime($user->tanggal_lahir))
                        : "-",
                    "Jenis Kelamin" => ucfirst($user->jenis_kelamin ?? "-"),
                    "Status Perkawinan" => ucwords(
                        str_replace("_", " ", $user->status_perkawinan ?? "-"),
                    ),
                    "Agama" => ucfirst($user->agama ?? "-"),
                    "Pekerjaan" => ucfirst($user->pekerjaan ?? "-"),
                ];
            @endphp
            @foreach ($fields as $label => $value)
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $label }}</p>
                    <div
                        class="bg-[#F8FAFC] border-b border-gray-200 rounded-t-lg px-4 py-3"
                    >
                        <p class="text-sm text-dark">{{ $value }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3">
        <form
            method="POST"
            action="{{ route('admin.warga.toggle', $user) }}"
            onsubmit="return confirm('Yakin ubah status akun ini?');"
        >
            @csrf
            @method ("PATCH")
            <button
                type="submit"
                class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition"
            >
                {{
                    $user->status_akun === "aktif"
                            ? "Nonaktifkan Akun"
                            : "Aktifkan Akun"
                }}
            </button>
        </form>
        <button
            onclick="openModal('editModal')"
            class="px-6 py-3 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-hover transition"
        >
            Edit Data Warga
        </button>
    </div>
    {{-- ============================================ --}}
    {{-- MODAL: EDIT PROFIL WARGA --}}
    {{-- ============================================ --}}
    <div
        id="editModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/40"
            onclick="closeModal('editModal')"
        ></div>
        <div
            class="relative bg-white rounded-2xl w-full max-w-[836px] max-h-[90vh] overflow-y-auto shadow-xl"
        >
            {{-- Header --}}
            <div
                class="flex items-center justify-between px-8 py-5 border-b border-gray-100 sticky top-0 bg-white z-10 rounded-t-2xl"
            >
                <h2 class="text-xl font-extrabold text-dark">
                    Edit Profil Warga
                </h2>
                <button
                    onclick="closeModal('editModal')"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form
                method="POST"
                action="{{ route('admin.warga.update', $user) }}"
                class="p-8"
            >
                @csrf
                @method ("PUT")

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >Nama Lengkap</label
                        >
                        <input
                            type="text"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                            required
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark placeholder-gray-400 focus:ring-primary focus:border-primary"
                        />
                        @error ("nama_lengkap")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIK --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >NIK</label
                        >
                        <input
                            type="text"
                            name="nik"
                            value="{{ old('nik', $user->nik) }}"
                            required
                            maxlength="20"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark placeholder-gray-400 focus:ring-primary focus:border-primary"
                        />
                        @error ("nik")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tempat Lahir --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >Tempat Lahir</label
                        >
                        <input
                            type="text"
                            name="tempat_lahir"
                            value="{{ old('tempat_lahir', $user->tempat_lahir) }}"
                            placeholder="Tempat Lahir"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark placeholder-gray-400 focus:ring-primary focus:border-primary"
                        />
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >Tanggal Lahir</label
                        >
                        <input
                            type="date"
                            name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $user->tanggal_lahir ? date('Y-m-d', strtotime($user->tanggal_lahir)) : '') }}"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark placeholder-gray-400 focus:ring-primary focus:border-primary"
                        />
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >Jenis Kelamin</label
                        >
                        <select
                            name="jenis_kelamin"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark focus:ring-primary focus:border-primary"
                        >
                            <option value="">Pilih</option>
                            <option
                                value="laki-laki"
                                {{
                                    old("jenis_kelamin", $user->jenis_kelamin) ===
                                        "laki-laki"
                                            ? "selected"
                                            : ""
                                }}
                            >
                                Laki-laki
                            </option>
                            <option
                                value="perempuan"
                                {{
                                    old("jenis_kelamin", $user->jenis_kelamin) ===
                                        "perempuan"
                                            ? "selected"
                                            : ""
                                }}
                            >
                                Perempuan
                            </option>
                        </select>
                    </div>

                    {{-- Status Perkawinan --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >Status Perkawinan</label
                        >
                        <select
                            name="status_perkawinan"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark focus:ring-primary focus:border-primary"
                        >
                            <option value="">Pilih</option>
                            @foreach ([
                                    "belum_menikah" => "Belum Menikah",
                                    "menikah" => "Menikah",
                                    "cerai_hidup" => "Cerai Hidup",
                                    "cerai_mati" => "Cerai Mati",
                                ]
                                as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    {{
                                        old(
                                                "status_perkawinan",
                                                $user->status_perkawinan,
                                            ) === $value
                                                ? "selected"
                                                : ""
                                    }}
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Agama --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >Agama</label
                        >
                        <input
                            type="text"
                            name="agama"
                            value="{{ old('agama', $user->agama) }}"
                            placeholder="Agama"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark placeholder-gray-400 focus:ring-primary focus:border-primary"
                        />
                    </div>

                    {{-- Pekerjaan --}}
                    <div>
                        <label
                            class="block text-[11px] font-bold text-primary uppercase tracking-wider mb-2"
                            >Pekerjaan</label
                        >
                        <input
                            type="text"
                            name="pekerjaan"
                            value="{{ old('pekerjaan', $user->pekerjaan) }}"
                            placeholder="Pekerjaan"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[51px] px-4 text-sm text-dark placeholder-gray-400 focus:ring-primary focus:border-primary"
                        />
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="grid grid-cols-2 gap-4 mt-8">
                    <button
                        type="button"
                        onclick="closeModal('editModal')"
                        class="h-[50px] border border-gray-200 rounded-2xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="h-[50px] bg-primary text-white rounded-2xl text-sm font-semibold hover:bg-primary-hover transition"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove("hidden");
            document.body.style.overflow = "hidden";
        }
        function closeModal(id) {
            document.getElementById(id).classList.add("hidden");
            document.body.style.overflow = "";
        }
        setTimeout(() => {
            const f = document.getElementById("flash-msg");
            if (f) f.style.display = "none";
        }, 3000);
    </script>

@endsection
