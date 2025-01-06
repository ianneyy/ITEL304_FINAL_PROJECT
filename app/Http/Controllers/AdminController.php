<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiControllers\ApiAdminController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReplyToMessage;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Events\Announcement;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{

    /**
     * showDashboard()
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showDashboard(Request $request): Factory|View
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminDashboard');

        $data = $response->json();

        return view('admin.dashboard', compact('data'));
    }

    /**
     * Summary of showAdminAnnouncement
     * 
     * @return Factory|View
     */
    public function showAdminAnnouncement(): Factory|View
    {
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminAnnouncement');

        $data = $response->json();

        return view('admin.admin_announcement', compact('data'));
    }

    public function showAdminReservation()
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminReservation');

        $data = $response->json();

        return view('admin.admin_reservation', compact('data'));
    }

    public function showWishlist()
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminWishlist');

        $data = $response->json();

        return view('admin.wishlist', compact('data'));
    }

    public function showQrScanner()
    {
        return view('admin.qrscanner');
    }

    public function paidReservation($id)
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminPaidReservation/' . $id);

        $data_recieved = $response->json();

        if ($data_recieved['status'] == 'success') {
            return redirect('/admin-reservation');
        }
    }

    public function updateUniform(Request $request, $id)
    {
        $imagePath = null;

        // requesting info in the api
        if ($request->hasFile('image_url')) {

            $file = $request->file('image_url');
            $filename = $file->getClientOriginalName();
            $path = 'img/';
            $file->move($path, $filename);

            $imagePath = $path . $filename;
        } else {
            // If no new image is uploaded, send the old image URL
            $imagePath = $request->file('old_image_url');
        }


        $response = Http::post('http://127.0.0.1:8000/api/requestUpdateUniform/' . $id, [
            'imageUrl' => $imagePath,
            'product_name' => $request->input('product_name'),
            'price' => $request->input('price'),
            'stocks' => $request->input('stocks'),
            'description' => $request->input('description'),
            'id' => $request->input('id'),
            'variations' => $request->input('variations'),
        ]);

        $data_recieved = $response->json();

        return  redirect()->route('inventory')->with('success', 'Uniform updated successfully.');
    }

    public function addAnnouncement(Request $request)
    {
        // requesting info in the api
        $response = Http::post('http://127.0.0.1:8000/api/requestAdminAddAnnouncement', [

            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        $data_recieved = $response->json();

        return redirect()->back()->with('success', 'Announcement added successfully!');
    }

    public function paidQrReservation($id)
    {

        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminQrPay/' . $id);

        $data = $response->json();

        return $data['success'];
    }

    public function getReservationDetails(Request $request)
    {
        $qrCodeData = $request->query('qrCodeData');
        $orderId = basename(parse_url($qrCodeData, PHP_URL_PATH)); // Extract order ID from QR code data

        $reservations = DB::table('student_reservation')
            ->where('order_id', $orderId)
            ->where('status', 'pending')
            ->get();

        if ($reservations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No reservations found.']);
        }
        $totalAmount = $reservations->sum('subTotal');
        return response()->json([
            'success' => true,
            'reservations' => $reservations,
            'totalAmount' => $totalAmount,
        ]);
    }
    public function showMessages()
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminMessages');

        $data = $response->json();

        return view('admin.messages', compact('data'));
    }

    public function sendReply(Request $request, $id)
    {
        // requesting info in the api
        $response = Http::post('http://127.0.0.1:8000/api/requestAdminReply/' . $id, [
            'name'  => $request->input('name'),
            'email' => $request->input('email'),
            'user_message' => $request->input('user_message'),
            'message' => $request->input('message'),
            'message_id' => $request->input('message_id'),
        ]);

        $data_recieved = $response->json();

        return back()->with('success', 'Reply sent successfully!');
    }
}
