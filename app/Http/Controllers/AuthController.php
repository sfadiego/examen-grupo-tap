<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RecuperarPasswordRequest;
use App\Mail\CredencialesRecuperadas;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $usuario = Usuario::where('usuario', $request->usuario)->first();

        // No usamos Auth::attempt() porque autentica contra el guard de sesión
        // 'web', creando una sesión real innecesaria para una API basada en
        // tokens (y que además rompe la revocación del token en logout()).
        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'usuario' => ['Las credenciales no son correctas.'],
            ]);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        // Agregamos las secciones a las que tiene acceso el usuario, para que el frontend pueda mostrar/ocultar links según corresponda.
        $usuario->setAttribute('secciones', $usuario->seccionesPermitidas()->pluck('nombre')->values());

        return response()->json([
            'usuario' => $usuario,
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
        $usuario = $request->user();
        $usuario->setAttribute('secciones', $usuario->seccionesPermitidas()->pluck('nombre')->values());

        return response()->json($usuario);
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
