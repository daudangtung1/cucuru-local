<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CategoryService extends BaseService
{
    /**
     * @param $id
     * @param bool $strict
     * @return Category
     */
    public function getById($id, $strict = true)
    {
        $category = $strict ? Category::find($id) : Category::findOrFail($id);

        return $category;
    }

    public function get($limit)
    {
        $categories = Category::paginate($limit);

        return [
            'data' => $categories->map(function ($category) {
                return $category->only([
                    'id',
                    'title',
                ]);
            }),
            'pagination' => $this->customPagination($categories)
        ];
    }
}
