<?php
# Путь до пакетов модулей
$path = '/local/firstbit/';

# Подключим модули
spl_autoload_register(function ($className) use ($path) {
    if (strstr($className, 'modules\\')) {
        $path = sprintf(
            '%s%s%s.php',
            $_SERVER["DOCUMENT_ROOT"],
            $path,
            str_replace(
                '\\',
                '/',
                $className
            )
        );

        if (file_exists($path)) {
            require_once $path;
        }
    }
});