<?php

defined('ABSPATH') || exit;

// PSR-4 autoloader: Jiejia\ArsPetal\Foo\Bar => app/Foo/Bar.php
spl_autoload_register(static function (string $class): void {
    $prefix = 'Jiejia\\ArsPetal\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = ARS_PETAL_DIR . 'app/' . $relative . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
