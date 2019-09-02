<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Private Messaging Messages View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'pm messages' template is used to display the private messaging thread page
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

	sp_SectionStart('tagClass=spHeadContainer', 'pmHead');
		sp_load_template('spHead.php');
	sp_SectionEnd('', 'pmHead');

	global $spPmThreadList;
	$spPmThreadList = new spPmThreadList();

	sp_SectionStart('tagClass=spBodyContainer', 'pmMessageContainer');
		sp_SectionStart('tagId=spPmContainer&tagClass=spPlainSection', 'pmMessageWrapper');
			sp_PmHeader();

			global $spPmMessageList, $spThisPmMessageList;
			$spPmMessageList = new spPmMessageList(SP()->rewrites->pageData['thread']);

			# page links
			# ---------------------------------------------------
			sp_SectionStart('tagId=spPmContainer&tagClass=spPlainSection', 'spPmPageLinks');
				sp_PmPageLinks('tagClass=spPageLinks spLeft&prevIcon=&nextIcon=&showEmpty=0', __sp('Page: '), __sp('Jump to page %PAGE%'), __sp('Jump to page'));
			sp_SectionEnd('tagClass=spClear', 'spPmPageLinks');

			sp_SectionStart('tagId=spPmBody&tagClass=spPlainSection spTopicPostContainer', 'pmMessageBody');
				if (sp_pm_messagelist()) {
					sp_SectionStart('tagId=spPmThreadSection'.$spPmMessageList->pm_thread_id.'&tagClass=spPmMessageHeaderSection', 'pmMessageList');
						sp_SectionStart('tagClass=spPlainSection spPmThreadHeader', 'pmThreadHeader');
							sp_ColumnStart('tagId=spColHeader1&tagClass=spColumnSection spLeft&height=35px');

								if (SP()->core->device == 'mobile') {
									sp_PmThreadTitle('tagClass=spRowName spLeft', '');
								}
								if (SP()->core->device != 'mobile') {
									sp_PmThreadTitle('tagClass=spRowName spLeft', __('Subject: ', 'sp-pm'));
								}

							sp_ColumnEnd();
							sp_ColumnStart('tagId=spColHeader2&tagClass=spColumnSection spRight&height=35px');
								sp_PmThreadDelete('tagClass=spPmDeleteIcon spRight', __('Delete this thread', 'sp-pm'));
								sp_PmThreadCollapseMessages('tagClass=spPmThreadCollapse spRight', __('Collapse all messages', 'sp-pm'));
								sp_PmThreadExpandMessages('tagClass=spPmThreadExpand spRight', __('Expand all messages', 'sp-pm'));
							sp_ColumnEnd();
						sp_SectionEnd('tagClass=spClear', 'pmThreadHeader');

						while (sp_pm_loop_messagelist()) {
							sp_pm_the_messagelist();
							sp_SectionStart('tagId=spPmMessageSection'.$spThisPmMessageList->message_id.'&tagClass=spPmMessageSection spTopicPostSection '.($spThisPmMessageList->read_status ? 'spPmRead' : 'spPmUnread'), 'pmMessage');
								# Column 1 of the message row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColMessage1&tagClass=spColumnSection spLeft&height=55px');
									sp_UserAvatar('context=user&size=50', $spThisPmMessageList->sender);
								sp_ColumnEnd();

								# Column 2 of the message row
								# ----------------------------------------------------------------------
								echo "<a class='spPMToggleView' data-target='PmMessageContent$spThisPmMessageList->message_id' title='".__('Click to toggle message content viewing', 'sp-pm')."'>";
								sp_ColumnStart('tagId=spColMessage2&tagClass=spColumnSection spLeft&height=55px');
									sp_PmMessageIndexSender('', __('From: ', 'sp-pm'));
									sp_PmMessageIndexRecipients('', __('To: ', 'sp-pm'), __('cc: ', 'sp-pm'), __('bcc: ', 'sp-pm'));

									if (SP()->core->device == 'mobile') {
										sp_InsertBreak();
										sp_PmMessageIndexDate('tagClass=spInRowLabel spLeft');
									}
								sp_ColumnEnd();

								if (SP()->core->device != 'mobile') {
									# Column 3 of the message row
									# ----------------------------------------------------------------------
									sp_ColumnStart('tagId=spColMessage3&tagClass=spColumnSection spLeft&height=55px');
										sp_SectionStart('tagClass=spRight', 'pmMessageDate');
											sp_PmMessageIndexDate('tagClass=spInRowLabel spRight');
										sp_SectionEnd('', 'pmMessageDate');
										sp_InsertBreak();
									sp_ColumnEnd();
								}
								echo '</a>';

								sp_InsertBreak('');

								# determine open/close status of each message
								$status = (!$spThisPmMessageList->read_status || $spPmMessageList->currentPm == 1 || (isset(SP()->user->thisUser->pmopenall) && SP()->user->thisUser->pmopenall)) ? '' : ' spInlineSection ';
								sp_SectionStart('tagId=PmMessageContent'.$spThisPmMessageList->message_id.'&tagClass=spPostSection spPmMessageContent'.$status, 'pmMessageContent');

									sp_SectionStart('tagClass=spPostContentSection', 'PmContentSection');
										sp_SectionStart('tagClass=spPostSection', 'pmContent');
											sp_PmMessageIndexContent('tagClass=spPostContent spPmContent');
										sp_SectionEnd('tagClass=spClear', 'pmContent');

											# message icon 'toolbar'
										sp_SectionStart('tagClass=spMessageActions spCenter', 'pmMessageActions');
											sp_PmMessageIndexReply('tagClass=spPmReply spInline&linkClass=spButton&icon=', __('Reply to sender', 'sp-pm'), __('Reply Sender', 'sp-pm'));
											sp_PmMessageIndexReplyAll('tagClass=spPmReplyAll spInline&linkClass=spButton&icon=', __('Reply to all', 'sp-pm'), __('Reply All', 'sp-pm'));
											sp_PmMessageIndexQuote('tagClass=spPmQuote spInline&linkClass=spButton&icon=', __('Quote and reply to sender', 'sp-pm'), __('Quote to Sender', 'sp-pm'));
											sp_PmMessageIndexQuoteAll('tagClass=spPmQuoteAll spInline&linkClass=spButton&icon=', __('Quote and reply to all', 'sp-pm'), __('Quote to All', 'sp-pm'));
											sp_PmMessageIndexForward('tagClass=spPmForward spInline&linkClass=spButton&icon=', __('Forward this message', 'sp-pm'), __('Forward', 'sp-pm'));
											sp_PmMessageIndexMarkUnread('tagClass=spPmMarkUnread spInline&linkClass=spButton&icon=', __('Mark this message as unread', 'sp-pm'), __('Mark Unread', 'sp-pm'));
											sp_PmMessageIndexDelete('tagClass=spPmMessageIndexDelete spInline&linkClass=spButton&icon=', __('Delete this message', 'sp-pm'), __('Delete', 'sp-pm'));
										sp_SectionEnd('', 'pmMessageActions');
										sp_InsertBreak('spacer=10px');

										sp_SectionStart('tagClass=spPostSection', 'pmAttachments');
											sp_PmMessageIndexAttachments();
										sp_SectionEnd('tagClass=spClear', 'pmAttachments');



									sp_SectionEnd('tagClass=spClear', 'PmContentSection');
								sp_SectionEnd('tagClass=spClear', 'pmMessageContent');
							sp_SectionEnd('tagClass=spClear', 'pmMessage');
						}
					sp_SectionEnd('tagClass=spClear', 'pmMessageList');

				    # page links
				    # ---------------------------------------------------
					sp_SectionStart('tagId=spPmContainer&tagClass=spPlainSection', 'spPmPageLinks');
						sp_InsertBreak('spacer=15px');
						sp_PmPageLinks('tagClass=spPageLinks spLeft&prevIcon=&nextIcon=&showEmpty=0', __sp('Page: '), __sp('Jump to page %PAGE%'), __sp('Jump to page'));
					sp_SectionEnd('tagClass=spClear', 'spPmPageLinks');

				} else {
					sp_NoPmMessages('tagClass=spMessage',
									__('You have no messages in this thread', 'sp-pm'),
									__('You do not have permission to use the PM system', 'sp-pm'),
									__('You have opted out of the PM system. You can opt back in from your forum profile.', 'sp-pm'),
									__('A thread ID must be provied when attempting to view a thread.', 'sp-pm'));
				}
			sp_SectionEnd('', 'pmMessageBody');
			sp_PmFooter();
		sp_SectionEnd('', 'pmMessageWrapper');
	sp_SectionEnd('', 'pmMessageContainer');

	sp_SectionStart('tagClass=spFootContainer', 'pmFoot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'pmFoot');
