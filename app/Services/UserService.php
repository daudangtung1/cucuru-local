<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService extends BaseService
{
    public function getById($id, $strict = true)
    {
        return $strict ? User::find($id) : User::findOrFail($id);
    }

    public function getListFollower($limit, $pageNo, $filterData = [])
    {
        $user = Auth::guard('api')->user();

        if (empty($user)) {
            return ['errors' => trans('user.message.account_not_login')];
        }

        $followers = $user->followers()->paginate($limit);
        $followers = [
            'data' => $followers,
            'pagination' => $this->customPagination($followers)
        ];

        return $followers;
    }

    public function getListFollow($limit, $pageNo, $filterData = [])
    {
        $user = Auth::guard('api')->user();

        if (empty($user)) {
            return ['errors' => trans('user.message.account_not_login')];
        }

        $follows = $user->follows()->paginate($limit);
        $follows = [
            'data' => $follows,
            'pagination' => $this->customPagination($follows)
        ];

        return $follows;
    }
}
