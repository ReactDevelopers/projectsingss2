<?php

return [
    'site-name' => [
        'description' => 'core::settings.site-name',
        'view' => 'text',
        'translatable' => true,
    ],
    'site-description' => [
        'description' => 'core::settings.site-description',
        'view' => 'textarea',
        'translatable' => true,
    ],
    'site-keywords' => [
        'description' => 'core::settings.site-keywords',
        'view' => 'text',
        'translatable' => true,
    ],
    'template' => [
        'description' => 'core::settings.template',
        'view' => 'core::fields.select-theme',
    ],
    'google-analytics' => [
        'description' => 'core::settings.google-analytics',
        'view' => 'textarea',
        'translatable' => false,
    ],
    'locales' => [
        'description' => 'core::settings.locales',
        'view' => 'core::fields.select-locales',
        'translatable' => false,
    ],
    'link-android-app' => [
        'description' => 'core::settings.link-android-app',
        'view' => 'text',
        'translatable' => false,
    ],
    'link-ios-app' => [
        'description' => 'core::settings.link-ios-app',
        'view' => 'text',
        'translatable' => false,
    ],
    'link-social-facebook' => [
        'description' => 'core::settings.link-social-facebook',
        'view' => 'text',
        'translatable' => false,
    ],
    'link-social-twitter' => [
        'description' => 'core::settings.link-social-twitter',
        'view' => 'text',
        'translatable' => false,
    ],
    'link-social-instagram' => [
        'description' => 'core::settings.link-social-instagram',
        'view' => 'text',
        'translatable' => false,
    ],
    'link-social-pinterest' => [
        'description' => 'core::settings.link-social-pinterest',
        'view' => 'text',
        'translatable' => false,
    ],
];
