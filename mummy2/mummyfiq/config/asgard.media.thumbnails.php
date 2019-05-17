<?php

return [
    'smallThumb' => [
        'resize' => [
            'width' => 50,
            'height' => null,
            'callback' => function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            },
        ],
    ],
    'mediumThumb' => [
        'resize' => [
            'width' => 180,
            'height' => null,
            'callback' => function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            },
        ],
    ],
    'resizeThumb' => [
        'resize' => [
            'width' => 500,
            'height' => null,
            'callback' => function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            },
        ],
    ],
    'largeThumb' => [
        'resize' => [
            'width' => 1000,
            'height' => null,
            'callback' => function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            },
        ],
    ],
];
