<body>
        <!--[if lt IE 7]>
            <p class="browserfericit">Folosești un browser <strong>învechit</strong>. Te rugăm să folosești un <a href="https://www.mozilla.org/en-US/firefox/new/">browser modern</a>.</p>
        <![endif]-->
    <header class="top_bar">
        <nav class="meniu_principal">

            <button id="deschide_meniu" alt="Deschide meniul"></button>

            <button id="logo_PHP-OS" alt="Insigna PHP OS"></button>
            
            <?php if (self::getPage('index')){ ?>
            <button id="arata_formular_postare" alt="Deschide formularul pentru postare"></button>
            <?php } ?>
        </nav>

        <div id="m_l" class="meniu_lateral chrome_scroll">
            <button id="inchide_meniu" class="buton_close" alt="Inchide meniul">&times;</button>
            <?php if($this->connected) {?>
            <a class="cont" href="/cont">
            <img class="avatar_meniu" src="<?php $this->user->user('avatar'); ?>">
            <span class="nume_cont"><?php $this->user->user('username'); ?></span>
            </a>
            <hr>
            <a href="/">Perete</a>
            <a href="/">Contacte</a>
            <a href="/">Grupuri</a>

            <?php if ($this->user->isAdmin()) { ?>
            <a href="/administrare">Admin</a>
            <?php }; ?>
            
            <a href="/deconectare">Logout</a>
            <hr>
            <div class="functii_utilizator">
                <div class="cutie_notificari">
                    <button id="notificari"></button>
                </div>
                <div class="cutie_messenger">
                    <button id="messenger"></button>
                </div>
            </div>
            <hr>
        <?php } else if(!$this->connected) {?>
            <a href="/conectare">Conectare</a>
            <a href="/inregistrare">Inregistrare</a>
            <a href="/despre">Despre</a>
        <?php } ?>
        </div>
    </header>
    <?php self::flash(); ?>

<!-- <iframe id="modal_messenger" src="https://kiwiirc.com/nextclient/?settings=ec6e5043421faf203220fd3832245d85"></iframe> -->

<main id="pagina">
