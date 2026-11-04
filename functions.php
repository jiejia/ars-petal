<?php
/**
 * The theme bootstrap file.
 *
 * It defines the theme paths, loads the autoloader and boots the theme (setup, assets,
 * customizer options and the A Ripple Song bridge). Templates in the theme root stay thin:
 * they call the classes in app/ and the partials in resources/templates/.
 *
 * @link    https://github.com/jiejia/ars-petal
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

if (!defined('ARS_PETAL_DIR')) {
    define('ARS_PETAL_DIR', trailingslashit(get_template_directory()));
}

if (!defined('ARS_PETAL_URL')) {
    define('ARS_PETAL_URL', trailingslashit(get_template_directory_uri()));
}

require_once ARS_PETAL_DIR . 'app/autoload.php';

new \Jiejia\ArsPetal\Theme();
