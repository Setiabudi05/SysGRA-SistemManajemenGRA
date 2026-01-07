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
            // 'role' akan otomatis terisi 'user' dari default database
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
        // 1. Jalankan validator
        $this->validator($request->all())->validate();

        // 2. Buat user baru
        event(new Registered($user = $this->create($request->all())));

        // 3. (BAGIAN PENTING)
        // Baris ini adalah yang otomatis me-login-kan user.
        // Kita beri komentar agar tidak dijalankan.
        // $this->guard()->login($user); 

        // 4. Panggil method 'registered' (jika ada)
        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        // 5. Arahkan kembali ke halaman LOGIN dengan pesan sukses
        // Pastikan Anda punya 'flash message' di file blade login Anda untuk 'status'
        return redirect(route('login'))->with('status', 'Registrasi berhasil! Silakan login dengan akun baru Anda.');
    }
}