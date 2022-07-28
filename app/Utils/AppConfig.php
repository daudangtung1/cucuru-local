<?php

namespace App\Utils;

class AppConfig
{
    const HTTP_RESPONSE_STATUS_OK = 200;
    const HTTP_RESPONSE_STATUS_ERROR = 422;
    const HTTP_RESPONSE_STATUS_NOT_AUTHENTICATED = 401;
    const HTTP_RESPONSE_STATUS_NOT_AUTHORIZED = 403;
    const HTTP_RESPONSE_STATUS_NOT_FOUND = 404;
    const HTTP_RESPONSE_STATUS_TOO_MANY_ATTEMPTS = 419;

    const DEFAULT_ITEMS_PER_PAGE = 10;
    const ALLOWED_ITEMS_PER_PAGE = [5, 10, 20, 30];

    const DEFAULT_PAGE = 1;
    const FRONT_END_DATE_TIME_FORMAT = 'd/m/Y H:i:s';

    const PLATFORM_WEB_VALUE = 1;
    const PLATFORM_IOS_VALUE = 2;
    const PLATFORM_ANDROID_VALUE = 3;

    const PLATFORM_WEB_NAME = 'Web';
    const PLATFORM_IOS_NAME = 'IOS';
    const PLATFORM_ANDROID_NAME = 'Android';

    const UNLOCK = 'Unlock';
}
