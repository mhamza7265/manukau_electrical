@component('mail::message')
# You have a new customer enquiry.
#### subject: {{ $subject }}

- Customer Name: {{ $name}} 
- Customer Email: {{ $email }}
- Customer Phone: {{ $phone }}
- Query: {{ $message }}


@endcomponent
