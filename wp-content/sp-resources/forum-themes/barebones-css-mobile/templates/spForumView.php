<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	forum
#	Author		:	Simple:Press
#
#	The 'forum' template is used to display the Forum/Topic Index Listing
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
	sp_SectionStart('tagClass=spHeadContainer', 'head');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

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
					sp_SectionStart('tagClass=spForumViewSection', 'subForum');

						sp_SectionStart('tagClass=spForumViewHeader', 'header');

							sp_SectionStart('tagClass=spFlexHeadContainer', 'flexheader');

								sp_ColumnStart('tagId=spIconCol&tagClass=spIconColumnSectionTitle  spLeft&width=0&height=0px');
									sp_ForumHeaderIcon('tagId=spSubForumHeaderIcon&tagClass=spHeaderIcon spLeft');
								sp_ColumnEnd();

								sp_ColumnStart('tagId=spHeadColumn2&tagClass=spTitleColumnTitle spLeft&width=80%&height=0');
									sp_ForumHeaderName('tagId=spSubForumHeaderName&tagClass=spHeaderName');
									sp_InsertBreak();
								sp_ColumnEnd();

							sp_SectionEnd('', 'flexheader');

							sp_SubForumHeaderDescription('', __sp('Sub Forums'));
							sp_InsertBreak();

						sp_SectionEnd('', 'header');

						sp_InsertBreak();

						sp_SectionStart('tagClass=spForumSubforumContainer', 'subforumlist');

							while (SP()->forum->view->loop_subforums()) : SP()->forum->view->the_subforum() ; if(SP()->forum->view->is_child_subforum()) :

							# Start the 'subforum' section - note the special subform call above
							# ----------------------------------------------------------------------
							# Column 1 of the forum row
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spGroupForumHeader', 'forumHeader');

								sp_SubForumIndexName('tagClass=spRowName spLeft', __sp('Browse topics in %NAME%'));
								sp_SubForumIndexTopicCount('tagClass=spInRowCount spLeft', __sp(''));
								sp_InsertBreak('');

							sp_SectionEnd('', 'forumHeader');

							sp_SectionStart('tagClass=spGroupForumSection', 'subForumForums');

								sp_ColumnStart('tagClass=spColumnSection spLeft&width=89%&height=auto');
									sp_SubForumIndexLastPost('tagClass=holder spLeft&order=TLDU&truncate=30&nicedate=1&date=0&time=1&stackdate=1&itemBreak= ', __sp('Last Post'), __sp('No Topics'));
								sp_ColumnEnd();

								sp_ColumnStart('tagClass=spActionsColumnSection spRight&width=10%&height=55px');
									sp_SubForumIndexLockIcon('tagClass=spIcon spRight', __sp('This forum is locked'));
								sp_ColumnEnd();

								sp_InsertBreak();

							sp_SectionEnd('', 'subForumForums');



								sp_ForumHeaderSubForums('stack=0&unreadIcon=sp_TopicStatusPost.png', __sp(''), __sp('Browse topics in %NAME%'));
									sp_SubForumIndexInlinePosts();



							endif ; endwhile;

						sp_SectionEnd('', 'subforumlist');

					sp_SectionEnd('', 'subForum');

				endif;
				# End of subforum section
				sp_InsertBreak('spacer=15px');
				# Start the 'forumHeader' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spForumViewSection', 'forum');

					sp_SectionStart('tagClass=spForumViewHeader', 'header');

						sp_SectionStart('tagClass=spFlexHeadContainer', 'flexheader');

							sp_ColumnStart('tagId=spIconCol&tagClass=spIconColumnSectionTitle  spLeft&width=0&height=0px');
								sp_ForumHeaderIcon('tagClass=spHeaderIcon spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn2&tagClass=spTitleColumnTitle spLeft&width=80%&height=0');
								sp_ForumHeaderName('tagClass=spHeaderName');
								sp_InsertBreak('');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn3&tagClass=spTitleColumnTitle spRight&width=5%&height=0');
								sp_ForumHeaderRSSButton('tagClass=spLink spRight&icon=', __sp('RSS'), __sp('Subscribe to the RSS feed for this forum'));
								sp_InsertBreak('');
							sp_ColumnEnd();

							sp_InsertBreak('');

						sp_SectionEnd('', 'flexheader');

						sp_ForumHeaderDescription('tagClass=spHeaderDescription');

						sp_SectionStart('tagClass=spActionsBar spActionsBarHeader', 'headerButtons');

							sp_TopicNewButton('tagClass=spFootButton spRight&iconLock=&icon=', __sp('New Topic'), __sp('Start a new topic'), __sp('Locked'));
							if(function_exists('sp_SubscriptionsSubscribeForumButton')) sp_SubscriptionsSubscribeForumButton('tagClass=spFootButton spRight&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Unsubscribe'), __sp('Subscribe to this forum'), __sp('UnSubscribe from this forum'));

						sp_SectionEnd('tagClass=spClear', 'headerButtons');

						sp_InsertBreak('');

					sp_SectionEnd('', 'header');


					sp_SectionStart('tagClass=spForumTopicContainer', 'topiclist');

						# Start the Topic Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_topics()) : while (SP()->forum->view->loop_topics()) : SP()->forum->view->the_topic();

							# Start the 'topic' section
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spForumTopicSection', 'topic');

								# Column 1 of the forum row
								# ----------------------------------------------------------------------

								# Column 2 of the topic row
								# ----------------------------------------------------------------------
								sp_UserNewPostFlag('', 'forum');
								sp_TopicIndexName('tagClass=spTopicRowName', __sp('Browse the thread %NAME%'));

								sp_ColumnStart('tagClass=spColumnSection spLeft&width=97%&height=55px');
									sp_InsertBreak();
									sp_TopicIndexFirstPost('iconClass=spIcon spLeft&icon=&labelLink=1&nicedate=1&date=1&time=1&stackdate=0&stackuser=0&stackdate=0&itemBreak= ', __sp(''));
									sp_InsertBreak('spacer=5px');
									sp_TopicIndexReplyCount('tagClass=spRowDescription spLeft&labelClass=spPostsLabel&numberClass=spBoldCount', __sp('Replies'), __sp('Reply:'));
									sp_TopicIndexViewCount('tagClass=spRowDescription spLeft&labelClass=spViewsLabel&numberClass=spBoldCount&before= &after= ', __sp('Views'), __sp('View'));
									sp_InsertBreak('spacer=5px');
									sp_TopicIndexLastPost('iconClass=spIcon spLeft&icon=&labelLink=1&nicedate=1&date=0&time=0&stackdate=0&stackuser=0&stackdate=0&itemBreak= ', __sp('Last Post '));
									sp_InsertBreak('spacer=5px');
									if (function_exists('sp_TopicIndexTopicStatus')) sp_TopicIndexTopicStatus('tagClass=spTopicIndexStatus spLeft&icon=', __sp('Search for other topics with this status'));
									sp_InsertBreak('spacer=5px');
									if (function_exists('sp_TopicIndexTagsList')) sp_TopicIndexTagsList('tagClass=spTopicTagsList spLeft', __sp('Tags'), __sp('Show the tags for this topic'));
								sp_ColumnEnd();

								sp_InsertBreak();

								sp_ColumnStart('tagClass=spStatusColumnSection spRight&width=100%&height=auto');
									sp_TopicForumToolButton("tagClass=spToolsButtonMobile spLeft&icon=&hide=0", __sp('Tools'), __sp('Open the forum toolset'));
									sp_TopicIndexStatusIcons('tagClass=spStatusIcon spRight', __sp('This topic is locked'), __sp('This topic is pinned'), __sp('This topic has unread posts'));
								sp_ColumnEnd();

								sp_InsertBreak();

							sp_SectionEnd('', 'topic');

							sp_InsertBreak('spacer=15px');

						endwhile; else:
							sp_NoTopicsInForumMessage('tagClass=spMessage', __sp('There are no topics in this forum'));
						endif;

					sp_SectionEnd('', 'topiclist');

				sp_SectionEnd('', 'forum');

				# Start the bottom 'pagelinks' section
				# ----------------------------------------------------------------------
					sp_SectionStart('tagClass=spPageLinksBottom', 'pageLinks');

						sp_TopicIndexPageLinks('tagClass=spPageLinks spPageLinksBottom spRight&prevIcon=&nextIcon=&showLinks=1&showEmpty=1', '', __sp('Jump to page %PAGE% of topics'), __sp('Jump to page'));
						sp_InsertBreak();

					sp_SectionEnd('', 'pageLinks');

				# Start the 'editor' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spHiddenSection', 'editor');

					sp_TopicEditorWindow($addTopicForm);

				sp_SectionEnd('', 'editor');

			else:
				sp_NoForumMessage('tagClass=spMessage', __sp('Access denied - you do not have permission to view this page'), __sp('The requested forum does not exist'));
			endif;

		sp_SectionEnd('', 'forumView');

	sp_SectionEnd('', 'body');

	# Footer buttons section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spActionsBar', 'footerButtons');

		sp_TopicNewButton('tagClass=spFootButton spRight&iconLock=&icon=', __sp('New Topic'), __sp('Start a new topic'), __sp('Locked'));
		if(function_exists('sp_SubscriptionsSubscribeForumButton')) sp_SubscriptionsSubscribeForumButton('tagClass=spFootButton spRight&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Unsubscribe'), __sp('Subscribe to this forum'), __sp('UnSubscribe from this forum'));

	sp_SectionEnd('tagClass=spClear', 'footerButtons');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');

?>