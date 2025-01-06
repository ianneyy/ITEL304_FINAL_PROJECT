<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Events\NewPendingReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class ReserveController extends Controller
{


    // Log current session and user


    public function reserve(Request $request)
    {
        $userId = Auth::guard('student')->id();

        $response = Http::post('http://127.0.0.1:8000/api/requestStudentToCartWishlistUniform/' . $userId, [
            'image_url' => $request->input('image_url'),
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'qty' => $request->input('qty'),
            'action' => $request->input('action'),
            'department' => $request->input('department') ?? null,
            'size' => $request->input('size') ?? null,
        ]);

        $data = $response->json();

        if ($data['status'] == 'error_noUser') {
            return redirect()->route('student.user-login')->with('error', 'Student not found');
        } else if ($data['status'] == 'success_reservation') {
            return redirect(url('/fill-up-form'));
        } else if ($data['status'] == 'cart_success') {
            return redirect()->back()->with('cart_success', true);
        } else if ($data['status'] == 'cart_error') {
            return redirect()->back()->with('error', 'Failed to add to cart.');
        } else if ($data['status'] == 'success_wishlist') {
            return redirect()->back()->with('success', 'Item added to wishlist.');
        }
    }
    public function sendToFillUpForm(Request $request)
    {
        $userId = Auth::guard('student')->id();

        $student = DB::table('students')
            ->where('id', $userId)
            ->first();

        $fullName = $student->name;
        $email = $student->email;
        $selectedItems = json_decode($request->input('selected_items'), true);
        $totalPrice = $request->input('total_price');
        $uniqueCartId = uniqid('cart_');
        $orderId = uniqid('order_');

        foreach ($selectedItems as $item) {



            // Insert each item into the reservation table
            DB::table('reservation')->insert([
                'order_id' => $orderId,
                'user_id' => $userId,
                'full_name' => $fullName,
                'email' => $email,
                'name' => $item['name'],
                'price' => $item['price'],
                'image_url' => $item['image_url'],
                'variation_type' => $item['variation_type'],
                'size' => $item['size'],
                'qty' => $item['qty'],
                'subTotal' => $item['subTotal'],
                'total_price' => $totalPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('cart')
                ->where('id', $item['id'])
                ->where('user_id', $userId)

                ->update([
                    'cart_id' => $uniqueCartId,
                    'updated_at' => now()
                ]);
        }
        return redirect(url('/fill-up-form'));
    }


    public function showReserve()
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestStudentShowReserveRequest');

        $data = $response->json();

        return view('pages.fill_up_form', compact('data'));
    }
    public function continueShopping($id)
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestStudentContinueShoping/' . $id);

        $data_recieved = $response->json();

        if ($data_recieved['status'] == 'success') {
            return redirect(url('/student/uniforms'));
        }
    }
    public function addReserve(Request $request)
    {

        $userId = Auth::guard('student')->id();

        $response = Http::post('http://127.0.0.1:8000/api/requestStudentAddReservation/' . $userId, [
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'contact_number' => $request->input('contact_number'),
            'reservation_date' => $request->input('reservation_date'),
            'pay_method' => $request->input('pay_method'),
            'total_price' => $request->input('total_price'),
        ]);

        $data = $response->json();

        return redirect(url('/student/qrcode'));
    }


    public function apiCall($httpRequest)
    {

        $response = Http::get('http://127.0.0.1:8000/api/' . $httpRequest);

        $data_recieved = $response->json();

        return $response;
    }
}
