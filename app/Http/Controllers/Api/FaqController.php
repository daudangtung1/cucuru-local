<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\FaqService;
use Illuminate\Http\Request;

class FaqController extends ApiController
{
    /**
     * @var FaqService
     */
    protected $faqService;

    public function __construct()
    {
        parent::__construct();
        $this->faqService = new FaqService();
    }

    public function index(Request $request, FaqService $faqService)
    {
        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);

        $faqs = $this->faqService->get($limit, $pageNo);
        $this->customPagination($faqs['pagination']);

        return $this->responseSuccess($faqs['data']);
    }

    public function show(Request $request, FaqService $faqService)
    {
        return $faqService->getById($request->id);
    }

    /*public function create(Request $request, FaqService $faqService)
    {
        return $faqService->create(
            $request->validate([
                'title' => 'required',
                'content' => 'required',
                'faq_type_id' => 'required'
            ])
        );
    }*/
}
