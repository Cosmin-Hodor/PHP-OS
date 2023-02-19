<?php
/**
* 2020 C. Hodor - OS Private Community
*/

session_name('PHPOsesiunea');
session_start();

// Definitii folosite pentru sesiunea userului.
$GLOBALS['config'] = array
(
    'remember' => array
    (
        'cookie_name' => 'negresa_hash',
        'cookie_expiry' => 2592000
    ),

    'session' => array
    (
        'session_name' => 'user',
        'token_name' => 'token'
    )
);

$startTime = microtime(true);

$localDir = dirname(__FILE__);

require_once ($localDir.'/defines.php');

// Definim charsetul in php.ini.
ini_set('default_charset', 'utf-8');

// Rectificam charsetul pentru Apache. (In unele cazuri e prea tarziu)
if (!headers_sent())
{
    header('Content-Type: text/html; charset=utf-8');
}

// Integram setarile & autoload.
require_once(_ROOT_DIR . '/config/db_config.php');
require_once(_ROOT_DIR . '/config/encrypt_keys.php');
require_once(_AUTOLOAD_DIR);

spl_autoload_register([Autoloader::init(), 'load']);

// Initializam metoda de raportare erori.
//ErrorHandler::getInstance()->init();

// Redefinim REQUEST_URI daca este gol (Posibil pe unele servere)
if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI']))
{
    if (!isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME']))
    {
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_FILENAME'];
    }

    if (isset($_SERVER['SCRIPT_NAME']))
    {
        if (basename($_SERVER['SCRIPT_NAME']) == 'index.php' && empty($_SERVER['QUERY_STRING']))
        {
            $_SERVER['REQUEST_URI'] = dirname($_SERVER['SCRIPT_NAME']) . '/';
        } else 
        {
            $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
            if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
            {
                $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
    }
}

// Setam limita de upload a unui fisier la 100M
if (Tools::convertBytes(ini_get('upload_max_filesize')) < Tools::convertBytes('100M'))
{
    ini_set('upload_max_filesize', '100M');
}

// Redefinim HTTP_HOST in caz ca este gol
if (!isset($_SERVER['HTTP_HOST']) || empty($_SERVER['HTTP_HOST']))
{
    $_SERVER['HTTP_HOST'] = @getenv('HTTP_HOST');
}

// $db = Connect::init()->update('users', 2, array(
//     'Username' => '_bits2',
//     'Password' => 'Kek',
//     'Recovery_String' => 'Kek2',
//     'Is_Active' => '1',
//     'Is_Reported' => '0',
//     'Is_Blocked' => '0',
//     'Role' => '3'
// ));

// $db = Connect::init();

// $db->get('*','users');

// if (!$db->count())
// {
//     echo 'No user';
// } else
// {
//     foreach($db->results() as $user)
//     {
//         echo $user->Username, '<br>';
//         echo $user->Password, '<br>';
//     }
// }