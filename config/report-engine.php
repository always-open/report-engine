<?php

$multi_formats = [
    'html',
    'json',
    'config',
    'report',
];

if (app()->environment() !== 'production') {
    $multi_formats[] = 'explain';
    $multi_formats[] = 'sql';
}

return [
    'allowed_multi_formats' => $multi_formats,
    'default_format' => 'html',
    'locale' => [
        'default' => 'en_us',
    ]
];
