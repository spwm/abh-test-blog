<?php

return [
    'base_url' => getenv('APP_BASE_URL') ?: 'http://localhost',
    'per_page' => (int) (getenv('APP_PER_PAGE') ?: 9),
];
