<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/** 
* Class ErrorHandler
* @since 1.0 
*/

class ErrorHandlerCore
{
    /**
     * @var ErrorHandler -> Instanta singulara.
     */

    protected static $instance;

    /**
     * @var bool true -> Doar daca functia a fost instantata inainte.
     */

    protected $initialized = false;

    /**
     * @var array -> Lista de erori, avertizari sau notite intampinate in timpul unui request.
     */

    protected $errorMessages = [];

    /**
     * @var object Logger conform PSR
     */

    protected $logger = null;

    /**
     * Instantam ErrorHandlerul
     * 
     * @return ErrorHandlerCore
     * 
     * @since 1.0
     */

    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new ErrorHandlerCore();
        }

        return self::$instance;
    }

    /**
     * Initializam logica pentru ErrorHandler
     * @throws ExceptionError
     * @since 1.0
     */

    public function init()
    {
        if ($this-> initialized)
        {
            throw new ExceptionError('ErrorHandler a fost deja initializat.');
        }
        
        @ini_set('display_errors', 'off');
        @error_reporting(E_ALL | E_STRICT);

        // Setam handlerul pentru exceptii neprinse
        set_exception_handler([$this, 'uncaughtExceptionHandler']);

        // Setam handlerul pentru errori
        set_error_handler([$this, 'errorHandler']);

        // Inregistram shutdown handlerul pentru erorile fatale
        register_shutdown_function([$this, 'shutdown']);

        $this->initialized = true;
    }

    /**
     * Metoda de retur a listei cu erori
     * 
     * @param bool $includeSupported daca este true, lista va detine chiar si erorile care au fost cenzurate folosind operatorul @
     * 
     * @param init $mask mascheaza returul mesajelor -> E_ALL
     * 
     * @return array returneaza lista cu mesajul erorilor
     * 
     * @since 1.0
     */

    public function getErrorMessages( $includeSuppressed = false, $mask = E_ALL )
    {
       if ($this->errorMessages)
       {
           return array_filter($this->errorMessages, function ($error) use ($includeSuppressed, $mask)
           {
               if (!$includeSuppressed && $error['suppressed'])
               {
                   return false;
               }
               return (bool)($error['errno'] & $mask);
           });
       }
        return [];
    }

    /**
     * Metoda de retur a exceptiilor "uncaught"
     * 
     * @param Exceptia $e -> Unchaught Exception
     * 
     * @since 1.0
     */

    public function uncaughtExceptionHandler($e)
    {
        $exception = new ExceptionError($e->getMessage(), $e->getCode(),
                                        null, $e->getTrace(), 
                                        $e->getFile(), $e->getLine());
        $exception->displayMessage();
    }

    /**
     * Error Handler. Inregistreaza erori, avertizari sau notite in lista
     * $errors si le preda catre init.
     *
     * @param int $errno -> Nivelul erorii
     * @param string $errstr -> Mesajul erorii
     * @param string $errfile -> Fisierul in care exista eroarea
     * @param int $errline -> Linia in care eroarea s-a intamplat
     *
     * @return bool
     *
     * @since 1.0
     */

     public function errorHandler($errno, $errstr, $errfile, $errline)
     {
         $suppressed = error_reporting() === 0;
         $file = $errfile;
         $line = $errline;

         $error = 
         [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $file,
            'errline' => $line,
            'suppressed' => $suppressed,
            'type' => static::getErrorType($errno),
            'level' => static::getLogLevel($errno),
         ];

         $this->errorMessages[] = $error;

         if (!$suppressed)
         {
            $this->logMessage($error);
         }

         return $suppressed || static::displayErrorEnabled();
     }

    /**
     * Shutdown Handler ne permite sa reactionam erorilor fatale.
     *
     * @since 1.0
     */

     public function shutdown()
     {
         $error = error_get_last();

        if (static::isFatalError($error['type']))
        {
            $stack = 
            [
                1=>
                [
                    'file' => $error['file'],
                    'line' => $error['line'],
                    'type' => 'Fatal error',
                ]
            ];

            $exception = new ErrorException($error['message'], 0, null,
                                            $stack, $error['file'],
                                            $error['line']);
            $exception->displayMessage();
        }

     }

    /**
     * Setam un logger external.
     * Daca parametul $replay este "true", orice eroare va fi omisa.
     *
     * @param $logger
     * @param bool $replay
     *
     * @since 1.0
     */

     public function setLogger(LoggerInterface $logger, $replay = false)
     {
         $this->logger = $logger;

         if ($replay)
         {
            foreach ($this->getErrorMessages(false) as $errorMessage)
            {
                $this->logMessage($errorMessage);
            }
         }
     }

    /**
     * Trimite mesajul catre loggerul in conformitate cu PSR.
     *
     * @param $msg
     *
     * @since 1.0
     */

     protected function logMessage($msg)
     {
        if (!$this->logger)
        {
            return;
        }

        $message = static::formatErrorMessanger($msg);

        switch ($msg['level'])
        {
            case LogLevel::EMERGENCY:
                $this->logger->emergency($message);
            break;

            case LogLevel::ALERT:
                $this->logger->alert($message);
            break;

            case LogLevel::CRITICAL:
                $this->logger->critical($message);
            break;

            case LogLevel::ERROR:
                $this->logger->error($message);
            break;

            case LogLevel::WARNING:
                $this->logger->warning($message);
            break;

            case LogLevel::NOTICE:
                $this->logger->notice($message);
            break;

            case LogLevel::INFO:
                $this->logger->info($message);
            break;
            
            case LogLevel::DEBUG:
                $this->logger->debug($message);
            break;
        }
     }

    /**
     * Convertim $msg intr-un "string"
     *
     * @param Aria mesajului $msg
     *
     * @return string
     * 
     * @since 1.0
     */

    public static function formatErrorMessage($msg)
    {
        $file = static::normalizeFileName($msg['errfile']);

        return $msg['type'].': '
               .$msg['errstr'].' in '.$file.' at line '.$msg['errline'];
    }

    /**
     * Returneaza numele fisierulul relativ la ROOT
     *
     * @param $file
     *
     * @return string file
     *
     * @since 1.0
     */

    public static function normalizeFileName($file)
    {
        return ltrim(str_replace([_ROOT_DIR, '\\'], ['', '/'], $file), '/');
    }

    /**
     * Returneaza tipul de eroare relativ la nivelul respectiv.
     *
     * @param int $errno -> Nivelul de eroare
     *
     * @return string tipul de eroare
     *
     * @since 1.0
     */

    public static function getErrorType($errno)
    {
        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
                return 'Fatal error';
            case E_USER_WARNING:
            case E_WARNING:
                return 'Warning';
            case E_USER_NOTICE:
            case E_NOTICE:
                return 'Notice';
            case E_USER_DEPRECATED:
            case E_DEPRECATED:
                return 'Deprecation';
            default:
                return 'Unknown error';
        }
    }     

    /**
     * Returneaza daca errno este o eroare fatala.
     * 
     * @return boolean
     * 
     * @since 1.0
     */

     public static function isFatalError($errno)
     {
         return ($errno === E_USER_ERROR || $errno === E_ERROR);
     }

    /**
     * Returneaza o eroare in conformitate cu PSR.
     *
     * @param init $errno -> Nivelul errori
     *
     * @return string Nivelul log-ului
     *
     * @since 1.0
     */

     public static function getLogLevel($errno)
     {
        switch ($errno) 
        {
            case E_USER_ERROR:
            case E_ERROR:
                return 'error';
            case E_USER_WARNING:
            case E_WARNING:
            case E_USER_DEPRECATED:
            case E_DEPRECATED:
                return 'warning';
            case E_USER_NOTICE:
            case E_NOTICE:
                return 'notice';
            default:
                return 'warning'; 
        }
    }

    /**
     * Returneaza "true" daca display_errors este activ.
     *
     * @return boolean
     *
     * @since 1.0
     */

    public static function displayErrorEnabled() 
    {
        $value = @ini_get('display_errors');
        switch (strtolower($value)) 
        {
            case 'on':
            case 'yes':
            case 'true':
            case 'stdout':
            case 'stderr':
            case '1':
                return true;
            case 'off':
            case 'no':
            case '0':
                return false;
            default:
                return (bool) (int) $value;
        }
    }
}