<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService extends BaseService
{
    /**
     * @param $id
     * @param bool $strict
     * @return Notification
     */
    public function getById($id, $strict = true)
    {
        $notification = $strict ? Notification::find($id) : Notification::findOrFail($id);

        return $notification;
    }

    /**
     * @param $limit
     * @param $pageNo
     * @param array $filterData
     * @param array $sortData
     * @return bool|mixed|null
     */
    public function get($limit, $pageNo, array $filterData, array $sortData)
    {
        $typeSort = 'desc';
        $defaultSortField = [
            'created_at',
        ];

        $fieldSort = 'created_at';
        if (isset($sortData['sort_by']) && in_array($sortData['sort_by'], $defaultSortField)) {
            $fieldSort = $sortData['sort_by'];
        }

        if (isset($sortData['type_sort']) && in_array($sortData['type_sort'], ['desc', 'asc'])) {
            $typeSort = $sortData['type_sort'];
        }

        $notifications = Notification::query()->where('user_id', Auth::guard('api')->id());

        if (isset($filterData['is_important'])) {
            $notifications = $notifications->where("is_important", $filterData['is_important']);
        }

        $notifications = $notifications->orderBy($fieldSort, $typeSort)->paginate($limit);

        return [
            'data' => $notifications,
            'pagination' => $this->customPagination($notifications)
        ];
    }
}
