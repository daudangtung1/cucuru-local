<?php

namespace App\Services;

use App\Models\Faq;

class FAQService extends BaseModelService
{
    public function __construct(Faq $faq)
    {
        $this->model = $faq;
    }
}
