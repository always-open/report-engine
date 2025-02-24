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
    'locale' => [
        'default' => 'en_us',
    ]
];
