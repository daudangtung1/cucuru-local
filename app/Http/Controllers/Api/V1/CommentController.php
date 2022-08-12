<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\CommentService;
use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;

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

    public function store(Request $request)
    {
        try {
            if (!$this->customValidate($request, [
                'content' => 'required',
                'commentable_id' => 'required',
                'commentable_type' => 'required|in:' . implode(',', Comment::COMMENT_TYPE),
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
}
