<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SalesController extends Controller
{
  public function showSales()
  {
    // requesting info in the api
    $response = Http::get('http://127.0.0.1:8000/api/requestAdminSales');

    $data = $response->json();

    return view('admin.sales', compact('data'));
  }
}
