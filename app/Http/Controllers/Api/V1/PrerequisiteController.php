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

        $arrayPage = explode(',', $request->page);
        foreach ($arrayPage as $page) {
            $filePath = resource_path('lang/' . $request->lang . '/' . $page . '.php');

            if (!File::isFile($filePath)) {
                $fileData[$page] = __('prerequisite.file_not_exist');
            } else {
                $fileData[$page] = include($filePath);
            }
        }

        return $this->responseSuccess($fileData);
    }
}
