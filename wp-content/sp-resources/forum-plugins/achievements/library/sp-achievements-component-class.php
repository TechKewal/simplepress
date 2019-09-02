<?php
/*
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function dpa_init_simplepress_extension() {
	achievements()->extensions->simplepress = new DPA_SimplePress_Forum_Extension;
	do_action( 'dpa_init_simplepress_extension' );
}

class DPA_SimplePress_Forum_Extension extends DPA_CPT_Extension {
	public function __construct() {
		$this->actions = array(
			'sph_topic_create'	=> __('User creates a new forum topic', 'sp-achieve'),
			'sph_post_create'	=> __('User creates a new topic reply', 'sp-achieve'),
		);

		$this->contributors = array(
			array(
				'name'         => 'Andy Staines',
				'gravatar_url' => 'https://www.gravatar.com/avatar/375b9c21dcc5d937d7d8b288d52f8d58.png',
			),
			array(
				'name'         => 'Steve Klasen',
				'gravatar_url' => 'https://www.gravatar.com/avatar/375b9c21dcc5d937d7d8b288d52f8d58.png',
			),
		);

		$this->description     = __('Simple:Press - the forum plugin for WordPress', 'sp-achieve');
		$this->id              = 'simple-press';
		$this->image_url       = SPACHIMAGES.'sp-logo.png';
		$this->name            = __('Simple:Press', 'sp-achieve');
		$this->small_image_url = SPACHIMAGES.'sp-logo-small.png';
		$this->version         = SPACHDBVERSION;
		$this->wporg_url       = SPHOMESITE;

		add_filter( 'dpa_handle_event_name',    array( $this, 'event_name'              ), 10, 2 );
	}

	function event_name($event_name, $func_args) {
		if(is_object($func_args[0])) return $event_name;

		# Switch the event name for new topics
		if(is_array($func_args[0])) {
			if(isset($func_args[0]['action'])) {
				if ('topic' === $func_args[0]['action']) $event_name = 'sph_topic_create';
			}
		}
		return $event_name;
	}
}
