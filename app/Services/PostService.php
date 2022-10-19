<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Media;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            $postData['created_by'] = Auth::guard('api')->id();

            if (empty($postData['published_at'])) {
                $postData['published_at'] = Carbon::now();
            }

            $post = Post::create($postData);
            $this->saveMedia($post, $postData);

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
            if (isset($postData['delete_medias'])) {
                $deleteMedias = Media::whereIn('id', $postData['delete_medias'])->get();

                foreach ($deleteMedias as $deleteMedia) {
                    Storage::disk('s3')->delete($deleteMedia->link);
                }

                Media::destroy($postData['delete_medias']);
            }

            $post->update(array_filter($postData));
            $this->saveMedia($post, $postData);

            return $post->refresh();
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

    private function saveMedia($post, $postData) {
        if (isset($postData['medias'])) {
            $alias = $post->getMorphClass();

            foreach ($postData['medias'] as $media) {
                $mediaType = get_file_type($media);
                if (is_null($mediaType)) continue;

                $mediaName = $media->getClientOriginalName();
                if (!$media->storeAs("posts/$post->id", $mediaName, 's3')) continue;
                Media::create([
                    "mediatable_type" => $alias,
                    "mediatable_id" => $post->id,
                    "link" => "/posts/$post->id/$mediaName",
                    "type" => Media::MIMETYPE[$mediaType],
                ]);
            }
        }

    }
}
