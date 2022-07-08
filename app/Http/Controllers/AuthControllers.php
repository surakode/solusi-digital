<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthControllers extends Controller
{
        //
    public function __construct()
    {
        $this->middleware('auth')->except('login', 'forgotRequest', 'forgot', 'authenticate');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $var = ['nav' => 'login', 'subNav' => 'login', 'title' => 'Portal Apps'];

        if (Auth::check()) {
            return redirect('dashboard');
        }
        return view('auth.login', $var);
    }
    public function authenticate(Request $request)
    {
        // Validation
        $vMessage = config('global.vMessage'); //get global validation messages
        $validator = Validator::make($request->only('email', 'password'), [
            'email' => 'bail|required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Wajib Memasukkan Email',
            'email.email' => 'Format email salah',
            'password.required' => 'Wajib Memasukkan Kata Sandi'
        ]);
        $valid = Helper::validationFail($validator);
        if (!is_null($valid)) {
            return redirect('login')->with('status', $valid);
        }


        $email = $request->email;
        $password = $request->password;
        $remember = $request->remember;
        $checkAuth = Helper::Auth($email, $password, 'email');
        // dd($checkAuth);
        $result = (object) $checkAuth;
        if ($result->status == 'success') {
            if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
                return redirect('dashboard')
                    ->with('status', $result)
                    ->with('logging', true);
            }
        }
        return redirect('login')->with('status', $result);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('dashboard'); // mengatasi preventback dan route dashboard terdapat midleware auth
    }

    public function forgot()
    {
        return view('auth.forgot');
    }


    public function forgotRequest(Request $request)
    {
        return view('auth.forgot');
    }


    public function recover($hash)
    {
        return view('auth.recover');
    }


    public function recoverRequest(Request $request)
    {
        return view('auth.forgot');
    }
}
