<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;
use PDOException; // Import this to catch database connection errors

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle 404 Not Found
        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        // Handle Unique Constraint Violation (Duplicate Data)
        if ($exception instanceof QueryException) {
            if ($exception->errorInfo[1] == 1062) { // MySQL error code for duplicate entry
                return response()->view('errors.unique', [], 409); // 409 Conflict status
            }
        }
        // Handle Validation Errors
        if ($exception instanceof ValidationException) {
            return response()->view('errors.validation', [], 422);
        }

        // Handle 429 (Too Many Requests - If request takes too long)
        if ($exception instanceof ThrottleRequestsException) {
            return response()->view('errors.throttle', [], 429);
        }

        // Handle Database Connection Errors (SQLSTATE[HY000] [2002])
        if ($exception instanceof PDOException) {
            return response()->view('errors.database', [], 500);
        }

        // Handle All Other Errors
        return response()->view('errors.500', [], 500);
    }
}
