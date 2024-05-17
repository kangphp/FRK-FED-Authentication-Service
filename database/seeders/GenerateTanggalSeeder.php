<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\generate_tanggal;

class GenerateTanggalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        generate_tanggal::create([
            'tipe' => 'FED',
            'tgl_awal_pengisian' => '2024-06-01',
            'tgl_akhir_pengisian' => '2024-06-10',
            'periode_awal_approve_assesor_1' => '2024-06-11',
            'periode_akhir_approve_assesor_1' => '2024-06-15',
            'periode_awal_approve_assesor_2' => '2024-06-16',
            'periode_akhir_approve_assesor_2' => '2024-06-20',
            'tahun_ajaran' => '2023/2024',
        ]);

        generate_tanggal::create([
            'tipe' => 'FRK',
            'tgl_awal_pengisian' => '2024-01-01',
            'tgl_akhir_pengisian' => '2024-01-10',
            'periode_awal_approve_assesor_1' => '2024-01-11',
            'periode_akhir_approve_assesor_1' => '2024-01-15',
            'periode_awal_approve_assesor_2' => '2024-01-16',
            'periode_akhir_approve_assesor_2' => '2024-01-20',
            'tahun_ajaran' => '2023/2024',
        ]);

        // Tambahkan data lainnya jika diperlukan
    }
}
