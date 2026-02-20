<?php

use App\Kernel;

require_once dirname(__DIR__).'/config/bootstrap.php';

return function (array $context) {
    return new Kernel(
        $context['APP_ENV'] ?? 'prod',
        (bool) ($context['APP_DEBUG'] ?? false)
    );
};


