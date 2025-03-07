<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;

class CustomAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username_or_email' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            'password' => $request->password,
            filter_var($request->username_or_email, FILTER_VALIDATE_EMAIL) 
                ? 'email' 
                : 'username' => $request->username_or_email
        ];

        Log::debug('Login attempt with credentials:', $credentials);

        if (Auth::attempt($credentials)) {
            Log::info('User authenticated: ', [Auth::user()]);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        Log::warning('Failed login attempt for: '.$request->username_or_email);
        return back()->withErrors([
            'username_or_email' => 'Invalid credentials',
        ])->withInput();
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Add request logging
        Log::debug('Registration Form Data:', $request->all());

        $validated = $request->validate([
            'first_name' => 'required|string|max:125',
            'last_name' => 'required|string|max:125',
            'academic_level' => 'required|string|max:255',
            'gender' => 'required|string',
            'date_of_birth' => 'required|date',
            'country' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'username' => 'required|string|unique:users|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the name field
        $fullName = trim($request->first_name . ' ' . $request->last_name);
        $fullName = substr($fullName, 0, 255);
        
        // Log the generated name
        Log::debug('Generated Name:', ['name' => $fullName]);

        // Create user with logging
        try {
            $user = User::create([
                'name' => $fullName,                
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'academic_level' => $request->academic_level,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'country' => $request->country,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);
            
            Log::info('User created successfully:', $user->toArray());
        } catch (\Exception $e) {
            Log::error('Registration Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to maintain normal error flow
        }
        

        return redirect('/login')->with('success', 'Account created!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
