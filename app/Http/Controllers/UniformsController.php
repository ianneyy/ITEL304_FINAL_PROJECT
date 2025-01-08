<?php

namespace App\Http\Controllers;

use App\Models\Uniforms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use Illuminate\Support\Facades\Http;

class UniformsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \App\Http\Requests\StoreUniformsRequest  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(StoreUniformsRequest $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Models\Uniforms  $uniforms
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(Uniforms $uniforms)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \App\Http\Requests\UpdateUniformsRequest  $request
    //  * @param  \App\Models\Uniforms  $uniforms
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(UpdateUniformsRequest $request, Uniforms $uniforms)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \App\Models\Uniforms  $uniforms
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(Uniforms $uniforms)
    // {
    //     //
    // }

    public function showUniforms()
    {

        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestLandingPage');

        $data = $response->json();

        return view('pages.uniforms', compact('data'));
    }

    public function showUniformsTable()
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminInventory');

        $data = $response->json();
        
        return view('admin.inventory', compact('data'));
    }

    public function showAddForm()
    {
        return view('admin.add_uniforms');
    }

    public function addUniform(Request $request)
    {
        // requesting info in the api

        dd($request->input('variations'));


        $image_url = null;
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $filename = $file->getClientOriginalName();
            $path = 'img/';
            $file->move($path, $filename);
            $image_url = $path . $filename;
        }

        $response = Http::post('http://127.0.0.1:8000/api/requestAdminAddUniform', [
            'image_url' => $image_url,
            'product_name' => $request->input('product_name'),
            'price' => $request->input('price'),
            'stocks' => $request->input('product_stock'),
            'description' => $request->input('description'),
            'variations' => $request->input('variations') ?? [],
            [],
            // Add more fields if there are any other form inputs
        ]);

        $data_recieved = $response->json();

        return redirect(url('/inventory'));
    }


    public function showEditForm($id)
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestEditAdminUniformForm/' . $id);

        $data = $response->json();

        return view('admin.edit_uniforms', compact('data'));
    }

    public function deleteUniforms($productId, $sizeId)
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminDeleteUniform/' . $productId . '/' . $sizeId);

        $data_recieved = $response->json();

        return redirect(url('/inventory'));
    }

    public function cancelReservation($id)
    {

        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestStudentCancelUniform/' . $id);

        $data = $response->json();

        return redirect()->back()->with('success', 'Reservation cancelled successfully');
    }

    public function deleteProduct($productId)
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestAdminDeleteProduct/' . $productId);

        $data_recieved = $response->json();

        return redirect(url('/inventory'));
    }

    public function showDetails($id)
    {
        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestStudentShowUniformDetails/' . $id);

        $data = $response->json();

        return view('pages.view_details', compact('data'));
    }

    public function showAnnouncement()
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

        return view('pages.announcement', compact('data'));

        // requesting info in the api
        // $response = Http::get('http://127.0.0.1:8000/api/requestStudentAnnouncement');   

        // $data_recieved = $response->json();

        // return view('pages.announcement', compact('data_recieved'));
    }
    public function showMessageForm()
    {
        $userId = Auth::guard('student')->id();

        // requesting info in the api
        $response = Http::get('http://127.0.0.1:8000/api/requestStudentShowMessageForm/' . $userId);

        $data = $response->json();

        return view('pages.contact_us', compact('data'));
    }
    public function addMessage(Request $request)
    {
        // requesting info in the api
        $response = Http::post('http://127.0.0.1:8000/api/requestStudentMessage', [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'message' => $request->input('message'),
        ]);

        $data = $response->json();

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    public function showSizeGuide()
    {
        return view('pages.size_guide');
    }

    public function showHelp()
    {
        return view('pages.help');
    }
}
