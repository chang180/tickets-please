<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api/v1')
                ->middleware('api')
                ->name('api.v1.')
                ->group(base_path('routes/api_v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function ($request, Throwable $exception) {
            $className = get_class($exception);

            $handlers = [
                ModelNotFoundException::class => 'handleModelNotFoundException',
                ValidationException::class => 'handleValidationException',
                NotFoundHttpException::class => 'handleNotFoundHttpException',
                AuthenticationException::class => 'handleAuthenticationException',

            ];

            if (array_key_exists($className, $handlers)) {
                return $handlers[$className]($exception);
            }
        });
    })->create();

function handleAuthenticationException(AuthenticationException $exception)
{
    return new Response([
        'errors' => [
            'status' => 401,
            'message' => 'Unauthenticated',
            'source' => '', 
        ]
    ]);
}



function handleNotFoundHttpException(NotFoundHttpException $exception)
{
    $className = get_class($exception);
    $index = strrpos($className, '\\');
    return new Response([
        'errors' => [
            'type' => substr($className, $index + 1),
            'status' => 0,
            'message' => $exception->getMessage(),
            'source' => 'Line: ' . $exception->getLine() . ' in ' . $exception->getFile(),
        ]
    ]);
}



function handleValidationException(ValidationException $exception)
{
    foreach ($exception->errors() as $key => $value)
        foreach ($value as $message) {
            $errors[] = [
                'status' => 422,
                'message' => $message,
                'source' => $key
            ];
        }
    return new Response([
        'errors' => $errors
    ]);
}

function handleModelNotFoundException(ModelNotFoundException $exception)
{
    return new Response([
        'errors' => [
            'status' => 404,
            'message' => 'Resource not found',
            'source' => $exception->getModel()
        ]
    ]);
}
