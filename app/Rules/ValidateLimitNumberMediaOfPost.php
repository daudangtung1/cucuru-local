<?php

namespace App\Rules;

use App\Models\Post;
use Illuminate\Contracts\Validation\Rule;

class ValidateLimitNumberMediaOfPost implements Rule
{
    protected $post;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $numberMediaOfPost = $this->post->number_media_of_post;

        return $numberMediaOfPost - count(request()->delete_medias ?? []) + count($value) <= config('filesystems.limit_post_media');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('post.message.validate_number_media_error', ['limit' => config('filesystems.limit_post_media')]);
    }
}
