<?php

spl_autoload_register(function ($className) {
    $directories = array(
        __DIR__ . DIRECTORY_SEPARATOR . 'Controllers',
        __DIR__ . DIRECTORY_SEPARATOR . 'Services',
        __DIR__ . DIRECTORY_SEPARATOR . 'Repositories',
        __DIR__ . DIRECTORY_SEPARATOR . 'Dtos',
        __DIR__ . DIRECTORY_SEPARATOR . 'Database',
        __DIR__ . DIRECTORY_SEPARATOR . 'Helpers',
        __DIR__ . DIRECTORY_SEPARATOR . 'Exceptions',
    );

    foreach ($directories as $directory) {
        $file = $directory . DIRECTORY_SEPARATOR . $className . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});