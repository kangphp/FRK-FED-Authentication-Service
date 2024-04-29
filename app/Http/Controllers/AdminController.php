<?php

namespace App\Http\Controllers;

use App\Models\generate_tanggal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            if (empty($token)) {
                throw ValidationException::withMessages(['error' => 'Unauthorized']);
            }
            /**
             * Validasi role yang request
             */

            if (empty($request)) {
                throw ValidationException::withMessages(['error' => 'Empty Request']);
            }

            if ($request->get('role') != 'Dosen') {
                throw ValidationException::withMessages(['error' => 'Unauthorized']);
            }

            $typeTanggal = $request->get('tipe_tanggal');

            $tgl_awal_pengisian = $request->get('tgl_awal_pengisian');
            $tgl_akhir_pengisian = $request->get('tgl_akhir_pengisian');

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

            return response()->json(['result' => true, 'data' =>  $tryGen], 201);
        } catch (ValidationException $e) {
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }
    }

    public function get_tanggal(Request $request)
    {
        $token = request()->bearerToken();
        try {
            // Validasi token dan request
            if (empty($token)) {
                throw ValidationException::withMessages(['error' => 'Unauthorized']);
            }

            if(empty($request))
            {
                throw ValidationException::withMessages(['error' => 'Empty Request']);
            }

            if (!in_array($request->type, ['FRK', 'FED']))
            {
                throw ValidationException::withMessages(['error' => 'Invalid type entered']);
            }

            if ($request->type == "FRK")
            {
                $data = generate_tanggal::all()->where('tipe', 'FRK')->sortByDesc('tgl_awal_pengisian');
            } else if ($request->type == "FED") {
                $data = generate_tanggal::all()->where('tipe', 'FED')->sortByDesc('tgl_awal_pengisian');
            }

        } catch (ValidationException $e) {
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }

        return response()->json(['result' => true, 'data' => $data], 201);
    }
}
