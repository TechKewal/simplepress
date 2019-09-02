<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	list (simplified topic listing)
#	Author		:	Simple:Press
#
#	The 'list' template is used to display a simplified Topic Listing
#
# --------------------------------------------------------------------------------------

	# Start the 'listView' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spListSection spListViewSection', 'listView');

		sp_ListViewHead();

		# Start the List Loop
		# ----------------------------------------------------------------------
		if (SP()->forum->view->has_topiclist()) : while (SP()->forum->view->loop_topiclist()) : SP()->forum->view->the_topiclist();

			# Start the 'list' section
			# ----------------------------------------------------------------------
			sp_ListViewBodyStart();

			sp_SectionStart('tagClass=spTopicListSection spTextLeft', 'list');

				sp_ListForumName('', __sp('Browse topics in %NAME%'), '');
				sp_InsertBreak();

				sp_ColumnStart('tagClass=spColumnSection spLeft&width=auto&height=auto');
					sp_ListTopicName('tagClass=spLeft', __sp('Browse the thread %NAME%'));
					sp_InsertBreak('');
					sp_ListLastPost('tagClass=spLeft&height=0px&break=0&icon=', __sp(''));
					sp_InsertBreak('spacer=8px');
					sp_UserNewPostFlag('locationClass=spLeft', 'list');
				sp_ColumnEnd();

				sp_ListViewBodyEnd();

				sp_InsertBreak();

			sp_SectionEnd('', 'list');

		endwhile; else:
			sp_NoTopicsInListMessage('tagClass=spMessage', __sp('There were no topics found'));
		endif;

		sp_ListViewFoot();

	sp_SectionEnd('', 'listView');
?>