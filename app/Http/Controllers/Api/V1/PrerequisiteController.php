<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PrerequisiteController extends ApiController
{
    public function getPageInfo(Request $request)
    {
        if (!$this->customValidate($request, [
            'page' => 'required',
            'lang' => 'required',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $filePath = resource_path('lang/' . $request->lang . '/' . $request->page . '.php');

        if (!File::isFile($filePath)) {
            return $this->responseFail(__('prerequisite.file_not_exist'));
        }

        return $this->responseSuccess(include($filePath));
    }
}
