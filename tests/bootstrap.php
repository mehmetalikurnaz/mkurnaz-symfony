<?php

use Symfony\Component\Dotenv\Dotenv;

require \dirname(__DIR__) . '/vendor/autoload.php';

$bootstrapFile = \dirname(__DIR__) . '/config/bootstrap.php';
$envFile = \dirname(__DIR__) . '/.env';

if (file_exists($bootstrapFile)) {
    require $bootstrapFile;
} elseif (class_exists(Dotenv::class) && method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv($envFile);
}
