<?php
/*
Simple:Press
Thank and Points Plugin support components
$LastChangedDate: 2013-06-02 05:52:24 +0100 (Sun, 02 Jun 2013) $
$Rev: 10348 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_extend_mycred($defs) {
	$defs['receive_thanks'] = array(
		'creds'   => 1,
		'log'     => '%plural% for receiving post thanks'
	);
	return $defs;
}

function sp_thanks_do_prefs_create($args) {
	if(empty($args->prefs['receive_thanks'])) {
		$prefs = $args->defaults;
	} else {
		$prefs = $args->prefs;
	}
	?>
	<!-- Creds for change -->
	<label for="<?php echo $args->field_id(array('receive_thanks', 'creds' ) ); ?>" class="subheader"><?php echo $args->core->template_tags_general( __( '%plural% for Receiving Post Thanks', 'sp-thanks' ) ); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'receive_thanks', 'creds' ) ); ?>" id="<?php echo $args->field_id( array( 'receive_thanks', 'creds' ) ); ?>" value="<?php echo $args->core->number( $prefs['receive_thanks']['creds'] ); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id( array( 'receive_thanks', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'receive_thanks', 'log' ) ); ?>" id="<?php echo $args->field_id( array( 'receive_thanks', 'log' ) ); ?>" value="<?php echo $prefs['receive_thanks']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e( 'Available template tag: General and %receive_thanks%', 'mycred' ); ?></span>
		</li>
	</ol>
	<?php
}

function sp_thanks_do_save_mycred($userid, $topicid) {
	if(function_exists('sp_mycred_process_points')) {
		$add = true;
		$topic = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'topic_name');
		$m = sprintf(__('Thanks received from %s for a post in the topic %s', 'sp-thanks'), SP()->user->thisUser->display_name, $topic);
		sp_mycred_process_points($add, 'receive_thanks', $userid, $m);
	}
}
