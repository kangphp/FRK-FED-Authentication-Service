<?php

namespace App\Http\Controllers;

use App\Models\Assign;
use App\Models\generate_tanggal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{

    /**
     * @param Request $request
     * @return void
     * @throws ValidationException
     *
     * @extra_param role,
     * @extra_param type_tanggal,
     * tgl_awal_pengisian,
     * tgl_akhir_pengisian,
     * periode_awal_approve_assesor_1,
     * periode_akhir_approve_assesor_1,
     * periode_awal_approve_assesor_2,
     * periode_akhir_approve_assesor_2,
     * tahun_ajaran
     */

    public function generate_tanggal(Request $request)
    {
        $token = request()->bearerToken();

        try {
            // if (empty($token)) {
            //     throw ValidationException::withMessages(['error' => 'Unauthorized']);
            // }

            /**
             * Validasi role yang request
             */

            if (empty($request)) {
                throw ValidationException::withMessages(['error' => 'Empty Request']);
            }

            if ($request->get('role') != 'Staf Human Resources') {
                throw ValidationException::withMessages(['error' => 'Unauthorized']);
            }

            $typeTanggal = $request->get('tipe_tanggal');

            $tgl_awal_pengisian = $request->get('tgl_awal_pengisian');
            $tgl_akhir_pengisian = $request->get('tgl_akhir_pengisian');

            /**
             * Pengecekan inputan yang di berikan sudah benar atau tidak
             */

            if (strtotime($tgl_akhir_pengisian) < strtotime($tgl_awal_pengisian)) {
                throw ValidationException::withMessages(['error' => 'Tgl akhir pengisian harus lebih besar dari tgl awal pengisian']);
            } else if (strtotime(date("Y-m-d")) > strtotime($tgl_awal_pengisian)) {
                throw ValidationException::withMessages(['error' => 'Tanggal awal pengisian yang di tetapkan sudah lewat']);
            }

            $periode_awal_approve_assesor_1 = $request->get('periode_awal_approve_assesor_1');
            $periode_akhir_approve_assesor_1 = $request->get('periode_akhir_approve_assesor_1');

            $periode_awal_approve_assesor_2 = $request->get('periode_awal_approve_assesor_2');
            $periode_akhir_approve_assesor_2 = $request->get('periode_akhir_approve_assesor_2');

            $tahun_ajaran = $request->get('tahun_ajaran');

            $tryGen = generate_tanggal::create([
                'tipe' => $typeTanggal,
                'tgl_awal_pengisian' => $tgl_awal_pengisian,
                'tgl_akhir_pengisian' => $tgl_akhir_pengisian,
                'periode_awal_approve_assesor_1' => $periode_awal_approve_assesor_1,
                'periode_akhir_approve_assesor_1' => $periode_akhir_approve_assesor_1,
                'periode_awal_approve_assesor_2' => $periode_awal_approve_assesor_2,
                'periode_akhir_approve_assesor_2' => $periode_akhir_approve_assesor_2,
                'tahun_ajaran' => $tahun_ajaran
            ]);

            return response()->json(['result' => true, 'data' => $tryGen], 201);
        } catch (ValidationException $e) {
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }
    }

    public function get_tanggal(Request $request)
    {
        $token = request()->bearerToken();
        try {
            // Validasi token dan request
            // if (empty($token)) {
            //     throw ValidationException::withMessages(['error' => 'Unauthorized']);
            // }

            if (empty($request)) {
                throw ValidationException::withMessages(['error' => 'Empty Request']);
            }

            if (!in_array($request->type, ['FRK', 'FED'])) {
                throw ValidationException::withMessages(['error' => 'Invalid type entered']);
            }

            if ($request->type == "FRK") {
                $data = generate_tanggal::all()->where('tipe', 'FRK')->sortByDesc('tgl_awal_pengisian')->first();
            } else if ($request->type == "FED") {
                $data = generate_tanggal::all()->where('tipe', 'FED')->sortByDesc('tgl_awal_pengisian')->first();
            }

        } catch (ValidationException $e) {
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }

        return response()->json(['result' => true, 'data' => $data], 201);
    }

    public function post_assign(Request $request)
    {
        $token = request()->bearerToken();

        try {
            $validator = Validator::make($request->all(), [
                'id_pegawai' => 'numeric|required',
                'id_FRK' => "numeric|required",
                'id_FED' => "numeric|required",
                'jabatan' => 'string|required'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if (preg_match('/Ketua Program Studi/i', $request->jabatan))
            {
                $tipeAsesor = 2;
            } else if (preg_match('/Dekan Fakultas/i', $request->jabatan)) {
                $tipeAsesor = 1;
            } else if (preg_match('/Wakil Rektor|\bREKTOR\b/i', $request->jabatan)) {
                $tipeAsesor = 3;
            } else {
                throw ValidationException::withMessages(['error' => 'Invalid jabatan entered']);
            }

            $requestDataDosen = Http::withToken($token)->asForm()->post('https://cis-dev.del.ac.id/api/library-api/dosen?pegawaiid=' . $request->id_pegawai)->body();
            $prodiDosen = json_decode($requestDataDosen, true)['data']['dosen'][0]['prodi'];

            $prodiFITE = ['S1 Informatika','S1 Sistem Informasi','S1 Teknik Elektro'];
            $prodiVokasi = ['DIII Teknologi Informasi','DIII Teknologi Komputer', 'DIV Teknologi Rekayasa Perangkat Lunak'];
            $prodiBP = ['S1 Teknik Bioproses'];
            $prodiFTI = ['S1 Manajemen Rekayasa'];

            if (in_array($prodiDosen, $prodiFITE)) {
                $fakultas = "Fakultas Informatika dan Teknik Elektro";
            } else if (in_array($prodiDosen, $prodiVokasi)) {
                $fakultas = "Fakultas Vokasi";
            } else if (in_array($prodiDosen, $prodiBP)) {
                $fakultas = "Fakultas Bioteknologi";
            } else if (in_array($prodiDosen, $prodiFTI)) {
                $fakultas = "Fakultas Teknik Industri";
            }

            $tryAssign = [
                'id_pegawai' => $request->get('id_pegawai'),
                'tipe_asesor' => $tipeAsesor,
                'program_studi' => $prodiDosen,
                'id_tanggal_frk' => $request->id_FRK,
                'id_tanggal_fed' => $request->id_FED,
                'fakultas' => $fakultas,
            ];

            $tryAssign = Assign::create($tryAssign);
        } catch (ValidationException $e) {
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }

        return response()->json(['result' => true, 'data' => $tryAssign], 201);
    }

    public function get_eligible_asesor(Request $request)
    {
        $token = request()->bearerToken();

        try {
            $requestDataUnit = Http::withToken($token)->asForm()->post('https://cis-dev.del.ac.id/api/library-api/unit?nama=&id_unit=&limit=&with_member=')->body();

            $data = json_decode($requestDataUnit, true);

            $result = [];

            foreach ($data['data']['unit'] as $unit) {
                // Memeriksa apakah 'kepala' mengandung 'Ketua Program Studi' atau 'Dekan Fakultas'
                $matchFound = false;
                if (preg_match('/Ketua Program Studi|Dekan Fakultas|Wakil Rektor Bidang Perencanaan, Keuangan, dan Sumber Daya|Wakil Rektor Bidang Akademik dan Kemahasiswaan|\bREKTOR\b/i', $unit['kepala'])) {
                    $matchFound = true;
//                    $result[] = $unit;
                } else if ($unit['name'] == "Rektorat") {
                    // Loop melalui setiap anggota dalam unit
                    foreach ($unit['anggota'] as $anggota) {
                        if (preg_match('/Wakil Rektor Bidang Perencanaan, Keuangan, dan Sumber Daya|Wakil Rektor Bidang Akademik dan Kemahasiswaan/i', $anggota['jabatan'])) {
                            $matchFound = true;
                            break;
                        }
                    }
                }

                if ($matchFound) {
                    $result[] = $unit;
                }
            }

            $requestDataByUnit = Http::withToken($token)->asForm()->post('https://cis-dev.del.ac.id/api/library-api/unit?nama=&id_unit=102&limit=&with_member=1')->body();

            $resultWR = [];

            foreach (json_decode($requestDataByUnit, true)['data']['unit'] as $unit) {
                foreach ($unit['anggota'] as $anggota) {
                    if (preg_match('/Wakil Rektor Bidang Perencanaan, Keuangan, dan Sumber Daya|Wakil Rektor Bidang Akademik dan Kemahasiswaan/i', $anggota['jabatan']))
                    {
                        $resultWR = [[
                            'unit_id' => $unit['unit_id'],
                            'name' => $unit['name'],
                            'inisial' => $unit['inisial'],
                            'kepala_id' => $unit['kepala_id'],
                            'kepala' => $anggota['jabatan'],
                            'pegawai_id' => $anggota['pegawai_id'],
                            'nama' => $anggota['nama'],
                            'anggota' => []
                        ]];
                    }
                }
            }

            $result = array_merge_recursive($result, $resultWR);

            usort($result, function ($a, $b) {
                return strcmp($b['kepala'], $a['kepala']);
            });

            return response()->json(['result' => true, 'data' => $result], 200);
        } catch (ValidationException $e) {
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }
    }
}
