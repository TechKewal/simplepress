<?php
/*
Simple:Press
Answers Topic Plugin support components
$LastChangedDate: 2013-02-17 19:52:14 +0000 (Sun, 17 Feb 2013) $
$Rev: 9854 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_answers_topic_do_extend_mycred($defs) {
	$defs['answers_topic'] = array(
		'creds'   => 1,
		'log'     => '%plural% for having answer marked'
	);
	return $defs;
}

function sp_answers_topic_do_prefs_create($args) {
	if(empty($args->prefs['answers_topic'])) {
		$prefs = $args->defaults;
	} else {
		$prefs = $args->prefs;
	}
	?>
	<!-- Creds for change -->
	<label for="<?php echo $args->field_id(array('answers_topic', 'creds' ) ); ?>" class="subheader"><?php echo $args->core->template_tags_general( __( '%plural% for having a Post marked as Answered', 'sp-answers-topic' ) ); ?></label>
	<ol id="">
		<li>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'answers_topic', 'creds' ) ); ?>" id="<?php echo $args->field_id( array( 'answers_topic', 'creds' ) ); ?>" value="<?php echo $args->core->number( $prefs['answers_topic']['creds'] ); ?>" size="8" /></div>
		</li>
		<li class="empty">&nbsp;</li>
		<li>
			<label for="<?php echo $args->field_id( array( 'answers_topic', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
			<div class="h2"><input type="text" name="<?php echo $args->field_name( array( 'answers_topic', 'log' ) ); ?>" id="<?php echo $args->field_id( array( 'answers_topic', 'log' ) ); ?>" value="<?php echo $prefs['answers_topic']['log']; ?>" class="long" /></div>
			<span class="description"><?php _e( 'Available template tag: General and %answers_topic%', 'mycred' ); ?></span>
		</li>
	</ol>
	<?php
}

function sp_answers_topic_do_save_mycred($userid, $topicid, $action) {
	if(function_exists('sp_mycred_process_points')) {
		$topic = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'topic_name');
		$word = ($action) ? 'marked' : 'unmarked';
		$m = sprintf(__('Answer %s by %s for a post in the topic %s', 'sp-answers-topic'), $word, SP()->user->thisUser->display_name, $topic);
		sp_mycred_process_points($action, 'answers_topic', $userid, $m);
	}
}
