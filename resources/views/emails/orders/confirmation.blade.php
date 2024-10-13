@component('mail::message')
# Order Confirmation

Thank you for your order! Here are your order details:

- Order ID: {{ $order->id }}
- Amount: {{ $order->total_amount }}
- Status: {{ $order->status }}

We appreciate your business!

Thanks,<br>
{{ config('app.name') }}
@endcomponent
