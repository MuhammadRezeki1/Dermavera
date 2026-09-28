<?php

return [
    'price_max_age_days' => (int) env('PRICE_MAX_AGE_DAYS', 30),
    'guest_retention_days' => (int) env('GUEST_RETENTION_DAYS', 30),
    'algorithm_version' => 'saw-1.3.0',
];
