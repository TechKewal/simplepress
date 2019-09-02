<?php
/*
Simple:Press
Reputation Plugin support for MyCred
$LastChangedDate: 2017-05-27 18:27:35 -0700 (Sat, 27 May 2017) $
$Rev: 15395 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_extend_mycred($defs) {
	$defs['receive_reputation'] = array(
		'creds'   => 1,
		'log'     => '%plural% for receiving reputation'
	);
	$defs['lose_reputation'] = array(
		'creds'   => 1,
		'log'     => '%plural% for losing reputation'
	);
	$defs['give_reputation'] = array(
		'creds'   => 1,
		'log'     => '%plural% for giving/taking reputation'
	);
	return $defs;
}

function sp_reputation_do_prefs_create($args) {
	if (empty($args->prefs['receive_reputation'])) {
		$prefs = $args->defaults;
	} else {
		$prefs = $args->prefs;
	}
	?>
	<!-- Creds for change -->
	<label for="<?php echo $args->field_id(array('receive_reputation', 'creds')); ?>" class="subheader"><?php echo $args->core->template_tags_general(__('%plural% for Receiving Reputation', 'sp-thanks')); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name(array('receive_reputation', 'creds')); ?>" id="<?php echo $args->field_id(array('receive_reputation', 'creds')); ?>" value="<?php echo $args->core->number($prefs['receive_reputation']['creds']); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id(array('receive_reputation', 'log')); ?>"><?php _e('Log template', 'mycred'); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name(array('receive_reputation', 'log')); ?>" id="<?php echo $args->field_id(array('receive_reputation', 'log')); ?>" value="<?php echo $prefs['receive_reputation']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e('Available template tag: General and %receive_reputation%', 'mycred'); ?></span>
		</li>
	</ol>

	<label for="<?php echo $args->field_id(array('lose_reputation', 'creds')); ?>" class="subheader"><?php echo $args->core->template_tags_general(__('%plural% for Losing Reputation', 'sp-thanks')); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name(array('lose_reputation', 'creds')); ?>" id="<?php echo $args->field_id(array('lose_reputation', 'creds')); ?>" value="<?php echo $args->core->number($prefs['lose_reputation']['creds']); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id(array('lose_reputation', 'log')); ?>"><?php _e('Log template', 'mycred'); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name(array('lose_reputation', 'log')); ?>" id="<?php echo $args->field_id(array('lose_reputation', 'log')); ?>" value="<?php echo $prefs['lose_reputation']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e('Available template tag: General and %lose_reputation%', 'mycred'); ?></span>
		</li>
	</ol>

	<label for="<?php echo $args->field_id(array('give_reputation', 'creds')); ?>" class="subheader"><?php echo $args->core->template_tags_general(__('%plural% for Giving/Taking Reputation', 'sp-thanks')); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name(array('give_reputation', 'creds')); ?>" id="<?php echo $args->field_id(array('give_reputation', 'creds')); ?>" value="<?php echo $args->core->number($prefs['give_reputation']['creds']); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id(array('give_reputation', 'log')); ?>"><?php _e('Log template', 'mycred'); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name(array('give_reputation', 'log')); ?>" id="<?php echo $args->field_id(array('give_reputation', 'log')); ?>" value="<?php echo $prefs['give_reputation']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e('Available template tag: General and %give_reputation%', 'mycred'); ?></span>
		</li>
	</ol>
	<?php
}

function sp_reputation_do_save_mycred($giver_id, $receiver_id, $amount) {
	if (function_exists('sp_mycred_process_points')) {
		$giver = SP()->memberData->get($giver_id, 'display_name');
		$reciever = SP()->memberData->get($receiver_id, 'display_name');
		$m = sprintf(__('%s received reputation from %s', 'sp-reputation'), $giver, $reciever);

		# process receive or lose
		if ($amount < 0) {
			sp_mycred_process_points(true, 'lose_reputation', $receiver_id, $m);
		} else {
			sp_mycred_process_points(true, 'receive_reputation', $receiver_id, $m);
		}

		# process give/take
		sp_mycred_process_points(true, 'give_reputation', $giver_id, $m);
	}
}
