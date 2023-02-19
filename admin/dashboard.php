<?php if($this->user->isAdmin()) { ?>
<div class="perete">

    <!--[if lt IE 7]>
    <p>Folosesti un browser <strong>invechit</strong>. Te rugam <a href="https://www.mozilla.org/en-GB/firefox/">sa-ti actualizezi</a> browser-ul pentru o experienta completa.</p>
    <![endif]-->
<h2>Lista Membri</h2>
<section class="lista_useri element">
    <?php foreach($this->user->user_list() as $user){ ?>
        <div class="user">
            <div class="nume">Nume: <a href="profil/<?php echo $user->Username; ?>"><?php echo $user->Username; ?></a> </div>
            <div class="creat">Creat: <?php $date = strtotime($user->Date_Created);
                                    echo date("d/m/y g:i A", $date); ?> </div>
            <div class="last_activ">Vazut: <?php $date = strtotime($user->Last_Seen);
                                    echo date("d/m/y g:i A", $date); ?> </div>
            <div class="reported">Raportat: <?php echo $user->Is_Reported; ?> </div>
            <div class="blocat">Blocat: <?php echo $user->Is_Blocked; ?> </div>
        </div>
    <?php }; ?>
</section>

<h2>Genereaza Invitatii</h2>
<section class="element">
    <div class="generare_invitatii">
        <span>Genereaza invitatii: </span>
        <input type="text" name="numarul_invitatii" id="numarul_invitatii" placeholder="Nr. Invitatii">
        <button id="genereaza_invitatii">Genereaza</button>
    </div>
</section>

<h2>Lista Coduri Invitatii</h2>
<span>Numar Invitatii: </span>
<section class="lista_invitatii element">
    <?php foreach($this->user->code_list() as $code){ ?>
        <div class="invitatie">
            <div class="nume">Cod: <?php echo $code->Invitation_Code;?> </div>
        </div>
    <?php }; ?>
</section>

</div>

<script>
    $("#genereaza_invitatii").click(function()
    {
        $.ajax(
        {
            url: "/nodes/RequestProtocol.php",
            type: "POST",
            data: 
            {
                generare_invitatii: $("#numarul_invitatii").val(),
                token: "<?php echo Tools::hash(Keys::get('pepper')); ?>"
            },

            success: function()
            {
                location.reload();
            }
        });
    });
</script>
<?php }; ?>