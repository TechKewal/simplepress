<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Private Messaging Threads View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'pm thread' template is used to display the private messaging inbox thread list
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

	sp_SectionStart('tagClass=spHeadContainer', 'pmHead');
		sp_load_template('spHead.php');
	sp_SectionEnd('', 'pmHead');

    global $spPmThreadList, $spThisPmThreadList;

	$spPmThreadList = new spPmThreadList();

	sp_SectionStart('tagClass=spBodyContainer', 'pmThreadContainer');
		sp_SectionStart('tagId=spPmContainer&tagClass=spPlainSection', 'pmThreadWrapper');

            sp_PmHeader();

			# page links
			# ---------------------------------------------------
			sp_SectionStart('tagId=spPmContainer&tagClass=spPlainSection', 'spPmPageLinks');
				sp_PmPageLinks('tagClass=spPageLinks spLeft&prevIcon=&nextIcon=&showEmpty=0', __sp('Page: '), __sp('Jump to page %PAGE%'), __sp('Jump to page'));
			sp_SectionEnd('tagClass=spClear', 'spPmPageLinks');

			sp_SectionStart('tagId=spPmBody&tagClass=spPlainSection spForumTopicContainer', 'pmThreadBody');
    			if (sp_pm_has_threadlist()) {
                    while (sp_pm_loop_threadlist()) {
                        sp_pm_threadlist();

            			sp_SectionStart('tagId=spPmThreadSection'.$spThisPmThreadList->thread_id.'&tagClass=spPmThreadSection spForumTopicSection '.($spThisPmThreadList->read_status ? 'spPmRead' : 'spPmUnread'), 'pmThread');
    						# Column 1 of the thread row
    						# ----------------------------------------------------------------------
    						sp_ColumnStart('tagId=spColThread1&tagClass=spColumnSection spLeft&height=65px');
                                sp_UserAvatar('context=user&size=50', $spThisPmThreadList->last_sender_id);
    						sp_ColumnEnd();

    						# Column 2 of the thread row
    						# ----------------------------------------------------------------------
                            echo "<a class='' href='".SP()->spPermalinks->get_url("private-messaging/thread/$spThisPmThreadList->thread_id")."' title='".__('Click to view this thread', 'sp-pm')."'>";
    						sp_ColumnStart('tagId=spColThread2&tagClass=spColumnSection spLeft&height=65px');
								if (SP()->core->device == 'mobile') {
									# last message
									sp_PmThreadIndexTitle('tagClass=spRowName');
									sp_PmThreadIndexSender('tagClass=spInRowLabel&spanClass=', __('Last Received: ', 'sp-pm'));
									sp_InsertBreak();
									sp_PmThreadIndexDate('tagClass=spInRowLabel&spanClass=', __(' on ', 'sp-pm'));

									sp_InsertBreak();

									# first message
									sp_PmThreadIndexFirstSender('tagClass=spInRowLabel&spanClass=', __('Thread Started by: ', 'sp-pm'));
									sp_InsertBreak();
									sp_PmThreadIndexFirstDate('tagClass=spInRowLabel&spanClass=', __(' on ', 'sp-pm'));
								}

								if (SP()->core->device != 'mobile') {
									# last message
									sp_PmThreadIndexTitle('tagClass=spRowName', __('Subject: ', 'sp-pm'));
									sp_PmThreadIndexSender('tagClass=spInRowLabel&spanClass=', __('Last Message: ', 'sp-pm'));
									sp_PmThreadIndexDate('tagClass=spInRowLabel&spanClass=', __(' on ', 'sp-pm'));

									sp_InsertBreak();

									# first message
									sp_PmThreadIndexFirstSender('tagClass=spInRowLabel&spanClass=', __('Thread Started by: ', 'sp-pm'));
									sp_PmThreadIndexFirstDate('tagClass=spInRowLabel&spanClass=', __(' on ', 'sp-pm'));
								}

    						sp_ColumnEnd();
                            echo '</a>';

    						# Column 3 of the thread row
    						# ----------------------------------------------------------------------
    						sp_ColumnStart('tagId=spColThread3&tagClass=spColumnSection spRight&height=65px');
                            	sp_SectionStart('tagClass=spRight', 'pmThreadActions');
            						sp_PmThreadIndexDelete('tagClass=spPmDeleteIcon', __('Delete this thread', 'sp-pm'));
                            	sp_SectionEnd('', 'pmThreadActions');

        						sp_InsertBreak('spacer=5px');

                            	sp_SectionStart('tagClass=spRight', 'pmThreadMeta');
									if (SP()->core->device == 'mobile') {
	                                    sp_PmThreadIndexMessageCount('tagClass=spLabelBordered spRight&spanClass=');
									}

									if (SP()->core->device != 'mobile') {
	                                    sp_PmThreadIndexMessageCount('tagClass=spLabelSmall spRight&spanClass=', __(' Message(s)', 'sp-pm'));
	                                }
                            	sp_SectionEnd('', 'pmThreadMeta');
    						sp_ColumnEnd();
            			sp_SectionEnd('tagClass=spClear', 'pmThread');
				    }
				    # page links
				    # ---------------------------------------------------
					sp_SectionStart('tagId=spPmContainer&tagClass=spPlainSection', 'spPmPageLinks');
						sp_InsertBreak('spacer=15px');
						sp_PmPageLinks('tagClass=spPageLinks spLeft&prevIcon=&nextIcon=&showEmpty=0', __sp('Page: '), __sp('Jump to page %PAGE%'), __sp('Jump to page'));
					sp_SectionEnd('tagClass=spClear', 'spPmPageLinks');
                } else {
					sp_NoPmThreads('tagClass=spMessage',
                                    __('Your inbox is empty', 'sp-pm'),
                                    __('You do not have permission to use the PM system', 'sp-pm'),
                                    __('You have opted out of the PM system. You can opt back in from your forum profile.', 'sp-pm'));
				}
			sp_SectionEnd('', 'pmThreadBody');

            sp_PmFooter();
		sp_SectionEnd('', 'pmThreadWrapper');
	sp_SectionEnd('', 'pmThreadContainer');

	sp_SectionStart('tagClass=spFootContainer', 'pmFoot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'pmFoot');
