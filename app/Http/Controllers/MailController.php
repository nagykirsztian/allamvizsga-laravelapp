<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;
use App\Http\Controllers\SensorController;
use App\Models\User;

class MailController extends Controller
{


    public function sendEmailWithData($data)
    {
        // Send email to all users with the provided data
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new CustomEmail($data));
        }

        \Log::info('Emails sent successfully to all users.');
    }
}
