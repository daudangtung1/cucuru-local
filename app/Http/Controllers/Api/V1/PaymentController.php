<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Mockery\Exception;

class PaymentController extends ApiController
{
    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * PaymentController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->paymentService = new PaymentService();
    }

    public function index(Request $request)
    {
        try {
            if (!$this->customValidate($request, [
                'from_date' => 'sometimes|date_format:Y/m/d',
                'to_date' => 'sometimes|date_format:Y/m/d',
                'status' => 'sometimes|in:' . implode(',', array_values(Payment::PAYMENT_STATUS)),
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $pageNo = $this->getValidPageNo($request->input('page'));
            $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);
            $filterData = $request->only(['from_date', 'to_date', 'status']);

            $posts = $this->paymentService->getList($limit, $pageNo, $filterData);

            if (isset($posts['errors'])) {
                return $this->responseFail($posts['errors']);
            }

            $this->customPagination($posts['pagination']);

            return $this->responseSuccess($posts['data']);
        } catch (Exception $exception) {
            return $this->responseFail($exception);
        }
    }
}
