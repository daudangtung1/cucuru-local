<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\PostService;
use App\Utils\AppConfig;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;

class PostController extends ApiController
{
    /**
     * @var PostService
     */
    protected $postService;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->postService = new PostService();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filterData = $request->only([
            'title',
            'status',
        ]);

        $sortData = $request->only('sort_by', 'type_sort');
        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);

        // Get list post by filter data
        $posts = $this->postService->getListPost($limit, $pageNo, $filterData, $sortData);
        $this->customPagination($posts['pagination']);

        return $this->responseSuccess($posts['data']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            if (!$this->customValidate($request, [
                'title' => 'required|max:255',
                'content' => 'required',
                'status' => 'required|numeric|in:1,2',
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $postData = $request->only('title', 'content', 'status');
            $this->transactionStart();
            $post = $this->postService->create($postData);

            return $this->responseSuccess($post, trans('post.message.create_success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = $this->postService->getById($id);

        if ($post) {
            return $this->responseSuccess($post);
        }

        return $this->responseFail(trans('post.message.post_not_found'), $post,
            AppConfig::HTTP_RESPONSE_STATUS_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $post = $this->postService->getById($id);

            if (empty($post)) {
                return $this->responseFail(trans('post.message.post_not_found'), $post,
                    AppConfig::HTTP_RESPONSE_STATUS_NOT_FOUND);
            }

            // TODO: Chỗ này mai sau thêm quyền check xem có quyền cập nhật không

            if (!$this->customValidate($request, [
                'content' => 'string|nullable',
                'title' => 'string|nullable|max:255',
                'status' => 'numeric|in:1,2|nullable',
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $postData = $request->only(
                'status',
                'title',
                'content',
            );

            $this->transactionStart();

            $post = $this->postService->update($post, $postData);

            return $this->responseSuccess(['post_id' => $post->id], trans('post.message.update_success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $post = $this->postService->getById($id);

            if (empty($post)) {
                return $this->responseFail(trans('post.message.post_not_found'), $post,
                    AppConfig::HTTP_RESPONSE_STATUS_NOT_FOUND);
            }
            // TODO: chỗ này mai sau check quyền xóa
            $this->transactionStart();
            $this->postService->destroy($post);

            return $this->responseSuccess(null, trans('post.message.delete_success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }
}
