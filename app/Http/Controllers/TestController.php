<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{

    public function mailCheck()
    {
        try {
            $to = 'yourmail@techmavesoftware.com'; // Replace with the recipient's email address
            $subject = 'Test Subject DDDDDDDD';
            $body = '<p>This is a test email body.</p>';
            $sendmail = Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });
            if($sendmail){
                return response()->json(['status' => 'Success'], 200);
            }else{
                return response()->json(['status' => 'Failed'], 400);
            }
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function clearData()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('clear-compiled');
            Artisan::call('config:clear');
            return response()->json(['status' => 'Success'], 200);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function storageLink(){
        try {
            Artisan::call('storage:link');
            return response()->json(['status' => 'Success'], 200);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
