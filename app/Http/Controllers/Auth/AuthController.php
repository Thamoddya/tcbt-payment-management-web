<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nic' => 'required|string',
            'password' => 'required|string|',
        ]);

        $credentials = $request->only('nic', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'nic' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('nic'));
    }
}
