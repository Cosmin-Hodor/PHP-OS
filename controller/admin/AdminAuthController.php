<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/** 
* Metoda de conectare a administratorului.
* @since 1.0 
*/

class AdminAuthController extends AdminController
{
    public function __construct()
    {
        $this->errors = [];
        $this->display_header = false;
        $this->display_footer = false;
        $this->meta_title = 'Administration Panel';
        $this->css_files = [];
        parent::__construct();
        $this->layout = _ADMIN_DIR . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->admin_themes;
    }
}