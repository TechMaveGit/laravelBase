<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
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

    /**
     * Register the exception handling callbacks for the application.
     */
    // public function register()
    // {
    //     $this->reportable(function (Throwable $e) {
    //         // ...
    //     });

    //     $this->renderable(function (Throwable $e, $request) {
    //         if ($request->is('web/*')) {
    //             return response()->view('errors.custom-error', [], 500);
    //         }else if ($request->expectsJson()) {
    //             return response()->json(['message' => 'Something went wrong'], 500);
    //         }
    //     });
    // }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Custom handling for API requests
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        // Default handling for web requests
        return parent::render($request, $exception);
    }

    /**
     * Handle exceptions for API requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleApiException($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        } elseif ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }elseif ($exception instanceof QueryException) {
            return response()->json(['error' => 'Database query error'], 500);
        }elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            return response()->json(['error' => "HTTP error with status code $statusCode"], $statusCode);
        }

        return $this->customApiResponse($exception);
    }

    /**
     * Create a JSON response for API errors.
     *
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function customApiResponse($exception)
    {
        $statusCode = $this->getStatusCode($exception);

        $response = [
            'success' => false,
            'message' => $this->getErrorMessage($exception, $statusCode),
            'status'  => $statusCode,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Get the HTTP status code from the exception.
     *
     * @param  \Throwable  $exception
     * @return int
     */
    protected function getStatusCode($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return 500; // Default status code for unhandled exceptions
    }

    /**
     * Get the error message from the exception.
     *
     * @param  \Throwable  $exception
     * @param  int  $statusCode
     * @return string
     */
    protected function getErrorMessage($exception, $statusCode)
    {
        switch ($statusCode) {
            case 401:
                return 'Unauthorized';
            case 403:
                return 'Forbidden';
            case 404:
                return 'Not Found';
            case 419:
                return 'Page Expired';
            default:
                return ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
        }
    }
}
