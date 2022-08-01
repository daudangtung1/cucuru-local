<?php

namespace App\Http\Controllers;


use App\Utils\AppConfig;

class ApiController extends Controller
{
    use ApiResponseTrait;

    const DEFAULT_LIMIT = 10;

    public function __construct()
    {
        parent::__construct();
    }

    protected function getValidLimit($limit, $defaultLimit = null)
    {
        $limit = (int)$limit;

        if (empty($limit) || !in_array($limit, AppConfig::ALLOWED_ITEMS_PER_PAGE)) {
            $limit = empty($defaultLimit) ? AppConfig::DEFAULT_ITEMS_PER_PAGE : $defaultLimit;
        }

        return $limit;
    }

    protected function getValidPageNo ($pageNo)
    {
        $pageNo = (int)$pageNo;

        if (empty($pageNo) || $pageNo < 1) {
            $pageNo = AppConfig::DEFAULT_PAGE;
        }

        return $pageNo;
    }

    protected function customPagination($pagination)
    {
        $this::addBlockResponse('_pagination', $pagination);
    }
}
