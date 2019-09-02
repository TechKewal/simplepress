<?php
/*
Simple:Press
Polls Plugin mycred Support Routines
$LastChangedDate: 2014-02-13 03:49:18 +0000 (Thu, 13 Feb 2014) $
$Rev: 11070 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_polls_do_extend_mycred($defs) {
	$defs['create_poll'] = array(
		'creds'   => 1,
		'log'     => '%plural% for creating a poll'
	);
	$defs['vote_in_poll'] = array(
		'creds'   => 1,
		'log'     => '%plural% for voting in a poll'
	);
	$defs['receive_poll_votes'] = array(
		'creds'   => 1,
		'log'     => '%plural% for receiving poll votes'
	);
	return $defs;
}

function sp_polls_do_prefs_create($args) {
	if(empty($args->prefs['create_poll'])) {
		$prefs = $args->defaults;
	} else {
		$prefs = $args->prefs;
	}
	?>
	<label for="<?php echo $args->field_id(array('create_poll', 'creds' ) ); ?>" class="subheader"><?php echo $args->core->template_tags_general( __( '%plural% for Creating a Poll', 'sp-polls' ) ); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'create_poll', 'creds' ) ); ?>" id="<?php echo $args->field_id( array( 'create_poll', 'creds' ) ); ?>" value="<?php echo $args->core->number( $prefs['create_poll']['creds'] ); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id( array( 'create_poll', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'create_poll', 'log' ) ); ?>" id="<?php echo $args->field_id( array( 'create_poll', 'log' ) ); ?>" value="<?php echo $prefs['create_poll']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e( 'Available template tag: General and %create_poll%', 'mycred' ); ?></span>
		</li>
	</ol>

	<label for="<?php echo $args->field_id(array('vote_in_poll', 'creds' ) ); ?>" class="subheader"><?php echo $args->core->template_tags_general( __( '%plural% for Voting in a Poll', 'sp-polls' ) ); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'vote_in_poll', 'creds' ) ); ?>" id="<?php echo $args->field_id( array( 'vote_in_poll', 'creds' ) ); ?>" value="<?php echo $args->core->number( $prefs['vote_in_poll']['creds'] ); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id( array( 'vote_in_poll', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'vote_in_poll', 'log' ) ); ?>" id="<?php echo $args->field_id( array( 'vote_in_poll', 'log' ) ); ?>" value="<?php echo $prefs['vote_in_poll']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e( 'Available template tag: General and %vote_in_poll%', 'mycred' ); ?></span>
		</li>
	</ol>

	<label for="<?php echo $args->field_id(array('receive_poll_votes', 'creds' ) ); ?>" class="subheader"><?php echo $args->core->template_tags_general( __( '%plural% for your Poll receiving Votes', 'sp-polls' ) ); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'receive_poll_votes', 'creds' ) ); ?>" id="<?php echo $args->field_id( array( 'receive_poll_votes', 'creds' ) ); ?>" value="<?php echo $args->core->number( $prefs['receive_poll_votes']['creds'] ); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id( array( 'receive_poll_votes', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'receive_poll_votes', 'log' ) ); ?>" id="<?php echo $args->field_id( array( 'receive_poll_votes', 'log' ) ); ?>" value="<?php echo $prefs['receive_poll_votes']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e( 'Available template tag: General and %receive_poll_votes%', 'mycred' ); ?></span>
		</li>
	</ol>
	<?php
}

function sp_polls_do_create_save_mycred($pollid, $userid) {
	if(function_exists('sp_mycred_process_points')) {
		$add = true;
		$poll = SP()->DB->table(SPPOLLS, "poll_id=$pollid", 'poll_question');
		$m = sprintf(__('Poll - %s - Created', 'sp-polls'), $poll);
		sp_mycred_process_points($add, 'create_poll', $userid, $m);
	}
}

function sp_polls_do_vote_save_mycred($pollid, $voterid, $ownerid) {
	if(function_exists('sp_mycred_process_points')) {
		$add = true;
		$poll = SP()->DB->table(SPPOLLS, "poll_id=$pollid", 'poll_question');
		$m = sprintf(__('Poll - %s - Took part and voted', 'sp-polls'), $poll);
		sp_mycred_process_points($add, 'vote_in_poll', $voterid, $m);
		$m = sprintf(__('%s voted in the poll - %s', 'sp-polls'), SP()->user->thisUser->display_name, $poll);
		sp_mycred_process_points($add, 'receive_poll_votes', $ownerid, $m);
	}
}
