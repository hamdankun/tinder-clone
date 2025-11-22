<?php

return [
    'api' => [
        'title' => 'Tinder Clone API',
        'description' => 'RESTful API for Tinder Clone Application - Discover, Like, and Match with other users',
        'version' => '1.0.0',
        'host' => env('SWAGGER_HOST', 'localhost:8000'),
        'base_path' => '/api',
        'schemes' => [env('SWAGGER_SCHEME', 'http')],
        'consumes' => ['application/json'],
        'produces' => ['application/json'],
    ],
    'routes' => [
        [
            'url' => '/api/documentation',
            'prefix' => 'api',
            'name' => 'swagger',
            'as' => 'l5-swagger.',
        ],
    ],
    'paths' => [
        'use_absolute_path' => true,
        'docs_json' => 'api-docs.json',
        'docs_yaml' => 'api-docs.yaml',
        'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
        'annotations' => [
            base_path('app'),
        ],
    ],
];
