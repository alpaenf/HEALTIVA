<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Kader → kader dashboard
            return redirect()->route('kader.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function redirectToGoogle()
    {
        // Encode flow type in state so it survives the Google roundtrip
        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => 'kader_' . Str::random(40)])
            ->redirect();
    }

    public function redirectToGoogleTamu()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => 'tamu_' . Str::random(40)])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Determine flow from the state parameter Google echoes back
            $state = $request->input('state', '');
            $flow  = str_starts_with($state, 'tamu_') ? 'tamu' : 'kader';

            // ── Tamu / Pasien flow ──────────────────────────────────────
            if ($flow === 'tamu') {
                // Regenerate session ID so a fresh session is committed to DB
                // and the Set-Cookie header is included in the redirect response.
                // (stateless OAuth does not carry session across the Google roundtrip)
                $request->session()->regenerate();
                $request->session()->put('tamu_google_verified', true);
                $request->session()->put('tamu_google_name', $googleUser->getName());
                $request->session()->save(); // force-write before redirect
                return redirect()->route('patient.lookup');
            }

            // ── Kader / Admin flow ──────────────────────────────────────
            $user = User::where('email', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            if (!$user || !in_array($user->role, ['admin', 'kader'])) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Google Anda belum terdaftar. Hubungi admin untuk mendaftarkan email Anda.',
                ]);
            }

            // Link google_id on first Google login
            $user->update([
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);

            Auth::login($user, true);

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('kader.dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Login Google gagal. Silakan coba lagi.']);
        }
    }
}
