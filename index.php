<?php
/**
 * WordPress requires index.php in the theme root. The templates live in resources/templates
 * (see app/Setup/TemplateLoader.php), so this only hands over to the list template.
 *
 * @package ArsPetal
 */

defined('ABSPATH') || exit;

require __DIR__ . '/resources/templates/index.php';
