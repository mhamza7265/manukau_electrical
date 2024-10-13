<?php

namespace App\Http\Middleware;

use Closure;
use Helper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cart = Helper::getAllProductFromCart();
        if (empty($cart)) {
            request()->session()->flash('error', 'Cart is empty!');
            return redirect()->route('cart');
        }
        return $next($request);
    }
}
