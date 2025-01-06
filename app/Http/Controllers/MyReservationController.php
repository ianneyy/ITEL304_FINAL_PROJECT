<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MyReservationController extends Controller
{
    public function showMyReservation()
    {
        $userId = Auth::guard('student')->id();

        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestStudentReservation/' . $userId);

        $data = $response->json();

        return view('pages.reservation', compact('data'));
    }
}
