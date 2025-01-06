<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class QrCodeController extends Controller
{
   public function showQrCode()
   {
      $userId = Auth::guard('student')->id();

      // requesting info in the api
      $response = Http::get('http://127.0.0.1:8000/api/requestStudentShowQr/' . $userId);

      $data = $response->json();

      return view('pages.success', compact('data'));
   }
   public function showQrCodebyID($id)
   {
      $userId = Auth::guard('student')->id();

      // requesting info in the api
      $response = Http::get('http://127.0.0.1:8000/api/requestStudentViewQr/' . $userId . '/' . $id);

      $data = $response->json();

      return view('pages.success', compact('data'));
   }
   public function show(Request $request) {}
}
