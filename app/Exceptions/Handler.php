<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Exceptions\BaseException;
use App\Exceptions\UserAlreadyExistException;
use Illuminate\Support\Facades\Log;
use Throwable;
use ParseError;

class Handler extends ExceptionHandler
{
    protected $levels = [
        BaseException::class => 'warning',
    ];

    protected $dontReport = [
        ValidationException::class,
        BaseException::class,
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    // public function report(Throwable $exception)
    // {
    //     if ($exception instanceof BaseException) {
    //         Log::channel('daily')->warning('BaseValidationException detected', [
    //             'message' => $exception->getMessage(),
    //             'errors' => $exception->getErrors(),
    //         ]);
    //     }
    //     parent::report($exception);
    // }

    public function render($request, Throwable $exception)
    {

        $request->headers->set('Accept', 'application/json');

        // if (app()->environment('local')) {
        //     return parent::render($request, $exception);
        // }

        // TODO: エラーが起きたらログをここで取るようにしたい
        //
        if($exception instanceof UserAlreadyExistException){
            return response()->json([
                'status' => 409,
                'message' => 'ユーザーはすでに存在しています。'. $exception->getMessage(),
            ], 409);

        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 404,
                'message' => 'リクエストされたリソースが見つかりません。'. $exception->getMessage(),
            ], 404);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 405,
                'message' => 'このHTTPメソッドは許可されていません。',
            ], 405);
        }
        
        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 422,
                'message' => '入力データが無効です。'. $exception->getMessage(),
                'errors' => $exception->errors(),
            ], 422);
        }
        if ($exception instanceof BaseException) {
            return response()->json([
                'status' => 422,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
            ], 422);
        }
        if ($exception instanceof ParseError) {
            return response()->json([
                'status' => 500,
                'message' => 'サーバー内部エラーが発生しました（構文エラーの可能性）。',
            ], 500);
        }
        return response()->json([
            'status' => 500,
            'message' => '処理中に致命的なエラーが発生しました。',
            'error' => $exception->getMessage(),
        ], 500);
    }
}
