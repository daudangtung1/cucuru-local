<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentService extends BaseService
{
    /**
     * @param $id
     * @param bool $strict
     * @return Comment
     */
    public function getById($id, $strict = true)
    {
        $post = $strict ? Comment::find($id) : Comment::findOrFail($id);

        return $post;
    }

    /**
     * @param $postData
     * @return mixed
     * @throws CustomException
     */
    public function create($commentData)
    {
        try {
            $commentData['created_by'] = Auth::guard('api')->id();

            if ($commentData['commentable_type'] == Comment::COMMENT_TYPE['POST']) {
                $commentData['commentable_type'] = Post::class;
                $post = Post::find($commentData['commentable_id']);
                if (empty($post)) {
                    return ['error' => trans('post.message.post_not_found')];
                }
            }

            $comment = Comment::create($commentData);

            return $comment;
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }
}
