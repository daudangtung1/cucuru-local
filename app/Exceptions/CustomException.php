<?php

namespace App\Exceptions;

use function trans;

class CustomException extends \Exception
{
    const USER_LEVEL      = 0;
    const DATABASE_LEVEL  = 1;
    const APP_LEVEL       = 2;
    const UNHANDLED_LEVEL = 3;
    const NONE_LEVEL      = 4;

    protected $attachedData;

    public function __construct($message = '', $level = 2, $attachedData = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(self::formatMessage($message, $level), $code, $previous);

        $this->attachedData = $attachedData;
        if ($previous) {
            $this->line = $previous->getLine();
            $this->file = $previous->getFile();
            if (empty($message)) {
                $this->message = self::formatMessage($previous->getMessage(), $level);
            }
        }
    }

    public function getAttachedData()
    {
        return $this->attachedData;
    }

    public static function formatMessage($message = '', $level = 2)
    {
        return empty($message) ?
            trans('error.level_' . $level . '_failed') : trans('error.level_' . $level, ['message' => $message]);
    }
}
