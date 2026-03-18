@extends ("layouts.warga")
@section ("title", "Profil Pengguna")
@section ("page-title", "Profil Pengguna")

@php
    $user = $user ?? auth()->user();
@endphp

@section ("content")
    <div class="max-w-3xl mx-auto">
        {{-- Profile Header --}}
        <div
            class="bg-gradient-to-r from-primary to-[#1D4ED8] rounded-2xl p-6 lg:p-8 mb-6 text-white relative overflow-hidden"
        >
            <div
                class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"
            ></div>
            <div
                class="absolute -bottom-8 -right-20 w-60 h-60 bg-white/5 rounded-full"
            ></div>

            <div class="relative z-10 flex items-center gap-5">
                <div
                    class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center shrink-0"
                >
                    <span class="text-3xl font-bold">{{
                        strtoupper(
                            substr($user->nama_lengkap ?? "U", 0, 1),
                        )
                    }}</span>
                </div>
                <div>
                    <h1 class="text-xl lg:text-2xl font-extrabold">
                        {{ $user->nama_lengkap }}
                    </h1>
                    <p class="text-white/70 text-sm mt-1">NIK: {{ $user->nik ?: "-" }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        @if ($user->is_verified)
                            <span
                                class="inline-flex items-center gap-1.5 bg-emerald-400/20 text-emerald-100 text-xs font-semibold px-3 py-1 rounded-full"
                            >
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Terverifikasi
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 bg-yellow-400/20 text-yellow-100 text-xs font-semibold px-3 py-1 rounded-full"
                            >
                                Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('warga.profil.update') }}" method="POST">
            @csrf
            @method ("PUT")

            {{-- Data Pribadi --}}
            <div
                class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 mb-6"
            >
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center"
                    >
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0f172a]">
                        Data Pribadi
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Nama Lengkap
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            type="text"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                            required
                        />
                        @error ("nama_lengkap")
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >NIK</label
                        >
                        @if (blank($user->nik))
                            <input
                                type="text"
                                name="nik"
                                value="{{ old('nik', $user->nik) }}"
                                maxlength="16"
                                inputmode="numeric"
                                placeholder="Masukkan 16 digit NIK"
                                class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                            />
                            <p class="text-xs text-[#94a3b8] mt-1">NIK hanya dapat diisi sekali setelah akun dibuat.</p>
                        @else
                            <input
                                type="text"
                                value="{{ $user->nik }}"
                                class="w-full bg-gray-100 border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#94a3b8] cursor-not-allowed"
                                disabled
                            />
                            <p class="text-xs text-[#94a3b8] mt-1">NIK tidak dapat diubah.</p>
                        @endif
                        @error ("nik")
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Tempat Lahir</label
                        >
                        <input
                            type="text"
                            name="tempat_lahir"
                            value="{{ old('tempat_lahir', $user->tempat_lahir) }}"
                            placeholder="Masukkan tempat lahir"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Tanggal Lahir</label
                        >
                        <input
                            type="date"
                            name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Jenis Kelamin</label
                        >
                        <select
                            name="jenis_kelamin"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        >
                            <option value="">-- Pilih --</option>
                            <option
                                value="Laki-laki"
                                {{
                                    old("jenis_kelamin", $user->jenis_kelamin) ===
                                        "Laki-laki"
                                            ? "selected"
                                            : ""
                                }}
                                >Laki-laki
                            </option>
                            <option
                                value="Perempuan"
                                {{
                                    old("jenis_kelamin", $user->jenis_kelamin) ===
                                        "Perempuan"
                                            ? "selected"
                                            : ""
                                }}
                                >Perempuan
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Agama</label
                        >
                        <select
                            name="agama"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        >
                            <option value="">-- Pilih --</option>
                            @foreach (["Islam", "Kristen", "Katolik", "Hindu", "Buddha", "Konghucu"]
                                as $agama)
                                <option
                                    value="{{ $agama }}"
                                    {{
                                        old("agama", $user->agama) === $agama
                                                ? "selected"
                                                : ""
                                    }}
                                    >{{ $agama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Status Perkawinan</label
                        >
                        <select
                            name="status_perkawinan"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        >
                            <option value="">-- Pilih --</option>
                            @foreach (["Belum Menikah", "Menikah", "Cerai Hidup", "Cerai Mati"] as $status)
                                <option
                                    value="{{ $status }}"
                                    {{
                                        old(
                                                "status_perkawinan",
                                                $user->status_perkawinan,
                                            ) === $status
                                                ? "selected"
                                                : ""
                                    }}
                                    >{{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Pekerjaan</label
                        >
                        <input
                            type="text"
                            name="pekerjaan"
                            value="{{ old('pekerjaan', $user->pekerjaan) }}"
                            placeholder="Masukkan pekerjaan"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >No. HP / WhatsApp</label
                        >
                        <input
                            type="text"
                            name="no_hp"
                            value="{{ old('no_hp', $user->no_hp) }}"
                            placeholder="Contoh: 08xxxxxxxxxx"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                        />
                    </div>
                </div>
            </div>

            {{-- Alamat --}}
            <div
                class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 mb-6"
            >
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center"
                    >
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0f172a]">
                        Alamat & Domisili
                    </h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <label
                            class="block text-sm font-semibold text-[#0f172a] mb-2"
                            >Alamat Lengkap</label
                        >
                        <textarea
                            name="alamat"
                            rows="3"
                            placeholder="Masukkan alamat lengkap"
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-4 py-3 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none"
                            >{{
                                old(
                                    "alamat",
                                    $user->alamat,
                                )
                            }}</textarea
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label
                                class="block text-sm font-semibold text-[#0f172a] mb-2"
                                >RT / RW</label
                            >
                            <input
                                type="text"
                                name="rt_rw"
                                value="{{ old('rt_rw', $user->rt_rw) }}"
                                placeholder="Contoh: 001/002"
                                class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-[#0f172a] mb-2"
                                >Desa / Kelurahan</label
                            >
                            <input
                                type="text"
                                name="desa"
                                value="{{ old('desa', $user->desa) }}"
                                placeholder="Nama desa"
                                class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-[#0f172a] mb-2"
                                >Kecamatan</label
                            >
                            <input
                                type="text"
                                name="kecamatan"
                                value="{{ old('kecamatan', $user->kecamatan) }}"
                                placeholder="Nama kecamatan"
                                class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl h-[48px] px-4 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-primary text-white font-bold text-sm px-8 py-3 rounded-xl hover:bg-primary-hover transition shadow-md shadow-primary/30"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

@endsection
