<?php

namespace App\Http\Controllers;

use App\Utils\TransactionHelper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $validationErrors;

    /**
     * @var Request
     */
    protected $currentRequest;
    protected $transactionHelper;

    public $visitorIp;

    public function __construct()
    {
        $this->validationErrors = collect([]);
        $this->currentRequest = request();
        $this->transactionHelper = TransactionHelper::getInstance();
        $this->visitorIp = request()->ip();
    }

    protected function transactionStart($connection = null)
    {
        $this->transactionHelper->start($connection);
    }

    protected function transactionComplete()
    {
        $this->transactionHelper->complete();
    }

    protected function transactionStop()
    {
        $this->transactionHelper->stop();
    }

    public function customValidate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $this->validationErrors = collect([]);
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $this->validationErrors = $validator->errors();
            return false;
        }

        return true;
    }

    protected function getValidationErrors()
    {
        return $this->validationErrors->all();
    }

    protected function getFirstValidationError()
    {
        return $this->validationErrors->first();
    }
}
