<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RecuperarPasswordRequest;
use App\Mail\CredencialesRecuperadas;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login, logout y datos del usuario autenticado (Sanctum).
 */
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'usuario' => ['Las credenciales no son correctas.'],
            ]);
        }

        /** @var Usuario $user */
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'usuario' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function recuperarPassword(RecuperarPasswordRequest $request)
    {
        $usuario = Usuario::where('usuario', $request->usuario)->firstOrFail();

        $nuevaPassword = Str::random(12);
        $usuario->update(['password' => $nuevaPassword]);

        Mail::to($usuario->usuario)->send(new CredencialesRecuperadas($usuario, $nuevaPassword));

        return response()->json([
            'message' => 'Se enviaron las nuevas credenciales al correo registrado.',
        ]);
    }
}
