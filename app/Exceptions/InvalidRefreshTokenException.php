<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidRefreshTokenException extends Exception
{
    /**
     * InvalidRefreshTokenException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if ($previous) {
            $this->line = $previous->getLine();
            $this->file = $previous->getFile();
        }
        if (empty($message)) {
            $this->message = self::formatMessage($previous->getMessage());
        }
    }

    /**
     * @param string $message
     * @param int $level
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public static function formatMessage($message = '', $level = 2)
    {
        return empty($message) ?
            trans('error.level_' . $level . '_failed') : trans('error.level_' . $level, ['message' => $message]);
    }
}
