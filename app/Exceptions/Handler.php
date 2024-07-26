<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use App\Http\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $e)
    {
        // Log the exception details
        error_log('exception: ' . $e->getMessage());

        return ApiResponse::error($e->getMessage(), 500) ;
    }

    // this function for handel Exception
    public function render($request, Exception|Throwable $e): Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
    {
        // sql exception
        if ($e instanceof QueryException) {

            $errorCode = $e->getCode();
            error_log('SQL Exception: ' . $e->getMessage());
            return ApiResponse::error($e->getMessage(), 500) ;

        }// end of if

        // not found url
        if ($e instanceof NotFoundHttpException) {

            error_log('SQL Exception: ' . $e->getMessage());
            return ApiResponse::error('not found url',404);

        }// end of if

        error_log($e->getMessage()) ;
        return ApiResponse::error($e->getMessage(),500) ;
        //return parent::render($request, $exception);
    }// end of render

    protected function prepareJsonResponse($request, Throwable $e): JsonResponse
    {
        return ApiResponse::error( $e->getMessage(),$this->isHttpException($e) ? $e->getStatusCode() : 500) ;

    }// end of prepareJsonResponse

}// end of Handler
