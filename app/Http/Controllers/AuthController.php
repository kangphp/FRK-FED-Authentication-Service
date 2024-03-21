<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if ($request->isMethod('POST')) {
                $requestLogin = Http::asForm()->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                    'username' => $request->usernameLogin,
                    'password' => $request->passwordLogin
                ])->body();

                $data = json_decode($requestLogin, true);

                if ($data['result'] == false) {
                    throw ValidationException::withMessages(['error' => 'Invalid Username or Password']);
                } else {
//                    $this->getDataDosen($data['user']['user_id'], $data['token']);

                    $responseArray = [
                        'result' => true,
                        'request_token' => $data['token'],
                        'data' =>
                            [
                                'user' => [
                                    'user_id' => $data['user']['user_id'],
                                    'username' => $data['user']['username'],
                                    'email' => $data['user']['email'],
                                    'role' => $data['user']['role'],
                                    'status' => $data['user']['status'],
                                    'jabatan' => $data['user']['jabatan'],
                                ],
                                'data_lengkap' => $this->getDataDosen($data['user']['user_id'], $data['token'])
                            ]
                    ];

//                    return response()->json($responseArray, 200);
                    return response()->json($responseArray, 200);
                }
            } else {
                // Jika metode bukan POST, lemparkan pengecualian
                throw ValidationException::withMessages(['error' => 'Method Not Allowed']);
            }
        } catch (ValidationException $e) {
            // Tangani pengecualian (exception) jika terjadi validasi gagal atau metode bukan POST
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }
    }

    private function getDataDosen($uid, $token)
    {
        $requestDataDosen = Http::withToken($token)->asForm()->post('https://cis-dev.del.ac.id/api/library-api/dosen?userid=' . $uid)->body();
        $jsonDataDosen = json_decode($requestDataDosen, true);

        $pegawaiId = $jsonDataDosen['data']['dosen'][0]['pegawai_id'];

        $requestDataPegawai = Http::withToken($token)->asForm()->post('https://cis-dev.del.ac.id/api/library-api/pegawai?pegawaiid=' . $pegawaiId)->body();
        $jsonDataPegawai = json_decode($requestDataPegawai,true);

        $arrayData = [
            'dosen' => [
                "pegawai_id" => $jsonDataDosen['data']['dosen'][0]['pegawai_id'],
                "dosen_id" => $jsonDataDosen['data']['dosen'][0]['dosen_id'],
                "nip" => $jsonDataDosen['data']['dosen'][0]['nip'],
                "nama" => $jsonDataDosen['data']['dosen'][0]['nama'],
                "prodi_id" => $jsonDataDosen['data']['dosen'][0]['prodi_id'],
                "prodi" => $jsonDataDosen['data']['dosen'][0]['prodi'],
                "jabatan_akademik" => $jsonDataDosen['data']['dosen'][0]['jabatan_akademik'],
                "jabatan_akademik_desc" => $jsonDataDosen['data']['dosen'][0]['jabatan_akademik_desc'],
                "jenjang_pendidikan" => $jsonDataDosen['data']['dosen'][0]['jenjang_pendidikan'],
                "nidn" => $jsonDataDosen['data']['dosen'][0]['nidn'],
            ],
            'pegawai' => $jsonDataPegawai['data']['pegawai'][0]
        ];
        return [$arrayData];
    }
}
