<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Topic
#	Author		:	Simple:Press
#
#	The 'Topic' template is used to display the Topic/Post Index Listing
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
		'labelHeading'			=> __sp('Reply to'),
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
								sp_TopicHeaderIcon('tagClass=spRowIcon spLeft');
							sp_ColumnEnd();

							sp_ColumnStart('tagId=spHeadColumn2&tagClass=spTitleColumnTitle spLeft&width=80%&height=0');
								sp_TopicHeaderName('tagClass=spHeaderName spLeft');
								sp_InsertBreak('');
								if (function_exists('sp_TopicHeaderShowBlogLink')) sp_TopicHeaderShowBlogLink('icon=', __sp('Read the original blog post'), __sp('Click to go to original blog post'));
								sp_InsertBreak('');

								if (function_exists('sp_TopicTagsList')) {
									sp_TopicTagsList('tagClass=spTopicTagsList spLeft&delimiter=|&delimiterClass=spTagsDelimiter', __sp(''));
									sp_RelatedTopicsButton('tagClass=spRelated spLeft&icon=', __sp(' -  Related Tags'), __sp('Get a list of related topics based on tags for this topic'));
								}
								sp_InsertBreak('');
							sp_ColumnEnd();

							sp_ColumnStart('tagClass=spHeadColumn3 spRight&width=auto&height=0');
								if (function_exists('sp_TopicStatus')) {
									sp_TopicStatus('statusClass=spLink spRight&icon=', __sp('Search for other topics with this status'), __sp(''));
								}
								sp_TopicHeaderRSSButton('tagClass=spLink spRight&iconClass=spIcon&icon=', __sp('RSS'), __sp('Subscribe to the RSS feed for this topic'));
								sp_InsertBreak();
							sp_ColumnEnd();

						sp_SectionEnd('', 'flexheader');

						# Footer buttons section
						# ----------------------------------------------------------------------
						sp_SectionStart('tagClass=spActionsBar', 'headerButtons');

							if (function_exists('sp_TopicIndexRating')) {
								sp_TopicIndexRating('tagClass=spTopicRating spLeft', __sp('Topic Rating:'));
							}

							sp_ColumnStart('tagClass=holder spRight&width=0&height=0');
								sp_PostNewButton('tagId=spPostNewButtonBottom&tagClass=spFootButton spRight&iconLock=sp_ForumStatusLockWhite.png&icon=', __sp('Add Reply'), __sp('Add a new post in this topic'), __sp('This topic is locked'));
								if (function_exists('sp_PrintTopicView')) { sp_PrintTopicView('tagClass=spFootButton spRight&icon=', __sp('Print Topic'), __sp('Topic Print Options')); }
								if (function_exists('sp_SubscriptionsSubscribeButton')) sp_SubscriptionsSubscribeButton('tagClass=spFootButton spRight&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Un-subscribe'), __sp('Subscribe to this topic'), __sp('Unsubscribe from this topic'));
								if (function_exists('sp_WatchesWatchButton')) sp_WatchesWatchButton('tagClass=spFootButton spRight&watchIcon=&stopWatchIcon=', __sp('Watch'), __sp('Stop Watching'), __sp('Watch this topic'), __sp('Stop watching this topic'));
							sp_ColumnEnd();

						sp_SectionEnd('tagClass=spClear', 'headerButtons');

					sp_SectionEnd('', 'header');

					sp_InsertBreak('');

					sp_SectionStart('tagClass=spTopicPostContainer', 'postlist');

						# Start the Post Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_posts()) : while (SP()->forum->view->loop_posts()) : SP()->forum->view->the_post();

						# Start the 'post' section
						# ----------------------------------------------------------------------
						?><div class="spTransitionHover"><?php
						sp_SectionStart('tagClass=spTopicPostSection', 'post');

							sp_PostIndexAnchor();

							# Column 1 of the post row
							# ----------------------------------------------------------------------
							sp_ColumnStart('tagId=spColTopic1&tagClass=spUserSection spLeft&width=0&height=50px');
								sp_UserAvatar('tagClass=spPostUserAvatar spCenter&context=user', SP()->forum->view->thisPostUser);
								sp_PostIndexUserName('tagClass=spPostUserName spCenter');
								sp_PostIndexUserLocation('tagClass=spPostUserLocation spCenter');
								sp_PostIndexUserBadges('tagClass=spCenter');
								if (function_exists('sp_PostIndexUserReputationLevel')) sp_PostIndexUserReputationLevel('tagClass=spPostReputationLevel spCenter');
								if (function_exists('sp_PostIndexRepUser')) sp_PostIndexRepUser('tagClass=spCenter', '', __sp('Give/Take Reputation'));
								sp_PostIndexUserPosts('tagClass=spPostUserPosts spCenter', __sp('%COUNT% Posts'));
								sp_PostIndexUserStatus('tagClass=spCenter spPostUserStatus&onlineIcon=&offlineIcon=', __sp('(Online)'), __sp('(Offline)'));
								if (function_exists('sp_PostIndexCubePoints')) sp_PostIndexCubePoints('tagClass=spPostUserCubePoints spCenter', __sp('CubePoints'));
								sp_SectionStart('tagClass=spSocialButtons spCenter', 'user-identities');
								sp_PostIndexUserWebsite('', __sp('Visit my website'));
								sp_PostIndexUserTwitter('', __sp('Follow me on Twitter'));
								sp_PostIndexUserFacebook('', __sp('Connect with me on Facebook'));
								sp_PostIndexUserMySpace('', __sp('See MySpace'));
								sp_PostIndexUserLinkedIn('', __sp('My LinkedIn network'));
								sp_PostIndexUserYouTube('', __sp('View my YouTube channel'));
								sp_PostIndexUserGooglePlus('', __sp('Interact with me on Google Plus'));
								sp_SectionEnd('', 'user-identities');
							sp_ColumnEnd();

							# Column 2 of the post row
							# ----------------------------------------------------------------------
							sp_ColumnStart('tagId=spColTopic2&tagClass=spPostSection spRight&width=0');

								# Start the 'post' section
								# ----------------------------------------------------------------------
								sp_SectionStart('tagClass=spPostActionSection', 'action');

									sp_PostIndexPinned('tagClass=spStatusIcon spLeft', __sp('This post is pinned'));
									if (function_exists('sp_PostIndexPostByEmail')) sp_PostIndexPostByEmail('tagClass=spStatusIcon spLeft', __sp('This post was sent by email'));
									sp_PostIndexNumber('tagClass=spLabelBordered spLeft');
									sp_PostIndexPermalink('tagClass=spButton spLeft', '', __sp('The post permalink'));
									sp_PostIndexEditHistory('tagClass=spButton spLeft', '', __sp('Edited by %USER% on %DATE%'), __sp('View edit history'));
									sp_UserNewPostFlag('', 'topic');
									sp_PostIndexUserDate('tagClass=spPostUserDate spLeft&stackdate=0&nicedate=0');

									# Hover Over Tools
									# ----------------------------------------------------------------------
									?><div class="spTransitionHoverContent"><?php

									sp_PostForumToolButton("tagClass=spPostActionLabel spLeft&hide=0&icon=", __sp('Tools'), __sp('Open the forum toolset'));
									sp_PostIndexPrint('tagClass=spPostActionLabel spLeft&icon=', __sp('Print'), __sp('Print this post'));

									if (function_exists('sp_PostIndexDeleteThread')) {
										sp_PostIndexDeleteThread('tagClass=spPostActionLabel spLeft&icon=', __sp('Delete'), __sp('Delete'), __sp('Delete this thread'), __sp('Delete this post'));
									} else {
										sp_PostIndexDelete('tagClass=spPostActionLabel spLeft&icon=', __sp('Delete'), __sp('Delete this post'));
									}

									if(function_exists('sp_PostIndexThreadedReply')) sp_PostIndexThreadedReply('tagClass=spPostActionLabel spRight&icon=', __sp('Reply'), __sp('Add threaded reply to this post'));
									sp_PostIndexQuote('tagClass=spPostActionLabel spRight&icon=', __sp('Quote'), __sp('Quote this post'));
									sp_PostIndexEdit('tagClass=spPostActionLabel spRight&icon=', __sp('Edit'), __sp('Edit this post'));

									if (function_exists('sp_thanks_thank_the_post')) sp_thanks_thank_the_post('tagClass=spPostActionLabel spRight&iconThanks=&iconThanked=', __sp('Thank'), __sp('Thanked'), __sp('Add thanks to this post'), __sp('You have already thanked this post'));
									if (function_exists('sp_PostIndexReportPost')) sp_PostIndexReportPost('tagClass=spPostActionLabel spRight&icon=', __sp('Report'), __sp('Report this post to admin'));

								sp_SectionEnd('', 'action');
								?></div><?php

								sp_SectionStart('tagClass=spPostContentSection', 'content');

									if (function_exists('sp_PostIndexRatePost')) {
										sp_PostIndexRatePost('tagClass=spLabelBordered spPostRating spRight');
										sp_InsertBreak();
									}
									sp_PostIndexContent('', __sp('Awaiting Moderation'));

								sp_SectionEnd('', 'content');

								sp_SectionStart('tagClass=spPostContentHolder', 'postholder');

									if (function_exists('sp_thanks_thanks_for_post')) {
										sp_thanks_thanks_for_post('tagClass=spThanksList spLeft');
									}
									if (function_exists('sp_ShareThisTopicIndexTag')) {
										sp_ShareThisTopicIndexTag('tagClass=ShareThisTopicIndex spRight');
										sp_InsertBreak('direction=right');
									}
									if (function_exists('sp_AnswersTopicAnswer')) {
										if (SP()->forum->view->thisPost->post_index == 1) {
											sp_AnswersTopicSeeAnswer('tagClass=spAnswersTopicSeeAnswer spPostButton spRight&icon=', __sp('See Chosen Answer'), __sp('Go to the post marked as the answer'));
										}
											sp_InsertBreak('direction=right');
											sp_AnswersTopicAnswer('tagClass=spRight&icon=', __sp('Answers Post'), __sp('This post answers the topic'));
											sp_InsertBreak('direction=right');
											sp_AnswersTopicPostIndexAnswer('tagClass=spPostButton spRight&markIcon=&unmarkIcon=', __sp('Answers Question'), __sp('Mark this post as correct answer'), __sp('Unmark as Answer'), __sp('Unmark this post as correct answer'));
									}

								sp_SectionEnd('', 'postholder');

								# Start the Signature section
								# ----------------------------------------------------------------------
								sp_PostIndexUserFlexSignature('tagClass=spPostUserSignature');
								sp_InsertBreak();

							sp_ColumnEnd();

						sp_SectionEnd('', 'post');
						?></div><?php

						endwhile; else:
							sp_NoPostsInTopicMessage('tagClass=spMessage', __sp('There are no posts in this topic'));
						endif;

					sp_SectionEnd('', 'postlist');

				sp_SectionEnd('', 'topic');

				sp_UsersAlsoViewing('includeAdmins=1&includeMods=1&includeMembers=1&displayToAll=1', __sp('is currently browsing this topic'));

				sp_InsertBreak();

				# Start the 'pagelinks' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spPageLinksBottomSection', 'pageLinks');

					sp_PostIndexPageLinks('tagClass=spPageLinksBottom spRight&prevIcon=&nextIcon=&showEmpty=1', __sp(''), __sp('Jump to page %PAGE%'), __sp('Jump to page'));
					sp_InsertBreak();

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



	# Footer buttons section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spActionsBar', 'footerButtons');

		sp_PostNewButton('tagId=spPostNewButtonBottom&tagClass=spFootButton spRight&iconLock=sp_ForumStatusLockWhite.png&icon=', __sp('Add Reply'), __sp('Add a new post in this topic'), __sp('This topic is locked'));
		if (function_exists('sp_PrintTopicView')) {
			sp_PrintTopicView('tagClass=spFootButton spRight&icon=', __sp('Print Topic'), __sp('Topic Print Options'));
		}
		if (function_exists('sp_SubscriptionsSubscribeButton')) sp_SubscriptionsSubscribeButton('tagClass=spFootButton spRight&subscribeIcon=&unsubscribeIcon=', __sp('Subscribe'), __sp('Un-subscribe'), __sp('Subscribe to this topic'), __sp('Unsubscribe from this topic'));
		if (function_exists('sp_WatchesWatchButton')) sp_WatchesWatchButton('tagClass=spFootButton spRight&watchIcon=&stopWatchIcon=', __sp('Watch'), __sp('Stop Watching'), __sp('Watch this topic'), __sp('Stop watching this topic'));

	sp_SectionEnd('tagClass=spClear', 'footerButtons');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');

?>