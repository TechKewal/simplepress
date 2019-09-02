<?php
/*
Simple:Press
Birthdays - general support routines
$LastChangedDate: 2013-07-18 19:38:48 +0100 (Thu, 18 Jul 2013) $
$Rev: 10437 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_birthdays_do_extend_mycred($defs) {
	$defs['users_birthday'] = array(
		'creds'   => 1,
		'log'     => '%plural% for a birthday'
	);
	return $defs;
}

function sp_birthdays_do_prefs_create($args) {
	if(empty($args->prefs['users_birthday'])) {
		$prefs = $args->defaults;
	} else {
		$prefs = $args->prefs;
	}
	?>
	<!-- Creds for change -->
	<label for="<?php echo $args->field_id(array('users_birthday', 'creds' ) ); ?>" class="subheader"><?php echo $args->core->template_tags_general( __( '%plural% for a Birthday', 'sp-birthdays' ) ); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'users_birthday', 'creds' ) ); ?>" id="<?php echo $args->field_id( array( 'users_birthday', 'creds' ) ); ?>" value="<?php echo $args->core->number( $prefs['users_birthday']['creds'] ); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id( array( 'users_birthday', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'users_birthday', 'log' ) ); ?>" id="<?php echo $args->field_id( array( 'users_birthday', 'log' ) ); ?>" value="<?php echo $prefs['users_birthday']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e( 'Available template tag: General and %users_birthday%', 'mycred' ); ?></span>
		</li>
	</ol>
	<?php
}

function sp_birthdays_do_save_mycred($userid) {
	if(function_exists('sp_mycred_process_points')) {
		$add = true;
		$m = __('Birthday', 'sp-birthdays');
		sp_mycred_process_points($add, 'users_birthday', $userid, $m);
	}
}
