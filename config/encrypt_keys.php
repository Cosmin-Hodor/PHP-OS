<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/** 
* Keys detine cheitele de criptare necesare.
* @since 1.0 
*/

class Keys
{ 
    private const consumer_key = '08414!a03f895#fb4@958463574$1eced48aa7c%188ccad4&40c566e92*2da0d8d#71e152bbef^06f2bb30$a49%c303#fd241*f31!d25a';
    private const encrypt_key = '7b1%dcc8d9#df$2c1a20985!f53@62597eebb6c28^^ab2b1a8%8f6f8e224c89(7c6be7b452536e1a30)e&734#e2*6ed29f51c9d6f5cfa';
    private const cookie_key = '2ceeaebdca6158f&#$?>"2ece1^2ef0)((8a19fda4e4c06545f38b28681fac06812471fa0%d426a7d3#dd76b91003e44baba6edbdf56@';

    /**
     * Obtine cheile de criptare intr-un mod incapsulat.
     * 
     * @since 1.0
     */
    public static function get(string $Key)
    {
        switch ($Key)
        {
            case 'consumer':
                return keys::consumer_key;
            break;

            case 'pepper':
                return keys::encrypt_key;
            break;
            
            case 'cookie':
                return keys::cookie_key;
            break;

            default:
                echo '<script>console.log(\'\%cERROR: Requested key is not valid. (Encryption Keys)\', \'background: red; color: yellow;\')</script>';
        }

    }
}