<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Customer Registration API.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'loyalty_points' => 0,
            'is_active' => true,
        ]);

        $token = $customer->createToken('android-app-customer')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Customer registered successfully',
            'token' => $token,
            'customer' => $this->formatCustomer($customer),
        ], 201);
    }

    /**
     * Customer Login API (via Phone or Email).
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // phone or email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $login = $request->login;
        $customer = Customer::where('phone', $login)
            ->orWhere('email', $login)
            ->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone/email or password credentials.',
            ], 401);
        }

        if (! $customer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.',
            ], 403);
        }

        $token = $customer->createToken('android-app-customer')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'token' => $token,
            'customer' => $this->formatCustomer($customer),
        ]);
    }

    /**
     * Customer Profile API.
     */
    public function profile(Request $request): JsonResponse
    {
        $customer = $request->user();

        return response()->json([
            'success' => true,
            'customer' => $this->formatCustomer($customer),
        ]);
    }

    /**
     * Update Customer Profile API.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $customer = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,'.$customer->id,
            'phone' => 'required|string|max:30|unique:customers,phone,'.$customer->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'customer' => $this->formatCustomer($customer->fresh()),
        ]);
    }

    /**
     * Register FCM Device Token for Push Notifications.
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = $request->user();
        $customer->update([
            'notes' => json_encode(array_merge(
                is_array(json_decode($customer->notes, true)) ? json_decode($customer->notes, true) : [],
                ['fcm_token' => $request->fcm_token, 'fcm_updated_at' => now()->toIso8601String()]
            )),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM Device Token registered successfully',
        ]);
    }

    /**
     * Logout and Revoke Active Token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Format Customer DTO.
     */
    private function formatCustomer(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'avatar' => $customer->avatar ? asset('storage/'.$customer->avatar) : null,
            'address' => $customer->address,
            'city' => $customer->city,
            'postal_code' => $customer->postal_code,
            'loyalty_points' => (int) $customer->loyalty_points,
            'total_spent' => (float) $customer->total_spent,
            'total_orders_count' => (int) $customer->total_orders_count,
            'delivered_orders_count' => (int) $customer->delivered_orders_count,
            'delivery_success_rate' => (float) $customer->delivery_success_rate,
            'is_active' => (bool) $customer->is_active,
            'created_at' => $customer->created_at?->toIso8601String(),
        ];
    }
}
