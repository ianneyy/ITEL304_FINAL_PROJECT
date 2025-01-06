<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function showCart()
    {
        $userId = Auth::guard('student')->id();

        $cartData = DB::table('reservation')
            ->where('user_id', $userId)
            ->get();
        return view('layout.design', compact('cartData'));
    }
    public function deleteCart($id)
    {

        DB::table('cart')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
