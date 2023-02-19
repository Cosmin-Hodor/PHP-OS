<div class="inregistrare">
    <section class="conectare">
        <div class="bloc_conectare">
            <form action="/inregistrare" method="post">
                <input type="email" name="email" placeholder="Email">
                <input type="text" name="username" placeholder="Nume Utilizator" value="<?php echo Tools::getValue('username'); ?>" title="Nume utilizator." required>
                <div class="show_password_hug">
                    <input type="password" id="parola" name="password" placeholder="Parola" title="Parola de acces." required>
                    <div id="arata_parola"></div>
                </div>
                <div class="tooltip_hug">
                    <input type="text" name="recovery_string" placeholder="Cuvinte cheie" title="Cuvinte cheie pentru recuperarea contului." required>
                    <span class="tooltip_text">Unul sau mai multe cuvinte ce vor genera un sir de caractere.</span>
                </div>
                <input type="text" name="cod_invitare" placeholder="Cod Invitare" value="<?php echo Tools::getValue('cod_invitare'); ?>" title="Codul de invitare." required>
                <input type="hidden" name="token" value="<?php Token::generate(); ?>">
                <input type="submit" name="Inregistrare" value="Inregistrare">
            </form>
            <?php if (self::isPost()) AuthController::init()->getErrors(); ?>
        </div>
        <p>(*) Cuvintele cheie sunt necesare pentru recuperarea contului in caz ti-ai uitat parola.<br></p>
    </section>
</div>