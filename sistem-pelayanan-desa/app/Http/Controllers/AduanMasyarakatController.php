<?php

namespace App\Http\Controllers;

use App\Models\AduanMasyarakat;
use App\Notifications\AdminNewAduanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AduanMasyarakatController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "nama_depan" => "required|string|max:100",
                "nama_belakang" => "required|string|max:100",
                "email" => "required|email|max:255",
                "isi_aduan" => "required|string|max:5000",
                "lampiran" => "nullable|file|mimes:jpg,jpeg,png,pdf|max:2048",
            ],
            [
                "lampiran.max" => "Ukuran lampiran maksimal 2MB.",
                "lampiran.mimes" => "Lampiran hanya boleh berupa JPG, JPEG, PNG, atau PDF.",
            ],
        );

        if ($validator->fails()) {
            return redirect()
                ->to(route("home") . "#aduan")
                ->withErrors($validator, "aduanForm")
                ->withInput();
        }

        $lampiranPath = $request->file("lampiran")?->store(
            "aduan-masyarakat",
            "public",
        );

        $aduan = AduanMasyarakat::create([
            "nama_depan" => $request->string("nama_depan")->toString(),
            "nama_belakang" => $request->string("nama_belakang")->toString(),
            "email" => $request->string("email")->toString(),
            "isi_aduan" => $request->string("isi_aduan")->toString(),
            "lampiran_path" => $lampiranPath,
            "status" => "baru",
        ]);

        $this->dispatchAdminNotification(
            new AdminNewAduanNotification($aduan),
        );

        return redirect()
            ->to(route("home") . "#aduan")
            ->with("aduan_success", "Aduan berhasil dikirim. Tim desa akan meninjau laporan Anda.");
    }
}
