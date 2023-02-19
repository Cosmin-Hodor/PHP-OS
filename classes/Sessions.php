<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/** 
* Sessions Contine toate metodele necesare pentru a instanta/citii/sterge o sesiune.
* @since 1.0 
*/

Class Session
{
    /**
     * Preia configuratiile cerute din GLOBALS.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function setup($path = null)
    {
        if ($path)
        {
            $config = $GLOBALS['config'];
            $path = explode('/', $path);

            foreach($path as $item)
            {
                if (isset($config[$item]))
                {
                    $config = $config[$item];
                }
            }
            return $config;
        }
        return false;
    }

    /**
     * Verifica daca sesiunea actuala exista.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function exists($name)
    {
        return (isset($_SESSION[$name])) ? true : false; 
    }

    /**
     * Adauga data in sesiune.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function put($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    /**
     * Instanteaza o sesiune.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function get($name)
    {
        return $_SESSION[$name];
    }

    /**
     * Sterge o sesiune.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function delete($name)
    {
        if (self::exists($name))
        {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Daca o sesiune exista, va fi stearsa, daca nu exista, va fi creata.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function flash($name, $string = '')
    {
        if (self::exists($name))
        {
            $session = self::get($name);

            self::delete($name);

            return $session;
        }
        else
        {
            self::put($name, $string);
        }
    }
}