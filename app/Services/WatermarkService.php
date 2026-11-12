<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * PBI-03 — menyimpan media asli portofolio (privat) dan membuat versi
 * ber-watermark otomatis (publik) menggunakan ekstensi GD.
 */
class WatermarkService
{
    private const MAX_WIDTH = 1600;

    /**
     * @return array{media_url: string, media_watermark_url: string}
     */
    public function simpanPortofolio(UploadedFile $file, int $idVendor, string $teks): array
    {
        $nama = Str::uuid()->toString();
        $asli = $file->storeAs("portofolio/{$idVendor}/asli", $nama.'.'.$file->extension(), 'local');

        $gambar = $this->muatGambar($file->getRealPath());
        $gambar = $this->perkecil($gambar);
        $this->terapkanWatermark($gambar, $teks);

        ob_start();
        imagejpeg($gambar, null, 85);
        $hasil = ob_get_clean();
        imagedestroy($gambar);

        $watermark = "portofolio/{$idVendor}/watermark/{$nama}.jpg";
        Storage::disk('local')->put($watermark, $hasil);

        return ['media_url' => $asli, 'media_watermark_url' => $watermark];
    }

    private function muatGambar(string $path): \GdImage
    {
        $data = file_get_contents($path);
        $gambar = $data !== false ? @imagecreatefromstring($data) : false;

        if (! $gambar instanceof \GdImage) {
            throw new RuntimeException('File gambar tidak dapat dibaca.');
        }

        return $gambar;
    }

    private function perkecil(\GdImage $gambar): \GdImage
    {
        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);

        $kanvasLebar = min($lebar, self::MAX_WIDTH);
        $kanvasTinggi = (int) round($tinggi * ($kanvasLebar / $lebar));
        $kanvas = imagecreatetruecolor($kanvasLebar, max(1, $kanvasTinggi));
        imagefill($kanvas, 0, 0, imagecolorallocate($kanvas, 255, 255, 255));
        imagecopyresampled($kanvas, $gambar, 0, 0, 0, 0, $kanvasLebar, $kanvasTinggi, $lebar, $tinggi);
        imagedestroy($gambar);

        return $kanvas;
    }

    /**
     * Watermark berupa teks besar transparan di tengah serta pita teks di bagian bawah.
     * Memakai font bawaan GD agar tidak bergantung pada file font eksternal.
     */
    private function terapkanWatermark(\GdImage $gambar, string $teks): void
    {
        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $teks = mb_strimwidth('KolaboraEO · '.$teks, 0, 60, '…');
        $teksAscii = Str::ascii($teks);

        // Teks besar di tengah: render kecil lalu diperbesar.
        $font = 5;
        $teksLebar = imagefontwidth($font) * strlen($teksAscii);
        $teksTinggi = imagefontheight($font);
        $stempel = imagecreatetruecolor($teksLebar + 8, $teksTinggi + 4);
        imagealphablending($stempel, false);
        imagesavealpha($stempel, true);
        imagefill($stempel, 0, 0, imagecolorallocatealpha($stempel, 0, 0, 0, 127));
        imagestring($stempel, $font, 4, 2, $teksAscii, imagecolorallocatealpha($stempel, 255, 255, 255, 70));

        $targetLebar = (int) round($lebar * 0.8);
        $targetTinggi = (int) round(($teksTinggi + 4) * ($targetLebar / ($teksLebar + 8)));
        imagealphablending($gambar, true);
        imagecopyresampled(
            $gambar, $stempel,
            (int) (($lebar - $targetLebar) / 2), (int) (($tinggi - $targetTinggi) / 2),
            0, 0, $targetLebar, $targetTinggi, $teksLebar + 8, $teksTinggi + 4,
        );
        imagedestroy($stempel);

        // Pita bawah.
        $pita = max(22, (int) round($tinggi * 0.06));
        imagefilledrectangle($gambar, 0, $tinggi - $pita, $lebar, $tinggi, imagecolorallocatealpha($gambar, 23, 61, 73, 50));
        imagestring($gambar, 3, 10, $tinggi - (int) (($pita + imagefontheight(3)) / 2), $teksAscii, imagecolorallocate($gambar, 255, 255, 255));
    }
}
