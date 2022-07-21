<?php

namespace App\Http\Controllers;


use App\Exceptions\CustomException;
use App\Utils\AppConfig;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ApiResponseTrait
{
    protected static $extraResponse = [
        '_block' => null,
    ];

    public static function addBlockResponseMessage($message, $fresh = false)
    {
        if ($fresh || self::$extraResponse['_block'] == null) {
            self::$extraResponse['_block'] = [];
        }
        self::$extraResponse['_block'][] = $message;
    }

    public static function addBlockResponse ($key, $value, $fresh = false)
    {
        if ($fresh || empty(self::$extraResponse[$key])) {
            self::$extraResponse[$key] = [];
        }
        self::$extraResponse[$key] = $value;
    }

    /**
     * @param $failed
     * @param null $data
     * @param string $message
     * @param null $customStatus
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($failed, $data = null, $message = '', $customStatus = null)
    {
        $status = !is_null($customStatus) ? $customStatus : ($failed ? AppConfig::HTTP_RESPONSE_STATUS_ERROR : AppConfig::HTTP_RESPONSE_STATUS_OK);
        return response()->json([
            '_status' => $status,
            '_success' => !$failed,
            '_messages' => empty($message) ? null : (array)$message,
            '_data' => $data,
            '_extra' => self::$extraResponse,
        ], $status);
    }

    /**
     * @param null $data
     * @param string $message
     * @param null $customStatus
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseSuccess($data = null, $message = '', $customStatus = null)
    {
        $this->transactionComplete();
        return $this->response(false, $data, $message, $customStatus);
    }

    /**
     * @param string $message
     * @param null $data
     * @param null $customStatus
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseFail($message = '', $data = null, $customStatus = null)
    {
        $this->transactionStop();

        if ($message instanceof ModelNotFoundException) {
            return $this->response(true, null, '404 - ' . trans('error_http.404'), AppConfig::HTTP_RESPONSE_STATUS_NOT_FOUND);
        } elseif ($message instanceof CustomException) {
            $exception = $message;
            $message = $exception->getMessage();
            $data = array_merge((array)$data, [
                'attached' => $exception->getAttachedData(),
                'exception' => [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ],
            ]);
            logError($exception);

            // show detail error in json format
            if (config('app.env') == 'production' && config('app.debug') == false) {
                return $this->response(true, [], __('error.level_3_failed'), $customStatus);
            } else {
                return $this->response(true, $data, $message, $customStatus);
            }
        } elseif ($message instanceof \Exception) {
            $exception = $message;
            $message = $exception->getMessage();
            $data = array_merge((array)$data, [
                'exception' => [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ],
            ]);
            logError($exception);
            // show detail error in json format
            if (config('app.env') == 'production' && config('app.debug') == false) {
                return $this->response(true, [], __('error.level_3_failed'), $customStatus);
            } else {
                return $this->response(true, $data, $message, $customStatus);
            }
        }

        return $this->response(true, $data, $message, $customStatus);
    }
}
