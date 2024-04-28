<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{

    public function generate_tanggal(Request $request)
    {
        /**
         * Validasi role yang request
         */

        if ($request->role != 'admin') {
            throw ValidationException::withMessages(['error' => 'Unauthorized']);
        }

        try {

        } catch (ValidationException $e) {

        }
    }
}
