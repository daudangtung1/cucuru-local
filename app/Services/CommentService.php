<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Comment;
use App\Models\Post;

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
            // TODO:Vì chưa có auth nên tạm thời để random id
            $commentData['created_by'] = rand(1, 5);

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
