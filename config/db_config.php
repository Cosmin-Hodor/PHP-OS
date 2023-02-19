<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/** 
* db_config contine toate setarile necesare pentru baza de date.
* @since 1.0 
*/

class ConnectionSetup
{
    private static $instance;

    protected 
    $serverName,
    $dbName,
    $userName,
    $userPass;

    private function __construct()
    {
        $this->serverName = "127.0.0.1";
        $this->dbName = "phpos";
        $this->userName = "root";
        $this->userPass = "";
    }

    public static function database()
    {
        if (!self::$instance)
        {
            self::$instance = new ConnectionSetup;
        }

        return self::$instance;
    }
}