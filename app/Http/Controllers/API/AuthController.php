<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman/form login untuk web
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Registrasi User Baru (API)
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = DB::transaction(function () use ($request) {
                return User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'peminjam',
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat,
                ]);
            });

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Registrasi berhasil.',
                'data' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proses Login (Mendukung Web Session & API)
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Jika Request Datang dari Web Form HTML (Bukan JSON/AJAX Header)
        if (!$request->wantsJson()) {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate(); // Mencegah CSRF fixation attack

                $user = Auth::user();

                // Redirect otomatis berdasarkan Role User
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'petugas') {
                    return redirect()->route('petugas.peminjaman.index');
                }

                // DIUBAH: Mengarahkan ke route peminjam.dashboard (sesuai web.php)
                return redirect()->route('peminjam.dashboard');
            }

            return back()->withErrors([
                'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
            ])->onlyInput('email');
        }

        // 3. Jika Request Datang dari API (Postman / Frontend Mobile / JS)
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Ambil Data Profil User yang Sedang Login
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'message' => 'Data profil berhasil diambil.',
            'data' => new UserResource(auth()->user()),
        ]);
    }

    /**
     * Logout (Mendukung Web Session & API Token)
     */
    public function logout(Request $request)
    {
        // Jika logout dari Web Blade
        if (!$request->wantsJson()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        // Jika logout dari API (Hapus Token)
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logout berhasil. Token telah dihapus.',
        ]);
    }
}