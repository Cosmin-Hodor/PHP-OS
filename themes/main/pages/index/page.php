<div id="modal_postare" class="fundal_modal">
    <div class="modal_postare">
        <div class="antent_modal">
            <button id="inchide_modal" class="buton_close">&times;</button>
        </div>
    <!-- Formularul pentru a trimite postari -->
        <div class="formular_postare">
            <input type="text" name="titlu_postare" id="titlu_postare" placeholder="Titlul Postarii (Optional)">
            <div id="continut_post" name="continut_post"></div>
            <input id="trimite_postare" type="submit" name="postare" value="Trimite">
        </div>
    </div>
</div>

<div class="perete">
    <?php Posts::init()->load(); ?>
    <input id="token_general" type="hidden" value="<?php echo Tools::hash(Keys::get('pepper')); ?>">
</div>