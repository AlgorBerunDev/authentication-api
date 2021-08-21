<?php

return [
    'access_expired' => env('JWT_ACCESS_EXPIRED', '+60 minutes'),
    'refresh_expired' => env('JWT_REFRESH_EXPIRED', '+90 days'),
];
