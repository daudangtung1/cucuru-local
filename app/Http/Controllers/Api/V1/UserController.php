<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::all();
        return $this->responseSuccess(['users' => $users, 'Danh sách user']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getFollower(Request $request)
    {
        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);
        $followers = $this->userService->getListFollower($limit, $pageNo);

        if (isset($followers['errors'])) {
            return $this->responseFail($followers['errors']);
        }

        $this->customPagination($followers['pagination']);

        return $this->responseSuccess($followers['data']);
    }

    public function getFollow(Request $request)
    {
        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);
        $follows = $this->userService->getListFollow($limit, $pageNo);

        if (isset($follows['errors'])) {
            return $this->responseFail($follows['errors']);
        }

        $this->customPagination($follows['pagination']);

        return $this->responseSuccess($follows['data']);
    }

    public function getInviteCode()
    {
        return $this->responseSuccess(['aff_code' => $this->userService->getInviteCode()]);
    }
}
