<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Faq;

class FaqService extends BaseService
{
    /**
     * @param $limit
     * @param $pageNo
     * @param array $filterData
     * @param array $sortData
     * @return bool|mixed|null
     */
    public function getList($limit, $pageNo, array $filterData)
    {
        $faqs = new Faq();

        if (isset($filterData['faq_type_id'])) {
            $faqs = $faqs->where('faq_type_id', $filterData['faq_type_id'])->paginate($limit);
        } else {
            $faqs = $faqs->paginate($limit);
        }

        return [
            'data' => $faqs,
            'pagination' => $this->customPagination($faqs)
        ];
    }

    /**
     * @param $id
     * @param bool $strict
     * @return Faq
     */
    public function getById($id, $strict = true)
    {
        return $strict ? Faq::find($id) : Faq::findOrFail($id);
    }
}
