<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Forum
#	Author		:	Simple:Press
#
#	The 'Forum' template is used to display the Forum/Topic Index Listing
#
# --------------------------------------------------------------------------------------

	# == ADD TOPIC FORM - OBJECT DEFINITION ========================
	$addTopicForm = array(
		'tagClass'				=> 'spForm',
		'hide'					=> 1,
		'controlFieldset'		=> 'spEditorFieldset',
		'controlInput'			=> 'spControl',
		'controlSubmit'			=> 'spSubmit',
		'controlOrder'			=> 'cancel|save',
		'maxTitleLength'		=> 200,
		'labelHeading'			=> __sp('Add Topic'),
		'labelGuestName'		=> __sp('Guest name (Required)'),
		'labelGuestEmail'		=> __sp('Guest email (Required)'),
		'labelModerateAll'		=> __sp('NOTE: new posts are subject to administrator approval before being displayed'),
		'labelModerateOnce'		=> __sp('NOTE: first posts are subject to administrator approval before being displayed'),
		'labelTopicName'		=> __sp('Topic name'),
		'labelSmileys'			=> __sp('Smileys'),
		'labelOptions'			=> __sp('Options'),
		'labelOptionLock'		=> __sp('Lock this topic'),
		'labelOptionPin'		=> __sp('Pin this topic'),
		'labelOptionTime'		=> __sp('Edit topic timestamp'),
		'labelMath'				=> __sp('Math Required'),
		'labelMathSum'			=> __sp('What is the sum of'),
		'labelPostButtonReady'	=> __sp('Submit Topic'),
		'labelPostButtonMath'	=> __sp('Do Math To Save'),
		'labelPostCancel'		=> __sp('Cancel'),
		'tipSmileysButton'		=> __sp('Open/Close to Add a Smiley'),
		'tipOptionsButton'		=> __sp('Open/Close to select Posting Options'),
		'tipSubmitButton'		=> __sp('Save the New Topic'),
		'tipCancelButton'		=> __sp('Cancel the New Topic'),
		'iconMobileSubmit'		=> 'sp_EditorSave.png',
		'iconMobileCancel'		=> 'sp_EditorCancel.png',
		'iconMobileSmileys'		=> 'sp_EditorSmileys.png',
		'iconMobileOptions'		=> 'sp_EditorOptions.png'
	);
	# ==============================================================


	# Load the forum header template - normally first thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadContainer', 'forumHead');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'forumHead');

	sp_SectionStart('tagClass=spBodyContainer', 'forumBody');

		# Start the 'groupView' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spListSection', 'forumView');

			# Set the Forum
			# ----------------------------------------------------------------------
			if (SP()->forum->view->this_forum()):

				# Are there sub forums to display
				if (SP()->forum->view->has_subforums()) :

					# Start the 'SubForumHeader' section
					# ----------------------------------------------------------------------
					sp_SectionStart('tagClass=spForumViewSection', 'forumViewSubForums');

						sp_SectionStart('tagClass=spForumViewHeader', 'forumHeader');

							sp_ColumnStart('tagId=spHeadColumn1&tagClass=spTitleColumn spLeft&width=5%&height=0px');
								sp_ForumHeaderIcon('tagId=spSubForumHeaderIcon&tagClass=spHeaderIcon spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn2&tagClass=spTitleColumn spLeft&width=91%&height=0px');
								sp_ForumHeaderName('tagId=spSubForumHeaderName&tagClass=spHeaderName');
								sp_SubForumHeaderDescription('', __sp('Sub Forums'));
							sp_ColumnEnd();

							sp_InsertBreak();

							# Column Titles
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spCategoryLabels', 'subForumSectionTitles');

								sp_ColumnStart('tagId=spIconCol&tagClass=spIconColumnSection  spLeft&width=0&height=0px');
									sp_ForumHeaderIcon('tagClass=spRowIconHidden spLeft');
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

							sp_SectionEnd('', 'subForumSectionTitles');

						sp_SectionEnd('', 'forumHeader');

						sp_SectionStart('tagClass=spForumSubforumContainer', 'subForumView');

							while (SP()->forum->view->loop_subforums()) : SP()->forum->view->the_subforum() ; if(SP()->forum->view->is_child_subforum()) :

								# Start the 'subforum' section - note the special subform call above
								# ----------------------------------------------------------------------
								sp_SectionStart('tagClass=spGroupForumSection', 'eachSubForum');

									# Column 1 of the forum row
									# ----------------------------------------------------------------------
									sp_ColumnStart('tagId=spIconCol&tagClass=spColumnSection spLeft&width=0&height=0px');
										sp_SubForumIndexIcon('tagClass=spRowIcon spLeft');
									sp_ColumnEnd();


									# Column 2 of the forum row
									# ----------------------------------------------------------------------
									sp_ColumnStart('tagId=spColGroup2&tagClass=spColumnSection spLeft&width=0&height=0px');
										sp_SubForumIndexName('tagClass=spRowName', __sp('Browse topics in %NAME%'));
										sp_InsertBreak('direction=left');
										sp_SubForumIndexDescription('tagClass=spRowDescription');
									sp_ColumnEnd();

									# Column 3 of the forum row
									# ----------------------------------------------------------------------
									sp_ColumnStart('tagId=spColGroup3&tagClass=spColumnSection spLeft&width=0&height=0px');
										sp_SubForumIndexTopicCount('tagClass=spInRowCount spCenter', __sp(''));
									sp_ColumnEnd();

									# Column 4 of the forum row
									# ----------------------------------------------------------------------
									sp_ColumnStart('tagId=spColGroup4&tagClass=spColumnSection spLeft&width=0&height=0px');
										sp_SubForumIndexLastPost('tagClass=holder spLeft&order=TLDU&nicedate=1&date=0&time=1&stackdate=1&itemBreak= ', __sp(''), __sp('No Topics'));
									sp_ColumnEnd();

									# Column 5 of the forum row
									# ----------------------------------------------------------------------
									sp_ColumnStart('tagId=spColGroup5&tagClass=spColumnSection spRight&width=0&height=0px');
										sp_SubForumIndexLockIcon('tagClass=spIcon spRight', __sp('This forum is locked'));
									sp_ColumnEnd();

									sp_SubForumIndexInlinePosts();
									sp_InsertBreak();

								sp_SectionEnd('', 'eachSubForum');

								sp_ForumHeaderSubForums('unreadIcon=sp_SubForumUnreadIcon.png', __sp('Sub-Forums'), __sp('Browse topics in %NAME%'));

							endif ; endwhile;

						sp_SectionEnd('', 'subForumList');

					sp_SectionEnd('', 'subForumView');

				endif;
				# End of subforum section

				# Start the 'forumHeader' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spForumViewSection', 'eachForum');

					sp_SectionStart('tagClass=spForumViewHeader', 'forumHeader');

						sp_SectionStart('tagClass=spFlexHeadContainer', 'forumFlexHeader');

							sp_ColumnStart('tagId=spIconCol&tagClass=spIconColumnSectionTitle  spLeft&width=0&height=0px');
								sp_ForumHeaderIcon('tagClass=spRowIcon spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn2&tagClass=spTitleColumnTitle spLeft&width=80%&height=0');
								sp_ForumHeaderName('tagClass=spHeaderName spLeft');
								if(function_exists('sp_SubscriptionsSubscribeForumButton')) sp_SubscriptionsSubscribeForumButton('tagClass=spSubButton spLeft&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Un-Subscribe'), __sp('Subscribe to this forum'), __sp('Unsubscribe from this forum'));
								sp_InsertBreak('');
								sp_ForumHeaderDescription('tagClass=spHeaderDescription');
								sp_InsertBreak();
								sp_ForumHeaderMessage('tagClass=spHeaderMessage');
							sp_ColumnEnd();

							sp_ColumnStart('tagClass=spHeadColumn3 spRight&width=auto&height=0');
								sp_ForumHeaderRSSButton('tagClass=spLink spRight&iconClass=spIcon&icon= spRight', __sp('RSS'), __sp('Subscribe to the RSS feed for this forum'));
								sp_InsertBreak();
								sp_TopicNewButton('tagClass=spHeaderAddButton spRight iconStatusClass=spLockPosition&iconLock=sp_ForumStatusLockWhite.png&icon=', __sp('New Topic'), __sp('Start a new topic'), __sp('Locked'));
							sp_ColumnEnd();

							sp_InsertBreak('');

						sp_SectionEnd('', 'forumFlexHeader');

						# Column Titles
						# ----------------------------------------------------------------------
						sp_SectionStart('tagClass=spCategoryLabels', 'topicSectionTitles');

							sp_ColumnStart('tagId=spColForum1&tagClass=spTitleColumnHidden spLeft&width=0&height=0px');
								sp_ForumHeaderIcon('tagClass=spRowIconHidden spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColForum2&tagClass=spTitleColumn spLeft&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Topics'));
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColForum3&tagClass=spTitleColumn spLeft&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Last Post'));
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColForum4&tagClass=spColumnCountViews spLeft&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Views'));
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spColForum5&tagClass=spColumnCountReplies spLeft&width=0&height=0px');
								sp_UniversalTitle('tagClass=spUniversalLabel spLeft&labelClass=spColumnTitle', __sp('Replies'));
							sp_ColumnEnd();

						sp_SectionEnd('', 'topicSectionTitles');

					sp_SectionEnd('', 'forumHeader');

					sp_InsertBreak('spacer=0px');

					sp_SectionStart('tagClass=spForumTopicContainer', 'forumViewTopics');

						# Start the Topic Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_topics()) : while (SP()->forum->view->loop_topics()) : SP()->forum->view->the_topic();
							?><div class="spTransitionHover"><?php

							# Start the 'topic' section
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spForumTopicSection', 'eachTopic');

								# Column 1 of the topic row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColForum1&tagClass=spIconColumnSection spLeft&width=0&height=0px');
									sp_TopicIndexIcon('tagClass=spRowIcon spLeft');
									sp_InsertBreak();
									if (function_exists('sp_TopicIndexRating')) sp_TopicIndexRating('tagClass=spStatusIcon spCenter&skipZero=1');
								sp_ColumnEnd();

								# Column 2 of the topic row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColForum2&tagClass=spColumnSection spLeft&width=0&height=0px');
									sp_UserNewPostFlag('', 'forum');
									sp_TopicIndexName('tagClass=spRowName  spLeft', __sp('Browse the thread %NAME%'));
									sp_InsertBreak();
									if (function_exists('sp_TopicDescription')) sp_TopicDescription();
									sp_InsertBreak();
									if (function_exists('sp_TopicIndexTagsList')) sp_TopicIndexTagsList('tagClass=spTopicTagsList spLeft&icon=&delimiter=/&delimiterClass=spTagsDelimiterForum&collapse=0&iconClass=spIcon', __sp('Tags: '), __sp('Show the tags for this topic'));
									sp_InsertBreak();
									if (function_exists('sp_TopicIndexTopicStatus')) sp_TopicIndexTopicStatus('tagClass=spTopicIndexStatus&icon=', __sp('Search for other topics with this status'), __sp('Status:'));
									sp_TopicIndexFirstPost('tagClass=holder spLeft&icon=&nicedate=1&date=1&time=1&stackdate=0&stackuser=0&stackdate=0&itemBreak=&beforeUser=&beforeDate= ', __sp(''));
									?><div class="spTransitionHoverContent"><?php
									sp_TopicForumToolButton("hide=0&icon=tagClass=spInRowLabel", __sp('Tools'), __sp('Open the forum toolset'));
									?></div><?php
								sp_ColumnEnd();

								# Column 3 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColForum3&tagClass=spColumnSection spLeft&height=0px');
									sp_TopicIndexLastPost('tagClass=holder spLeft&nicedate=1&date=0&time=0&stackuser=0&stackdate=0&beforeUser=&labelLink=1&icon=sp_goNewPost.png', __sp(''));
									sp_InsertBreak();
									sp_TopicIndexStatusIcons('tagClass=spStatusIcon', __sp('This topic is locked'), __sp('This topic is pinned'), __sp('This topic has unread posts'));
									sp_InsertBreak();
								sp_ColumnEnd();

								# Column 4 and 5 of the forum row
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagId=spColForum4&tagClass=spColumnCountViews spRight&width=0&height=0px');
									sp_TopicIndexViewCount('tagClass=spRowDescription spLeft&labelClass=spViewsLabel&numberClass=&before= &after= ', __sp(''), __sp(''));
								sp_ColumnEnd();

								sp_ColumnStart('tagId=spColForum5&tagClass=spColumnCountReplies spRight&width=0&height=0px');
									sp_TopicIndexReplyCount('tagClass=spRowDescriptionBold spLeft&labelClass=spPostsLabel&numberClass=&', __sp(''), __sp(''));
								sp_ColumnEnd();

								sp_InsertBreak();

							sp_SectionEnd('', 'eachTopic');
							?></div><?php

						endwhile; else:
							sp_NoTopicsInForumMessage('tagClass=spMessage', __sp('There are no topics in this forum'));
						endif;

					sp_SectionEnd('', 'forumViewTopics');

				sp_SectionEnd('', 'eachForum');

			# Start the bottom 'pagelinks' section
			sp_SectionStart('tagClass=spPageLinksBottomSection', 'forumPageLinksFoot');

				sp_TopicIndexPageLinks('tagClass=spPageLinksBottom spRight&prevIcon=&nextIcon=&showEmpty=0', __sp(''), __sp('Jump to page %PAGE%'), __sp('Jump to page'));
				sp_InsertBreak();

			sp_SectionEnd('', 'forumPageLinksFoot');


			# Start the 'editor' section
			# ----------------------------------------------------------------------
			sp_SectionStart('tagClass=spHiddenSection', 'postEditor');

				sp_TopicEditorWindow($addTopicForm);

			sp_SectionEnd('', 'postEditor');

			else:
				sp_NoForumMessage('tagClass=spMessage', __sp('Access denied - you do not have permission to view this page'), __sp('The requested forum does not exist'));
			endif;

		sp_SectionEnd('', 'forumView');

	sp_SectionEnd('', 'forumBody');

	# Footer buttons section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spActionsBar', 'forumFootButtons');

		sp_TopicNewButton('tagClass=spFootButton spHeaderAddButton spRight iconStatusClass=spLockPosition&iconLock=sp_ForumStatusLockWhite.png&icon=', __sp('New Topic'), __sp('Start a new topic'), __sp('Locked'));
		if(function_exists('sp_SubscriptionsSubscribeForumButton')) sp_SubscriptionsSubscribeForumButton('tagClass=spFootButton spRight&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Unsubscribe'), __sp('Subscribe to this forum'), __sp('Unsubscribe from this forum'));

	sp_SectionEnd('tagClass=spClear', 'forumFootButtons');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'forumFoot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'forumFoot');
