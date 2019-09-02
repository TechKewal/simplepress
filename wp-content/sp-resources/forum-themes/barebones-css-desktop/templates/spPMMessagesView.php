<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Private Messaging Messages View
#	Author		:	Simple:Press
#
#	The 'pm messages' template is used to display the private messaging thread page
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2014-01-23 00:39:27 +0000 (Thu, 23 Jan 2014) $
$Rev: 10990 $
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

			sp_SectionStart('tagId=spPmBody&tagClass=spPlainSection spTopicPostContainer', 'pmMessageBody');

				if (sp_pm_messagelist()) {

					sp_SectionStart('tagId=spPmThreadSection'.$spPmMessageList->pm_thread_id.'&tagClass=spPmMessageHeaderSection', 'pmMessageList');

						sp_SectionStart('tagClass=spPlainSection spPmThreadHeader', 'pmThreadHeader');

							sp_ColumnStart('tagClass=spColHeader1 spColumnSection spLeft&height=auto');

								sp_PmThreadTitle('tagClass=spRowName spLeft', __('Subject: ', 'sp-pm'));

							sp_ColumnEnd();

						sp_SectionEnd('tagClass=spClear', 'pmThreadHeader');

						while (sp_pm_loop_messagelist()) {

							sp_pm_the_messagelist();

							sp_SectionStart('tagId=spPmMessageSection'.$spThisPmMessageList->message_id.'&tagClass=spPmMessageSection spTopicPostSection '.($spThisPmMessageList->read_status ? 'spPmRead' : 'spPmUnread'), 'pmMessage');

								# Column 1 of the message row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColMessage1&tagClass=spColumnSection spLeft&height=auto&width=auto');
									sp_UserAvatar('context=user&size=50', $spThisPmMessageList->sender);
								sp_ColumnEnd();

								# Column 2 of the message row
								# ----------------------------------------------------------------------
								echo "<a class='' href='javascript:void(null)' onclick='spjToggleLayer(\"PmMessageContent".$spThisPmMessageList->message_id."\");' title='".__('Click to toggle message content viewing', 'sp-pm')."'>";

								sp_ColumnStart('tagId=spColMessage2&tagClass=spColumnSection spLeft&height=auto&width=auto');
									sp_PmMessageIndexSender('tagClass=spInRowLabel spInRowLabelBold spLeft', __('From: ', 'sp-pm'));
									sp_InsertBreak();
									sp_PmMessageIndexDate('tagClass=spInRowLabel spLeft');
									sp_InsertBreak();
									sp_PmMessageIndexRecipients('tagClass=spInRowLabel', __('To: ', 'sp-pm'), __('cc: ', 'sp-pm'), __('bcc: ', 'sp-pm'));
								sp_ColumnEnd();

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
										sp_SectionStart('tagClass=pmMessageActions', 'pmMessageActions');

											sp_PmMessageIndexReplyAll('tagClass=spButton spLeft&icon=', __('Reply to all', 'sp-pm'), __sp('Reply All'));
											sp_PmMessageIndexQuoteAll('tagClass=spButton spLeft&icon=', __('Quote and reply to all', 'sp-pm'), __sp('Quote All'));
											sp_PmMessageIndexDelete('tagClass=spButton spRight&icon=', __('Delete this message', 'sp-pm'), __sp('Delete'));
											sp_PmMessageIndexMarkUnread('tagClass=spButton spRight&icon=', __('Mark this message as unread', 'sp-pm'), __sp('Mark Unread'));
											sp_PmMessageIndexForward('tagClass=spButton spRight&icon=', __('Forward this message', 'sp-pm'), __sp('Forward'));
											sp_PmMessageIndexQuote('tagClass=spButton spRight&icon=', __('Quote and reply to sender', 'sp-pm'), __sp('Quote'));
											sp_PmMessageIndexReply('tagClass=spButton spRight&icon=', __('Reply to sender', 'sp-pm'), __sp('Reply'));

										sp_SectionEnd('', 'pmMessageActions');

										sp_InsertBreak();

										echo '<hr />';

										sp_SectionStart('tagClass=spPostSection', 'pmAttachments');

											sp_PmMessageIndexAttachments();

										sp_SectionEnd('tagClass=spClear', 'pmAttachments');

									sp_SectionEnd('tagClass=spClear', 'PmContentSection');

								sp_SectionEnd('tagClass=spClear', 'pmMessageContent');

							sp_SectionEnd('tagClass=spClear', 'pmMessage');
						}

					sp_SectionEnd('tagClass=spClear', 'pmMessageList');

				} else {

					sp_NoPmMessages('tagClass=spMessage',
									__('You have no messages in this thread', 'sp-pm'),
									__('You do not have permission to use the PM system', 'sp-pm'),
									__('You have opted out of the PM system. You can opt back in from your forum profile.', 'sp-pm'),
									__('A thread ID must be provided when attempting to view a thread.', 'sp-pm'));
				}

			sp_SectionEnd('', 'pmMessageBody');

			sp_PmFooter();

		sp_SectionEnd('', 'pmMessageWrapper');

	sp_SectionEnd('', 'pmMessageContainer');

	sp_SectionStart('tagClass=spFootContainer', 'pmFoot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'pmFoot');
?>