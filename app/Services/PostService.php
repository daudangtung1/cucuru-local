<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Post;

class PostService extends BaseService
{
    /**
     * @param $limit
     * @param $pageNo
     * @param array $filterData
     * @param array $sortData
     * @return bool|mixed|null
     */
    public function getListPost($limit, $pageNo, array $filterData, array $sortData)
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

        $posts = new Post();

        if (isset($filterData['title'])) {
            $posts = $posts->whereRaw("title LIKE CONCAT('%',CONVERT('{$filterData['title']}', BINARY),'%')");
        }

        if (isset($filterData['status']) && in_array($filterData['status'], [Post::ACTIVE, Post::INACTIVE])) {
            $posts = $posts->where('status', $filterData['status']);
        }

        $posts = $posts->orderBy($fieldSort, $typeSort)->paginate($limit);

        // Đoạn này vẫn tạo bảng pivot và vẫn chọc vào cơ sở dữ liệu nên không thể ném nó ra ngoài controller được.
        $posts = [
            'data' => $posts,
            'pagination' => $this->customPagination($posts)
        ];

        return $posts;
    }

    /**
     * @param $id
     * @param bool $strict
     * @return Post
     */
    public function getById($id, $strict = true)
    {
        $post = $strict ? Post::find($id) : Post::findOrFail($id);

        return $post;
    }

    /**
     * @param $postData
     * @return mixed
     * @throws CustomException
     */
    public function create($postData)
    {
        try {
            // TODO:Vì chưa có auth nên tạm thời để random id
            $postData['user_id'] = rand(1, 5);
            $post = Post::create($postData);

            return $post;
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }

    /**
     * @param $post
     * @param $postData
     * @return mixed
     * @throws CustomException
     */
    public function update($post, $postData)
    {
        try {
            $post->update(array_filter($postData));

            return $post;
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }

    /**
     * @param $post
     * @return mixed
     * @throws CustomException
     */
    public function destroy($post)
    {
        try {
            $post->delete();

            return $post;
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }
}
