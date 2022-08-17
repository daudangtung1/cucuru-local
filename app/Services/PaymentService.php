<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Payment;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentService extends BaseService
{
    /**
     * @param $id
     * @param bool $strict
     * @return Post
     */
    public function getById($id, $strict = true)
    {
        return $strict ? Payment::find($id) : Payment::findOrFail($id);
    }

    public function getList($limit, $pageNo, $filterData = [])
    {
        $payments = new Payment();
        $payments = $payments->where('created_by', Auth::guard('api')->id());

        if ((!empty($filterData['from_date']) && empty($filterData['to_date'])) ||
            (!empty($filterData['to_date']) && empty($filterData['from_date']))) {
            return ['errors' => trans('payment.message.invalid_range_time')];
        } else if (!empty($filterData['from_date']) && !empty($filterData['to_date'])) {
            $from = Carbon::createFromFormat('Y/m/d', $filterData['from_date'])->startOfDay();
            $to = Carbon::createFromFormat('Y/m/d', $filterData['to_date'])->endOfDay();

            if ($from->timestamp > $to->timestamp) {
                return ['errors' => trans('payment.message.from_time_gather_than_to_time')];
            }

            $payments = $payments->whereBetween('created_at', [$from, $to]);
        }

        if (isset($filterData['status'])) {
            $payments = $payments->where('status', $filterData['status']);
        }

        $payments = $payments->paginate($limit);

        return [
            'data' => $payments,
            'pagination' => $this->customPagination($payments)
        ];
    }
}
