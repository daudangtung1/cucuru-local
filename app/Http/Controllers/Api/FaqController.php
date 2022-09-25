<?php

namespace App\Http\Controllers\Api;

use App\Services\FaqService;
use Illuminate\Http\Request;

class FaqController
{
   public function list(Request $request, FaqService $faqService)
   {
      return $faqService->list(
         $request->only(['title', 'content'])
      );
   }

   public function show(Request $request, FaqService $faqService)
   {
      return $faqService->get($request->id);
   }

   public function create(Request $request, FaqService $faqService)
   {
      return $faqService->create(
         $request->validate([
            'title' => 'required',
            'content' => 'required',
            'faq_type_id' => 'required'
         ])
      );
   }
}
