<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/**
* Class Autoloader
* @since 1.0 
*/

class Autoloader
{
    /**
     * @var Autoloader
     */
    protected static $instance;

    /**
     * @var $root_dir Root relative la calea fisierului.
     */
    protected $root_dir;


    protected function __construct()
    {
        $this->root_dir = _ROOT_DIR . _DS;
    }

    /**
     * Initializeaza o noua instanta Autoloader.
     * 
     * @since 1.0
     * @version 1.0 Versiune Initiala
     */
    public static function init()
    {
        if (!self::$instance)
        {
            self::$instance = new Autoloader();
        }

        return self::$instance;
    }

    /**
     * Incarca clasele, controller-ul si core-ul.
     * 
     * @since 1.0
     * @version 1.0 Versiune Initiala
     */
    public function load()
    {
        $classes = array_merge(
            $this->getClass('classes/'),
            $this->getClass('controller/'),
            $this->getClass('core/')
        );

        foreach ($classes as $file)
        {
            require_once $file;
        }
    }

    /**
     * Obtine clasele recursiv din caile date.
     * 
     * @param string $path Calea relativa de la root -> fisier.
     * 
     * @return array
     * 
     * @since 1.0
     * @version 1.0 Versiune Initiala
     */
    public function getClass(string $path)
    {
        $classes = [];

        foreach (scandir($this->root_dir . $path) as $file)
        {

            if (substr($file, -4) == '.php' && $file !== 'index.php' && $file !== 'Autoload.php' && !in_array($file, $classes, true))
            {
                array_push($classes, $path . $file);
            }

            if (is_dir($this->root_dir . $path . $file) && substr($file, 0, 1) !== '.' && !in_array($file, $classes, true))
            {
                $classes = array_merge($classes, $this->getClass($path . $file .'/'));
            }

        }

        return $classes;
    }

}