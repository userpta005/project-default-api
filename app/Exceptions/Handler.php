<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected $dontReport = [];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $th) {
            $message = 'Erro interno do servidor.';
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $errors = [];

            if (App::environment('local')) {
                ds()->clear();
                ds($th);
            }

            if ($th instanceof AuthenticationException) {
                $message = 'Não autenticado.';
                $status = Response::HTTP_UNAUTHORIZED;
            }

            if ($th instanceof ValidationException) {
                $message = __($th->getMessage());
                $status = $th->status;
                $errors = $th->errors();
            }

            if ($th instanceof HttpException) {
                $message = __($th->getMessage());
                $status = $th->getStatusCode();
            }

            return response()->json([
                'message' => $message,
                'errors' => $errors,
            ], $status);
        });
    }
}
