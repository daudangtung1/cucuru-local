<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * NotificationController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->notificationService = new NotificationService();
    }

    public function index(Request $request)
    {
        if (!$this->customValidate($request, [
            'is_important' => 'sometimes|in:0,1',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $sortData = $request->only('sort_by', 'type_sort');
        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);

        $filterData = $request->only(['is_important']);
        $notfications = $this->notificationService->get($limit, $pageNo, $filterData, $sortData);
        $this->customPagination($notfications['pagination']);

        return $this->responseSuccess($notfications['data']);
    }
}
