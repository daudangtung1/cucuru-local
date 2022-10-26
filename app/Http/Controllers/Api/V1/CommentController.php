<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comment;
use App\Models\Post;
use App\Utils\AppConfig;
use Illuminate\Http\Request;
use App\Services\CommentService;
use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Validator;

class CommentController extends ApiController
{
    /**
     * @var CommentService
     */
    protected $commentService;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->commentService = new CommentService();
    }

    public function index(Request $request, $postId)
    {
        $pageNo = $this->getValidPageNo($request->input('page'));
        $limit = $this->getValidLimit($request->input('limit'), self::DEFAULT_LIMIT);

        $comments = $this->commentService->get($postId, $limit, $pageNo);

        if (isset($comments['error'])) {
            return $this->responseFail($comments['error']);
        }

        $this->customPagination($comments['pagination']);
        return $this->responseSuccess($comments['data']);
    }

    public function store(Request $request)
    {
        try {
            if (!$this->customValidate($request, [
                'content' => 'required',
                'commentable_id' => 'required',
                'commentable_type' => 'required|in:' . implode(',',  array_flip(Comment::COMMENT_TYPE)),
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $commentData = $request->only('commentable_id', 'content', 'commentable_type');
            $this->transactionStart();
            $comment = $this->commentService->create($commentData);

            if ($comment['error']) {
                return $this->responseFail($comment['error']);
            }

            return $this->responseSuccess($comment, trans('comment.message.create_success'));
        } catch (CustomException $e) {
            return $this->responseFail($e);
        }
    }

    public function delete($id)
    {
        $comment = $this->commentService->getById($id, false);

        if (Gate::forUser(Auth::guard('api')->user())->denies('is-owner', $comment)) {
            return $this->responseFail(trans('comment.message.can_not_delete'), "", Response::HTTP_FORBIDDEN);
        }

        $result = $this->commentService->delete($comment);

        if ($result) {
            return $this->responseSuccess([], __('comment.message.delete_success'));
        }

        return $this->responseFail(__('comment.message.delete_fail'));
    }

    public function index_post($postId)
    {
        $cmts = $this->commentService->getCmtByPost($postId);
        return response(['cmts' => $cmts['data']], '200');
    }

    public function create_cmt(Request $request)
    {
        $request->only(['content', 'user_id', 'commentable_type', 'commentable_id']);
        $data_validate = $this->validate($request, [
            'content' => 'required',
            'commentable_id' => 'required',
            'commentable_type' => 'required|in:' . implode(',',  array_flip(Comment::COMMENT_TYPE)),
        ]);
        if ($data_validate) return false;
        $this->commentService->getCmtByPost($data_validate);
    }
}
