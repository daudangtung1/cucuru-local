<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\FaqService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function index(Request $request)
    {
        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);

        $faqs = $this->faqService->get($limit, $pageNo);
        $this->customPagination($faqs['pagination']);

        return $this->responseSuccess($faqs['data']);
    }

    public function show($id)
    {
        $faq = $this->faqService->getById($id);

        if ($faq) {
            return $this->responseSuccess($faq);
        }

        return $this->responseFail(
            trans('faq.message.post_not_found'),
            $faq,
            Response::HTTP_NOT_FOUND
        );
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
