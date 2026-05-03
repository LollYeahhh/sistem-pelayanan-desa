<?php

namespace App\Support;

use App\Models\PermohonanSurat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ArsipExcelExporter
{
    private const HEADERS = [
        "No",
        "No Registrasi",
        "Nama Pemohon",
        "NIK",
        "Jenis Layanan",
        "Status",
        "Tanggal Proses",
        "Petugas/Reviewer",
        "Catatan Admin / Alasan Penolakan",
        "Dokumen Upload Tersedia",
        "Nama File Dokumen Hasil",
        "MIME Dokumen",
        "Path Penyimpanan Relatif",
    ];

    public function export(Collection $arsip, int $year, array $months): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Arsip Dokumen");

        $this->fillHeader($sheet);
        $this->fillRows($sheet, $arsip);
        $this->autosizeColumns($sheet);

        $directory = storage_path("app/exports");
        File::ensureDirectoryExists($directory);

        $monthToken = collect($months)
            ->map(fn($month) => str_pad((string) $month, 2, "0", STR_PAD_LEFT))
            ->implode("_");

        $path = $directory . DIRECTORY_SEPARATOR . sprintf(
            "arsip-dokumen-%d-%s-%s.xlsx",
            $year,
            $monthToken,
            Str::uuid(),
        );

        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    protected function fillHeader($sheet): void
    {
        foreach (self::HEADERS as $index => $header) {
            $column = chr(ord("A") + $index);
            $cell = "{$column}1";

            $sheet->setCellValueExplicit($cell, $header, DataType::TYPE_STRING);
        }

        $sheet->getStyle("A1:M1")->getFont()->setBold(true);
        $sheet->freezePane("A2");
    }

    protected function fillRows($sheet, Collection $arsip): void
    {
        $row = 2;

        foreach ($arsip->values() as $index => $item) {
            /** @var PermohonanSurat $item */
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValueExplicit("B{$row}", (string) ($item->no_registrasi ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$row}", (string) ($item->user->nama_lengkap ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("D{$row}", (string) ($item->user->nik ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("E{$row}", (string) ($item->layanan->nama_layanan ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("F{$row}", $this->resolveStatusLabel($item->status), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit(
                "G{$row}",
                $item->reviewed_at?->format("Y-m-d H:i:s") ?? "-",
                DataType::TYPE_STRING,
            );
            $sheet->setCellValueExplicit("H{$row}", (string) ($item->reviewer->nama_lengkap ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("I{$row}", (string) ($item->rejection_reason ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("J{$row}", filled($item->file_dokumen_path) ? "Ya" : "Tidak", DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("K{$row}", (string) ($item->file_dokumen_nama ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("L{$row}", (string) ($item->file_dokumen_mime ?? "-"), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("M{$row}", (string) ($item->file_dokumen_path ?? "-"), DataType::TYPE_STRING);

            $row++;
        }
    }

    protected function autosizeColumns($sheet): void
    {
        foreach (range("A", "M") as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    protected function resolveStatusLabel(?string $status): string
    {
        return match ($status) {
            "disetujui" => "Diterima",
            "ditolak" => "Ditolak",
            "selesai" => "Selesai",
            default => Str::headline((string) $status),
        };
    }
}
