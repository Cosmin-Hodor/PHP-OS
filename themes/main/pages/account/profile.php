<div class="perete">
<section class="detalii_cont">
    <div class="antent_avatar">
        <div class="avatar_hug">
            <img class="avatar" src="<?php $this->user_profile->user('avatar'); ?>">
        </div>
        <div class="detalii_hug">
            <h2 id="nume_cont" alt="Numele utilizatorului"><?php $this->user_profile->user('username'); ?></h2>
            <h3 alt="Data de participare">Creat la: <?php $this->user_profile->user('created'); ?></h3>
            <span alt="Rolul in grup al utilizatorului">Rol in grup: <?php $this->user_profile->user('role'); ?></span>
            <?php if($this->user->isAdmin()) {?>
            <br>
            <span id="statut_prietenie"></span>
            <div class="element" style="max-width: max-content;margin: 5px auto;">
            <span><?php $this->user_profile->user('reported'); ?></span><br>
            <span id="statut"><?php $this->user_profile->user('banned'); ?></span>
            </div>
            <?php }; ?>
        </div>
    </div>
</section>

<section class="functii element">
    <button id="adauga_prieten" alt="Cerere prietenie">Cerere</button>
    <button class="blocheaza_utilizator" alt="Blocare utilizator">Blocare</button>
    <button class="blocheaza_utilizator" alt="Blocare utilizator">Report</button>
    <button  alt="Plus reputatie">+Rep</button>
    <button  alt="Minus reputatie">-Rep</button>
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

<?php if($this->user->isAdmin()) {?>
<h3>Control Admin</h3>
<section class="functii_admin element">
<?php if(!$this->user_profile->isAdmin()) {?>
    <button id="sterge_utilizator" alt="Cerere prietenie">Sterge</button>
<?php }; ?>
    <?php if ($this->user_profile->banned() !== '1') {?>
    <button id="blocheaza_utilizator" alt="Blocare utilizator">Block</button>
    <?php } else { ?>
    <button id="deblocheaza_utilizator" alt="Deblocare utilizator">Unblock</button>        
    <?php }; ?>
</section>
<?php }; ?>


</div>

<script>
    $("#adauga_prieten").click(function()
    {
        $.ajax(
        {
            url: "/nodes/RequestProtocol.php",
            type: "POST",
            data: 
            {
                cerere_prietenie: "<?php $this->user_profile->user('id'); ?>",
                expeditor: "<?php $this->user->user('id'); ?>", 
                token: "<?php echo Tools::hash(Keys::get('pepper')); ?>"
            }
        });
    });
</script>


    <?php if($this->user->isAdmin()) { ?>
<script>
        $("#sterge_utilizator").click(function()
        {
            $.ajax(
            {
                url: "/nodes/RequestProtocol.php",
                type: "POST",
                data: 
                {
                    sterge_utilizator: "<?php $this->user_profile->user('id'); ?>",
                    token: "<?php echo Tools::hash(Keys::get('pepper')); ?>"
                },

                success: function(output)
                {
                    window.history.back();
                }
            });
        }); 
</script>
<?php }; ?>

<?php if ($this->user_profile->banned() !== '1') {?>
<script>
    $("#blocheaza_utilizator").click(function()
    {
        $.ajax(
        {
            url: "/nodes/RequestProtocol.php",
            type: "POST",
            data: 
            {
                ban_utilizator: "<?php $this->user_profile->user('id'); ?>",
                token: "<?php echo Tools::hash(Keys::get('pepper')); ?>"
            },

            success: function()
            {
                location.reload();
            }
        });
    });
</script>
    <?php } else {?>
<script>
    $("#deblocheaza_utilizator").click(function()
    {
        $.ajax(
        {
            url: "/nodes/RequestProtocol.php",
            type: "POST",
            data: 
            {
                unban_utilizator: "<?php $this->user_profile->user('id'); ?>",
                token: "<?php echo Tools::hash(Keys::get('pepper')); ?>"
            },

            success: function()
            {
                location.reload();
            }
        });
    });    
</script>
<?php } ?>