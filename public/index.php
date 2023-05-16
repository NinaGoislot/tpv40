<?php

use App\Kernel;

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'project_dir' => dirname(__DIR__)
];

if( ! file_exists(getenv("USERPROFILE").'/symfony/'))
	require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
else
	require_once getenv("USERPROFILE").'/symfony/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
