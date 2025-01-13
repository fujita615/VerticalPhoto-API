<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFromAdminMail;
use App\Mail\ContactFromUserMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    //
    public function sendMail(ContactFormRequest $request)
    {
        $from_data = $request->validated();
        $email_admin = env('MAIL_FROM_ADDRESS');
        $email_user = $from_data['email'];

        //管理者宛
        Mail::to($email_admin)->send(new ContactFromAdminMail($from_data));
        //お客様宛
        Mail::to($email_user)->send(new ContactFromUserMail($from_data));
        return response(200);
    }
}
