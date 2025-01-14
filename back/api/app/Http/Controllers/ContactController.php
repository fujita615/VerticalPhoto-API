<?php

namespace App\Http\Controllers;

use App\Mail\ContactFromAdminMail;
use App\Mail\ContactFromUserMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class ContactController extends Controller
{
    //お問い合わせフォームへの自動返信

    public function sendMail(Request $request)
    {
        $validated = $request->validate([

            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'text' => ['required', 'string', 'max:500']
        ]);



        $from_data = $validated;
        $email_admin = env('MAIL_FROM_ADDRESS');
        $email_user = $from_data['email'];

        //管理者宛
        Mail::to($email_admin)->send(new ContactFromAdminMail($from_data));
        //お客様宛
        Mail::to($email_user)->send(new ContactFromUserMail($from_data));
        return response(200);
    }
}
