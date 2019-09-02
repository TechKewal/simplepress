<?php
/*
Simple:Press
Push Notifications plugin uninstall routine
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the post rating plugin uninstall only
function sp_push_notifications_do_uninstall() {
	
	# delete the user activioty records
    SP()->activity->delete('type='.SPACTIVITY_SUBS_PUSHOVER_TOPIC);
	SP()->activity->delete('type='.SPACTIVITY_SUBS_PUSHOVER_FORUM);

    SP()->activity->delete('type='.SPACTIVITY_SUBS_PUSHBULLET_TOPIC);
	SP()->activity->delete('type='.SPACTIVITY_SUBS_PUSHBULLET_FORUM);

    SP()->activity->delete('type='.SPACTIVITY_SUBS_ONESIGNAL_TOPIC);
	SP()->activity->delete('type='.SPACTIVITY_SUBS_ONESIGNAL_FORUM);

	# remove our activity types
	SP()->activity->delete_type('topic pushover notifications');
	SP()->activity->delete_type('forum pushover notifications');

	SP()->activity->delete_type('topic pushbullet notifications');
	SP()->activity->delete_type('forum pushbullet notifications');

	SP()->activity->delete_type('topic onesignal notifications');
	SP()->activity->delete_type('forum onesignal notifications');

	# remove our profile tab/meuns
    SP()->profile->delete_tab('Push Notifications');

	# remove the auth (permissions)
	if (!empty(SP()->core->forumData['auths_map']['pushover'])) SP()->auths->delete('pushover');
	if (!empty(SP()->core->forumData['auths_map']['pushbullet'])) SP()->auths->delete('pushbullet');
	if (!empty(SP()->core->forumData['auths_map']['onesignal'])) SP()->auths->delete('onesignal');
	if (!empty(SP()->core->forumData['auths_map']['onesignal_rest_api_key'])) SP()->auths->delete('onesignal_rest_api_key');
	if (!empty(SP()->core->forumData['auths_map']['forumpushover'])) SP()->auths->delete('forumpushover');
	if (!empty(SP()->core->forumData['auths_map']['forumpushbullet'])) SP()->auths->delete('forumpushbullet');
	if (!empty(SP()->core->forumData['auths_map']['forumonesignal'])) SP()->auths->delete('forumonesignal');

    # delete our option table
    SP()->options->delete('push-notifications');

	# remove glossary entries
	sp_remove_glossary_plugin('push-notifications');

	# remove scripts from main directory
	if(SPPNSITEPROTOCOL == 'https'){
		$files = array(
			'manifest.json',
			'OneSignalSDKUpdaterWorker.js',
			'OneSignalSDKWorker.js'
		);
		foreach($files as $file){
			rename(
				get_home_path().$file,
				get_home_path().'wp-content/sp-resources/forum-plugins/push-notifications/resources/jscript/onesignal/'.$file
			);
		}
	}

}

function sp_push_notifications_do_deactivate() {

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'push-notifications');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

	# remove our profile tab/meuns
    SP()->profile->delete_tab('Push Notifications');

    SP()->auths->deactivate('pushover');
    SP()->auths->deactivate('pushbullet');
    SP()->auths->deactivate('onesignal');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-push-notifications');
}