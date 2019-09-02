<?php
/*
Simple:Press
Push Notification Plugin Admin Members Form
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

    if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

    # double check we have a user
    if (empty($userid)) return;

    $ajaxURL = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."update_keys", 'push-notifications'));
    $u_id = get_current_user_id();
?>
<p><? _e('On this panel, you may update your API keys.', 'push-notifications')?></p>
<hr>
<?php
    if( SP()->auths->get('pushover', '', $u_id) || 
        SP()->auths->get('pushbullet', '', $u_id) ){ 
?>

<form id="user_keys" action="<?= $ajaxURL ?>" type="GET">

    <?php if( SP()->auths->get('pushover', '', $u_id)){ ?>
        <div class="spColumnSection spProfileLeftCol">
            <p class="spProfileLabel"><?= __('Pushover User Key: ', 'push-notifications')?></p>
        </div>
        <div class="spColumnSection spProfileRightCol">
            <input type="text" class="spControl" name="keys[pushover_key]" size="40" value="<?= get_user_meta($userid, 'pushover_key', true)?>">
        </div>
    <?php } ?>

    <br>

    <?php if( SP()->auths->get('pushbullet', '', $u_id)){ ?>
        <div class="spColumnSection spProfileLeftCol">
            <p class="spProfileLabel"><?= __('Pushbullet User Key: ', 'push-notifications')?></p>
        </div>
        <div class="spColumnSection spProfileRightCol">
            <input type="text" class="spControl" name="keys[pushbullet_key]" size="40" value="<?= get_user_meta($userid, 'pushbullet_key', true)?>">
        </div>
    <?php } ?>

    <input type="hidden" name="user_id" value="<?=$userid?>">
    <div class="spProfileFormSubmit">
        <input type="submit" class="spSubmit" name="formsubmit" value="Update Keys">
    </div>
</form>
<script>

    var $ = jQuery;
    $(document).ready(function($) {
        $('#user_keys').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                type: $(this).attr('type'),
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function (response) {
                    spj.displayNotification(0, 'Api keys success updated!');
                }
            });
        })
    })

</script>

<?php  
    } else {
        ?>
        <p><? _e('You do not have enough permissions to access this location.', 'push-notifications')?></p>
        <?php
    }
?>