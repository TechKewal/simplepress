<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Private Messaging View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'pm' template is used to display the private messaging page
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

	# == ADD POST FORM - OBJECT DEFINITION ========================
	$addPmForm = array(
		'tagClass'				=> 'spForm',
		'hide'					=> 1,
		'controlFieldset'		=> 'spPmRecipients',
		'controlInput'			=> 'spControl',
		'controlSubmit'			=> 'spSubmit',
		'controlSelect'			=> 'spSelect',
		'labelSelect'		    => __('Select Individual Users: ', 'sp-pm'),
		'labelTo'				=> __('Send To: ', 'sp-pm'),
		'labelSelectHelp'		=> __("Start typing a member's name and it will auto-complete", 'sp-pm'),
		'labelPmAllUsers'		=> __('Send PM to All Users', 'sp-pm'),
		'labelPmAllUsersHelp'	=> __('Not recommended for large user base', 'sp-pm'),
		'labelPmUserGroupHelp'	=> __('Not recommended for large usergroups', 'sp-pm'),
		'labelPmUserGroupSelect'=> __('Send PM to a User Group', 'sp-pm'),
		'labelPmBuddyList'		=> __('Send PM to a Buddy', 'sp-pm'),
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
		'iconMobileSubmit'		=> 'sp_PmSendMessage.png'
	);

    global $spPmThreadList, $spThisPmThreadList;

    do_action('sp_pm_footer_begin');

	sp_SectionStart('tagId=spPmFooter&tagClass=spPmFooter spPlainSection', 'pmFooter');

		if (SP()->rewrites->pageData['pageview'] != 'pmthread') {
			sp_SectionStart('tagId=spPmFooterButtons&tagClass=spPmFooter spPlainSection', 'pmFootButtons');
				if ((SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive'))) {
					sp_PmEmptyInboxButton('tagClass=spRight', '', __('Empty your message inbox', 'sp-pm'));
					sp_PmMarkInboxReadButton('tagClass=spRight', '', __('Mark all threads in Inbox as read', 'sp-pm'));
					sp_PmComposeButton('tagClass=spRight', '', __('Compose a new private message', 'sp-pm'));
				} else {
					sp_PmEmptyInboxButton('tagClass=spButton spRight', __('Empty Inbox', 'sp-pm'), __('Empty your message inbox', 'sp-pm'));
					sp_PmMarkInboxReadButton('tagClass=spButton spRight', __('Mark Inbox Read', 'sp-pm'), __('Mark all threads in Inbox as read', 'sp-pm'));
					sp_PmComposeButton('tagClass=spButton spRight', __('Compose', 'sp-pm'), __('Compose a new private message', 'sp-pm'));
				}
			sp_SectionEnd('tagClass=spClear', 'pmFootButtons');
		}

    	sp_SectionStart('tagId=spPmFooterCompose&tagClass=spPmFooter spPlainSection', 'pmFootCompose');
    		sp_SectionStart('tagClass=spHiddenSection', 'pmComposeForm');
    			sp_PmComposeWindow($addPmForm);
    		sp_SectionEnd('', 'pmComposeForm');
    	sp_SectionEnd('tagClass=spClear', 'pmFootCompose');
	sp_SectionEnd('tagClass=spClear', 'pmFooter');

    do_action('sp_pm_footer_end');
