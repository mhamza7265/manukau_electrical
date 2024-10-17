<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendQuery extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $phone;
    public $message;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $subject, $phone, $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->message = $message;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->subject('New Customer Enquiry')->markdown('emails.customer_enquiry')->with(['name' => $this->name, 'email' => $this->email,  'phone' => $this->phone, 'message' => $this->message, 'subject' => $this->subject]);
    }
    
}
