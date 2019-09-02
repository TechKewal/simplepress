<?php
/*
Simple:Press
Reputation plugin ajax routine for management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

# get out of here if no action specified
if (empty($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'user-search') {
    global $wpdb;

	$out = '[]';

	$query = SP()->filters->str($_GET['term']);
	$where = "display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($query))."%'";
	$users = SP()->DB->table(SPMEMBERS, $where, '', 'display_name DESC', 25);
	if ($users) {
		$primary = '';
		$secondary = '';
		foreach ($users as $user) {
			$uname = SP()->displayFilters->name($user->display_name);
			$cUser = array ('id' => $user->user_id, 'value' => $uname);
			if (strcasecmp($query, substr($uname, 0, strlen($query))) == 0) {
				$primary.= json_encode($cUser).',';
			} else {
				$secondary.= json_encode($cUser).',';
			}
		}
		if ($primary != '' || $secondary != '') {
			if ($primary != '') $primary = trim($primary, ',').',';
			if ($secondary != '') $secondary = trim($secondary, ',');
			$out = '['.trim($primary.$secondary, ',').']';
		}
	}
	echo $out;

	die();
}

//if (!sp_nonce('reputation-manage')) die();

if ($action == 'del_level') {
    # ensure permission to be here
    if (!SP()->auths->current_user_can('SPF Manage Reputation')) die();

    if (empty($_GET['id'])) die();

    $id = SP()->filters->integer($_GET['id']);
    if (empty($id)) die();

    SP()->meta->delete($id);

	echo '1';

    die();
}

if ($action == 'delbadge') {
    # ensure permission to be here
    if (!SP()->auths->current_user_can('SPF Manage Reputation')) die();

	$file = SP()->filters->str($_GET['file']);
	$path = SP_STORE_DIR.'/'.SP()->plugin->storage['reputation'].'/'.$file;
	@unlink($path);
	echo '1';

    die();
}

if ($action == 'rep-popup') {
    # make sure we got user id and post id
    if (empty($_GET['user']) || empty($_GET['post'])) {
        echo SP()->displayFilters->title($option['popupwrong']);
        die();
    }

    # validate user id and post id
    $user_id = SP()->filters->integer($_GET['user']);
    $post_id = SP()->filters->integer($_GET['post']);
    if (empty($user_id) || empty($post_id)) {
        echo SP()->displayFilters->title($option['popupwrong']);
        die();
    }

    # grab post info for validation checks
    $post = SP()->DB->select('SELECT * FROM '.SPPOSTS." WHERE post_id=$post_id", 'row');

    # extra validation on post id and user id
    if (empty($post) || $user_id != $post->user_id) {
        echo SP()->displayFilters->title($option['popupwrong']);
        die();
    }

    # verify permissions to give and receive reputation
	if (!SP()->auths->get('use_reputation', $post->forum_id, SP()->user->thisUser->ID) || !SP()->auths->get('get_reputation', $post->forum_id, $post->user_id)) {
        echo SP()->displayFilters->title($option['popupwrong']);
        die();
    }

    # bail if user has already rated this user/post
    if (isset(SP()->user->thisUser->reputation_posts[$post_id])) {
        echo SP()->displayFilters->title($option['popupwrong']);
        die();
    }

    $option = SP()->options->get('reputation');

    $daily = SP()->user->thisUser->reputation_level->maxday - sp_reputation_get_daily_give(SP()->user->thisUser->ID);
    $max = min($daily, SP()->user->thisUser->reputation_level->maxgive);
    if ($max < 0) {
        echo SP()->displayFilters->title($option['popupmax']);
        die();
    }

   # should be good to proceed with reputation
    $out = '';
    $out = '<div id="spMainContainer">';
    $out.= '<div class="spRepUserHeader">'.SP()->displayFilters->title($option['popupheader']).'</div>';
	$out.= '<form action="" method="get" id="spRepUser" name="spRepUser">';
	$out.= '<input type="hidden" id="user" name="user" value="'.$user_id.'" />';
	$out.= '<input type="hidden" id="post" name="post" value="'.$post_id.'" />';
	$out.= '<p><input type="radio" id="rep-add" name="rep-type" value="give" checked="checked" /><label for="rep-add">'.SP()->displayFilters->title($option['popupgive']).'</label></p>';
	$out.= '<p><input type="radio" id="rep-sub" name="rep-type" value="take" /><label for="rep-sub">'.SP()->displayFilters->title($option['popuptake']).'</label></p>';
	$out.= '<p><label id="rep-amount-label">'.SP()->displayFilters->title($option['popupamount']).'</label><input id="rep-amount" class="spControl" type="text" name="rep-type" value="0"/> (Max: '.$max.')</p>';
	$out.= '<div class="rep-submit"><p><input type="submit" class="spSubmit" id="repuser" name="repuser" value="'.SP()->displayFilters->title($option['popupsubmit']).'" /></p></div>';
	$out.= '</form>';
    $out.= '</div>';
    echo $out;

	$site = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."reputation-manage&targetaction=rep-process", 'reputation-manage'));
?>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#repuser').click(function(e) {
					e.preventDefault();
					var user = $('#user').val();
					var post = $('#post').val();
					var type = $('input[name=rep-type]:checked').val();
					var amount = $('#rep-amount').val();
					var dataString = 'user=' + user + '& post=' + post + '& type=' + type + '& amount=' + amount;

					if (user == '' || post == '' || amount == '' || type == '') {
						alert('<?php echo SP()->displayFilters->title($option['popupinvalid']); ?>');
					} else if (amount == 0) {
						alert('<?php echo SP()->displayFilters->title($option['popupzero']); ?>');
					} else if (amount < 0) {
						alert('<?php echo SP()->displayFilters->title($option['popuppositive']); ?>');
					} else if (amount > <?php echo $max; ?>) {
						alert('<?php echo SP()->displayFilters->title($option['popupmax']); ?>');
					} else {
						$.ajax({
							method: 'POST',
							url: '<?php echo $site; ?>',
							data: dataString,
							success: function(response) {
								if (response == 0) {
									$('#dialog').remove();
									spj.displayNotification(0, "<?php echo SP()->displayFilters->title($option['popupupdated']); ?>");
								} else {
									alert('<?php echo SP()->displayFilters->title($option['popupwrong']); ?> (' + response + ')');
								}
							}
						});
					}
					return false;
				});
			})
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
    die();
}

if ($action == 'rep-process') {
    # make sure we got user id and post id
    if (empty($_POST['user']) || empty($_POST['post']) || empty($_POST['type']) || empty($_POST['amount'])) {
        echo 1;
        die();
    }

    # validate user id and post id
    $user_id = SP()->filters->integer($_POST['user']);
    $post_id = SP()->filters->integer($_POST['post']);
    $amount = SP()->filters->integer($_POST['amount']);
    if (empty($user_id) || empty($post_id) || empty($amount)) {
        echo 2;
        die();
    }

    # make sure we got valid action
    $type = SP()->filters->str($_POST['type']);
    if ($type != 'give' && $type != 'take') {
        echo 3;
        die();
    }

    # grab post info for validation checks
    $post = SP()->DB->select('SELECT * FROM '.SPPOSTS." WHERE post_id=$post_id", 'row');

    # extra validation on post id and user id
    if (empty($post) || $user_id != $post->user_id) {
        echo 4;
        die();
    }

    # verify permissions to give and receive reputation
	if (!SP()->auths->get('use_reputation', $post->forum_id, SP()->user->thisUser->ID) || !SP()->auths->get('get_reputation', $post->forum_id, $post->user_id)) {
        echo 5;
        die();
	}

    # double check reputation amount is less than max give value
    if ($amount > SP()->user->thisUser->reputation_level->maxgive || $amount < 0) {
        echo 6;
        die();
    }

    if (isset(SP()->user->thisUser->reputation_posts[$post_id])) {
        echo 7;
        die();
    }

    $daily = SP()->user->thisUser->reputation_level->maxday - sp_reputation_get_daily_give(SP()->user->thisUser->ID);
    $max = min($daily, SP()->user->thisUser->reputation_level->maxgive);
    if ($max < 0) {
        echo 8;
        die();
    }

    # should be good to process now - adjust amount based on giving or taking
    $amount = ($type == 'give') ? $amount : -$amount;

    # update the rate user reputation
    $newrep = SP()->memberData->get($user_id, 'reputation') + $amount;
    SP()->memberData->update($user_id, 'reputation', $newrep);

    # update the rating user daily give
    sp_reputation_update_daily_give(SP()->user->thisUser->ID, abs($amount));

    # record the user rating for this post
    SP()->activity->add(SP()->user->thisUser->ID, SPACTIVITY_REPUTATION, $post_id, $user_id);

    do_action('sph_reputation_given', SP()->user->thisUser->ID, $user_id, $amount, $newrep);

	echo 0;
    die();
}

die();
