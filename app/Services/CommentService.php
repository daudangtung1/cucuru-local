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
        return $strict ? Comment::find($id) : Comment::findOrFail($id);
    }

    public function get($postId, $limit, $pageNo)
    {
        $post = (new PostService())->getById($postId);

        if (empty($post)) {
            return ['error' => trans('post.message.post_not_found')];
        }

        $comments = $post->comments()->with([
            'comments',
            'user',
            'comments.user'
        ])->paginate($limit);

        return [
            'data' => $comments->map(function ($comment) {
                return $comment->only([
                    'id',
                    'content',
                    'user',
                    'created_at',
                    'comments'
                ]);
            }),
            'pagination' => $this->customPagination($comments)
        ];
    }

    /**
     * @param $postData
     * @return mixed
     * @throws CustomException
     */
    public function create($commentData)
    {
        try {
            $commentData['user_id'] = Auth::guard('api')->id();

            if ($commentData['commentable_type'] == (new Post)->getMorphClass()) {
                $alias = (new Post)->getMorphClass();
                $commentData['commentable_type'] = $alias;
                $post = Post::find($commentData['commentable_id']);

                if (empty($post)) {
                    return ['error' => trans('post.message.post_not_found')];
                }
            }

            if ($commentData['commentable_type'] == (new Comment())->getMorphClass()) {
                $alias = (new Comment())->getMorphClass();
                $commentData['commentable_type'] = $alias;
                $parentComment = $this->getById($commentData['commentable_id']);

                if (empty($parentComment)) {
                    return ['error' => trans('comment.message.comment_not_found')];
                }

                if ($parentComment->commentable_type === $alias) {
                    $commentData['commentable_id'] = $parentComment->commentable_id;
                }
            }

            return Comment::create($commentData);;
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }

    public function delete($comment)
    {
        try {
            return $comment->delete();
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }

    public function getCmtByPost($postId)
    {
        if (empty(Post::find($postId))) return ['error' => trans('post.message.post_not_found')];
        $cmts['data'] = Post::find($postId)->comments();
        return $cmts;
    }
}
