<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'guest_token' => ['nullable', 'string', 'max:64'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create user stats record
        UserStat::create([
            'user_id' => $user->id,
            'username' => $user->username,
        ]);

        // Migrate guest sessions if provided
        if (!empty($validated['guest_token'])) {
            \App\Models\ExamSession::where('guest_token', $validated['guest_token'])
                ->update(['user_id' => $user->id, 'guest_token' => null]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->only('id', 'username', 'email', 'avatar_url'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'guest_token' => ['nullable', 'string', 'max:64'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Migrate guest sessions if provided
        if (!empty($validated['guest_token'])) {
            \App\Models\ExamSession::where('guest_token', $validated['guest_token'])
                ->update(['user_id' => $user->id, 'guest_token' => null]);
        }

        $user->update(['last_activity_at' => now()]);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->only('id', 'username', 'email', 'avatar_url'),
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->only('id', 'username', 'email', 'avatar_url'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}