<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	group
#	Author		:	Simple:Press
#
#	The 'group' template is used to display the Group/Forum Index Listing
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
								sp_InsertBreak();
							sp_ColumnEnd();

							sp_ColumnStart('tagClass=spHeadColumn3 spRight&width=auto&height=0');
								sp_GroupHeaderRSSButton('tagClass=spLink spRight&iconClass=spIcon&icon= spRight', __sp('RSS'), __sp('Subscribe to the RSS feed for this forum group'));
							sp_ColumnEnd();

							sp_InsertBreak();

						sp_SectionEnd('', 'flexheader');

						sp_GroupHeaderDescription('tagClass=spHeaderDescription');
						sp_InsertBreak();
						sp_GroupHeaderMessage('tagClass=spHeaderMessage');
						sp_InsertBreak();
						sp_InsertBreak();

					sp_SectionEnd('', 'header');

					sp_SectionStart('tagClass=spGroupForumContainer', 'forumlist');

						# Start the Forum Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_forums()) : while (SP()->forum->view->loop_forums()) : SP()->forum->view->the_forum();

							# Start the 'forum' section
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spGroupForumHeader', 'forumHeader');

								sp_UserNewPostFlag('', 'group');
								sp_ForumIndexName('tagClass=spRowName spLeft', __sp('Browse topics in %NAME%'));
								sp_ForumIndexTopicCount('tagClass=spInRowCount spLeft', __sp('-'), __sp('Topic'));
								sp_InsertBreak('direction=left');
								sp_InsertBreak('');

							sp_SectionEnd('', 'forumHeader');

							sp_SectionStart('tagClass=spGroupForumSection', 'forum');

								# Column 1 of the forum row
								sp_ColumnStart('tagClass=spColumnSection spLeft&width=89%&height=auto');
									sp_ForumIndexLastPost('tagClass=holder spLeft&order=TLDU&truncate=40&nicedate=1&date=0&time=1&stackdate=1&itemBreak= ', __sp('Last Post'), __sp('No Topics'));
								sp_ColumnEnd();

								# Column 2 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagClass=spActionsColumnSection spRight&width=10%&height=55px');
									sp_ForumIndexLockIcon('tagClass=spIcon spRight', __sp('This forum is locked'));
									sp_InsertBreak();
									sp_ForumIndexAddIcon('tagClass=spIcon spRight', __sp('Add new topic in this forum'));
									sp_InsertBreak();
								sp_ColumnEnd();

							sp_SectionEnd('', 'forum');

							sp_SectionStart('tagClass=spSubForumHolder', 'subForumHolder');

								sp_ForumIndexSubForums('stack=0&unreadIcon=sp_SubForumUnreadIcon.png&stack=1&topicCount=0', __sp(''), __sp('Browse topics in %NAME%'));

							sp_SectionEnd('', 'subForumHolder');

							sp_InsertBreak('spacer=15px');
							sp_ForumIndexInlinePosts();

						endwhile; else:
							sp_NoForumsInGroupMessage('tagClass=spMessage', __sp('There are no forums in this group'));
						endif;

					sp_SectionEnd('', 'forumlist');

				sp_SectionEnd('', 'group');

			endwhile; else:
				sp_NoGroupMessage('tagClass=spMessage', __sp('The requested group does not exist or you do not have permission to view it'), __sp('No groups have been created yet'));
			endif;

		sp_SectionEnd('', 'groupView');

	sp_SectionEnd('', 'body');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');

?>