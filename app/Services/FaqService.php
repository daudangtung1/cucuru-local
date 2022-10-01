<?php

namespace App\Services;

use App\Models\Faq;

class FaqService extends BaseModelService
{
    public function __construct(Faq $faq)
    {
        $this->model = $faq;
    }
}
