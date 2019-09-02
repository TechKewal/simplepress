<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	List View (simplified topic listing)
#	Author		:	Simple:Press
#
#	The 'list' template is used to display a simplified topic listing
#
# --------------------------------------------------------------------------------------

	# Start the 'listView' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spListSection spListViewSection', 'listView');

		sp_ListViewHead();

		sp_SectionStart('tagClass=spCategoryLabels', 'listSectionTitles');

			sp_ColumnStart('tagClass=spColumnSection spLeft&width=35%&height=0');
				sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Topic'));
			sp_ColumnEnd();

			sp_ColumnStart('tagClass=spColumnSection spLeft&width=35%&height=0');
				sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Forum'));
			sp_ColumnEnd();

			sp_ColumnStart('tagClass=spColumnSection spLeft&width=15%&height=0');
				sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Last Post'));
			sp_ColumnEnd();

			sp_ColumnStart('tagClass=spColumnSection spLeft&width=7%&height=0');
				sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', '');
			sp_ColumnEnd();

		sp_SectionEnd('', 'listSectionTitles');

		# Start the List Loop
		# ----------------------------------------------------------------------
		if (SP()->forum->view->has_topiclist()) : while (SP()->forum->view->loop_topiclist()) : SP()->forum->view->the_topiclist();

			# Start the 'list' section
			# ----------------------------------------------------------------------
			sp_SectionStart('tagClass=spTopicListSection spTextLeft', 'eachListTopic');

				sp_ListViewBodyStart();

				sp_ColumnStart('tagClass=spColumnSection spLeft&width=35%&height=0');
					sp_ListTopicName('', __sp('Go to %NAME%'));
				sp_ColumnEnd();

				sp_ColumnStart('tagClass=spColumnSection spLeft&width=35%&height=0');
					sp_ListForumName('', __sp('Browse topics in %NAME%'), __sp(''));
				sp_ColumnEnd();

				sp_ColumnStart('tagClass=spColumnSection spLeft&width=15%&height=0');
					sp_ListLastPost('iconClass=spIcon spLeft&break=1&icon=sp_goNewPost.png&labelLink=1', __sp(''));
				sp_ColumnEnd();

				sp_ColumnStart('tagClass=spColumnSection spLeft&width=7%&height=0');
					sp_UserNewPostFlag('', 'list');
				sp_ColumnEnd();

				sp_ListViewBodyEnd();
				sp_InsertBreak();

			sp_SectionEnd('', 'eachListTopic');

		endwhile; else:
			sp_NoTopicsInListMessage('tagClass=spMessage', __sp('There were no topics found'));
		endif;

		sp_ListViewFoot();

	sp_SectionEnd('', 'listView');
