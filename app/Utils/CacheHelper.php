<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    private $cache;
    private $redisCache;
    private $fileCache;

    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new CacheHelper();
        }

        return self::$instance;
    }

    public function cache()
    {
        if (empty($this->cache)) {
            $this->cache = Cache::store();
        }

        return $this->cache;
    }

    public function redis()
    {
        if (empty($this->redisCache)) {
            $this->redisCache = Cache::store('redis');
        }

        return $this->redisCache;
    }

    public function file()
    {
        if (empty($this->fileCache)) {
            $this->fileCache = Cache::store('file');
        }

        return $this->fileCache;
    }
    #endregion

    #region Check Cache has data and still available
    public function hasDataOnRedis($cacheKey)
    {
        if ($this->redis()->has($cacheKey) && $cache = $this->redis()->get($cacheKey)) {
            return $cache;
        } else {
            return false;
        }
    }

    public function hasDataOnRedisWithTags(array $tags, $cacheKey)
    {
        if ($this->redis()->tags($tags)->has($cacheKey) && $cache = $this->redis()->tags($tags)->get($cacheKey)) {
            return $cache;
        } else {
            return false;
        }
    }

    public function hasDataOnFile($cacheKey)
    {
        if ($this->file()->has($cacheKey) && $cache = $this->file()->get($cacheKey)) {
            return $cache;
        } else {
            return false;
        }
    }
    #endregion

    #region Cache helper method
    public function saveToRedis($cacheKey, int $cacheTime, $cachedObject, $allowNull = true)
    {
        if ($allowNull || !empty($cachedObject)) {
            $this->redis()->put($cacheKey, $cachedObject, Carbon::now()->addMinutes($cacheTime));
        } else {
            return null;
        }

        return $this->redis()->get($cacheKey);
    }

    public function rememberRedis($cacheKey, int $cacheTime, $closure)
    {
        if ($cache = $this->hasDataOnRedis($cacheKey)) {
            return $cache;
        } else {
            return $this->saveToRedis($cacheKey, $cacheTime, call_user_func($closure));
        }
    }

    public function saveToRedisWithTags(array $tags, $cacheKey, int $cacheTime, $cachedObject, $allowNull = true)
    {
        if ($allowNull || !empty($cachedObject)) {
            $this->redis()->tags($tags)->put($cacheKey, $cachedObject, Carbon::now()->addMinutes($cacheTime));
        } else {
            return null;
        }

        return $this->redis()->tags($tags)->get($cacheKey);
    }

    public function rememberRedisTags(array $tags, $cacheKey, int $cacheTime, $closure)
    {
        if ($cache = $this->hasDataOnRedisWithTags($tags, $cacheKey)) {
            return $cache;
        } else {
            return $this->saveToRedisWithTags($tags, $cacheKey, $cacheTime, call_user_func($closure));
        }
    }

    #endregion

    public function flushRedisCache($cacheKey)
    {
        $this->redis()->forget($cacheKey);
    }

    public function flushRedisWithTags(array $tags, $cacheKey = null)
    {
        if (is_null($cacheKey)) {
            $this->redis()->tags($tags)->flush();
        } else {
            $this->redis()->tags($tags)->forget($cacheKey);
        }
    }

    public function cacheRedisPrefixOverride($prefix)
    {
        $this->redis()->getStore()->setPrefix($prefix);
    }

    public function cacheRedisPrefixReset()
    {
        $this->redis()->getStore()->setPrefix(config('cache.prefix'));
    }

    /**
     * @param array $params
     * @param null $parameter1 should be cache prefix
     * @param null $parameter2 should be limit if work with paginate
     * @param null $parameter3 should be page no if work with paginate
     * @return string
     */
    public function buildCacheKeyFromParams(array $params, $parameter1 = null, $parameter2 = null, $parameter3 = null)
    {
        $cacheKey = '';

        if ($parameter1) {
            $cacheKey .= $parameter1;
        }

        if ($parameter2) {
            $cacheKey .= $parameter2;
        }

        if ($parameter3) {
            $cacheKey .= $parameter3;
        }

        if ($params) {
            $params = Arr::sort($params);

            foreach ($params as $key => $param) {
                if (isset($param)) {
                    $cacheKey .= $key . '-' . $param;
                }
            }
        }

        return $cacheKey;
    }
}
