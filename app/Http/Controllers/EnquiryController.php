<?php

namespace App\Http\Controllers;

use App\Mail\ProductEnquiryMail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function index(Request $request, $id)
    {
        $product = Product::find($id);
        return view('frontend.pages.enquire', ['id' => $id, 'product' => $product]);
    }

    public function submit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'description' =>  'required',
            'id' =>  'required',
        ]);
        $product =  Product::find($request->id);
        
        
        $name = $request->input('name');
        $email = $request->input('email');
        $phone_number = $request->input('phone_number');
        $description = $request->input('description');

        Mail::to(env('ADMIN_EMAIL_ID'))->send(new ProductEnquiryMail($name, $email, $phone_number, $description,  $product));
        request()->session()->flash('success',  'Your enquiry has been sent successfully!');
        return redirect()->back();
    }
}
