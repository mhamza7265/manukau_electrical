<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        return view('frontend.pages.charge');
    }



    public function createCheckoutSession(Request $request)
    {

        $cart = Helper::getAllProductFromCart();
       
        // dd($cart);

        

        $order_data = json_decode($request->data, true);
        $order_data['order_number']='ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']=$request->user()->id;
        $order_data['shipping_id']=$request->shipping;
        $shipping = Shipping::where('id', $order_data['shipping_id'])->pluck('price');
        $order_data['sub_total']=Helper::totalCartPrice();
        $order_data['quantity']=Helper::cartCount();
        if(session('coupon')){
            $order_data['coupon']=session('coupon')['value'];
        }
        if($request->shipping){
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0]-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0];
            }
        }
        else{
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice();
            }
        }
        // return $order_data['total_amount'];
        $order_data['status']="process";
        $order_data['payment_method']='card';
        $order_data['payment_status']='unpaid';
        $order_data['first_name'] = $request->first_name;
        $order_data['last_name'] = $request->last_name;
        $order_data['email'] = $request->email;
        $order_data['phone'] = $request->phone;
        $order_data['address1'] = $request->address1;
        $order_data['address2'] = $request->address2;
        $order_data['country'] = $request->country;
        $order_data['post_code'] = $request->post_code;
        $order_data['shipping'] = $request->shipping;
        $order_data['payment_method'] = $request->payment_method;


        $validator = validator($order_data, [
            'first_name'=>'string|required',
            'last_name'=>'string|required',
            'email'=>'string|required',
            'phone'=>'numeric|required',
            'address1'=>'string|required',
            'address2'=>'string|nullable',
            'coupon'=>'nullable|numeric',
            'post_code'=>'string|nullable',
            'shipping' => 'required',
            'payment_method' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            $errors = reset($errors);
            return response()->json(['status' => false, 'type' => 'validation_error', 'message' => $errors[0]]);
        }

        if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
            // request()->session()->flash('error','Cart is Empty !');
            return response()->json(['status' => false , 'type' => 'empty', 'message' =>  'Cart is Empty !']);
        }

        foreach($cart as $item){
            $product = Product::find($item->product_id);
    
            // Check if the product exists
            if (!$product) {
                return response()->json(['status' => false , 'type' => 'no_product', 'message' => 'Product not found for ID: ' . $item->product_id]);
            }

            // Check for sufficient quantity
            if($product->stock < $item->quantity){
                return response()->json(['status' => false , 'type' => 'out_of_stock', 'message' =>  'Required quantity of ' . $product->title . ' is not available']);
            }
        }

        // Step 1: create a pending order
        $order = Order::create($order_data);

        // Step 2: Create the Stripe Checkout session
        $lineItems = []; // Prepare the line items from the cart
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'nzd',
                    'product_data' => [
                        'name' => $product->title,
                    ],
                    'unit_amount' => $item->price * 100, // Convert to cents
                ],
                'quantity' => $item['quantity'],
            ];
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));


        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [$lineItems],
                'mode' => 'payment',
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle the error
            return response()->json(['status' => false, 'error' => $e->getMessage()], 400);
        }
        // Step 3: Update the order with the Stripe session ID
        return response()->json(['status' => false, 'type' => 'request', 'request' => $order_data]);


        // Step 3: Store the Stripe session ID in the order
        $order->stripe_session_id = $session->id;
        $order->save();

        // Step 4: Redirect to the Stripe Checkout page
        return response()->json(['status' => true , 'session_id' => $session->id]);
    }

}
