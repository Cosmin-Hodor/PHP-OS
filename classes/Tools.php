<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/** 
* ToolsCore contine toate uneltele necesare pentru o normala functionare.
* @since 1.0 
*/

Class ToolsCore
{

    /**
     * Generator de parole random.
     *
     * @param int    $length Marimea dorita (optional)
     * @param string $flag   Ofera parole de tip (NUMERIC, ALPHANUMERIC, NO_NUMERIC, RANDOM)
     *
     * @return bool|string Parola
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function passwdGen($length = 16, $flag = 'ALPHANUMERIC')
    {
        $length = (int) $length;

        if ($length <= 0) return false;

        switch ($flag)
        {
            case 'NUMERIC':
                $str = '0123456789';
            break;

            case 'NO_NUMERIC':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;

            case 'RANDOM':
                $numBytes = (int) ceil(length * 0.96);
                $bytes = static::getBytes($numBytes);

                return substr(rtrim(base64_encode($bytes), '='), 0, $length);
            case 'ALPHANUMERIC':
            default:
                $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;

            $bytes = Tools::getBytes($length);
            $position = 0;
            $result = '';

            for ($i = 0; $i < $length; $i++)
            {
                $position = ($position + ord($bytes[$i])) % strlen($str);
                $result .= $str[$position];
            }

            return Tools::hash($result);
        }
    }

    /**
     * Compresam paginile HTML inainte de a le servi clientului.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function compressHtml($html)
    {
        return preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$html));
    }

    /**
     * Convertim valoarea unei setari PHP in biti.
     *
     * @param string $value Valoare pentru convertit
     *
     * @return int
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function convertBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = (int) substr($value, 0, $value_length - 1);
            $unit = mb_strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }

            return $qty;
        }
    }

    /**
     * Generator de biti random.
     *
     * @param int $length Marimea dorita de biti.
     *
     * @return bool|string Biti random.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function getBytes($length)
    {
        $length = (int) $length;

        if ($length <= 0) return false;

        $result = '';
        $entropy = '';
        $msecPerRound = 400;
        $bitsPerRound = 2;
        $total = $length;
        $hashLength = 20;

        while (strlen($result) < $length) {
            $bytes = ($total > $hashLength) ? $hashLength : $total;
            $total -= $bytes;

            for ($i = 1; $i < 3; $i++) {
                $t1 = microtime(true);
                $seed = mt_rand();

                for ($j = 1; $j < 50; $j++) {
                    $seed = sha1($seed);
                }

                $t2 = microtime(true);
                $entropy .= $t1.$t2;
            }

            $div = (int) (($t2 - $t1) * 1000000);

            if ($div <= 0) {
                $div = 400;
            }

            $rounds = (int) ($msecPerRound * 50 / $div);
            $iter = $bytes * (int) (ceil(8 / $bitsPerRound));

            for ($i = 0; $i < $iter; $i++) {
                $t1 = microtime();
                $seed = sha1(mt_rand());

                for ($j = 0; $j < $rounds; $j++) {
                    $seed = sha1($seed);
                }

                $t2 = microtime();
                $entropy .= $t1.$t2;
            }

            $result .= sha1($entropy, true);
        }

        return substr($result, 0, $length);
    }

    /**
     * Redirectioneaza user-ul catre o pagina.
     *
     * @param string       $url     Adresa de destinatie.
     * @param string       $baseUri Base URI (optional)
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala.
     */
    public static function redirect($url, $baseUri = _BASE_URI)
    {
        if (strpos($url, 'http://'))
        {
            $url_sanitised = substr($url, strlen($baseUri));
            $url = $baseUri . $url_sanitised;
        }

        header('Location: '.$url);
        exit;
    }

    /**
     * Obtine valoarea unui $_POST / $_GET
     * Daca nu este disponibil, ofera o valoare default
     *
     * @param string $key          Value key
     * @param mixed  $defaultValue (optional)
     *
     * @return mixed Value
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
     public static function getValueRaw($key, $defaultValue = false)
     {
         if (!isset($key) || empty($key) || !is_string($key))
         {
             return false;
         }

         return (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));
     }

    /**
     * Obtine valoarea unui $_POST / $_GET intr-un mod sigur
     *
     * @param string $key          Value key
     * @param mixed  $defaultValue (optional)
     *
     * @return mixed Value
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function getValue($key, $defaultValue = false)
    {
        $ret = static::getValueRaw($key, $defaultValue);

        if (is_string($ret))
        {
            return stripslashes(urldecode(preg_replace('/((\%5C0+|(\%00+)))/i', '', urlencode($ret))));
        }
        return $ret;
    }

    /**
     * Sterge un folder impreuna cu subfolderele sale.
     *
     * @param string $dirname Numele folderului.
     * @param bool   $deleteSelf
     *
     * @return bool
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function deleteDirectory($dirname, $deleteSelf = true)
    {
        $dirname = rtrim($dirname, '/') . '/';

        if (file_exists($dirname))
        {
            foreach ($files as $file)
            {
                if ($file != '.' && $file != '..' && $file != '.svn')
                {
                    if (is_dir($dirname.$file))
                    {
                        Tools::deleteDirectory($dirname.$file, true);
                    } else if (file_exists($dirname.$file))
                    {
                        @chmod($dirname.$file, 0777);
                        unlink($dirname.$file);
                    }
                }
            }
        }
    }

    /**
     * Verifica daca un youtube ID exista intr-o postare.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function isVideo($content)
    {
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[\w\-?&!#=,;]+/[\w\-?&!#=/,;]+/|(?:v|e(?:mbed)?)/|[\w\-?&!#=,;]*[?&]v=)|youtu\.be/)([\w-]{11})(?:[^\w-]|\Z)%i';

        return (preg_match($pattern, $content, $match)) ? true : false;
    }

    /**
     * Returneaza ID-ul unui video dintr-un URL youtube.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function youtubeID($url)
    {
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[\w\-?&!#=,;]+/[\w\-?&!#=/,;]+/|(?:v|e(?:mbed)?)/|[\w\-?&!#=,;]*[?&]v=)|youtu\.be/)([\w-]{11})(?:[^\w-]|\Z)%i';

        preg_match($pattern, $url, $match);

        return $match;
    }

    /**
     * Returneaza domain-ul site-ului.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function base()
    {
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        echo $link;
    }

    /**
     * Returneaza locatia actuala unde este executata functia.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function getLink()
    {
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
                $_SERVER['REQUEST_URI']; 
  
        echo $link; 
    }

    /**
     * Sterge un fisier.
     *
     * @param string $file         Adresa fisier
     * @param array  $excludeFiles Exclude fisiere
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function deleteFile($file, $excludeFiles = [])
    {
        if (isset($excludeFiles) && !is_array($excludeFiles)) $excludeFiles = [$excludeFiles] ;
        if (file_exists($file) && is_file($file) && array_search(basename($file), $excludeFiles) === false)
        {
            @chmod($file, 0777);
            unlink($file);
        }
    }

    /**
     * Expune un var dump in consola firebug
     *
     * @param object $object Expune obiectul
     *
     * @param string $type
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function fd($object, $type = 'log')
    {
        $types = ['log', 'debug', 'info', 'warn', 'error', 'assert'];

        if (!in_array($type, $types)) {
            $type = 'log';
        }

        echo '
			<script type="text/javascript">
				console.'.$type.'('.json_encode($object).');
			</script>
		';
    }

    /**
     * Arata o eroare intr-un obiect detaliat
     *
     * @param mixed $object
     * @param bool  $kill
     *
     * @return $object daca $kill = false;
     */
    public static function dieObject($object, $kill = true)
    {
        echo '<xmp style="text-align: left;">';
        print_r($object);
        echo '</xmp><br />';

        if ($kill) {
            die('END');
        }

        return $object;
    }

    /**
     * Alias al lui dieObject - Arata o eroare intr-un obiect detaliat.
     *
     * @param object $object Expune obiectul.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     * @return mixed
     */
    public static function d($object, $kill = true)
    {
        return (Tools::dieObject($object, $kill));
    }

    /**
     * Alias al lui dieObject - Arata o eroare intr-un obiect detaliat fara a intrerupe executia.
     *
     * @param object $object Expune obiectul.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     * @return mixed
     */
    public static function p($object, $kill = false)
    {
        return (Tools::dieObject($object, $kill));
    }

    /**
     * Printeaza erorile unui obiect intr-un log.
     *
     * @see     error_log()
     *
     * @param mixed       $object
     * @param int|null    $messageType
     * @param string|null $destination
     * @param string|null $extraHeaders
     *
     * @return bool
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function error_log($object, $messageType = null, $destination = null, $extraHeaders = null)
    {
        return error_log(print_r($object, true), $messageType, $destination, $extraHeaders);
    }

    /**
     * Hashuieste parolele cu ajutorul password_hash si al cheitei.
     *
     * @param string $password
     *
     * @return bool|string
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function hash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Compara parola hash-uita cu parola normala.
     *
     * @param string $password
     *
     * @return bool|string
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function verify_hash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Genereaza un string aleatoriu ce poate fi folosit.
     *
     * @return bool|string
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function salt($length)
    {
        return random_bytes($length);
    }

    /**
     * Genereaza un string unic bazat pe timp si entropie.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function unique()
    {
        return self::hash(uniqid('', true));
    }

    /**
     * Cripteaza un string
     *
     * @param string $data String ce trebuie criptat
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     * @return string
     */
    public static function encryptIV($data)
    {
        return md5(_COOKIE_KEY.$data);
    }

    /**
     * Sanitize pentru in-out DB fara strip_tags
     * 
     * @param     $string
     * @param int $type
     *
     * @return array|string
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala.
     */
    public static function htmlentitiesUTF8($string, $type = ENT_QUOTES)
    {
        if (is_array($string)) {
            return array_map(['Tools', 'htmlentitiesUTF8'], $string);
        }

        return htmlentities((string) $string, $type, 'utf-8');
    }

    /**
     * Sanitize pentru in-out DB.
     *
     * @param string $string String pentru curatare.
     * @param bool   $html   String ce contine HTML sau nu (optional)
     *
     * @return string String curatat
     *
     * @since   1.0
     * @version 1.0 Versiune Initiala
     */
    public static function safeOutput($string, $html = false)
    {
        if (!$html)
        {
            $string = strip_tags($string);
        }

        return @Tools::htmlentitiesUTF8($string, ENT_QUOTES);
    }

    /**
     * Inlocuieste caracterele speciale cu echivalentul lor.
     *
     * @param string $str
     *
     * @return string
     *
     * @since   1.0.0
     * @version 1.0.0 Initial version
     */
    public static function replaceAccentedChars($str)
    {
        /* One source among others:
            http://www.tachyonsoft.com/uc0000.htm
            http://www.tachyonsoft.com/uc0001.htm
            http://www.tachyonsoft.com/uc0004.htm
        */
        $patterns = [

            /* Lowercase */
            /* a  */
            '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}\x{00C0}-\x{00C3}\x{1EA0}-\x{1EB7}]/u',
            /* b  */
            '/[\x{0431}]/u',
            /* c  */
            '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
            /* d  */
            '/[\x{010F}\x{0111}\x{0434}\x{0110}\x{00F0}]/u',
            /* e  */
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}\x{00C8}-\x{00CA}\x{1EB8}-\x{1EC7}]/u',
            /* f  */
            '/[\x{0444}]/u',
            /* g  */
            '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
            /* h  */
            '/[\x{0125}\x{0127}]/u',
            /* i  */
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}\x{00CC}\x{00CD}\x{1EC8}-\x{1ECB}\x{0128}]/u',
            /* j  */
            '/[\x{0135}\x{0439}]/u',
            /* k  */
            '/[\x{0137}\x{0138}\x{043A}]/u',
            /* l  */
            '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
            /* m  */
            '/[\x{043C}]/u',
            /* n  */
            '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
            /* o  */
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}\x{00D2}-\x{00D5}\x{01A0}\x{01A1}\x{1ECC}-\x{1EE3}]/u',
            /* p  */
            '/[\x{043F}]/u',
            /* r  */
            '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
            /* s  */
            '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
            /* ss */
            '/[\x{00DF}]/u',
            /* t  */
            '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
            /* u  */
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}\x{00D9}-\x{00DA}\x{0168}\x{01AF}\x{01B0}\x{1EE4}-\x{1EF1}]/u',
            /* v  */
            '/[\x{0432}]/u',
            /* w  */
            '/[\x{0175}]/u',
            /* y  */
            '/[\x{00FF}\x{0177}\x{00FD}\x{044B}\x{1EF2}-\x{1EF9}\x{00DD}]/u',
            /* z  */
            '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
            /* ae */
            '/[\x{00E6}]/u',
            /* ch */
            '/[\x{0447}]/u',
            /* kh */
            '/[\x{0445}]/u',
            /* oe */
            '/[\x{0153}]/u',
            /* sh */
            '/[\x{0448}]/u',
            /* shh*/
            '/[\x{0449}]/u',
            /* ya */
            '/[\x{044F}]/u',
            /* ye */
            '/[\x{0454}]/u',
            /* yi */
            '/[\x{0457}]/u',
            /* yo */
            '/[\x{0451}]/u',
            /* yu */
            '/[\x{044E}]/u',
            /* zh */
            '/[\x{0436}]/u',

            /* Uppercase */
            /* A  */
            '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
            /* B  */
            '/[\x{0411}]/u',
            /* C  */
            '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
            /* D  */
            '/[\x{010E}\x{0110}\x{0414}\x{00D0}]/u',
            /* E  */
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
            /* F  */
            '/[\x{0424}]/u',
            /* G  */
            '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
            /* H  */
            '/[\x{0124}\x{0126}]/u',
            /* I  */
            '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
            /* J  */
            '/[\x{0134}\x{0419}]/u',
            /* K  */
            '/[\x{0136}\x{041A}]/u',
            /* L  */
            '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
            /* M  */
            '/[\x{041C}]/u',
            /* N  */
            '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
            /* O  */
            '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
            /* P  */
            '/[\x{041F}]/u',
            /* R  */
            '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
            /* S  */
            '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
            /* T  */
            '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
            /* U  */
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
            /* V  */
            '/[\x{0412}]/u',
            /* W  */
            '/[\x{0174}]/u',
            /* Y  */
            '/[\x{0176}\x{042B}]/u',
            /* Z  */
            '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
            /* AE */
            '/[\x{00C6}]/u',
            /* CH */
            '/[\x{0427}]/u',
            /* KH */
            '/[\x{0425}]/u',
            /* OE */
            '/[\x{0152}]/u',
            /* SH */
            '/[\x{0428}]/u',
            /* SHH*/
            '/[\x{0429}]/u',
            /* YA */
            '/[\x{042F}]/u',
            /* YE */
            '/[\x{0404}]/u',
            /* YI */
            '/[\x{0407}]/u',
            /* YO */
            '/[\x{0401}]/u',
            /* YU */
            '/[\x{042E}]/u',
            /* ZH */
            '/[\x{0416}]/u',
        ];

        // ö to oe
        // å to aa
        // ä to ae

        $replacements = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', 'AE', 'CH', 'KH', 'OE', 'SH', 'SHH', 'YA', 'YE', 'YI', 'YO', 'YU', 'ZH',
        ];

        return preg_replace($patterns, $replacements, $str);
    }
}
