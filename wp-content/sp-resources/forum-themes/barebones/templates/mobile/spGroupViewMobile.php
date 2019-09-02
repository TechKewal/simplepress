<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Group View Mobile
#	Author		:	Simple:Press
#
#	The 'group' template is used to display the Group/Forum Index Listing for Mobile
#
# --------------------------------------------------------------------------------------

	# Load the forum header template - normally first thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadContainer', 'forumHead');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'forumHead');

	sp_SectionStart('tagClass=spBodyContainer', 'forumBody');

		# Start the 'groupView' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spListSection', 'groupView');

			# Start the Group Loop
			# ----------------------------------------------------------------------
			if (SP()->forum->view->has_groups()) : while (SP()->forum->view->loop_groups()) : SP()->forum->view->the_group();

				# Start the 'groupHeader' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spGroupViewSection', 'eachGroup');

					sp_SectionStart('tagClass=spGroupViewHeader', 'groupHeader');

						sp_SectionStart('tagClass=spFlexHeadContainer', 'groupFlexHeader');

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

						sp_SectionEnd('', 'groupFlexHeader');

						sp_GroupHeaderDescription('tagClass=spHeaderDescription');
						sp_InsertBreak();
						sp_GroupHeaderMessage('tagClass=spHeaderMessage');
						sp_InsertBreak();
						sp_InsertBreak();

					sp_SectionEnd('', 'groupHeader');

					sp_SectionStart('tagClass=spGroupForumContainer', 'groupViewForums');

						# Start the Forum Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_forums()) : while (SP()->forum->view->loop_forums()) : SP()->forum->view->the_forum();

							# Start the 'forum' section
							# ----------------------------------------------------------------------
                            sp_SectionStart('', 'eachForum');

                                sp_SectionStart('tagClass=spGroupForumHeader', 'eachForumTitle');

	    							sp_UserNewPostFlag('', 'group');
		    						sp_ForumIndexName('tagClass=spRowName spLeft', __sp('Browse topics in %NAME%'));
			    					sp_ForumIndexTopicCount('tagClass=spInRowCount spLeft', __sp('-'), __sp('Topic'));
				    				sp_InsertBreak('');

							    sp_SectionEnd('', 'eachForumTitle');

                                sp_SectionStart('tagClass=spGroupForumSection', 'eachForumData');

							    	# Column 1 of the forum row
								    sp_ColumnStart('tagClass=spColumnSection spLeft&width=89%&height=auto');
									    sp_ForumIndexLastPost('tagClass=holder spLeft&order=TLDU&truncate=40&nicedate=1&date=0&time=1&stackdate=1&icon=&itemBreak= ', __sp('Last Post'), __sp('No Topics'));
				    				sp_ColumnEnd();

				    				# Column 2 of the forum row
					    			# ----------------------------------------------------------------------
						    		sp_ColumnStart('tagClass=spActionsColumnSection spRight&width=10%&height=55px');
							    		sp_ForumIndexLockIcon('tagClass=spIcon spRight', __sp('This forum is locked'));
								    	sp_InsertBreak();
			    						sp_ForumIndexAddIcon('tagClass=spIcon spRight', __sp('Add new topic in this forum'), __sp(''));
				    					sp_InsertBreak();
					    			sp_ColumnEnd();

                                sp_SectionEnd('', 'eachForumData');

							sp_SectionEnd('', 'eachForum');

							sp_SectionStart('tagClass=spSubForumHolder', 'eachForumSubs');

								sp_ForumIndexSubForums('stack=0&unreadIcon=sp_SubForumUnreadIcon.png&stack=1&topicCount=0', __sp(''), __sp('Browse topics in %NAME%'));

							sp_SectionEnd('', 'eachForumSubs');

							sp_InsertBreak('spacer=15px');
							sp_ForumIndexInlinePosts();

						endwhile; else:
							sp_NoForumsInGroupMessage('tagClass=spMessage', __sp('There are no forums in this group'));
						endif;

					sp_SectionEnd('', 'groupViewForums');

				sp_SectionEnd('', 'eachGroup');

			endwhile; else:
				sp_NoGroupMessage('tagClass=spMessage', __sp('The requested group does not exist or you do not have permission to view it'), __sp('No groups have been created yet'));
			endif;

		sp_SectionEnd('', 'groupView');

	sp_SectionEnd('', 'forumBody');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'forumFoot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'forumFoot');
