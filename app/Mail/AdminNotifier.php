<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminNotifier extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $_request, $customer_name, $customer_email, $message;

    public function __construct($_request, $customer_name, $customer_email, $message)
    {
        $this->_request = $_request;
        $this->customer_name = $customer_name;
        $this->customer_email = $customer_email;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emailTemplates.support.adminNotify', ['_request'=>$this->_request,'customer'=>$this->customer_name, 'customer_email'=>$this->customer_email, '_message'=>$this->message])->subject('aaronsdatabase.com New Request - '.date('Y-m-d H:i:s'));
    }
}
