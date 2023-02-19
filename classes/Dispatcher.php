<?php
/**
* 2020 C. Hodor - Open Source Community Platform
*/

class DispatcherCore
{
     /**
     * @var Dispatcher
     */
     private static $instance;

    /**
     * @var Theme (Deprecated)
     */
    // protected $theme = _ROOT_DIR . _DS . 'themes/main/load.php';

    /**
     * @var partials Caile catre elementele partiale.
     */
    protected $partials = array(
        'head' => 'themes/main/pages/partials/head.php',
        'header' => 'themes/main/pages/partials/header.php',
        'footer' => 'themes/main/pages/partials/footer.php'
    );

    /**
     * @var pages Caile catre pagini.
     */
    protected $pages = array(
        'index' => 'themes/main/pages/index/page.php',
        'login' => 'themes/main/pages/account/login.php',
        'logout' => 'themes/main/pages/account/logout.php',
        'register' => 'themes/main/pages/account/register.php',
        'account' => 'themes/main/pages/account/account.php',
        'profile' => 'themes/main/pages/account/profile.php',
        'post' => 'themes/main/pages/index/post.php',
        'admin' => 'admin/dashboard.php',
        'notifications' => 'themes/main/pages/account/notifications.php'
    );

    /**
     * @var page Pagina pe care se afla client-ul.
     */
    protected static $page;

    /**
     * @var user Obiectul utilizatorului nostru.
     */
    protected $user;

    /**
     * @var user_profile Obiectul utilizatorului vizitat.
     */
    protected $user_profile;

    /**
     * @var root_dir Calea relativa de la root la fisier.
     */
    protected $root_dir = _ROOT_DIR . _DS;

    /**
     * @var connected Este client-ul conectat la contul sau? (True/False)
     */
    protected $connected;

    /**
    * @var id ID-ul unui anumit obiect.
    */

    // [Todo] - Auto set the $this->theme from DB
    private function __construct()
    {
        if (Session::exists(Session::setup('session/session_name')))
        {
            $this->user = new Users(Session::get(Session::setup('session/session_name')));

            switch($this->user->banned())
            {
                case '0':
                    $this->connected = true;

                    if (isset($_POST['update']) && Token::check(Tools::getValue('token')))
                    {
                        AccountController::init()->update();
                    }

                    if (isset($_POST['postare']) && Token::check(Tools::getValue('token')))
                    {
                        PostController::init()->post();
                    }
                break;

                case '1':
                    $this->user->logout();
                    $this->connected = false;
                break;
            }
        }

        if(!$this->connected)
        {
            AuthController::init()->auth();
        }
    }

    public static function init()
    {
        if (!self::$instance)
        {
            self::$instance = new DispatcherCore();
        }
        
        return self::$instance;
    }

    public function load($location)
    {
        switch ($location)
        {
            case 'home':
                try
                {
                    ($this->connected) ? self::$page = 'index' : self::$page = 'login';
                    $this->load_page('index');
                } catch (Exception $e)
                {
                    echo '<h1>EROARE: Pagina nu poate fi servita. Te rugam sa ne contactezi la <MAILUL TAU></h1>';
                }
            break;

            case 'login':
                try
                {
                    (!$this->connected) ? self::$page = 'login' : self::$page = 'index';
                    $this->load_page('login');
                } catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            case 'logout':
                try
                {
                    $this->connected = false;
                    self::$page = 'logout';

                    if (Cookie::exists(Session::setup('remember/cookie_name')))
                    {
                        $user = new Users(Session::get(Session::setup('session/session_name')));
                    } else
                    {
                        $user = new Users;
                    }

                    $user->logout();
                    Tools::redirect('/');                     
                } catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            case 'register':
                try
                {
                    (!$this->connected) ? self::$page = 'register' : self::$page = 'index';
                    $this->load_page('register');
                } catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            case 'admin':
                try
                {
                    ($this->connected) ? self::$page = 'admin' : self::$page = 'login';

                    if ($this->user->isAdmin())
                    {
                        $this->load_page('admin');                        
                    } else
                    {
                        $this->load_page('index');
                    }

                } catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            case 'account':
                try
                {
                    ($this->connected) ? self::$page = 'account' : self::$page = 'login';
                    $this->load_page('account');
                } catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            case 'profile':
                try
                {
                    if (isset($_GET['u']) && isset($_GET['p']))
                    {
                        $this->user_profile = new Users(Tools::getValue('u'));
   
                        if (!$this->user_profile->exists())
                        {
                            Tools::redirect('/');
                        }
                    }

                    ($this->connected) ? self::$page = 'profile' : self::$page = 'login';
                    $this->load_page('profile');

                } catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            case 'post':
                try
                {
                    if (isset($_GET['p']) && isset($_GET['id']))
                    {
                        ($this->connected) ? self::$page = 'post' : self::$page = 'index';
                        $this->id = Tools::getValue('id');
                        $this->load_page('post');
                    }
                }   catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            case 'notifications':
                try
                {
                    ($this->connected) ? self::$page = 'notifications' : self::$page = 'login';
                    $this->load_page('notifications');
                } catch (Exception $e)
                {
                    header('Expires: Sun, 25 Feb 1996 23:50:00 GMT');
                    header('Location: /conectare');
                    exit;
                }
            break;

            default: 
            try
            {
                ($this->connected) ? self::$page = 'index' : self::$page = 'login';
                $this->load_page('index');
            } catch (Exception $e)
            {
                echo '<h1>EROARE: Pagina nu poate fi servita. Te rugam sa ne contactezi la <MAILUL TAU></h1>';
            }
        }
    }

    private function load_page($page)
    {        
        require_once $this->partials['head'];
        require_once $this->partials['header'];

        switch ($page)
        {
            case 'index':
                require_once ($this->connected) ? $this->pages[$page] : $this->pages['login'];
            break;

            case 'home':
                require_once ($this->connected) ? $this->pages['index'] : $this->pages['login'];
            break;

            case 'login':
                require_once (!$this->connected) ? $this->pages[$page] : $this->pages['index'];
            break;

            case 'register':
                require_once (!$this->connected) ? $this->pages[$page] : $this->pages['index'];
            break;

            case 'account':
                require_once ($this->connected) ? $this->pages[$page] : $this->pages['login'];
            break;

            case 'profile':
                require_once ($this->connected) ? $this->pages[$page] : $this->pages['login'];
            break;

            case 'logout':
                require_once ($this->connected) ? $this->pages[$page] : $this->pages['logout'];
            break;

            case 'post':
                require_once ($this->connected) ? $this->pages[$page] : $this->pages['index'];
            break;

            case 'admin':
                require_once ($this->connected) ? $this->pages[$page] : $this->pages['login'];
            break;

            case 'notifications':
                require_once ($this->connected) ? $this->pages[$page] : $this->pages['login'];
            break;
        }

        require_once $this->partials['footer'];
    }

    public static function isPost()
    {
        if (isset($_POST['Inregistrare']) || isset($_POST['Conectare']) || isset($_POST['update']))
        {
          return true;
        }
        return false;
    }

    public static function getPage($request)
    {
        return (self::$page == $request) ? true : false;
    }

    public function getJs()
    {
        switch (self::$page)
        {
            case 'index':
  echo '
        <script>
            var expeditor_id = '. $this->user->user('id', 1) .';
            var token_general = $("#token_general").val();
        </script>
        <script type="text/javascript" src="core/js/load.js"></script>
        <script type="text/javascript" src="core/js/post_modal.js"></script>
        <script type="text/javascript" src="core/js/trimite_postare.js"></script>
        <script type="text/javascript" src="core/js/incarca_postari.js"></script>
        <script type="text/javascript" src="core/js/votare.js"></script>';
            break;

            case 'post':
  echo '
        <script>
            var expeditor_id = '. $this->user->user('id', 1) .';
            var token_general = $("#token_general").val();
        </script>
        
        <script type="text/javascript" src="core/js/votare.js"></script>
        <script type="text/javascript" src="core/js/reactie.js"></script>';
            break;

            case 'account':
  echo '<script type="text/javascript" src="core/js/functii_cont.js"></script>';
            break;

            case 'login':
  echo '<script type="text/javascript" src="core/js/recuperare.js"></script>';
            break;

            case 'register':
  echo '<script type="text/javascript" src="core/js/inregistrare.js"></script>';
            break;
        }
    }

    public static function flash()
    {
        if (Session::exists('succes'))
        {
            echo '<p id="conectare_succes">' . Session::flash('succes') . '</p>';
        } else if (Session::exists('failed')) 
        {
            echo '<p id="actiune_esuata">' . Session::flash('failed') . '</p>';
        }
    }

    public static function connected()
    {
        return $this->connected;
    }
    
    // [Deprecated]
    protected function get_value($mapping, $keys)
    {
        foreach($keys as $key)
        {
            $output_value[] = $mapping[$key];
        }

        $result = implode('', $output_value);
        return $result;
    }

}