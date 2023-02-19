<!DOCTYPE html>

<html lang="ro">
    <head>
        <base href='<?php Tools::base(); ?>'>

        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title><?php Meta::init()->load('title',self::$page); ?></title>
        <!-- resursa stil-->
        <link rel="stylesheet" href="themes/main/assets/css/system.css">
        <link rel="stylesheet" href="themes/main/assets/css/elemente/top-bar_menu.css">

<?php 

switch(self::$page)
{
    case 'index': 
    ?>
        <link rel="stylesheet" href="themes/main/assets/css/main.css">
        <link rel="stylesheet" href="themes/main/assets/css/elemente/modal-postare.css">  
    <?php 
    break;

    case 'post':
    ?>
        <link rel="stylesheet" href="themes/main/assets/css/main.css">
    <?php        
    break;

    case 'admin':
        ?>
        <script type="text/javascript" src="core/plugins/jquery/jquery.js"></script>    
    <?php 
    break;

    case 'login': 
    ?>
        <link rel="stylesheet" href="themes/main/assets/css/elemente/conectare_recuperare.css">
    <?php 
    break;

    case 'register': 
    ?>
        <link rel="stylesheet" href="themes/main/assets/css/elemente/conectare_recuperare.css">
        <link rel="stylesheet" href="themes/main/assets/css/elemente/inregistrare.css">
    <?php 
    break;

    case 'account': 
    ?>
        <link rel="stylesheet" href="themes/main/assets/css/elemente/cont.css">
        <script type="text/javascript" src="core/plugins/jquery/jquery.js"></script>   
    <?php 
    break;

    case 'profile': 
    ?>
        <link rel="stylesheet" href="themes/main/assets/css/elemente/cont.css">
        <script type="text/javascript" src="core/plugins/jquery/jquery.js"></script>   
    <?php 
    break;}?>
        <script type="text/javascript" src="core/plugins/jquery/jquery.js"></script>  
        <!--propietati meta -->
        <meta name="description" content="<?php Meta::init()->load('description',self::$page); ?>" />
        <!--robots meta https://developers.google.com/search/reference/robots_meta_tag -->
        <meta name="robots" content="index, follow, max-snippet: -1, max-image-preview:large, max-video-preview: -1" />
        <link rel="canonical" href="" />
        <!--og meta tags-->
        <meta property="og:locale" content="ro" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?php Meta::init()->load('title',self::$page); ?>" />
        <meta property="og:description" content="<?php Meta::init()->load('description',self::$page); ?>" />
        <meta property="og:url" content="<?php Tools::getLink(); ?>" />
        <meta property="og:site_name" content="<?php Meta::init()->load('title','site'); ?>" />
        <meta property="og:image" content="images//hom-banner-compressed.jpg" />
        <meta property="og:image:secure_url" content="images//hom-banner-compressed.jpg" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="660" />
        <!--twitter meta-->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:description" content="<?php Meta::init()->load('description',self::$page); ?>" />
        <meta name="twitter:title" content="<?php Meta::init()->load('title',self::$page); ?>" />
        <meta name="twitter:site" content="<?php Tools::getLink(); ?>" />
        <meta name="twitter:image" content="images/hom-banner-compressed.jpg" />
        <meta name="twitter:creator" content="<?php Meta::init()->load('title','site'); ?>" />
        <!--og meta locatie-->
        <meta name="og:country-name" content="Romania" />
        <!--validare motoare de cautare-->
        <meta name="google-site-verification" content="" />
        <meta name="yandex-verification" content="" />
        <!--tag copyright-->
        <meta name="generator" content="<?php Meta::init()->load('title','site'); ?>" />
        <!-- favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/themes/main/assets/icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/themes/main/assets/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/themes/main/assets/icons/favicon-16x16.png">
        <link rel="manifest" href="/themes/main/assets/icons/site.webmanifest">
        <link rel="mask-icon" href="/themes/main/assets/icons/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="/themes/main/assets/icons/favicon.ico">
        <meta name="apple-mobile-web-app-title" content="PHP OS">
        <meta name="application-name" content="PHP OS">
        <meta name="msapplication-TileColor" content="#f2f2f2">
        <meta name="msapplication-config" content="/themes/main/assets/icons/browserconfig.xml">
        <meta name="theme-color" content="#f7f7f7">
        <!--lista meta tags - https://gist.github.com/lancejpollard/1978404 -->
    </head>