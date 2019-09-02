<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press - Print Topic Template (Print Topic Plugin)
#	Theme		:	Any
#	Template	:	Print topic
#	Author		:	Simple:Press
#
# --------------------------------------------------------------------------------------

	# check the topic ID is set. If not just die.
	if (empty($_POST['spTopicId'])) die();

?>
<style>
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 .07em !important;
	vertical-align: -0.1em !important;
	background: none !important;
	padding: 0 !important;
}
</style>
<?php

	$spThisPage = (SP()->filters->integer($_POST['spThisPage'])==2) ? true : false;
	$spEnlarge 	= (SP()->filters->integer($_POST['spEnlarge'])==2) ? true : false;
	$spPageNo = ($spThisPage) ? SP()->filters->integer($_POST['spPageNo']) : 0;
	$spTopicId = SP()->filters->integer($_POST['spTopicId']);
	$spPSize = (int) $_POST['spPSize'];
	if (empty($spPSize)) $spPSize = 65;
	$spIndex = (int) $_POST['spIndex'];
	$bLabel = ($spIndex) ? __('Post', 'sp-print') : __('Topic', 'sp-print');

	# Save size to a cookie...
?>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				spj.cookie('spPSize', <?php echo($spPSize); ?>, {expires: parseInt(365), path: '/'});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php

	# Set up some special CSS rules for printing
	# ----------------------------------------------------------------------
?>
	<style>

		body { background: #FFFFFF; color: #000000; }
		#spMainContainer { border: none; background: #FFFFFF; color: #000000; }
		#spMainContainer .spListSection { padding: 0; margin: 0; background: #FFFFFF; color: #000000; }
		#spMainContainer .spTopicViewSection { background: #FFFFFF; color: #000000; padding: 0; margin: 0; border: none; }
		#spMainContainer .spPrintHeading { color: #000000; font-size: 140%; font-weight: bold; }
		#spMainContainer .spTopicPostSection { display: block; padding: 0; background: #FFFFFF; color: #000000; }
		#spMainContainer .spTopicPostSection .spUserSection { display: block; padding: 4px; background: #FFFFFF; color: #000000; }
		#spMainContainer .spTopicPostSection .spPostSection,
		#spMainContainer .spTopicPostSection.spOdd .spPostSection,
		#spMainContainer .spTopicPostSection.spEven .spPostSection { border: none; background: #FFFFFF; color: #000000; }
		#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent { margin: 0; padding: 5px; background: #FFFFFF; color: #000000; }
		#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent p { padding: 0 0 0.75em 0; }
		#spMainContainer .spPostUserAvatar { padding: 0 0 0 5px; }
		#spMainContainer .spPrintNumber { border: 1px solid gray; padding: 3px 4px; }
		#spMainContainer .spPostIndexAttachments,
		#spMainContainer .sfmouseleft { display: none; }
		#spMainContainer a.spButton,
		#spMainContainer a.spButton:hover { color: #000000; }
		@media print {
			#spMainContainer { font-size: <?php echo($spPSize); ?>%; }
			#spMainContainer .spButton {display:none;}
		}

	</style>
<?php

	# Set the number of posts to print - this will be entire topic
	# ----------------------------------------------------------------------
	if (!$spThisPage) {
		$postcount = SP()->DB->table(SPTOPICS, 'topic_id='.SP()->rewrites->pageData['topicid'], 'post_count');
		SP()->core->forumData['display']['posts']['perpage'] = $postcount;
	}

	# Set image handing for printing
	# ----------------------------------------------------------------------
	if ($spEnlarge) {
		$sfimage = SP()->options->get('sfimage');
		$sfimageSave = $sfimage;
		$sfimage['enlarge'] = false;
		$sfimage['process'] = false;
		$sfimage['constrain'] = true;
		SP()->options->update('sfimage', $sfimage);
	}

	# Display the print button
	# ----------------------------------------------------------------------
	sp_PrintTopic('tagClass=spButton spLeft',  sprintf(__('Print this %s', 'sp-print'), $bLabel));
	echo '&nbsp;&nbsp;';
	sp_GoBack('tagClass=spButton spLeft', __('Go back to Topic', 'sp-print'));
	sp_InsertBreak('spacer=10px');

	# ----------------------------------------------------------------------
	# Topic View Print Starts Here
	# ----------------------------------------------------------------------

	# Start the 'topicView' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spListSection', 'topicView');

		# Set the Topic
		# ----------------------------------------------------------------------
		if (SP()->forum->view->this_topic($spTopicId, $spPageNo)):

			# Start the 'topicHeader' section
			# ----------------------------------------------------------------------
			sp_SectionStart('tagClass=spTopicViewSection', 'topic');

				echo "<div class='spPrintHeading'>Forum: ".SP()->forum->view->thisTopic->forum_name.'</div>';
				sp_InsertBreak();
				echo "<div class='spPrintHeading'>Topic: ".SP()->forum->view->thisTopic->topic_name.'</div>';
				sp_InsertBreak("spacer=15px");

				sp_SectionStart('tagClass=spTopicPostContainer', 'postlist');

					# Start the Post Loop
					# ----------------------------------------------------------------------
					if (SP()->forum->view->has_posts()) : while (SP()->forum->view->loop_posts()) : SP()->forum->view->the_post();
						include_once SP_PLUGIN_DIR.'/forum/content/sp-topic-view-functions.php';

						# Are we only printing as single post?
						if ($spIndex && $spIndex != SP()->forum->view->thisPost->post_index) continue;

						# Start the 'post' section
						# ----------------------------------------------------------------------
						sp_SectionStart('tagClass=spTopicPostSection', 'post');
							# User Info post row
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spUserSection');

								sp_ColumnStart('tagClass=spAvatarSection spLeft&width=40px&height=auto');
									sp_UserAvatar('tagClass=spPostUserAvatar spLeft&context=user&size=30', SP()->forum->view->thisPostUser);
								sp_ColumnEnd();

								sp_ColumnStart('tagClass=spRankSection spLeft&width=40%&height=auto');
									sp_PostIndexUserName('tagClass=spPostUserName spLeft');
									sp_PostIndexUserLocation('tagClass=spPostUserLocation spLeft');
								sp_ColumnEnd();

								sp_ColumnStart('tagClass=spIdentitySection spRight&width=40%&height=auto');
									sp_PostIndexNumber('tagClass=spPrintNumber spRight');
									sp_PostIndexUserDate('tagClass=spRight spPostUserDate&stackdate=0');
								sp_ColumnEnd();

							sp_SectionEnd('tagClass=spClear');

							# Post Content post row
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spPostSection');

								# Start the 'post' section
								# ----------------------------------------------------------------------

								sp_SectionStart('tagClass=spPostContentSection', 'content');
									sp_PostIndexContent('', __sp('Awaiting Moderation'));
								sp_SectionEnd('', 'content');
								sp_InsertBreak();

							sp_SectionEnd();

							sp_InsertBreak();

						sp_SectionEnd('', 'post');

					endwhile; else:
						sp_NoPostsInTopicMessage('tagClass=spMessage', __sp('There are no posts in this topic'));
					endif;

				sp_SectionEnd('', 'postlist');

			sp_SectionEnd('', 'topic');

		else:
			sp_NoTopicMessage('tagClass=spMessage', __sp('Access denied - you do not have permission to view this page'), __sp('The requested topic does not exist'));
		endif;

	sp_SectionEnd('', 'topicView');

	# ----------------------------------------------------------------------
	# Topic View Print Ends Here
	# ----------------------------------------------------------------------

	# Set image handing back to user settings
	# ----------------------------------------------------------------------
	if ($spEnlarge) {
		SP()->options->update('sfimage', $sfimageSave);
	}
