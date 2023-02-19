<div class="perete">

    <!--[if lt IE 7]>
    <p>Folosesti un browser <strong>invechit</strong>. Te rugam <a href="https://www.mozilla.org/en-GB/firefox/">sa-ti actualizezi</a> browser-ul pentru o experienta completa.</p>
    <![endif]-->
<section class="detalii_cont">
    <div class="antent_avatar">
        <div class="avatar_hug">
            <img class="avatar" src="<?php $this->user->user('avatar'); ?>">
        </div>
        <div class="detalii_hug">
            <h2 id="nume_cont" alt="Numele tau de utilizator"><?php $this->user->user('username'); ?></h2>
            <h3 alt="Data de participare">Creat la: <?php $this->user->user('created'); ?></h3>
            <span alt="Rolul tau in grup">Rol in grup: <?php $this->user->user('role'); ?></span>
        </div>
    </div>
</section>

<section class="postari_cont element">
    <h2>Istoric Cont</h2>
    <div class="istoric_cont antent_detalii">
        <div class="hug">
            <button id="istoric_postari"></button>
            <h3>Postari</h3>
        </div>
        <div class="hug">
            <button id="istoric_comentarii"></button>
            <h3>Comentarii</h3>
        </div>
    </div>
</section>

<section class="modificare_cont element" id="panou_administrare_cont">
    <h2>Administrare</h2>
    <form action="" method="post" class="modifica_detalii antent_detalii">
        <div class="actualizare_detalii">
            <input type="text" placeholder="Nume Nou" name="nume_nou">
            <button type="submit" name="update" value="nume_nou" id="actualizare_nume">Trimite</button>
        </div>
        <div class="actualizare_detalii">
            <input type="text" placeholder="Parola Noua" name="parola_noua" id="parola_noua">
            <button type="submit" name="update" value="parola_noua" id="actualizare_parola">Trimite</button>
        </div>
        <div class="actualizare_detalii">
            <input type="text" placeholder="Cuvinte Recuperare" name="cuvinte_recuperare_noi">
            <button type="submit" name="update" value="cuvinte_recuperare_noi" id="actualizare_recovery">Trimite</button>
        </div>
        <div class="actualizare_detalii">
            <input type="text" placeholder="URL Avatar(.jpeg, .jpg, .png, .gif)" name="adresa_avatar">
            <button type="submit" name="update" value="adresa_avatar" id="adresa_avatar">Trimite</button>
        </div>
        <input id="token" type="hidden" name="token" value="<?php Token::generate(); ?>">
    </form>
    <?php if (self::isPost()) AuthController::init()->getErrors(); ?>
</section>
</div>