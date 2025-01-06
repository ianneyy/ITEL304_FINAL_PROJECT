<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WishlistController extends Controller
{
    public function deleteWishlist($id)
    {

        $response = Http::get('http://127.0.0.1:8000/api/requestStudentDeleteWishList/' . $id);

        $data = $response->json();

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
