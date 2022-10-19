<?php

return [
    'post_image' => [
        'size' => [
            'default' => 'default',
            '600' => '600',
            '300' => '300',
            '150' => '150',
        ]
    ],
    'user' => [
        'user_avatar' => [
            '200' => '200',
            '100' => '100',
            '50' => '50',
        ],
        'default_folder' => env('DEFAULT_AVATAR', 'image/avatar/default'),
    ],
    'author' => [
        'author_avatar' => [
            '200' => '200',
            '100' => '100',
            '50' => '50',
        ],
    ],
    'cover' => [
        'size' => [
            'default' => 'default',
            '1280' => '628',
            '640' => '314',
            '320' => '157',
        ]
    ],
];
