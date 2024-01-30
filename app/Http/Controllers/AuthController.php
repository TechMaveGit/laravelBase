<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        try {
            if ($request->isMethod('POST')) {
                $this->validate($request, [
                    'email' => 'required|email|exists:users,email',
                    'password' => 'required'
                ]);

                $remember_me = $request->has('remember_me') ? true : false;

                if (Auth::guard('web')->attempt($request->only(['email', 'password']), $remember_me)) {
                    return redirect()->route('dashboard');
                }
            }
            return view('login');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('Error : ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function regester(Request $request)
    {
        try {
            if ($request->isMethod('POST')) {
                $this->validate($request, [
                    'email' => "required|email|unique:sellers,email",
                    'password' => 'required|min:6',
                ]);
                $credentials = $request->only('email', 'password');

                $user = User::create(['email' => $request->email, 'password' => Hash::make($request->password)]);
                if ($user) {
                    if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
                        return redirect()->route('dashboard')->with('success', 'Account Created Successfully !');
                    } else {
                        return redirect()->route('login')->with('success', 'Account Created Successfully !');
                    }
                }
                return redirect()->back()->with('error', 'Somthing Went Wrong !');
            }
            return view('register');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('Error : ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function forgotpwd(Request $request)
    {
        try {
            if ($request->isMethod('POST')) {
                $this->validate($request, [
                    'email' => 'required|email|exists:users,email'
                ]);

                $sendmail = Mail::to($request->email)->send(new ForgotPasswordMail('Forgot Password', ['code' => '5655']));
                if ($sendmail) {
                    User::where('email', $request->email)->update(['verify_code' => '5655']);
                    return  redirect()->back()->with('success', 'Forgot Password Mail Send Successfully.');
                }
            }
            return view('forgot-password');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('Error : ' . $e->getMessage());
        }
    }
}
