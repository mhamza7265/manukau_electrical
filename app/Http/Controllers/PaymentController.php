<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use App\Notifications\StatusNotification;
use App\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use App\Mail\OrderAdminNotificationMail;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class PaymentController extends Controller
{
    public function index()
    {
        // Retrieve data from the session
        $firstName = session('first_name');
        $lastName = session('last_name');
        $email = session('email');
        $phone = session('phone');
        $address1 = session('address1');
        $address2 = session('address2');
        $postCode = session('post_code');
        $shipping = session('shipping');
        $coupon = session('coupon');
        $paymentMethod = session('payment_method');


        return view('frontend.pages.charge', compact('firstName', 'lastName', 'email', 'phone', 'address1', 'address2', 'postCode', 'shipping',  'coupon', 'paymentMethod'));
    }

    
    public function paymentIntent(Request $request)
    {
        $cart = Helper::getAllProductFromCart();
        if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
            request()->session()->flash('error','Cart is Empty !');
            return back();
        }

        // dd($request->all());
        $firstName = session('first_name');
        $lastName = session('last_name');
        $email = session('email');
        $phone = session('phone');
        $address1 = session('address1');
        $address2 = session('address2');
        $country = session('country');
        $postCode = session('post_code');
        $shipping = session('shipping');
        $coupon = session('coupon');
        $paymentMethod = session('payment_method');


        $order_data = json_decode($request->data, true);
        // $order_data['amount'] = $request->amount;
        // $order_data['email'] = $request->email;
        // $order_data['phone'] = $phone;
        // $order_data['first_name'] = $firstName;
        // $order_data['last_name'] = $lastName;
        // $order_data['address1'] = $address1;
        // $order_data['address2'] = $address2;
        // $order_data['post_code'] = $postCode;
        // $order_data['shipping'] = $shipping;
        // $order_data['coupon'] = $coupon;
        // $order_data['payment_method'] = $paymentMethod;


        $order_data['order_number'] = 'ORD-'.strtoupper(Str::random(10));
        $order_data['user_id'] = $request->user()->id;
        $order_data['shipping_id'] = $shipping;
        $shippingData = Shipping::where('id', $order_data['shipping_id'])->pluck('price');
        $order_data['sub_total'] = Helper::totalCartPrice();
        $order_data['quantity'] = Helper::cartCount();
        if(session('coupon')) $order_data['coupon'] = session('coupon')['value'];
        $shipping ? $order_data['total_amount'] = Helper::totalCartPrice() + $shippingData[0] : $order_data['total_amount']=Helper::totalCartPrice();
        $order_data['status'] = "pending";
        $order_data['payment_status'] = 'unpaid';
        $order_data['first_name'] = $firstName;
        $order_data['last_name'] = $lastName;
        $order_data['email'] = $request->email;
        $order_data['phone'] = $phone;
        $order_data['address1'] = $address1;
        $order_data['address2'] = $address2;
        $order_data['country'] = $country;
        $order_data['post_code'] = $postCode;
        $order_data['payment_method'] = $paymentMethod;

        $validator = validator($order_data,[
            'first_name'=>'string|required',
            'last_name'=>'string|required',
            'address1'=>'string|required',
            'address2'=>'string|nullable',
            'coupon'=>'nullable|numeric',
            'phone'=>'numeric|required',
            'country' => 'string|required',
            'post_code'=>'string|nullable',
            'email'=>'string|required',
            'shipping_id' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            $errors = reset($errors);
            return response()->json(['status' => false, 'type' => 'validation_error', 'message' => $errors[0]]);
        }

        $order_data['stripe_payment_id'] = null;
        
        // create a pending order
        $order = Order::create($order_data);
        
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        
        // Create a Stripe Customer
        $customer = \Stripe\Customer::create([
            'name' => $firstName . ' ' . $lastName,
            'email' => $request->email,
        ]);

        // return response()->json(['status' => false, 'customer' => $customer]);
        $cartCurrent = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->get();
        
        // extract cart ids from $cartCurrent and convert array into string to send  to stripe as metadata
        $cartIds = [];
        foreach($cartCurrent as $item){
            $cartIds[] = $item->id;
        }
        $string = implode(', ', $cartIds);

        // return response()->json(['status' => false, 'cart' => $cartCurrent]);
        // Create a Payment Intent
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $order->total_amount * 100, // Amount in cents
            'currency' => 'nzd',
            'customer' => $customer->id,
            'payment_method_types' => ['card'],
            'metadata' => ['order_id' => $order->id, 'cart_id' =>  $string, 'user_id' =>  auth()->user()->id],
        ]);

       $dateToday = date('d-m-Y');

        return response()->json(['status' => true,  'type' => 'success', 'client_secret' => $paymentIntent->client_secret, 'order' => $order,  'date' => $dateToday]);
    }


    public function handleWebhook(Request $request)
    {
        Log::info('webhook hit');
        // Set your Stripe secret key
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Retrieve the raw request payload
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret =  env('STRIPE_WEBHOOK_SECRET');  // Optional for verifying webhook

        // Verify webhook signature (optional but recommended for security)
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle specific event types
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $orderId = $paymentIntent->metadata->order_id;
            $cartId = $paymentIntent->metadata->cart_id;
            $userId = $paymentIntent->metadata->user_id;

            Log::info('payment-intent:' , $paymentIntent->toArray());

            // Find the order in your database
            $order = Order::find($orderId);

            $cartIdArr = explode(',', $cartId);

            Log::info('order', $order->toArray());
            // Update order as paid and deduct stock
            if($order){
                $order->payment_status = 'paid';
                $order->stripe_payment_id = $paymentIntent->id;
                $order->save();
            

                // Deduct stock or perform other actions
                foreach ($cartIdArr as $item) {
                    $cartItem = Cart::find($item);
                    $product = Product::find($cartItem->product_id);
                    if($product){
                        if ($product->stock >= $cartItem->quantity) {
                            $product->stock -= $cartItem->quantity;
                            $product->save();
                        }else{
                            Log::warning("Insufficient stock for product ID {$cartItem->product_id}. Requested: {$cartItem->quantity}, Available: {$product->stock}");
                        }
                    }else{
                        Log::error("Product with ID {$cartItem->product_id} not found.");
                    }
                    
                }

                    Cart::where('user_id', $userId)->where('order_id', null)->update(['order_id' => $order->id]);
            }
        }

        $users = User::where('role','admin')->first();
        $details=[
            'title'=>'New order created',
            'actionURL'=>route('order.show', $order->id),
            'fas'=>'fa-file-alt'
        ];
        Notification::send($users, new StatusNotification($details));

        // Send email to the user
        Mail::to($order->user->email)->send(new OrderConfirmationMail($order));

        // Send email to the admin
        Mail::to(env('ADMIN_EMAIL_ID'))->send(new OrderAdminNotificationMail($order));

        return response()->json(['status' => 'success'], 200);  // Respond with 200 OK
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
