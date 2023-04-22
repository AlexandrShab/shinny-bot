<?php

spl_autoload_register(function (string $class) {
    $path = __DIR__ . "/classes/$class.php";
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
    if (is_readable($path)) {
        require $path;
    } 
});
