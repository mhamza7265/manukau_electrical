@component('mail::message')
# New Order Received

A new order has been placed!

- Order ID: {{ $order->id }}
- User Email: {{ $order->user->email }}
- Amount: {{ $order->total_amount }}
- Status: {{ $order->status }}

Please check the admin panel for more details.

Thanks,<br>
{{ config('app.name') }}
@endcomponent