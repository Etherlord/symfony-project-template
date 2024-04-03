<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
