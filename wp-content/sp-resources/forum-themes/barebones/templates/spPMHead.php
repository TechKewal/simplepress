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
$LastChangedDate: 2013-11-05 01:15:36 +0000 (Tue, 05 Nov 2013) $
$Rev: 10825 $
*/

    global $spPmThreadList, $spThisPmThreadList;

    do_action('sp_pm_header_begin');

	sp_SectionStart('tagId=spPmHeader&tagClass=spPmHeader spPlainSection spTopicViewSection', 'pmHeader');

		sp_SectionStart('tagId=spPmHeaderButtons&tagClass=spPmHeaderButtons spPlainSection', 'pmHeadButtons');

			sp_PmQuickLinksThreads('tagClass=spControl spSelect spLeft&show=5', __('New/Recent Messages', 'sp-pm'));

    		if (SP()->core->device != 'mobile') {

			   sp_PmComposeButton('tagClass=spButton spRight', __('Compose', 'sp-pm'), __('Compose a new private message', 'sp-pm'));
				sp_PmEmptyInboxButton('tagClass=spButton spRight', __('Empty Inbox', 'sp-pm'), __('Empty your message inbox', 'sp-pm'));
    			sp_PmMarkInboxReadButton('tagClass=spButton spRight', __('Mark Inbox Read', 'sp-pm'), __('Mark all threads in Inbox as read', 'sp-pm'));

			}

		sp_SectionEnd('tagClass=spClear', 'pmHeadButtons');

    	sp_SectionStart('tagId=spPmHeaderMessages&tagClass=spPmHeaderMessages spPlainSection', 'pmHeadMessages');

			sp_PmHeaderIcon('tagId=spPmHeaderIcon&tagClass=spHeaderIcon spLeft');
            sp_PmInboxMessages('break=0',
                    __('Your inbox has %MCOUNT% messages in %TCOUNT% threads', 'sp-pm'),
                    __('Your inbox has exceeded the maximum allowed (%MAX%). You will not be able to send any more PMs until your inbox size is reduced', 'sp-pm'),
                    __('Your inbox has reached the maximum allowed (%MAX%). You will not be able to send any more PMs until your inbox size is reduced', 'sp-pm'),
                    __('Your inbox is approaching the maximum allowed (%MAX%). You will not be able to send any more PMs if your inbox reaches the maximum size', 'sp-pm'),
                    __('Please note, PMs are automatically removed after %COUNT% days.', "sp-pm"));

		sp_SectionEnd('', 'pmHeadMessages');

		if (SP()->core->device == 'mobile') {

			sp_SectionStart('tagClass=spActionsBar', 'headerButtons');

				sp_PmComposeButton('tagClass=spFootButton spRight', __('Compose', 'sp-pm'), __('Compose a new private message', 'sp-pm'));
				sp_PmEmptyInboxButton('tagClass=spFootButton spRight', __('Empty Inbox', 'sp-pm'), __('Empty your message inbox', 'sp-pm'));
				sp_PmMarkInboxReadButton('tagClass=spFootButton spRight', __('Mark All Read', 'sp-pm'), __('Mark all threads in Inbox as read', 'sp-pm'));

			sp_SectionEnd('tagClass=spClear', 'headerButtons');

		}

	sp_SectionEnd('tagClass=spClear', 'pmHeader');

    do_action('sp_pm_header_end');
