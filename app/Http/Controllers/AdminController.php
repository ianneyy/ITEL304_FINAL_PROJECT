<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReplyToMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{



     public function showDashboard(){
        $recentData = DB::table('student_reservation')->get();

        $dataCount = DB::table('student_reservation')
            ->count();


        $pendingDataCount = DB::table('student_reservation')
        ->where('status', 'pending')
        ->count();

        return view('admin.dashboard', compact('recentData', 'dataCount', 'pendingDataCount'));
    }
    public function showAdminReservation()
    {
       

        $data = DB::table('student_reservation')
        ->where('status', 'pending')
        ->get();

        $completedData = DB::table('student_reservation')
        ->where('status', 'completed')
        ->get();

        return view('admin.admin_reservation', compact('data', 'completedData'));
    }
    public function paidReservation($id)
    {
        $query = DB::table('student_reservation')->where('id', $id)->update([
            'status' => 'completed'
           
        ]);

        if ($query){
            return redirect('/admin-reservation');
        }
    }
    // public function showAdminAnnouncement(){
    //     return view('admin.admin_announcement');
    // }
    public function addAnnouncement(Request $request){
        Log::info('validating');
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);
        
        $announcementDate = now()->setTimezone('Asia/Manila');
        $query = DB::table('announcement')
        ->insert([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'announcement_date' => $announcementDate,
            'created_at' => now()->setTimezone('Asia/Manila'),
            'updated_at' => now()->setTimezone('Asia/Manila'),
            
        ]);
        if( $query ){
            return redirect()->back()->with('success', 'Announcement added successfully!');
        };
    }

    public function showAdminAnnouncement()
    {
        $data = DB::table('announcement')->get();

        $data = $data->reverse();
        // Format the announcement_date for each entry
        $data = $data->map(function ($announcement) {
            $carbonDate = Carbon::parse($announcement->announcement_date);

            // Format date and time separately
            if ($carbonDate->isToday()) {
                $announcement->formatted_date = 'Today';
                $announcement->formatted_time = $carbonDate->format('g:i A');
            } elseif ($carbonDate->isYesterday()) {
                $announcement->formatted_date = 'Yesterday';
                $announcement->formatted_time = $carbonDate->format('g:i A');
            } else {
                $announcement->formatted_date = $carbonDate->format('l, F j, Y');
                $announcement->formatted_time = $carbonDate->format('g:i A');
            }

            return $announcement;
        });

        return view('admin.admin_announcement', compact('data'));
    }
    public function showQrScanner()
    {
        return view('admin.qrscanner');
    }
    public function getReservationDetails(Request $request)
    {
        $qrCodeData = $request->query('qrCodeData');
        $orderId = basename(parse_url($qrCodeData, PHP_URL_PATH)); // Extract order ID from QR code data

        $reservations = DB::table('student_reservation')
        ->where('order_id', $orderId)
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
    public function showMessages(){
        $data = DB::table('contact_us')
        ->get();
        $data = $data->reverse();

        return view('admin.messages', compact('data'));
    }

    public function sendReply(Request $request, $id)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'required|string',
        ]);
        $messageId = $request->input('message_id');
        
         $email = DB::table('contact_us')
         ->where('id', $messageId)
         ->value('email');

        // $replyMessage = DB::table('contact_us')
        // ->where('id', $messageId)
        //     ->value('reply');
        $senderName = DB::table('contact_us')
        ->where('id', $messageId)
            ->value('name');
        $userMessage = DB::table('contact_us')
        ->where('id', $messageId)
            ->value('message');

        $reply = $request->input('message');
        $reply = "LSPU BAO Replied: " . $reply;
        DB::table('contact_us')
        ->where('id', $messageId)
        ->update([
            'reply' => $reply
        ]);
        // $email = $request->input('email');
        $replyMessage = $request->input('message');
        // $senderName = $request->input('name');

       


        // Send the reply email
        Mail::to($email)->send(new ReplyToMessage($replyMessage, $senderName, $email, $userMessage));

       
        // Optionally, return a success response
        return back()->with('success', 'Reply sent successfully!');
    }
   
}
