<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $description;
    public $product;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($name, $email, $description, $product)
    {
        $this->name = $name;
        $this->email = $email;
        $this->description = $description;
        $this->product = $product;
    }


    public function build()
    {
        return $this->subject('Product Enquiry')
        ->markdown('emails.product_enquiry')
        ->with(['name' => $this->name, 'email' => $this->email,  'description' => $this->description, 'product' => $this->product]);
    }
}
