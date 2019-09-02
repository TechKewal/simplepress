<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	topic
#	Author		:	Simple:Press
#
#	The 'topic' template is used to display the Topic/Post Index Listing
#
# --------------------------------------------------------------------------------------

	# == ADD POST FORM - OBJECT DEFINITION ========================
	$addPostForm = array(
		'tagClass'				=> 'spForm',
		'hide'					=> 1,
		'controlFieldset'		=> 'spEditorFieldset',
		'controlInput'			=> 'spControl',
		'controlSubmit'			=> 'spSubmit',
		'controlOrder'			=> 'cancel|save',
		'labelHeading'			=> __sp('Add Reply'),
		'labelGuestName'		=> __sp('Guest name (required)'),
		'labelGuestEmail'		=> __sp('Guest email (required)'),
		'labelModerateAll'		=> __sp('NOTE: new posts are subject to administrator approval before being displayed'),
		'labelModerateOnce'		=> __sp('NOTE: first posts are subject to administrator approval before being displayed'),
		'labelSmileys'			=> __sp('Smileys'),
		'labelOptions'			=> __sp('Options'),
		'labelOptionLock'		=> __sp('Lock this topic'),
		'labelOptionPin'		=> __sp('Pin this post'),
		'labelOptionTime'		=> __sp('Edit post timestamp'),
		'labelMath'				=> __sp('Math Required'),
		'labelMathSum'			=> __sp('What is the sum of'),
		'labelPostButtonReady'	=> __sp('Submit Reply'),
		'labelPostButtonMath'	=> __sp('Do Math To Save'),
		'labelPostCancel'		=> __sp('Cancel'),
		'tipSmileysButton'		=> __sp('Open/Close to Add a Smiley'),
		'tipOptionsButton'		=> __sp('Open/Close to select Posting Options'),
		'tipSubmitButton'		=> __sp('Save the New Post'),
		'tipCancelButton'		=> __sp('Cancel the New Post'),
		'iconMobileSubmit'		=> 'sp_EditorSave.png',
		'iconMobileCancel'		=> 'sp_EditorCancel.png',
		'iconMobileSmileys'		=> 'sp_EditorSmileys.png',
		'iconMobileOptions'		=> 'sp_EditorOptions.png'
	);

	# == EDIT POST FORM - OBJECT DEFINITION ========================
	$editPostForm = array(
		'tagClass'				=> 'spForm',
		'controlFieldset'		=> 'spEditorFieldset',
		'controlInput'			=> 'spControl',
		'controlSubmit'			=> 'spSubmit',
		'controlOrder'			=> 'cancel|save',
		'labelHeading'			=> __sp('Edit Post'),
		'labelSmileys'			=> __sp('Smileys'),
		'labelPostButton'		=> __sp('Save Edited Post'),
		'labelPostCancel'		=> __sp('Cancel'),
		'tipSmileysButton'		=> __sp('Open/Close to Add a Smiley'),
		'tipSubmitButton'		=> __sp('Save the Edited Post'),
		'tipCancelButton'		=> __sp('Cancel the Post Edits'),
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

		# Start the 'topicView' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spListSection', 'topicView');

			# Set the Topic
			# ----------------------------------------------------------------------
			if (SP()->forum->view->this_topic()):

				# Start the 'topicHeader' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spTopicViewSection', 'topic');

					sp_SectionStart('tagClass=spTopicViewHeader', 'header');

						sp_SectionStart('tagClass=spFlexHeadContainer', 'flexheader');

							sp_ColumnStart('tagId=spIconCol&tagClass=spIconColumnSectionTitle  spLeft&width=0&height=0px');
								sp_TopicHeaderIcon('tagClass=spHeaderIcon spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn2&tagClass=spTitleColumnTitle spLeft&width=80%&height=0');
								sp_TopicHeaderName('tagClass=spHeaderName');
								sp_InsertBreak();
								if (function_exists('sp_TopicHeaderShowBlogLink')) sp_TopicHeaderShowBlogLink('tagClass=spLink spLeft&icon=', __sp('View Blog Post'), __sp('Click to go to original blog post'));
								sp_InsertBreak();
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn3&tagClass=spTitleColumnTitle spRight&width=5%&height=0');
								sp_TopicHeaderRSSButton('tagClass=spLink spRight&icon=', __sp('RSS'), __sp('Subscribe to the RSS feed for this topic'));
								sp_InsertBreak();
							sp_ColumnEnd();
							sp_InsertBreak();

						sp_SectionEnd('', 'flexheader');

						sp_SectionStart('tagClass=statusHolder', 'additional');

							sp_InsertBreak();
							if (function_exists('sp_TopicStatus')) sp_TopicStatus('tagClass=spTopicViewStatus spLeft&icon=', __sp('Search for other topics with this status'), '');
							sp_InsertBreak();
							if (function_exists('sp_TopicTagsList')) sp_TopicTagsList('tagClass=spTopicTagsList spLeft&icon=', __sp('Tags: '));

						sp_SectionEnd('', 'additional');

						sp_InsertBreak();

						# Header Buttons Section
						# ----------------------------------------------------------------------
						sp_SectionStart('tagClass=spActionsBar spActionsBarHeader', 'headerButtons');
							sp_PostNewButton('tagClass=spFootButton spRight&icon=', __sp('Add Reply'), __sp('Add a new post in this topic'), __sp('This topic is locked'));
							if (function_exists('sp_SubscriptionsSubscribeButton')) sp_SubscriptionsSubscribeButton('tagClass=spFootButton spRight&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Unsubscribe'), '', '');
							if (function_exists('sp_WatchesWatchButton')) sp_WatchesWatchButton('tagClass=spFootButton spRight&watchIcon=&stopWatchIcon=', __sp('Watch'), __sp('Un-Watch'), '', '');
						sp_SectionEnd('tagClass=spClear', 'headerButtons');

						sp_InsertBreak();

					sp_SectionEnd('', 'header');

					sp_InsertBreak('spacer=5px');

					sp_SectionStart('tagClass=spTopicPostContainer', 'postlist');

						# Start the Post Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_posts()) : while (SP()->forum->view->loop_posts()) : SP()->forum->view->the_post();

							# Start the 'post' section
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spTopicPostSection', 'post');

								sp_PostIndexAnchor();

									sp_SectionStart('', 'post-info');

										sp_PostIndexNumber('tagClass=spPostNumber spLeft');
										sp_UserNewPostFlag('', 'topic');
										sp_PostIndexUserDate('tagClass=spPostUserDate spLeft&stackdate=0');
										sp_OpenCloseControl("targetId=spPostAction&context=postLoop&linkClass=spFooterButton spRight&default=closed&setCookie=0&asLabel=1", __sp('Post Actions'), __sp('Hide'));

										# Start the 'post' section
										# ----------------------------------------------------------------------

										sp_InsertBreak('');

											sp_SectionStart("tagId=spPostAction&context=postLoop&tagClass=spPostActionSection", 'action');

												sp_SectionStart("tagId=&tagClass=spFlexHolder&context=postLoop", 'holder');

													sp_PostForumToolButton("tagClass=spPostActionLabel spLeft&hide=0&icon=", __sp('Tools'), __sp('Open the forum toolset'));
													sp_PostIndexPrint('tagClass=spPostActionLabel spLeft&icon=', __sp('Print'), __sp('Print this post'));

													if (function_exists('sp_PostIndexDeleteThread')) {
														sp_PostIndexDeleteThread('tagClass=spPostActionLabel spLeft&icon=', __sp('Delete'), __sp('Delete'), __sp('Delete this thread'), __sp('Delete this post'));
													} else {
														sp_PostIndexDelete('tagClass=spPostActionLabel spLeft&icon=', __sp('Delete'), __sp('Delete this post'));
													}

													sp_PostIndexEdit('tagClass=spPostActionLabel spLeft&icon=', __sp('Edit'), __sp('Edit this post'));
													sp_PostIndexQuote('tagClass=spPostActionLabel spRight&icon=', __sp('Quote'), __sp('Quote this post'));

													if (function_exists('sp_PostIndexThreadedReply')) sp_PostIndexThreadedReply('tagClass=spPostActionLabel spRight&icon=', __sp('Reply'), __sp('Reply to this post'));

													if (function_exists('sp_thanks_thank_the_post')) sp_thanks_thank_the_post('tagClass=spPostActionLabel spRight&iconThanks=&iconThanked=', __sp('Thank'), __sp('Thanked'), __sp('Add thanks to this post'), __sp('You have already thanked this post'));
													if (function_exists('sp_PostIndexReportPost')) sp_PostIndexReportPost('tagClass=spPostActionLabel spRight&icon=', __sp('Report'), __sp('Report this post to admin'));

												sp_SectionEnd('', 'holder');

											sp_SectionEnd('', 'action');

									sp_SectionEnd('', 'post-info');

									# User Info post row
									# ----------------------------------------------------------------------
									sp_SectionStart('tagClass=spUserSectionMobile');

										sp_InsertBreak('');

										sp_ColumnStart('tagClass=spAvatarSection spLeft&width=70%&height=15px');
											sp_UserAvatar('tagClass=spPostUserAvatar spLeft&context=user', SP()->forum->view->thisPostUser);
											sp_PostIndexUserName('tagClass=spPostUserName spLeft');
											sp_InsertLineBreak();
											if (function_exists('sp_PostIndexUserLocation')) { sp_PostIndexUserLocation('tagClass=spPostUserLocation spLeft');
												sp_InsertLineBreak();
											}
											sp_PostIndexUserPosts('tagClass=spPostUserPosts spLeft', __sp('Posts: %COUNT%'));
											sp_InsertBreak('');

											sp_SectionStart('tagClass=spIdentitySection', 'user-identities');

												sp_PostIndexUserWebsite('tagClass=spLeft', __sp('Visit my website'));
												sp_PostIndexUserTwitter('tagClass=spLeft', __sp('Follow me on Twitter'));
												sp_PostIndexUserFacebook('tagClass=spLeft', __sp('Connect with me on Facebook'));
												sp_PostIndexUserMySpace('tagClass=spLeft', __sp('See MySpace'));
												sp_PostIndexUserLinkedIn('tagClass=spLeft', __sp('My LinkedIn network'));
												sp_PostIndexUserYouTube('tagClass=spLeft', __sp('View my YouTube channel'));
												sp_PostIndexUserGooglePlus('tagClass=spLeft', __sp('Interact with me on Google Plus'));
												sp_InsertLineBreak();

											sp_SectionEnd('', 'user-identities');

										sp_ColumnEnd();

										sp_ColumnStart('tagClass=spUserStatsSection spRight&width=30%&height=20px');
											sp_ColumnStart('tagClass=spIdentityHolder spRight&width=auto&height=auto');
												sp_PostIndexUserBadges('tagClass=spCenter');
												sp_InsertBreak('');
												if (function_exists('sp_PostIndexCubePoints')) {
													sp_InsertBreak();
													sp_PostIndexCubePoints('tagClass=spPostUserCubePoints', __sp('CubePoints'));
												}
												sp_InsertBreak('');
												if (function_exists('sp_PostIndexUserReputationLevel')) sp_PostIndexUserReputationLevel('tagClass=spPostReputationLevel spCenter');
												if (function_exists('sp_PostIndexRepUser')) sp_PostIndexRepUser('tagClass=spCenter', __sp('Reputation'), __sp('Give/Take Reputation'));
												sp_InsertBreak();
											sp_ColumnEnd();
										sp_ColumnEnd();

										sp_InsertBreak('');

									sp_SectionEnd('tagClass=spClear');

									# Post Content post row
									# ----------------------------------------------------------------------
									sp_SectionStart('tagClass=spPostSection');

										sp_SectionStart('tagClass=spPostContentSection', 'content');

											if (function_exists('sp_PostIndexRatePost')) {
												sp_PostIndexRatePost('tagClass=spRight');
												sp_InsertBreak();
											}
											sp_PostIndexContent('', __sp('Awaiting Moderation'));
											sp_InsertBreak();
											if (function_exists('sp_ShareThisTopicIndexTag') || function_exists('sp_AnswersTopicAnswer') || function_exists('sp_thanks_thanks_for_post')){

												sp_ColumnStart('tagClass=spPluginSection spCenter&height=auto');
													if (function_exists('sp_ShareThisTopicIndexTag')) sp_ShareThisTopicIndexTag('tagClass=ShareThisTopicIndex spRight');
													if (function_exists('sp_AnswersTopicAnswer')) {
													if (SP()->forum->view->thisPost->post_index == 1) sp_AnswersTopicSeeAnswer('tagClass=spAnswersTopicSeeAnswer spRight', '', __sp('Go to the post marked as the answer'));
														sp_AnswersTopicAnswer('tagClass=spRight', '', __sp('This post answers the topic'));
														sp_AnswersTopicPostIndexAnswer('tagClass=spAnswersTopicAnswersButton spRight', '', __sp('Mark this post as topic answer'), '', __sp('Unmark this post as topic answer'));
													}
												sp_ColumnEnd();

												if (function_exists('sp_thanks_thanks_for_post')) {
													sp_InsertBreak();
													sp_thanks_thanks_for_post();
												}
												sp_InsertBreak();
												}
										sp_SectionEnd('', 'content');

									sp_PostIndexUserSignature('tagClass=spPostUserSignature spCenter&maxHeightBottom=');

									sp_InsertBreak();

								sp_SectionEnd();

							sp_SectionEnd('', 'post');

						endwhile; else:
							sp_NoPostsInTopicMessage('tagClass=spMessage', __sp('There are no posts in this topic'));
						endif;

					sp_SectionEnd('', 'postlist');

				sp_SectionEnd('', 'topic');

				sp_UsersAlsoViewing('includeAdmins=1&includeMods=1&includeMembers=1&displayToAll=1', __sp('Is viewing'));

				sp_SectionStart('tagClass=spPageLinksBottom', 'pageLinks');

					sp_PostIndexPageLinks('tagClass=spPageLinks spPageLinksBottom spRight&prevIcon=&nextIcon=&showLinks=2&showEmpty=1', '', __sp('Jump to page %PAGE% of this topic'), __sp('Jump to page'));

				sp_SectionEnd('', 'pageLinks');

				# Start the 'editor' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spHiddenSection', 'editor');

					sp_PostEditorWindow($addPostForm, $editPostForm);

				sp_SectionEnd('', 'editor');

			else:
				sp_NoTopicMessage('tagClass=spMessage', __sp('Access denied - you do not have permission to view this page'), __sp('The requested topic does not exist'));
			endif;

		sp_SectionEnd('', 'topicView');

	sp_SectionEnd('', 'body');

	sp_InsertBreak();

	# Footer Buttons Section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spActionsBar', 'footerButtons');

		sp_PostNewButton('tagClass=spFootButton spRight&icon=', __sp('Add Reply'), __sp('Add a new post in this topic'), __sp('This topic is locked'));
		if (function_exists('sp_SubscriptionsSubscribeButton')) sp_SubscriptionsSubscribeButton('tagClass=spFootButton spRight&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Unsubscribe'), '', '');
		if (function_exists('sp_WatchesWatchButton')) sp_WatchesWatchButton('tagClass=spFootButton spRight&watchIcon=&stopWatchIcon=', __sp('Watch'), __sp('Un-Watch'), '', '');

	sp_SectionEnd('tagClass=spClear', 'footerButtons');


	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');
?>