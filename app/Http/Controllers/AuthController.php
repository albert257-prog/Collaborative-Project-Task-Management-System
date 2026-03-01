<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Handles user authentication lifecycle including login, registration, and logout.
 */
class AuthController extends Controller
{
    /**
     * Display the login form view.
     */
    public function showLogin() 
    { 
        return view('auth.login'); 
    }

    /**
     * Handle an authentication attempt.
     **/
    public function login(Request $request) 
    {
        // 1. Validate input with strict email checks (RFC format and DNS record existence)
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns'], 
            'password' => ['required'],
        ]);

        // 2. Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Success: Regenerate session ID to prevent 'Session Fixation' attacks
            $request->session()->regenerate();
            
            return redirect()->intended('dashboard')
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        // Failure: Return to login with error and keep the email input for convenience
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Display the registration form view.
     */
    public function showRegister() 
    { 
        return view('auth.register'); 
    }

    /**
     * Handle the creation of a new user account.
     **/
    public function register(Request $request) 
    {
        // 1. Validate registration data against security best practices
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed', // Requires password_confirmation field match
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(), // Security check against known data leaks
            ],
        ]);

        // 2. Persist the user to the database with a hashed password
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // 3. Automatically log the user in after registration
        Auth::login($user);
        
        return redirect('/dashboard')->with('success', 'Account created successfully!');
    }

    /**
     * Log the user out of the application and clear session data.
     */
    public function logout(Request $request) 
    {
        // 1. Terminate the Auth session
        Auth::logout();

        // 2. Invalidate the session data and regenerate the CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('info', 'You have been logged out.');
    }
}