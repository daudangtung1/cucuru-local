<?php

namespace App\Services;

use App\Models\Faq;

class FaqService extends BaseService
{
    /**
     * @param $id
     * @param $strict
     * @return mixed
     */
    public function getById($id, $strict = true)
    {
        return $strict ? Faq::find($id) : Faq::findOrFail($id);
    }

    public function get($limit, $pageNo)
    {
        $faqs = Faq::query()->paginate($limit);

        return [
            'data' => $faqs->map(function ($faq) {
                return $faq->only([
                    'id', 'title', 'content',
                ]);
            }),
            'pagination' => $this->customPagination($faqs)
        ];
    }
}
