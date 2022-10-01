<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Utils\FolderHelper;
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
        $availableLocales = FolderHelper::getInstance()->getLocaleFolders();
        $languages = [];

        // Validate language to prevent hack
        if (!in_array($request->lang, $availableLocales)) {
            return $this->responseFail(__('prerequisite.file_not_exist'));
        }

        $availablePages = FolderHelper::getInstance()->getFilesInPath(
            resource_path('lang' . DIRECTORY_SEPARATOR . $request->lang),
            true,
            true
        );
        unset($availablePages[0]);

        foreach ($arrayPage as $page) {
            if (!in_array($page, $availablePages)) {
                continue;
            }

            $filePath = resource_path('lang/' . $request->lang . '/' . $page . '.php');

            if (!File::isFile($filePath)) {
                $languages[$page] = __('prerequisite.file_not_exist');
            } else {
                $languages[$page] = include($filePath);
            }
        }

        return $this->responseSuccess([
            'languages' => $languages
        ]);
    }
}
