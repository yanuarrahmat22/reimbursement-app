<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }

  public function login(Request $request)
  {
    $request->validate([
      'nip' => 'required|string',
      'password' => 'required|string',
    ]);

    $nip = $request->nip;
    $password = $request->password;

    // Cari pengguna berdasarkan NIP
    $user = User::where('nip', $nip)->first();

    if (!$user) {
      // NIP tidak ditemukan
      return back()->withErrors(['nip' => 'NIP tidak ditemukan.'])->withInput();
    }

    if (Auth::attempt(['nip' => $nip, 'password' => $password])) {
      // Autentikasi berhasil
      return redirect()->intended('/home'); // Ganti '/home' dengan halaman tujuan setelah login
    } else {
      // Password salah
      return back()->withErrors(['password' => 'Password salah.'])->withInput();
    }
  }
}
