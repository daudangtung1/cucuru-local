<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Models\Faq;
use App\Models\FaqType;
use App\Services\FaqService;
use App\Utils\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FaqController extends ApiController
{
    /**
     * @var FaqService
     */
    protected $faqService;

    /**
     * FaqController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->faqService = new FaqService();
    }

    public function index(Request $request)
    {
        if (!$this->customValidate($request, [
            'faq_type_id' => 'sometimes|numeric|exists:faq_types,id',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);
        $filterData = empty($faqTypes) && empty($request->faq_type_id) ? [] : ['faq_type_id' => $request->faq_type_id];
        $faqs = $this->faqService->getList($limit, $pageNo, $filterData);
        $this->customPagination($faqs['pagination']);

        return $this->responseSuccess($faqs['data']);
    }

    public function show($id)
    {
        $faq = $this->faqService->getById($id);

        if (empty($faq)) {
            return $this->responseFail(trans('general.not_found'), null, Response::HTTP_NOT_FOUND);
        }

        return $this->responseSuccess($faq);
    }
}
