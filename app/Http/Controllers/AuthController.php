<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw new AuthorizationException('auth.failed');
        }

        $token = $user->createToken(
            $request->userAgent(),
            ['*'],
            Carbon::now()->addMinutes(config('sanctum.expiration'))
        );

        return response()->json([
            'message' => 'Login realizado.',
            'data' => [
                'token' => $token->plainTextToken,
            ],
        ], Response::HTTP_OK);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        $tokens = $user->tokens()->where('token', '!=', $user->currentAccessToken()->token);

        return response()->json([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'devices' => [
                    'current' => $currentToken->name,
                    'others' => $tokens->pluck('name'),
                ],
            ],
        ], Response::HTTP_OK);
    }

    public function user(): JsonResponse
    {
        $user = User::query()
            ->select(['id', 'name', 'email', 'email_verified_at'])
            ->findOrFail(auth()->id());

        return response()->json([
            'data' => $user,
        ], Response::HTTP_OK);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Dispositivo desconectado.',
        ], Response::HTTP_OK);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Todos os dispositivos foram desconectados.',
        ], Response::HTTP_OK);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw new AuthorizationException($status);
        }

        return response()->json([
            'message' => (__($status)),
        ], Response::HTTP_OK);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email:rfc,dns'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new AuthorizationException($status);
        }

        return response()->json([
            'message' => (__($status)),
        ], Response::HTTP_OK);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            throw new AuthorizationException('Esse email já foi verificado.');
        }

        if (! hash_equals((string) $user->getKey(), (string) $request->route('id'))) {
            throw new AuthorizationException('ID de usuário inválido.');
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            throw new AuthorizationException('Hash de verificação de e-mail inválido.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        $user->password = Hash::make($request->get('password'));
        $user->save();

        $user->tokens()->delete();
        $token = $user->createToken(
            $request->userAgent(),
            ['*'],
            Carbon::now()->addMinutes(config('sanctum.expiration'))
        );

        return response()->json([
            'message' => 'Email verificado com sucesso.',
            'data' => [
                'token' => $token->plainTextToken,
            ],
        ], Response::HTTP_OK);
    }

    public function resendEmailVerification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            throw new AuthorizationException('Esse email já foi verificado.');
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Link de verificação de email reenviado.',
        ], Response::HTTP_OK);
    }
}
