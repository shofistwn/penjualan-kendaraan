<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  protected MongoModel $userModel;

  public function __construct()
  {
    $this->userModel = new MongoModel('users');
  }

  public function login(Request $request)
  {
    // Validasi input
    $validator = \Validator::make($request->all(), [
      'email' => 'required',
      'password' => 'required'
    ]);

    if ($validator->fails()) {
      // Jika validasi gagal, kembalikan respons dengan pesan error validasi
      return response()->json([
        'success' => false,
        'message' => 'Validasi gagal',
        'errors' => $validator->errors()
      ], 422);
    }

    $credentials = $validator->validated();
    if (!$token = auth()->attempt($credentials)) {
      // Jika autentikasi gagal, kembalikan respons dengan pesan error autentikasi
      return response()->json([
        'success' => false,
        'message' => 'Password atau email salah'
      ], 401);
    }

    // Jika autentikasi berhasil, kembalikan respons dengan token akses
    return response()->json([
      'success' => true,
      'message' => 'Login berhasil',
      'data' => [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60
      ]
    ]);
  }

  public function logout()
  {
    // Logout pengguna
    auth()->logout();

    // Kembalikan respons berhasil logout
    return response()->json([
      'success' => true,
      'message' => 'Logout berhasil'
    ]);
  }

  public function register(Request $request)
  {
    // Validasi input
    $validator = \Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|min:8'
    ]);

    if ($validator->fails()) {
      // Jika validasi gagal, kembalikan respons dengan pesan error validasi
      return response()->json($validator->errors(), 422);
    }

    $requestData = $validator->validated();
    $requestData['password'] = bcrypt($requestData['password']);

    // Simpan pengguna baru ke dalam database
    $user = $this->userModel->save($requestData);

    if ($user) {
      // Jika penyimpanan sukses, kembalikan respons berhasil
      return response()->json([
        'message' => 'Pendaftaran berhasil'
      ]);
    }
  }
}