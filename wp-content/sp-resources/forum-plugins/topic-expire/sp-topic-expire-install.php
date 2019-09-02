<?php
/*
Simple:Press
Topic Expire plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_topic_expire_do_install() {
	$options = SP()->options->get('topic-expire');
	if (empty($options)) {
        SP()->DB->execute('ALTER TABLE '.SPTOPICS." ADD (expire_date datetime default NULL), ADD (expire_action int(4) NOT NULL default '0')");
        SP()->DB->execute("ALTER TABLE ".SPTOPICS." ADD KEY expire_date_idx (expire_date)");

        $options['dbversion'] = SPEXPIREDBVERSION;
        SP()->options->update('topic-expire', $options);
    }

    # set up our cron
	wp_clear_scheduled_hook('sph_topic_expire_cron');
    wp_schedule_event(strtotime('tomorrow + 1 minute'), 'daily', 'sph_topic_expire_cron');

    # add a new permission into the auths table
	SP()->auths->add('set_topic_expire', __('Can set a topic expiration when creating a new topic', 'sp-topic-expire'), 1, 1, 0, 0, 3);

    # activation so make our auth active
    SP()->auths->activate('set_topic_expire');
}

# sp reactivated.
function sp_topic_expire_do_sp_activate() {
	wp_clear_scheduled_hook('sph_topic_expire_cron');
    wp_schedule_event(strtotime('tomorrow + 1 minute'), 'daily', 'sph_topic_expire_cron');
}

# permissions reset
function sp_topic_expire_do_reset_permissions() {
	SP()->auths->add('set_topic_expire', __('Can set a topic expiration when creating a new topic', 'sp-topic-expire'), 1, 1, 0, 0, 3);
}
