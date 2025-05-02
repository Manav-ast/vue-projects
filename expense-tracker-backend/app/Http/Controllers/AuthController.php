<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return ResponseHelper::success([
      'token' => $token,
      'user' => $user
    ], 'User registered successfully', 201);
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
      ]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return ResponseHelper::success([
      'token' => $token,
      'user' => $user
    ], 'Login successful');
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return ResponseHelper::success([], 'Logged out successfully');
  }

  public function user(Request $request)
  {
    return ResponseHelper::success($request->user());
  }
}
