<?php
/**
* 2020 C. Hodor - OS Private Community
*/

if (!defined('_ROOT_DIR'))
{
    define('_ROOT_DIR', realpath($localDir . '/..'));
}

if (!defined('_CORE_DIR'))
{
    define('_CORE_DIR', realpath($localDir . '/..'));
}

define('_THEMES_DIR', _ROOT_DIR . '/themes/');

if (!defined('_ADMIN_DIR'))
{
    define('_ADMIN_DIR', _ROOT_DIR . '/admin/');
}

if (!defined('_CACHE_DIR'))
{
    define('_CACHE_DIR', _ROOT_DIR . '/cache/');
}

if (!defined('_AUTOLOAD_DIR'))
{
    define('_AUTOLOAD_DIR', _CORE_DIR . '/classes/autoload.php');
}

if (!defined('_CLASS_DIR'))
{
    define ('_CLASS_DIR', _ROOT_DIR . '\\classes\\');
}

if (!defined('_BASE_URI'))
{
    define ('_BASE_URI', 'https://');
}

if (!defined('_DOMAIN_URL') && isset($_POST['SERVER_NAME']))
{
    define ('_DOMAIN_URL', $_POST['SERVER_NAME']);
}

if (!defined('_DS'))
{
    define('_DS', DIRECTORY_SEPARATOR);
}

if (!defined('_DIR'))
{
    define('_DIR', __DIR__);
}

define('_CONFIG_DIR', _CORE_DIR . '/config/');

// Settings PHP
define('_REGEX_PATTERN', '(.*[^\\\\])');
define('_MIN_TIME_GEN_PSWD','360');

if (!defined('_DB_PRECISION'))
{
    define('_DB_PRECISION', 6);
}