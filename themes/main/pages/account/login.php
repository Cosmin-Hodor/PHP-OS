<div id="logare">

    <!--[if lt IE 7]>
    <p>Folosesti un browser <strong>invechit</strong>. Te rugam <a href="https://www.mozilla.org/en-GB/firefox/">sa-ti actualizezi</a> browser-ul pentru o experienta completa.</p>
    <![endif]-->

    <section class="conectare">
        <div class="bloc_conectare">

            <form action="" method="post">
                <input type="text" name="username" placeholder="Nume Utilizator" value="<?php echo Tools::getValue('username'); ?>" autocomplete="off">
                <input type="password" name="password" id="camp_parola" placeholder="Parola">
                <input type="hidden" id="camp_recuperare" name="fir_recuperare" placeholder="Cuvinte Recuperare">
                <input type="submit" name="Conectare" value="Conectare">
                <div class="raman_conectat">
                    <input type="checkbox" name="remember_me">
                    <label for="remember_me">Doresc sa raman conectat.</label>
                </div>
                <input type="hidden" name="token" value="<?php Token::generate(); ?>">
            </form>

            <?php if (self::isPost()) AuthController::init()->getErrors(); ?>

            <button id="recuperare_cont">Ti-ai uitat parola?</button>
                <hr>
            <a href="/inregistrare">Creaza un cont de PHP OS!</a>

        </div>
    </section>
</div>