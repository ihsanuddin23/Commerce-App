<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ],
            [
                'email.required' => 'email harus diisi terlebih dahulu',
                'password.required' => 'password harus diisi terlebih dahulu'
            ]
        );
        if ($validator->passes()) {
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' =>
            $request->password], $request->get('remember'))) {

                $admin = Auth::guard('admin')->user();

                if ($admin->role == 2) {
                    return redirect()->route('admin.dashboard');
                } else {

                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'Anda tidak berwenang untu mengakses panel admin.
                    ');
                }

                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('admin.login')->with('error', 'Email atau Kata Sandi Salah');
            }
        } else {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }
}
