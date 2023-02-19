<?php
/**
* 2020 C. Hodor - OS Private Community
*/

ini_set('display_errors', 1);
error_reporting(E_ALL);

require(dirname(__FILE__).'/config/config.php');

Dispatcher::init()->load(Tools::getValue('p'));
