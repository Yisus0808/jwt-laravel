<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request){
        try {
            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6|confirmed'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'accessToken'=> $token,
                'user' => $user,
                'expireIn' => 3600
            ],201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    public function login(LoginRequest $request) {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Credenciales inválidas'
                ], 400);
            }

            $user = auth()->user();

            return response()->json([
                'accessToken' => $token,
                'user' => $user,
                'expireIn' => 3600
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'error' => 'No se pudo crear el token',
                'error2' => $e
            ], 500);
        }

    }

    public function me()
    {
        try {
            $user = auth()->user();
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo obtener los datos del usuario'], 500);
        }
    }

    // Refrescar el token
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
            return response()->json([
                'token' => $token
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo refrescar el token'], 500);
        }
    }

    // Cerrar sesión y revocar el token
    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'Sesión cerrada con éxito']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo cerrar la sesión'], 500);
        }
    }
}
