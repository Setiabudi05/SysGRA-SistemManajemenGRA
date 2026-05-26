<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// --- KITA TAMBAHKAN 2 BARIS INI ---
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * [INI YANG KITA TAMBAHKAN]
     * Arahkan ke Landing Page ('/') setelah berhasil register,
     * sama seperti di LoginController.
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'pelanggan', // Tambahkan ini agar otomatis jadi pelanggan
        ]);
    }

    // --- KITA TAMBAHKAN FUNGSI BARU DI BAWAH INI ---

    /**
     * Handle a registration request for the application.
     * FUNGSI INI DI-OVERRIDE UNTUK MENGUBAH PERILAKU DEFAULT
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // 1. Validasi input
        $this->validator($request->all())->validate();

        // 2. Buat user baru di database
        $user = $this->create($request->all());

        // 3. TRIGGER PENTING: Laravel mengirim email verifikasi di baris ini
        event(new Registered($user));

        // 4. Redirect ke login dengan pesan instruksi
        return redirect()->route('login')->with('status', 'Registrasi berhasil! Silakan cek Inbox atau Spam Gmail Anda untuk verifikasi sebelum login.');
    }
}
