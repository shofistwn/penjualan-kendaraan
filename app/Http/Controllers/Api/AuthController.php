<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  protected $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required',
      'password' => 'required'
    ]);

    if ($validator->fails()) {
      return $this->validationErrorResponse($validator);
    }

    $credentials = $validator->validated();
    if (!$token = auth()->attempt($credentials)) {
      return $this->authenticationErrorResponse();
    }

    return $this->successResponse([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60
    ], 'Login berhasil');
  }

  public function logout()
  {
    auth()->logout();

    return $this->successResponse([], 'Logout berhasil');
  }

  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|min:8'
    ]);

    if ($validator->fails()) {
      return $this->validationErrorResponse($validator);
    }

    $requestData = $validator->validated();
    $requestData['password'] = bcrypt($requestData['password']);

    $userId = $this->authService->register($requestData);
    $user = $this->authService->findById($userId);

    return $this->successResponse($user, 'Pendaftaran berhasil');
  }

  protected function validationErrorResponse($validator)
  {
    return response()->json([
      'success' => false,
      'message' => 'Validasi gagal',
      'errors' => $validator->errors()
    ], 422);
  }

  protected function authenticationErrorResponse()
  {
    return response()->json([
      'success' => false,
      'message' => 'Password atau email salah'
    ], 401);
  }

  protected function successResponse($data, $message)
  {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => $data
    ]);
  }
}