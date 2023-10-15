<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    if ($context['APP_ENV'] === "prod") {
        if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on')
            && (!isset($_SERVER['REDIRECT_HTTPS']) || $_SERVER['REDIRECT_HTTPS'] != 'on')
            && (!isset($_SERVER['REDIRECT_REDIRECT_HTTPS']) || $_SERVER['REDIRECT_REDIRECT_HTTPS'] != 'on') // this one is the one working....
        ){
            $url = sprintf('https://%s%s', $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
            die(header("Location: $url"));
        }
    }

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
