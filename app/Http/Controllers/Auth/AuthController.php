<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account has been '.$user->status.'.']);
        }

        $request->session()->regenerate();

        if ($user->hasRole('super_admin')) {
            return redirect('/super-admin');
        }
        if ($user->hasAnyRole(['admin', 'support_staff', 'finance_officer', 'content_moderator'])) {
            return redirect('/admin');
        }
        if ($user->hasRole('provider')) {
            return redirect()->route('provider.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'phone'      => 'nullable|string|max:20',
            'student_id' => 'nullable|string|max:50|unique:users',
            'password'   => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'student_id' => $data['student_id'] ?? null,
            'password'   => Hash::make($data['password']),
            'status'     => 'active',
        ]);
        $user->assignRole('student');

        Auth::login($user);
        return redirect()->route('student.dashboard')->with('success', 'Welcome to CampusHub!');
    }

    public function showProviderRegister()
    {
        return view('auth.register-provider');
    }

    public function registerProvider(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'phone'         => 'required|string|max:20',
            'password'      => 'required|min:8|confirmed',
            'business_name' => 'required|string|max:255',
            'bio'           => 'nullable|string',
            'location'      => 'nullable|string|max:255',
            'whatsapp'      => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'status'   => 'active',
        ]);
        $user->assignRole('provider');

        Provider::create([
            'user_id'       => $user->id,
            'business_name' => $data['business_name'],
            'bio'           => $data['bio'] ?? null,
            'location'      => $data['location'] ?? null,
            'whatsapp'      => $data['whatsapp'] ?? null,
            'status'        => 'pending',
        ]);

        Auth::login($user);
        return redirect()->route('provider.dashboard')
            ->with('info', 'Your provider application is under review. You can start accepting jobs once approved.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
