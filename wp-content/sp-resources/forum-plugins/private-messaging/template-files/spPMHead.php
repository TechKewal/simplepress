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

    global $spPmThreadList, $spThisPmThreadList;

    do_action('sp_pm_header_begin');

	sp_SectionStart('tagId=spPmHeader&tagClass=spPmHeader spPlainSection spTopicViewSection', 'pmHeader');
    	sp_SectionStart('tagId=spPmHeaderButtons&tagClass=spPmHeaderButtons spPlainSection', 'pmHeadButtons');
    		sp_PmQuickLinksThreads('tagClass=spControl spSelect spLeft&show=5', __('New/Recent Messages', 'sp-pm'));

            if ((SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive'))) {
    			sp_PmEmptyInboxButton('tagClass=spRight', '', __('Empty your message inbox', 'sp-pm'));
    			sp_PmMarkInboxReadButton('tagClass=spRight', '', __('Mark all threads in Inbox as read', 'sp-pm'));
    	        sp_PmComposeButton('tagClass=spRight', '', __('Compose a new private message', 'sp-pm'));
    		} else {
    			sp_PmEmptyInboxButton('tagClass=spButton spRight', __('Empty Inbox', 'sp-pm'), __('Empty your message inbox', 'sp-pm'));
    			sp_PmMarkInboxReadButton('tagClass=spButton spRight', __('Mark Inbox Read', 'sp-pm'), __('Mark all threads in Inbox as read', 'sp-pm'));
    	        sp_PmComposeButton('tagClass=spButton spRight', __('Compose', 'sp-pm'), __('Compose a new private message', 'sp-pm'));
    	    }
    	sp_SectionEnd('tagClass=spClear', 'pmHeadButtons');

    	sp_SectionStart('tagId=spPmHeaderMessages&tagClass=spPmHeaderMessages spPlainSection', 'pmHeadMessages');
			sp_PmHeaderIcon('tagId=spPmHeaderIcon&tagClass=spHeaderIcon spLeft');
            sp_PmInboxMessages('break=1',
                    __('Your inbox has %MCOUNT% messages in %TCOUNT% threads', 'sp-pm'),
                    __('Your inbox has exceeded the maximum allowed (%MAX%). You will not be able to send any more PMs until your inbox size is reduced', 'sp-pm'),
                    __('Your inbox has reached the maximum allowed (%MAX%). You will not be able to send any more PMs until your inbox size is reduced', 'sp-pm'),
                    __('Your inbox is approaching the maximum allowed (%MAX%). You will not be able to send any more PMs if your inbox reaches the maximum size', 'sp-pm'),
                    __('Please note, PMs are automatically removed after %COUNT% days.', "sp-pm"));
    	sp_SectionEnd('', 'pmHeadMessages');
	sp_SectionEnd('tagClass=spClear', 'pmHeader');

    do_action('sp_pm_header_end');
