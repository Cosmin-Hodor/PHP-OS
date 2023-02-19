<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/**
* Class Metas
* @since 1.0 
*/

Class MetasCore
{
    /**
     * @var MetasCore
     */
    protected static $instance;

    /**
     * @var Title Titlul paginii pe care se afla clientul.
     */
    protected $title = [];

    /**
     * @var Description Descrierea paginii pe care se afla clientul.
     */
    protected $description = [];

    /**
     * @var Keywords Cuvintele cheie a le paginii pe care se afla clientul.
     */
    protected $keywords = [];

    /**
     * @var Page Pagina clientului.
     */
    protected $page;

    protected function __construct()
    {
        $this->title = 
        [
            'index' => 'PHP OS | Comunitate Online',
            'login' => 'PHP OS | Conecteaza-te',
            'logout' => 'PHP OS | Deconectare',
            'register' => 'PHP OS | Participa',
            'account' => 'PHP OS | Contuar',
            'profile' => 'PHP OS | Profil',
            'admin' => 'PHP OS | Administrare',
            'post' => 'PHP OS | Vizualizare',
            'site' => 'PHP OS'
        ];

        $this->description = 
        [
            'index' => 'PHP OS este menit sa ofere o platforma online ce permite exprimarea libera si accesul la informatii.',
            'login' => 'Intra in contul tau de PHP OS, participa comunitatii.',
            'logout' => 'Deconecteaza-te din contul tau de PHP OS.',
            'register' => 'Participa in comunitatea PHP OS',
            'account' => 'Pagina contului tau iti permite sa modifici imaginea de profil, setarile preferentiale si multe altele.',
            'profile' => 'Profilul de PHP OS al unui utilizator.',
            'admin' => 'Pagina de administrare pentru PHP OS',
            'post' => 'Vizualizeaza acest thread.'
        ];

        $this->keywords = 
        [
            'index' => 'PHP OS, Comunitate Online, Memeuri, Documentatie',
            'login' => 'Conectare',
            'logout' => 'Deconectare',
            'register' => 'Inregistrare',
            'profile' => 'Profil',
            'post' => 'thread, topic, post'
        ];
    }

    /**
     * Returneaza tagurile meta.
     * 
     * @since 1.0
     * @version 1.0 Versiunea Initiala
     */
    public function load(string $key, string $clientPage)
    {
        $this->page = $clientPage;

        switch ($key)
        {
            case 'title':
                foreach ($this->title as $page => $title)
                {
                    if ($page == $this->page)
                    {
                        echo $title;
                    }
                }
            break;

            case 'description':
                foreach ($this->description as $page => $description)
                {
                    if ($page == $this->page)
                    {
                        echo $description;
                    }
                }
            break;

            case 'keywords':
                foreach ($this->keywords as $page => $keywords)
                {
                    if ($page == $this->page)
                    {
                        echo $keywords;
                    }
                }
            break;
        }
    }

    /**
     * Returneaza instanta clasei Metas
     * 
     * @var MetasCore Obiectul instantei
     * 
     * @since 1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function init()
    {
        if (!self::$instance)
        {
            self::$instance = new MetasCore;
        }
        return self::$instance;
    }
}
