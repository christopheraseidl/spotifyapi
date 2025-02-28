<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\User;
use App\Traits\GeneratesApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use GeneratesApiResponses;

    /**
     * Registrar un nuevo usuario.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return $this->success(
            'Registered.',
            [
                'token' => $user->createToken(
                    'api-token-'.$user->email,
                    ['api:query'],
                    now()->addWeek()
                )->plainTextToken,
            ],
            201
        );
    }

    /**
     * Generar un nuevo token de autenticación para un usuario registrado.
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated();

        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->failure('Invalid credentials.', [], 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->success(
            'Authenticated.',
            [
                'token' => $user->createToken(
                    'api-token-'.$user->email,
                    ['api:query'],
                    now()->addWeek()
                )->plainTextToken,
            ]
        );
    }

    /**
     * Eliminar el token actual del usuario.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->success('Logged out.');
    }
}
