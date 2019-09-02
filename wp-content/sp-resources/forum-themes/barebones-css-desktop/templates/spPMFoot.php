<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Private Messaging View
#	Author		:	Simple:Press
#
#	The 'pm' template is used to display the private messaging page
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2013-09-23 05:12:51 +0100 (Mon, 23 Sep 2013) $
$Rev: 10721 $
*/

	# == ADD POST FORM - OBJECT DEFINITION ========================
	$addPmForm = array(
		'tagClass'				=> 'spForm',
		'hide'					=> 1,
		'controlFieldset'		=> 'spPmRecipients',
		'controlInput'			=> 'spControl',
		'controlSubmit'			=> 'spSubmit',
		'controlSelect'			=> 'spSelect',
		'labelSelect'		    => __('Select From Members: ', 'sp-pm'),
		'labelSelectHelp'		=> __("Start typing a member's name in the input field above and it will auto-complete", 'sp-pm'),
		'labelRecipients'		=> __('Message Recipients: ', 'sp-pm'),
		'labelPmAllUsers'		=> __('PM All Users', 'sp-pm'),
		'labelPmAllUsersHelp'	=> __('Not recommended for large number of users', 'sp-pm'),
		'labelPmUserGroup'		=> __('Select Usergroup to PM: ', 'sp-pm'),
		'labelPmUserGroupHelp'	=> __('Not recommended for large usergroups', 'sp-pm'),
		'labelPmUserGroupSelect'=> __('Select Usergroup', 'sp-pm'),
		'labelPmBuddyList'		=> __('Select From Buddy List: ', 'sp-pm'),
		'labelPmAddBuddy'		=> __('Add ALL Recipients to Buddy List', 'sp-pm'),
		'labelTitle'			=> __('Subject: ', 'sp-pm'),
		'labelSendButton'	    => __('Send Private Message', 'sp-pm'),
		'labelCancelButton'	    => __('Cancel', 'sp-pm'),
		'labelSmileysButton'    => __('Smileys', 'sp-pm'),
		'labelSmileys'			=> __('Smileys', 'sp-pm'),
		'tipSubmitButton'	    => __('Send private message', 'sp-pm'),
		'tipCancelButton'	    => __('Cancel sending private message', 'sp-pm'),
		'tipSmileysButton'	    => __('Open/Close to add Smiley', 'sp-pm'),
		'iconMobileSmileys'		=> 'sp_EditorSmileys.png',
		'iconMobileCancel'		=> 'sp_EditorCancel.png',
		'iconMobileSubmit'		=> 'sp_EditorSave.png'
	);

    global $spPmThreadList, $spThisPmThreadList;

    do_action('sp_pm_footer_begin');

	sp_SectionStart('tagId=spPmFooter&tagClass=spPmFooter spPlainSection', 'pmFooter');

		sp_SectionStart('tagId=spPmFooterButtons&tagClass=spPmFooter spPlainSection', 'pmFootButtons');

			sp_PmEmptyInboxButton('tagClass=spButton spRight', __('Empty Inbox', 'sp-pm'), __('Empty your message inbox', 'sp-pm'));
			sp_PmMarkInboxReadButton('tagClass=spButton spRight', __('Mark Inbox Read', 'sp-pm'), __('Mark all threads in Inbox as read', 'sp-pm'));
			sp_PmComposeButton('tagClass=spButton spRight', __('Compose', 'sp-pm'), __('Compose a new private message', 'sp-pm'));

		sp_SectionEnd('tagClass=spClear', 'pmFootButtons');

	sp_SectionEnd('tagClass=spClear', 'pmFooter');

    sp_SectionStart('tagClass=spHiddenSection', 'pmComposeForm');

		sp_SectionStart('tagId=spPmFooterCompose&tagClass=spPmEditor spPlainSection', 'pmFootCompose');

			sp_PmComposeWindow($addPmForm);

		sp_SectionEnd('', 'pmComposeForm');

	sp_SectionEnd('tagClass=spClear', 'pmFootCompose');

	do_action('sp_pm_footer_end');
?>