<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $validated = Validator::make($request->all(), [
      'email' => 'required',
      'password' => 'required'
    ]);

    if ($validated->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validated->errors()
      ], 422);
    }

    $credentials = $validated->validated();
    if (!$token = auth()->attempt($credentials)) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
      ], 401);
    }

    return response()->json([
      'success' => true,
      'message' => 'Login successful',
      'data' => [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60
      ]
    ]);
  }

  public function logout()
  {
    auth()->logout();

    return response()->json([
      'success' => true,
      'message' => 'Logout successful'
    ]);
  }
}