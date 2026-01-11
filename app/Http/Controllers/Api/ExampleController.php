<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function ping()
    {
        return response()->json(['pong' => true]);
    }

    public function index()
    {
        return response()->json([
            'message' => 'API funcionando',
            'time' => now()->toDateTimeString(),
        ]);
    }
}
