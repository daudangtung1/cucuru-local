<?php
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Storage;
//use Intervention\Image\ImageManagerStatic as Image;

if (!function_exists('isAuth')) {
    function isAuth()
    {
        return auth()->check();
    }
}

if (!function_exists('authUser')) {
    function authUser()
    {
        return isAuth() ? auth()->user() : null;
    }
}

if (!function_exists('logError')) {
    function logError(\Exception $exception, $group = 0)
    {
        $user = authUser();

        $message = $exception instanceof CustomException ?
            $exception->getMessage() . ' (Data: ' . json_encode($exception->getAttachedData()) . ')' : $exception->getMessage();
        Log::error(implode(' ', [
            $group,
            empty($user) ? 0 : $user->id,
            request()->ip(),
            $exception->getFile(),
            $exception->getLine(),
            $message
        ]));
    }
}

if (!function_exists('uploadImageToAws')) {
    function uploadImageToAws($image, $name, array $sizes, $parentFolder = '', $constraint = true, $isUrl = false)
    {
        $img = Image::make($image);

        if ($isUrl) {
            $extension = pathinfo($image, PATHINFO_EXTENSION);
        } else {
            $extension = $image->getClientOriginalExtension();
        }


        if ($extension != 'jpg') {
            $img = $img->encode('jpg');
        }

        foreach ($sizes as $size) {
            $imageName = "$parentFolder/" . $name . "/$size.jpg";

            if ($size != 'default') {
                if ($constraint) {
                    $img = $img->resize($size, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    $img = $img->resize($size, $size);
                }
            }

            Storage::disk('s3')->put($imageName, $img->stream(), 'public');
        };
    }
}

if (!function_exists('customLog')) {
    function customLog($text) {
        \Illuminate\Support\Facades\File::append(storage_path() . '/logs/custom.log',
            \Carbon\Carbon::now() . ' ' . print_r($text, true) . PHP_EOL);
    }
}

if (!function_exists('gb_asset')) {
    /**
     * Global asset
     * Change asset to http or http depend on server url
     *
     * @param $path
     * @return string
     */
    function gb_asset($path)
    {
        if (\Request::server('HTTP_X_FORWARDED_PROTO') == 'https') {
            return secure_asset($path);
        }

        return asset($path);
    }
}

if (!function_exists('gb_url')) {
    /**
     * Global url
     * Change url to http or http depend on server url
     *
     * @param $meme
     * @param $forRouteName
     * @return string
     */
    function gb_url($meme, $forRouteName = true)
    {
        if (\Request::server('HTTP_X_FORWARDED_PROTO') == 'https') {
            return $forRouteName ? secure_url(route($meme, [], false)) : secure_url($meme);
        }

        return $forRouteName ? url(route($meme, [], false)) : url($meme);
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * get client ip
     *
     * @return array|false|string
     */
    function get_client_ip()
    {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ipAddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipAddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipAddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipAddress = getenv('REMOTE_ADDR');
        } else {
            $ipAddress = 'UnKnown';
        }

        if (is_array($ipAddress)) {
            $ipAddress = last($ipAddress);
        }

        return $ipAddress;
    }
}

if (!function_exists('checkNudeImage')) {
    /**
     * @param $fileImage
     * @param bool $is_url
     * @return bool
     * @throws Exception
     */
    function checkNudeImage($fileImage, $is_url = false)
    {
        try {
            if ($fileImage) {
                if ($is_url) {
                    $method = 'GET';
                    $options = [
                        'query' => [
                            'url' => $fileImage,
                            'models' => 'nudity',
                            'api_user' => config('constants.nude.api_user'),
                            'api_secret' => config('constants.nude.api_secret'),
                        ]
                    ];
                } else {
                    $extension = $fileImage->getClientOriginalExtension();
                    $fileName = $fileImage->getFilename();
                    $fileImage = Image::make($fileImage);

                    if ($extension != 'jpg') {
                        $fileImage = $fileImage->encode('jpg');
                    }

                    $method = 'POST';
                    $options = [
                        'headers' => [
                            'api_user' => config('constants.nude.api_user'),
                            'api_secret' => config('constants.nude.api_secret'),
                        ],
                        'multipart' => [
                            [
                                'name' => 'media',
                                'contents' => $fileImage->stream(),
                                'filename' => $fileName,
                            ],
                            [
                                'name' => 'models',
                                'contents' => 'nudity'
                            ]
                        ]
                    ];
                }
                $url = config('constants.nude.url_api_nude');

                $response = (new \GuzzleHttp\Client())->request($method, $url, $options);
                $data = json_decode((string)$response->getBody(), true);

                if ($data['nudity']['raw'] >= max($data['nudity']['partial'], $data['nudity']['safe']) ||
                    $data['nudity']['partial'] >= max($data['nudity']['raw'], $data['nudity']['safe'])) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            logError($e);
            throw $e;
        }
    }
}

if (!function_exists('isOwner')) {
    function isOwner($model)
    {
        return \Illuminate\Support\Facades\Gate::allows('is-owner', $model);
    }
}

if (!function_exists('thousandsCurrencyFormat')) {
    function thousandsCurrencyFormat($num)
    {

        if ($num > 1000) {

            $x = round($num);
            $xNumberFormat = number_format($x);
            $xArray = explode(',', $xNumberFormat);
            $xParts = array('k', 'm', 'b', 't');
            $xCountParts = count($xArray) - 1;
            $xDisplay = $xArray[0] . ((int)$xArray[1][0] !== 0 ? ',' . $xArray[1][0] : '');
            $xDisplay .= $xParts[$xCountParts - 1];

            return $xDisplay;

        }

        return $num ?? 0;
    }
}

if (!function_exists('shorten_string')) {
    /**
     * Shorten string
     * Get a sub string with words
     * shorten_string('I go to school', 3) => I go to
     *
     * @param $string
     * @param $wordsReturned
     * @return string
     */
    function shorten_string($string, $wordsReturned)
    {
        $array = explode(" ", $string);
        /*  Already short enough, return the whole thing*/
        if (count($array) <= $wordsReturned) {
            $retVal = preg_replace( "/\r|\n/", " ", $string );
        } /*  Need to chop of some words*/
        else {
            array_splice($array, $wordsReturned);
            $retVal = implode(" ", $array) . " ...";
            $retVal = preg_replace( "/\r|\n/", " ", $retVal );
        }
        return $retVal;
    }
}


if (!function_exists('is_nu_hiep_book')) {
    function is_nu_hiep_book($object)
    {
        if (empty($object)) {
            return false;
        } elseif (get_class($object) == \App\Models\Book::class) {
            $book = $object;
        } elseif (get_class($object) == \App\Models\Chapter::class) {
            $book = $object->book;
        } else {
            return false;
        }

        if ($book->getOriginal('kind') == \App\Models\Book::TRANSLATION && $book->getOriginal('sex') == \App\Models\Book::FEMALE) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('is_me_truyen_chu_book')) {
    function is_me_truyen_chu_book($object)
    {
        if (empty($object)) {
            return false;
        } elseif (get_class($object) == \App\Models\Book::class) {
            $book = $object;
        } elseif (get_class($object) == \App\Models\Chapter::class) {
            $book = $object->book;
        } else {
            return false;
        }

        if ($book->getOriginal('kind') == \App\Models\Book::TRANSLATION && $book->getOriginal('sex') == \App\Models\Book::MALE) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('is_vtruyen_book')) {
    function is_vtruyen_book($object)
    {
        if (empty($object)) {
            return false;
        } elseif (get_class($object) == \App\Models\Book::class) {
            $book = $object;
        } elseif (get_class($object) == \App\Models\Chapter::class) {
            $book = $object->book;
        } else {
            return false;
        }

        if ($book->getOriginal('kind') == \App\Models\Book::ORIGINAL) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_nu_hiep_book_link')) {
    function get_nu_hiep_book_link($book)
    {
        if (config('services.nu_hiep.allow_redirect') && is_nu_hiep_book($book)) {
            return config('services.nu_hiep.server') . '/truyen/' . $book->slug;
        }

        return 'javascript:';
    }
}

if (!function_exists('get_me_truyen_chu_book_link')) {
    function get_me_truyen_chu_book_link($book)
    {
        if (config('services.me_truyen_chu.allow_redirect') && is_me_truyen_chu_book($book)) {
            return config('services.me_truyen_chu.server') . '/truyen/' . $book->slug;
        }

        return 'javascript:';
    }
}

if (!function_exists('get_vtruyen_book_link')) {
    function get_vtruyen_book_link($book)
    {
        if (config('services.vtruyen.allow_redirect') && is_vtruyen_book($book)) {
            return config('services.vtruyen.server') . '/truyen/' . $book->slug;
        }

        return 'javascript:';
    }
}

if (!function_exists('get_book_link')) {
    function get_book_link($book)
    {
        if (is_nu_hiep_book($book)) {
            return get_nu_hiep_book_link($book);
        } elseif (is_me_truyen_chu_book($book)) {
            return get_me_truyen_chu_book_link($book);
        } elseif (is_vtruyen_book($book)) {
            return get_vtruyen_book_link($book);
        } else {
            return 'javascript:';
        }
    }
}

if (!function_exists('isActiveLeftBar')) {
    function isActiveLeftBar($parentPath = "")
    {
        if (strpos(\url()->current(), $parentPath) != 'false') {
            echo("class='active'");
        }
    }
}

if (!function_exists('isModerator')) {
    function isModerator()
    {
        return auth()->user()->isModerator();
    }
}

if (!function_exists('isAuthor')) {
    function isAuthor()
    {
        return auth()->user()->isAuthor();
    }
}

if (!function_exists('get_nu_hiep_chapter_link')) {
    function get_nu_hiep_chapter_link($chapter)
    {
        if (config('services.nu_hiep.allow_redirect') && is_nu_hiep_book($chapter)) {
            return config('services.nu_hiep.server') . '/truyen/' . $chapter->book->slug . '/chuong-' . $chapter->index;
        }

        return 'javascript:';
    }
}

if (!function_exists('get_me_truyen_chu_chapter_link')) {
    function get_me_truyen_chu_chapter_link($chapter)
    {
        if (config('services.me_truyen_chu.allow_redirect') && is_me_truyen_chu_book($chapter)) {
            return config('services.me_truyen_chu.server') . '/truyen/' . $chapter->book->slug . '/chuong-' . $chapter->index;
        }

        return 'javascript:';
    }
}

if (!function_exists('get_vtruyen_chapter_link')) {
    function get_vtruyen_chapter_link($chapter)
    {
        if (config('services.vtruyen.allow_redirect') && is_vtruyen_book($chapter)) {
            return config('services.vtruyen.server') . '/truyen/' . $chapter->book->slug . '/chuong-' . $chapter->index;
        }

        return 'javascript:';
    }
}

if (!function_exists('get_chapter_link')) {
    function get_chapter_link($chapter)
    {
        if (is_nu_hiep_book($chapter)) {
            return get_nu_hiep_chapter_link($chapter);
        } elseif (is_me_truyen_chu_book($chapter)) {
            return get_me_truyen_chu_chapter_link($chapter);
        } elseif (is_vtruyen_book($chapter)) {
            return get_vtruyen_chapter_link($chapter);
        } else {
            return 'javascript:';
        }
    }
}

if (! function_exists('arr_flip')) {
    function arr_flip($array)
    {
        $flipped = [];

        foreach ($array as $key => $value) {
            $value = (string) $value;

            $flipped[$value] = $key;
        }

        return $flipped;
    }
}

if (! function_exists('collection_to_map')) {
    /**
     * Get Collection Map by Key
     *
     * @param string     $key        Extract Key
     * @param \Illuminate\Support\Collection $collection Collection
     *
     * @return \Illuminate\Support\Collection
     */
    function collection_to_map($key, $collection)
    {
        return $collection->mapToGroups(function ($item) use ($key) {
            return [$item[$key] => $item];
        })->map->first();
    }
}

if (! function_exists('strip_vn')) {
    function strip_vn($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);

        return $str;
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        if (is_array($key)) {
            return \App\Models\Config::set($key[0], $key[1], $key[2]);
        }

        $value = \App\Models\Config::get($key);

        return is_null($value) ? value($default) : $value;
    }
}

if (!function_exists('is_bad_word')) {
    /**
     * Kiểm tra đoạn text có chứa từ ngữ tục tiểu
     *
     * @param $text
     * @return bool
     */
    function is_bad_word($text)
    {
        $badWords = explode(',', setting(\App\Models\Config::KEY_BAD_WORDS));

        $isBadWord = preg_match_all(
            "/\b(" . implode("|", $badWords) . ")\b/u",
            $text,
            $matches
        );

        return !!$isBadWord;
    }
}

if (!function_exists('content_beautify')) {
    /**
     * Beautify content string by removing or replace escape characters
     *
     * @param string $content
     * @return string|string[]
     */
    function content_beautify(string $content)
    {
        if (!empty($content)) {
            $content = htmlspecialchars_decode($content);
            $content = str_replace(['{{', '}}'], ['(', ')'], $content);
            $content = str_replace(['<p>', '</p>'], ['', ''], $content);
        }
        return $content;
    }
}
if (!function_exists('convert_associative_array_to_flatten_array')) {
    function convert_associative_array_to_flatten_array($array, $originKey, &$key = '', $level = 0)
    {
        if (!is_array($array)) {
            return FALSE;
        }

        if ($key == '') {
            $key = $originKey;
        }
        $result = array();

        foreach ($array as $keyItem => $value) {
            if (array_key_first($array) == $keyItem) {
                $level++;
            };

            if ($level == 1) {
                $key = $originKey . ($originKey ? '.' : '') . $keyItem;
            } else {
                $key .= '.' . $keyItem;
            }

            if (is_array($value)) {
                $result = array_merge($result, convert_associative_array_to_flatten_array($value, $originKey, $key, $level));
            } else {
                $result[$key] = $value;
                if ($level == 1) {
                    $key = $originKey;
                } else {
                    if (array_key_last($array) === $keyItem) {
                        $level--;
                        $key = explode('.', $key);

                        if ($level == 1) {
                            $key = array_slice($key, 0, $level);
                        } else {
                            $key = array_slice($key, 0, $level + 1);
                        }

                        $key = implode('.', $key);
                    } else {
                        $key = str_replace('.' . $keyItem, '', $key);
                    }
                }
            }
        }

        return $result;
    }
}
