<?php
/*
Simple:Press
Post Rating Plugin Support Routines
$LastChangedDate: 2013-11-27 10:31:32 +0000 (Wed, 27 Nov 2013) $
$Rev: 10892 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rating_do_extend_mycred($defs) {
	$defs['post_rating'] = array(
		'creds'   => 1,
		'log'     => '%plural% for having a post rated'
	);
	return $defs;
}

function sp_rating_do_prefs_create($args) {
	if(empty($args->prefs['post_rating'])) {
		$prefs = $args->defaults;
	} else {
		$prefs = $args->prefs;
	}
	?>
	<!-- Creds for change -->
	<label for="<?php echo $args->field_id(array('post_rating', 'creds' ) ); ?>" class="subheader"><?php echo $args->core->template_tags_general( __( '%plural% for having a Post Rated', 'sp-rating' ) ); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'post_rating', 'creds' ) ); ?>" id="<?php echo $args->field_id( array( 'post_rating', 'creds' ) ); ?>" value="<?php echo $args->core->number( $prefs['post_rating']['creds'] ); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id( array( 'post_rating', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'post_rating', 'log' ) ); ?>" id="<?php echo $args->field_id( array( 'post_rating', 'log' ) ); ?>" value="<?php echo $prefs['post_rating']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e( 'Available template tag: General and %post_rating%', 'mycred' ); ?></span>
		</li>
	</ol>
	<?php
}

function sp_rating_do_save_mycred($postid, $action) {
	if(function_exists('sp_mycred_process_points')) {
		$post = SP()->DB->table(SPPOSTS, "post_id=$postid", 'row');
		$topic = SP()->DB->table(SPTOPICS, 'topic_id='.$post->topic_id, 'topic_name');
		$m = sprintf(__('Rating made by %s for a post in the topic %s', 'sp-rating'), SP()->user->thisUser->display_name, $topic);
		sp_mycred_process_points($action, 'post_rating', $post->user_id, $m);
	}
}
