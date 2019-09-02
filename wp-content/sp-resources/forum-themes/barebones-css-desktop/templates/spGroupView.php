<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Group View
#	Author		:	Simple:Press
#
#	The 'group' template is used to display the Group/Forum Index Listing for Desktop
#
# --------------------------------------------------------------------------------------

	# Load the forum header template - normally first thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadContainer', 'head');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

		# Start the 'groupView' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spListSection', 'groupView');

			# Start the Group Loop
			# ----------------------------------------------------------------------
			if (SP()->forum->view->has_groups()) : while (SP()->forum->view->loop_groups()) : SP()->forum->view->the_group();

				# Start the 'groupHeader' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spGroupViewSection', 'group');

					sp_SectionStart('tagClass=spGroupViewHeader', 'header');

						sp_SectionStart('tagClass=spFlexHeadContainer', 'flexheader');

							sp_ColumnStart('tagId=spIconCol&tagClass=spIconColumnSectionTitle  spLeft&width=0&height=0px');
								sp_GroupHeaderIcon('tagClass=spRowIcon spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn2&tagClass=spTitleColumnTitle spLeft&width=80%&height=0');
								sp_GroupHeaderName('tagClass=spHeaderName');
								sp_GroupHeaderDescription('tagClass=spHeaderDescription');
								sp_InsertBreak();
							sp_ColumnEnd();

							sp_ColumnStart('tagClass=spHeadColumn3 spRight&width=auto&height=0');
									sp_GroupHeaderRSSButton('tagClass=spLink spRight&iconClass=spIcon&icon=', __sp('RSS'), __sp('Subscribe to the RSS feed for this forum group'));
							sp_ColumnEnd();

						sp_SectionEnd('', 'flexheader');

						sp_GroupHeaderMessage('tagClass=spHeaderMessage');

						# Column Titles
						# ----------------------------------------------------------------------
						sp_SectionStart('tagClass=spCategoryLabels', 'catlabels');

							sp_ColumnStart('tagId=spIconCol&tagClass=spColumnSection spLeft&width=0&height=0px');
								sp_GroupHeaderIcon('tagClass=spRowIconHidden spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColGroup2&tagClass=spTitleColumn spLeft&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Forum'));
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColGroup3&tagClass=spTitleColumn  spLeft&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel&labelClass=spColumnTitleCentered spLeft', __sp('Topics'));
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColGroup4&tagClass=spTitleColumn spLeft&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Last Post'));
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColGroup5&tagClass=spTitleColumn spRight&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', '');
							sp_ColumnEnd();

						sp_SectionEnd('', 'catlabels');

					sp_SectionEnd('', 'header');

					sp_SectionStart('tagClass=spGroupForumContainer', 'forumList');

						# Start the Forum Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_forums()) : while (SP()->forum->view->loop_forums()) : SP()->forum->view->the_forum();

							# Start the 'forum' section
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spGroupForumSection', 'forum');

								# Column 1 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spIconCol&tagClass=spColumnSection spLeft&width=0&height=0px');
									sp_ForumIndexIcon('tagClass=spRowIcon spLeft');
								sp_ColumnEnd();

								# Column 2 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColGroup2&tagClass=spColumnSection spLeft&width=0&height=0px');
									sp_UserNewPostFlag('', 'group');
									sp_ForumIndexName('tagClass=spRowName', __sp('Browse topics in %NAME%'));
									sp_InsertBreak('direction=left');
									sp_ForumIndexDescription('tagClass=spRowDescription');
								sp_ColumnEnd();

								# Column 3 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColGroup3&tagClass=spColumnSection spLeft&width=0&height=0px');
									sp_ForumIndexTopicCount('tagClass=spInRowCount spCenter', __sp(''), __sp(''));
								sp_ColumnEnd();

								# Column 4 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColGroup4&tagClass=spColumnSection spLeft&width=0&height=0px');
									sp_ForumIndexLastPost('tagClass=holder spLeft&order=TLDU&nicedate=1&date=0&time=1&stackdate=1&itemBreak= ', __sp(''), __sp('No Topics'));
								sp_ColumnEnd();

								# Column 5 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColGroup5&tagClass=spColumnSection spRight&width=0&height=0px');
									sp_ForumIndexLockIcon('tagClass=spIcon spRight', __sp('This forum is locked'));
									sp_ForumIndexAddIcon('tagClass=spIcon spRight', __sp('Add new topic in this forum'));
									sp_ForumIndexDeniedIcon('tagClass=spIcon spRight', __sp('No permission to start topics'));
								sp_ColumnEnd();

							sp_SectionEnd('', 'forum');

							sp_ForumIndexSubForums('unreadIcon=sp_SubForumUnreadIcon.png&topicCount=0', __sp('Sub-Forums:'), __sp('Browse topics in %NAME%'));


						endwhile; else:
							sp_NoForumsInGroupMessage('tagClass=spMessage', __sp('There are no forums in this group'));
						endif;

					sp_SectionEnd('', 'forumList');

				sp_SectionEnd('', 'group');

			endwhile; else:
				sp_NoGroupMessage('tagClass=spMessage', __sp('The requested group does not exist or you do not have permission to view it'), __sp('No groups have been created yet'));
			endif;

		sp_SectionEnd('', 'groupView');

		# RECENT POST LIST - STYLED BUT HIDDEN
		sp_RecentPostList('show=5&admins=1', __sp('Unread and recently updated topics'));

	sp_SectionEnd('', 'body');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');

?>