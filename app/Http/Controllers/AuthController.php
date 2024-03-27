<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            // Periksa jika validasi gagal
            if ($validator->fails()) {
                throw ValidationException::withMessages(['error' => 'Valid Input']);
            }

            // Membuat instance User
            $user = new User($request->username);

            // Lakukan autentikasi
            if ($user->authenticate($request->password)) {
                // Autentikasi berhasil, generate token JWT
                $token = JWTAuth::fromUser($user);

                $arrayResponse = [
                    'result' => true,
                    'token_auth' => $token,
                    'token_service' => $user->getTokenData(),
                    'data' => $user->getData()
                ];

                // Return token JWT sebagai respons
                return response()->json($arrayResponse, 200);
            } else {
                // Autentikasi gagal
                throw ValidationException::withMessages(['error' => 'Unauthorized']);
            }
        } catch (ValidationException $e) {
            return response()->json(['result' => false, 'error' => $e->getMessage()], 405); // 405 adalah kode status "Method Not Allowed"
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        // Mendapatkan pengguna yang terautentikasi
        $user = Auth::user();

        // Return informasi pengguna sebagai respons
        return response()->json($user);
    }
}
