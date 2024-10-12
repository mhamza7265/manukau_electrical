<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\Product;
use Closure;
use Helper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check if user is logedin
        if (!auth()->check()) {
            request()->session()->flash('error', 'You must be logged in to access the cart.');
            return redirect()->back();
        }
        
        // Get the cart items
        $cart = Helper::getAllProductFromCart();

        if(empty(Cart::where('user_id', auth()->user()->id)->where('order_id', null)->first())){
            request()->session()->flash('error','Cart is Empty !');
            return redirect()->back();
        }

        foreach($cart as $item){
            $product = Product::find($item->product_id);

    
            // Check if the product exists
            if (!$product) {
                request()->session()->flash('error', "Product not found for ID: {$item->product_id}");
                return redirect()->back();
            }

            // Check for sufficient quantity
            if($product->stock < $item->quantity){
                request()->session()->flash('error', "Required quantity of {$product->title} is not available");
                return redirect()->back();
            }
        }

        return $next($request);
    }
}
