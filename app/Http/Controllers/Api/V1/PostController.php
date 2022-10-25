<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use App\Rules\ValidateDeleteMediaOfPost;
use App\Rules\ValidateLimitNumberMediaOfPost;
use App\Services\PostService;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        $posts = $this->postService->get($limit, $pageNo, $filterData, $sortData);
        $this->customPagination($posts['pagination']);

        return $this->responseSuccess($posts['data']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!$this->customValidate($request, [
            'content' => 'sometimes',
            'is_adult' => 'required|in:0,1',
            'type' => 'required|in:' . implode(',', Post::TYPE),
            'published_at' => 'sometimes|date_format:Y-m-d H:i:s|after:now',
            'medias.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mpeg,mov|max:10240',
            'medias' => 'required|array|max:' . config('filesystems.limit_post_media'),
            'plan_id' => 'required|exists:plans,id,user_id,' . Auth::guard('api')->id(),
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $this->transactionStart();
        $post = $this->postService->create(
            $request->only('content', 'medias', 'published_at', 'is_adult', 'type', 'plan_id')
        );

        return $this->responseSuccess($post, trans('post.message.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = $this->postService->getById($id);

        if ($post) {
            return $this->responseSuccess($post);
        }

        return $this->responseFail(
            trans('post.message.post_not_found'),
            $post,
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $post = $this->postService->getById($id);

            if (empty($post)) {
                return $this->responseFail(
                    trans('post.message.post_not_found'),
                    null,
                    Response::HTTP_NOT_FOUND
                );
            }

            if (Gate::forUser(Auth::guard('api')->user())->denies('is-owner', $post)) {
                return $this->responseFail(
                    trans('post.message.can_not_update'),
                    null,
                    Response::HTTP_FORBIDDEN
                );
            }

            if (!$this->customValidate($request, [
                'content' => 'sometimes',
                'is_adult' => 'sometimes|in:0,1',
                'type' => 'sometimes|in:' . implode(',', Post::TYPE),
                'published_at' => 'sometimes|date_format:Y-m-d H:i:s|after:now',
                'medias.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mpeg,mov|max:10240',
                'medias' => ['required', 'array', new ValidateLimitNumberMediaOfPost($post)],
                'delete_medias' => ['sometimes', 'array', new ValidateDeleteMediaOfPost($post)],
                'plan_id' => 'sometimes|exists:plans,id,user_id,' . Auth::guard('api')->id(),
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $postData = $request->only(
                'type',
                'medias',
                'content',
                'is_adult',
                'delete_medias'
            );

            $this->transactionStart();
            $post = $this->postService->update($post, $postData);

            return $this->responseSuccess($post, trans('post.message.update_success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $post = $this->postService->getById($id);

            if (empty($post)) {
                return $this->responseFail(
                    trans('post.message.post_not_found'),
                    $post,
                    Response::HTTP_NOT_FOUND
                );
            }
            if (Gate::forUser(Auth::guard('api')->user())->denies('is-owner', $post)) {
                return $this->responseFail(
                    trans('post.message.can_not_delete'),
                    '',
                    Response::HTTP_FORBIDDEN
                );
            }

            $this->transactionStart();
            $this->postService->destroy($post);

            return $this->responseSuccess(null, trans('post.message.delete_success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }
}
