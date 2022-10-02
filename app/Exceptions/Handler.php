<?php

namespace App\Exceptions;

use Exception;
use App\Utils\AppConfig;
use App\Utils\TransactionHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (QueryException $e) {
            return new JsonResponse([
                'message' => 'Cannot create new record'
            ]);
        });
    }

    /**
     * Override parent render function
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Exception|Throwable $exception)
    {
        $isErrorShown = config('app.env') == 'production' && config('app.debug') == false;
        TransactionHelper::getInstance()->stop(); // prevent loss transaction
        logError($exception);

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                '_status' => AppConfig::HTTP_RESPONSE_STATUS_NOT_FOUND,
                '_success' => false,
                '_messages' => '404 - ' . trans('error_http.404'),
                '_data' => null
            ], AppConfig::HTTP_RESPONSE_STATUS_NOT_FOUND);
        }

        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                '_status' => AppConfig::HTTP_RESPONSE_STATUS_NOT_AUTHENTICATED,
                '_success' => false,
                '_messages' => $exception->getMessage(),
                '_code' => AppConfig::HTTP_RESPONSE_STATUS_NOT_AUTHENTICATED,
                '_data' => $isErrorShown ? [
                    'exception' => [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                    ],
                ] : null
            ], AppConfig::HTTP_RESPONSE_STATUS_NOT_AUTHENTICATED);
        }

        // show detail error in json format
        if (config('app.env') == 'production' && config('app.debug') == false) {
            return $this->response(
                true,
                [],
                __('error.level_3_failed'),
                AppConfig::HTTP_RESPONSE_STATUS_ERROR
            );
        }

        $response = parent::render($request, $exception);

        if ($exception instanceof InvalidSignatureException || config('app.debug') == false) {
            $data = [
                'exception' => [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ],
            ];

            return response()->json([
                '_status' => AppConfig::HTTP_RESPONSE_STATUS_ERROR,
                '_success' => false,
                '_messages' => [$response instanceof JsonResponse ? $this->renderMessage($response) : $exception->getMessage()],
                '_data' => $data
            ], AppConfig::HTTP_RESPONSE_STATUS_ERROR);
        }

        return $response;
    }

    /**
     * @param $response
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function renderMessage($response)
    {
        $data = $response->getData(true);
        if (!empty($data['message'])) {
            return $data['message'];
        }

        $rawMessage = 'error_http.' . $response->getStatusCode();
        $message = trans($rawMessage);
        if ($message == $rawMessage) {
            return trans('error.level_3', ['message' => trans('error_http.500')]);
        }
        return trans('error.level_3', ['message' => $message]);
    }
}
