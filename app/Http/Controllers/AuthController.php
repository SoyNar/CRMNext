<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @tags
 * UserController
**/
class AuthController extends Controller
{

    public function  showLogin()
    {
        return view('auth.login');
    }
    public function  showRegister()
    {
        return view('auth.register');
    }

   /**
    * Registrar usuario
   **/
    public function register(RegisterRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $request->session()->regenerate();
        return redirect('/login');
    }

    /**
     * Autenticar usuario
    */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas'
            ]);
        }
        $request->session()->regenerate();
        return redirect('/clients');
    }

    /**
     * Cerrar sesion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
