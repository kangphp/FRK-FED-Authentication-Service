<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class ApiGateway extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Welcome to API Gateway']);
    }


}
