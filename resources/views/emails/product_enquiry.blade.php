@component('mail::message')
# You have a new product enquiry.
#### Product: {{ $product->title }}

- Product ID: {{ $product->id}} 
- Contact Name: {{ $name }}
- Contact Email: {{ $email }}
- Enquiry: {{ $description }}


@endcomponent
