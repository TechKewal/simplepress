<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Private Messaging Threads View
#	Author		:	Simple:Press
#
#	The 'pm thread' template is used to display the private messaging inbox thread list
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2014-01-23 00:39:27 +0000 (Thu, 23 Jan 2014) $
$Rev: 10990 $
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

			if (sp_pm_has_threadlist()) {
				while (sp_pm_loop_threadlist()) {

					sp_pm_threadlist();

					sp_SectionStart('tagId=spPmThreadSection'.$spThisPmThreadList->thread_id.'&tagClass=spPmThreadSection spForumTopicSection '.($spThisPmThreadList->read_status ? 'spPmRead' : 'spPmUnread'), 'pmThread');

						# Column 1 of the thread row
						# ----------------------------------------------------------------------
						sp_ColumnStart('tagClass=spColThread1 spColumnSection spLeft&height=55px');
							sp_UserAvatar('context=user&size=50', $spThisPmThreadList->last_sender_id);
							sp_InsertBreak();
							sp_PmThreadIndexMessageCount('tagClass=spLabelBordered spCenter&spanClass=');
						sp_ColumnEnd();

						# Column 2 of the thread row
						# ----------------------------------------------------------------------
						echo "<a class='' href='".SP()->spPermalinks->get_url("private-messaging/thread/$spThisPmThreadList->thread_id")."' title='".__('Click to view this thread', 'sp-pm')."'>";
						sp_ColumnStart('tagClass=spColThread2 spColumnSection spLeft&height=55px');

							# last message
							sp_PmThreadIndexTitle('tagClass=spRowName spLeft');
							sp_InsertBreak();
							sp_PmThreadIndexSender('tagClass=spInRowLabel spLeft&spanClass=', __('Last Message: ', 'sp-pm'));
							sp_InsertBreak();
							sp_PmThreadIndexDate('tagClass=spInRowLabel spLeft&spanClass=&nicedate=1', __('', 'sp-pm'));
							sp_InsertBreak();
							# first message
							sp_PmThreadIndexFirstSender('tagClass=spInRowLabel&spanClass=', __('Started By: ', 'sp-pm'));

						sp_ColumnEnd();

						echo '</a>';

						# Column 3 of the thread row
						# ----------------------------------------------------------------------
						sp_ColumnStart('tagClass=spColThread3 spColumnSection spRight&height=0');

							sp_SectionStart('tagClass=spRight', 'pmThreadMeta');

								sp_InsertBreak('');

							sp_SectionEnd('', 'pmThreadMeta');

						sp_ColumnEnd();

						sp_SectionStart('tagClass=spRight', 'pmThreadActions');

							sp_PmThreadIndexDelete('tagClass=spPMDelete&linkClass=spButton&icon=', __('Delete this thread', 'sp-pm'), __('Delete Thread'));

						sp_SectionEnd('', 'pmThreadActions');

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

			sp_PmFooter();

		sp_SectionEnd('', 'pmThreadWrapper');

	sp_SectionEnd('', 'pmThreadContainer');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');

?>