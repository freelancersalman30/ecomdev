<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    /**
     * Show Customer Login Page
     */
    public function showLoginForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('customer.auth.login');
    }

    /**
     * Handle Customer Login Request
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Determine if login identifier is phone or email
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $customer = Customer::where($fieldType, $login)->first();

        // If customer exists and password is set, verify hash
        if ($customer && $customer->password && Hash::check($password, $customer->password)) {
            if (! $customer->is_active) {
                return redirect()->back()->withInput()->with('error', 'Your customer account is currently suspended. Please contact support.');
            }

            Auth::guard('customer')->login($customer, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'))->with('success', "Welcome back, {$customer->name}!");
        }

        // Alternative quick fallback for seed/existing customer without password
        if ($customer && ! $customer->password && $password === 'password') {
            $customer->password = Hash::make('password');
            $customer->save();

            Auth::guard('customer')->login($customer, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'))->with('success', "Welcome back, {$customer->name}!");
        }

        return redirect()->back()->withInput($request->only('login', 'remember'))->with('error', 'Invalid login credentials. Please check your phone/email and password.');
    }

    /**
     * Show Customer Register Page
     */
    public function showRegisterForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('customer.auth.register');
    }

    /**
     * Handle Customer Registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'password' => 'required|string|min:6|confirmed',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'city' => $request->city ?? 'Dhaka',
            'address' => $request->address,
            'loyalty_points' => 50, // Welcome gift points
            'delivery_success_rate' => 100.00,
            'is_active' => true,
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('success', 'Account created successfully! You received 50 bonus loyalty points.');
    }

    /**
     * Handle Customer Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out safely.');
    }
}
