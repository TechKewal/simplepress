<?php
/*
Simple:Press
Admin Bar Plugin Support Routines
$LastChangedDate: 2018-11-16 19:21:39 -0600 (Fri, 16 Nov 2018) $
$Rev: 15828 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_do_load_js($footer) {
	# always load the auto update scripts
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPABSCRIPT.'sp-admin-bar-update.js' : SPABSCRIPT.'sp-admin-bar-update.min.js';
	SP()->plugin->enqueue_script('spabupdate', $script, array('jquery'), false, $footer);

	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPABSCRIPT.'sp-admin-bar.js' : SPABSCRIPT.'sp-admin-bar.min.js';
	SP()->plugin->enqueue_script('spabjs', $script, array('jquery'), false, $footer);

	$strings = array(
		'loading'	=> __('Loading topic', 'spab'),
		'saving'	=> __('Saving post', 'spab'),
	);
	SP()->plugin->localize_script('spabjs', 'sp_adminbar_vars', $strings);
}

function sp_admin_bar_do_header() {
	$css = SP()->theme->find_css(SPABCSS, 'sp-admin-bar.css', 'sp-admin-bar.spcss');
	SP()->plugin->enqueue_style('sp-admin-bar', $css);
}

function sp_AdminBarGetWaiting() {
	# set up new posts
	if ((SP()->user->thisUser->admin || SP()->user->thisUser->moderator)) {
		global $spNewPosts;
		require_once SPABLIBDIR.'sp-admin-bar-components.php';
		$spNewPosts = sp_GetAdminsQueuedPosts();

		# check if the queue is up to 200 posts whi is dangerous - output message
		if (isset(SP()->rewrites->pageData['queue']) && SP()->rewrites->pageData['queue'] > 199) {
			$out = '<div class="spMessage">';
			$out.= '<p>'.sprintf(__('WARNING: The admin postbag contains %s posts which require removal', 'spab'), SP()->rewrites->pageData['queue']).'</p>';
			$out.= '</div>';
			$out = apply_filters('sph_AdminQueueWarning', $out);
			echo $out;
		}
	}
}

function sp_GetAdminsQueuedPosts() {
	$newposts = '';
	$clause = '';

	$records = SP()->DB->select('SELECT '.SPWAITING.'.forum_id, forum_slug, forum_name, forum_icon, topic_id, '.SPWAITING.'.post_count, '.SPWAITING.'.post_id
			 FROM '.SPWAITING.'
			 LEFT JOIN '.SPFORUMS.' ON '.SPWAITING.'.forum_id = '.SPFORUMS.'.forum_id
			 ORDER BY forum_id');

	if ($records) {
		# now grab all of the post record we are going to need in one query
		$pcount = count($records);
		$done = 0;

		foreach ($records as $record) {
			$clause.= '('.SPTOPICS.'.topic_id = '.$record->topic_id.' AND '.SPPOSTS.'.post_id >= '.$record->post_id.')';
			$done++;
			if ($done < $pcount) $clause.= ' OR ';
		}

		$preparedpostrecords = SP()->DB->select('SELECT '.SPPOSTS.'.topic_id, post_content, post_index, '.SPPOSTS.'.post_id, post_status, '.SPPOSTS.'.user_id, '.SPMEMBERS.'.display_name, guest_name, topic_slug, topic_name
				 FROM '.SPPOSTS.'
				 LEFT JOIN '.SPTOPICS.' ON '.SPPOSTS.'.topic_id = '.SPTOPICS.'.topic_id
				 LEFT JOIN '.SPMEMBERS.' ON '.SPPOSTS.'.user_id = '.SPMEMBERS.".user_id
				 WHERE $clause
				 AND (admin = 0 OR admin IS NULL) AND (moderator = 0 OR moderator IS NULL)
				 ORDER BY post_id");

		SP()->rewrites->pageData['queue'] = count($preparedpostrecords);

		$newposts = array();
		$findex = -1;
		$pindex = 0;
		$tindex = 0;

		foreach ($records as $record) {
			# Check this still has posts in it and they were not removed (it happens)
			$postrecords = array();
			foreach ($preparedpostrecords as $prepared) {
				if ($prepared->topic_id == $record->topic_id) $postrecords[] = $prepared;
			}
			# So - were they removed? if so don''t add them to the array and remove them from sfwaiting
			if ($postrecords == '') {
				sp_remove_from_waiting(true, $record->topic_id, $record->post_id);
				continue;
			}

			# make sure current user can moderate this forum
			if (!SP()->auths->get('moderate_posts', $record->forum_id)) continue;

			$forumid = $record->forum_id;
			if ($findex == -1 || $newposts[$findex]['forum_id'] != $forumid) {
				$findex++;
				$tindex = 0;
				$pindex = 0;
				$newposts[$findex]['forum_id'] = $record->forum_id;
				$newposts[$findex]['forum_name'] = $record->forum_name;
				$newposts[$findex]['forum_slug'] = $record->forum_slug;
				$newposts[$findex]['forum_icon'] = $record->forum_icon;
			}

			$newposts[$findex]['topics'][$tindex]['topic_id'] = $record->topic_id;
			$newposts[$findex]['topics'][$tindex]['post_id'] = $record->post_id;

			# isolate the post records for current topic
			$postrecords = array();
			foreach ($preparedpostrecords as $prepared) {
				if ($prepared->topic_id == $record->topic_id) $postrecords[] = $prepared;
			}

			if ($postrecords) {
				$newposts[$findex]['topics'][$tindex]['post_count'] = count($postrecords);
				$pindex = 0;
				foreach($postrecords as $postrecord) {
					$newposts[$findex]['topics'][$tindex]['topic_slug'] = $postrecord->topic_slug;
					$newposts[$findex]['topics'][$tindex]['topic_name'] = $postrecord->topic_name;

					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_id'] = $postrecord->post_id;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_status'] = $postrecord->post_status;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_index'] = $postrecord->post_index;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['post_content'] = $postrecord->post_content;
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['user_id'] = $postrecord->user_id;
					if (empty($postrecord->user_id)) {
						$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['display_name'] = $postrecord->guest_name;
						$thisuser = 'Guest';
					} else {
						$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['display_name'] = $postrecord->display_name;
						$thisuser = 'Member';
					}
					$newposts[$findex]['topics'][$tindex]['posts'][$pindex]['user_type'] = $thisuser;
					$pindex++;
				}
			}
			$tindex++;
		}
	}

	# if no new posts then housekeep sfwaiting
	if (!$newposts) SP()->DB->truncate(SPWAITING);

	return $newposts;
}

function sp_GetWaitingNumbers($postlist) {
	# check if topic in url - if yes and it is in postlist - remove it.
	$newposts = array();
	$index = 0;
	$modcount = 0;
	$readcount = 0;
	$spamcount = 0;
	if ($postlist) {
		if (!empty(SP()->rewrites->pageData['topicid'])) {
			$topicid=SP()->rewrites->pageData['topicid'];
			foreach ($postlist as $forum) {
				if (isset($forum['topics'])) {
					foreach ($forum['topics'] as $topic) {
						if (isset($topic['posts'])) {
							foreach ($topic['posts'] as $post) {
								$readcount++;
								if (!isset($post['topic_id']) || $post['topic_id'] != $topicid) {
									$newposts[$index] = new stdClass();
									$newposts[$index]->post_id = $post['post_id'];
									# increment mod count for this user
									if ($post['post_status'] == 1) $modcount++;
									if ($post['post_status'] == 2) $spamcount++;
									$index++;
								} else {
									if ($post['post_status'] != 0) {
										if ($post['post_status'] == 1) $modcount++;
										$newposts[$index]->post_id = $post['post_id'];
										if ($post['post_status'] == 2) $spamcount++;
										$index++;
									}
								}
							}
						}
					}
				}
			}
		} else {
			$newposts = $postlist;
			foreach ($postlist as $forum) {
				if (isset($forum['topics'])) {
					foreach ($forum['topics'] as $topic) {
						if (isset($topic['posts'])) {
							foreach ($topic['posts'] as $post) {
								$readcount++;
								# increment mod count for this user
								if ($post['post_status'] == 1) $modcount++;
								if ($post['post_status'] == 2) $spamcount++;
							}
						}
					}
				}
			}
		}
	}
	if ($newposts) {
		$readcount = $readcount - ($modcount+$spamcount);
	} else {
		$readcount = 0;
		$modcount = 0;
		$spamcount = 0;
	}

	$counts = array();
	$counts['read'] = $readcount;
	$counts['mod'] = $modcount;
	$counts['spam'] = $spamcount;

	return $counts;
}

# ------------------------------------------------------------------
# sp_GetWaitingUrl()
#
# Creates the new post urls and counts in the Admin Bar
#	$postlist:		array from the admin queue of posts
# ------------------------------------------------------------------
function sp_GetWaitingUrl($postlist, $a, $viewLabel, $unreadLabel, $modLabel, $spamLabel, $toolTip) {
	extract($a, EXTR_SKIP);
	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPABIMAGESMOB : SPABIMAGES;

	$buttonClass   = esc_attr($buttonClass);
	$iconClass	   = esc_attr($iconClass);
	$icon		   = SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$countClass	   = esc_attr($countClass);
	$viewLabel	   = SP()->displayFilters->title($viewLabel);
	$unreadLabel   = SP()->displayFilters->title($unreadLabel);
	$modLabel	   = SP()->displayFilters->title($modLabel);
	$spamLabel	   = SP()->displayFilters->title($spamLabel);
	$toolTip	   = esc_attr($toolTip);

	$counts = sp_GetWaitingNumbers($postlist);
	$readcount = $counts['read'] ;
	$modcount = $counts['mod'];
	$spamcount = $counts['spam'];

	$site = wp_nonce_url(SPAJAXURL.'admin-bar-newposts', 'admin-bar-newposts');
	$site2 = wp_nonce_url(SPAJAXURL.'admin-bar-update&amp;target=newposts', 'admin-bar-update');

	# All posts
	$out = "<a rel='nofollow' class='$buttonClass' id='spShowNewPostList' data-url='$site' data-update='$site2' title='$toolTip'>";
	$out.= $icon.$viewLabel;
	$out.= '</a>';

	# Ordinary Posts
	$out.= "<div class='spUnreadCount $countClass'>";
	$adminClass = ($readcount > 0) ? 'badge spUnreadUnread' : 'badge spUnreadRead';
	$out.= "$unreadLabel<span id='spUnread' class='$adminClass'>$readcount</span>";
	$out.= '</div>';

	$out.= "<div class='spModCount $countClass'>";
	$adminClass = ($modcount > 0) ? 'spModUnread badge' : 'spModRead badge';
	$out.= "$modLabel<span id='spNeedModeration' class='$adminClass'>$modcount</span>";
	$out.= '</div>';

	$out.= "<div class='spSpamCount $countClass'>";
	$adminClass = ($spamcount > 0) ? 'spSpamUnread badge' : 'spSpamRead badge';
	$out.= "$spamLabel<span id='spSpam' class='$adminClass'>$spamcount</span>";
	$out.= '</div>';

	return $out;
}

# ------------------------------------------------------------------
# sp_NewPostListAdmin()
#
# The complete admin bar display
# ------------------------------------------------------------------
function sp_NewPostListAdmin($newposts) {
	$defs = array('tagClassHeader'		=> 'spAdminQueueHeader',
	              'formClassHeader'		=> 'sfsubhead',
	              'buttonClassHeader'	=> 'spButton spRight',
	              'tagClassBody'		=> 'spMessageSuccess',
	              'tagClassListForum'	=> 'spAdminQueueForum',
	              'tagClassListTopic'	=> '',
	              'column1'				=> 'spColumnSection spLeft&height=30px&width=9%',
	              'column2'				=> 'spColumnSection spLeft&height=30px&width=90%',
	              'forumNameLabel'		=> 'spAdminForum',
	              'topicNameLabel'		=> 'spAdminQueueTopic',
				  'buttonClass'			=> 'spSubmit',
				  'labelClass'			=> 'spLabelSmall',
				  'tagClassPostData'	=> 'spAdminQueuePost',
				  'postUserData'		=> 'spPostSection',
				  'titleClass'			=> 'spAdminBarTitle',
				  'tagClassEachPost'	=> 'spAdminQueueThisPost',
				  'moderationAlert'		=> 'spAdminQueueMod spRight',
				  'userDelimeter'		=> '<br />',
				  'thisPostContent'		=> '',
				  'buttonSection'		=> 'spAdminQueueThisPostButtons',
				  'controlClass'		=> 'spSubmit',
				  'quickReplyBox'		=> 'spControl'
				 );

	$data = array();
	if (file_exists(SPTEMPLATES.'data/admin-bar-data.php')) {
		include SPTEMPLATES.'data/admin-bar-data.php';
		$data = sp_admin_bar_data();
	}

	$a = wp_parse_args($data, $defs);
	extract($a, EXTR_SKIP);
	# sanitize before use
	$tagClassHeader		= esc_attr($tagClassHeader);
	$formClassHeader	= esc_attr($formClassHeader);
	$buttonClassHeader	= esc_attr($buttonClassHeader);
	$tagClassBody		= esc_attr($tagClassBody);
	$tagClassListForum	= esc_attr($tagClassListForum);
	$tagClassListTopic	= esc_attr($tagClassListTopic);
	$forumNameLabel		= esc_attr($forumNameLabel);
	$topicNameLabel		= esc_attr($topicNameLabel);
	$buttonClass		= esc_attr($buttonClass);
	$labelClass			= esc_attr($labelClass);
	$tagClassPostData	= esc_attr($tagClassPostData);
	$postUserData		= esc_attr($postUserData);
	$titleClass			= esc_attr($titleClass);
	$tagClassEachPost	= esc_attr($tagClassEachPost);
	$moderationAlert	= esc_attr($moderationAlert);
	$thisPostContent	= esc_attr($thisPostContent);
	$buttonSection		= esc_attr($buttonSection);
	$controlClass		= esc_attr($controlClass);
	$quickReplyBox		= esc_attr($quickReplyBox);

?>
	<style>
		.spStackBtnLong {width:auto;white-space:normal !important;max-width: 155px; cursor:pointer !important;}
	</style>
<?php

	$alt = '';
	$nourl = '';

	if ($newposts) {
		$index = array();
		foreach ($newposts as $newpost) {
			$forumid = $newpost['forum_id'];
			$index[$forumid] = count($newpost['topics']);
		}

		# Set up the autoupdate url for admin bar counts
		$updateUrl = wp_nonce_url(SPAJAXURL.'autoupdate', 'autoupdate');

		# Display section heading
		sp_InsertBreak('spacer=4px');
		echo "<div class='spAdminQueueHeader $tagClassHeader'>";
		echo '<a id="newpoststop"></a>';

		$options = SP()->options->get('spAdminBar');
		if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
			$p = (SP()->core->device=='mobile' && current_theme_supports('sp-theme-responsive')) ? SPABIMAGESMOB : SPABIMAGES;
			echo "<form class='sfsubhead' action='".SP()->spPermalinks->get_url()."' method='post' name='removequeue'>";
			echo '<input type="hidden" name="doqueue" value="1" />';
			echo '<span class="spLeft">'.__('New/Unread Posts Management', 'spab').'</span>';

			echo "<a class='$buttonClassHeader' href='javascript:document.removequeue.submit();'>".SP()->theme->paint_icon('', $p, "sp_markRead.png").__('Empty the Admin Postbag', 'spab').'</a>';
			echo '</form>';
			$removal = true;
			$canremove = '1';
		}
		echo '</div>';
		# Start actual listing display
		echo "<div class='spInlineSection $tagClassBody' id='spAdminQueueMsg'></div>";

		# Display new posts heading
		sp_SectionStart("tagClass=spAdminQueueSection $tagClassBody", 'AdminQueue');
		echo '<span style="text-align:center" class="spAdminBarTitle">'.__('Forums and Topics', 'spab').'</span>';

		# Start with main forum header
		foreach ($newposts as $newpost) {
			# Display forum name
			echo "<div id='spAdminQueueForum".$newpost['forum_id']."' class='$tagClassListForum'>";
				sp_ColumnStart("tagClass=$column1");
					$icon = (!empty($newpost['forum_icon'])) ? SP()->theme->paint_custom_icon('spRopwIcon', SPCUSTOMURL.$newpost['forum_icon']) : SP()->theme->paint_icon('spTowIcon', SPTHEMEICONSURL, 'sp_ForumIcon.png');
					echo $icon;
				sp_ColumnEnd();

				sp_ColumnStart("tagClass=$column2");
					echo "<p class='$forumNameLabel'>";
					echo '<a class="spRowName" href="'.SP()->spPermalinks->build_url($newpost['forum_slug'], '', 1, 0).'">Forum: '.SP()->displayFilters->title($newpost['forum_name']).'</a>';
					echo '</p>';
					echo '<input type="hidden" id="tcount'.$newpost['forum_id'].'" value="'.$index[$newpost['forum_id']].'" />';
				sp_ColumnEnd();
				sp_InsertBreak();
			echo '</div>';

			# Now for each topic with new posts
			foreach ($newpost['topics'] as $topic) {
				$postcountmod = 0;
				$postcountord = 0;

				# a quick first pass to load the post count variables and check for spam
				$is_spam = false;
				foreach ($topic['posts'] as $post) {
					if ($post['post_status'] != 0 ? $postcountmod++ : $postcountord++);
					if ($post['post_status'] == 2) $is_spam = true;
					$lastpost_id = $post['post_id'];
				}

				# Display topics in forum
				$class = ($postcountmod) ? ' spModButton' : ' spUnreadButton';

				echo "<div id='spAdminQueueTopicList".$newpost['forum_id']."' class='$tagClassListTopic'>";
					echo "<div id='spAdminQueueTopic".$topic['topic_id']."' class='$topicNameLabel'>";
						sp_ColumnStart("tagClass=$column1");
							echo "<input type='button' name='openicon".$topic['topic_id']."' class='".$buttonClass.$class."' value='";
							if ($is_spam) {
								echo esc_attr(__('View Spam', 'spab'));
							} else {
								echo esc_attr(__('View', 'spab'));
							}
							echo "' data-topicid='spAdminQueueThisTopic".$topic['topic_id']."'/>";
							echo '<input type="hidden" id="pcount'.$topic['topic_id'].'" value="'.$topic['post_count'].'" />';
							echo '<input type="hidden" id="pcountmod'.$topic['topic_id'].'" value="'.$postcountmod.'" />';
							echo '<input type="hidden" id="pcountord'.$topic['topic_id'].'" value="'.$postcountord.'" />';
						sp_ColumnEnd();

						sp_ColumnStart("tagClass=$column2");
							echo '<p>'.sp_get_topic_newpost_url($newpost['forum_slug'], $topic['topic_slug'], SP()->displayFilters->title($topic['topic_name']), $lastpost_id, $post['post_index']).'</p>';
							$nourl = '';
							if ($topic['post_count'] == 1) {
								$note = __('There is 1 new post in this topic', 'spab');
							} else {
								$note = sprintf(__('There are %s new posts in this topic', 'spab'), $topic['post_count']);
							}

							echo "<p class='$labelClass'>".$note.'</p>';
						sp_ColumnEnd();
						sp_InsertBreak();
					echo '</div>';

					# Start display of post information
					echo "<div id='spAdminQueuePost".$topic['topic_id']."' class='$tagClassPostData'>";
						echo "<div id='spAdminQueueThisTopic".$topic['topic_id']."' class='$postUserData spInlineSection'>";
							echo "<p style='text-align:center' class='$titleClass'>".__('Post Details', 'spab').'</p>';
							$pindex = 0;
							$mod_required = false;

							# Start the post display loop
							foreach ($topic['posts'] as $post) {
								$is_spam = false;
								if ($pindex > 0) echo '<hr>';
								echo "<div id='spAdminQueueThisPost".$post['post_id']."' class='$tagClassEachPost'>";
								$pindex++;
								$lastpost = ($pindex == $topic['post_count']) ? true : false;
								if ($post['post_status'] != 0) {
									$mod_required = true;
									echo "<div class='$moderationAlert'>".__('Awaiting moderation', 'spab');
									if($post['post_status']==2) {
										$is_spam = true;
										echo '<br />'.__('Akismet marked as spam', 'spab');
									}
									echo '</div>';
								}
								sp_InsertBreak();

								echo '<b>'.SP()->displayFilters->name($post['display_name']).'</b>'.$userDelimeter.'<small>'.$post['user_type'].'</small>';
								echo $userDelimeter.'<small>'.sprintf(__('Post %s in Topic', 'spab'), $post['post_index']).'</small>';
								echo '<hr />';
								echo "<div class='$thisPostContent'>".SP()->displayFilters->content($post['post_content']).'</div>';
								echo '</div>';

								# Set up the ajax base url
								$basesite = wp_nonce_url(SPAJAXURL."moderation&amp;pid=".$post['post_id']."&amp;tid=".$topic['topic_id']."&amp;fid=".$newpost['forum_id'], 'moderation');

								echo "<div id='spAdminQueueThisPostButtons".$post['post_id']."' class='$buttonSection'>";
									echo '<table><tr>';
									if ($topic['post_count'] == 1) {
										$label = __('This Post', 'spab');
									} else {
										$label = __('All Posts', 'spab');
									}

									if ($lastpost) {
										$site = $basesite.'&amp;targetaction=0&amp;canremove='.$canremove;

										if ($mod_required) {
											if (SP()->auths->get('moderate_posts', $newpost['forum_id'])) {
												$posturl = SP()->spPermalinks->build_url($newpost['forum_slug'], $topic['topic_slug'], 0, $post['post_id'], $post['post_index']);
											}
										} else {
											$posturl = SP()->spPermalinks->build_url($newpost['forum_slug'], $topic['topic_slug'], 0, $post['post_id'], $post['post_index']);
										}

										if ($mod_required) {
											$m = (SP()->core->device == 'mobile') ? esc_attr(__("Approve & Load", 'spab')) : esc_attr(sprintf(__("Mark %s Approved and go to Topic", 'spab'), $label));
											echo '<td><input type="button" class="'.$controlClass.' spStackBtnLong spModeratePost" name="g0-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-url="'.$posturl.'" data-site="'.$site.'" data-removal="'.$removal.'" data-postid="'.$post['post_id'].'" data-forumid="'.$newpost['forum_id'].'" data-topicid="'.$topic['topic_id'].'" data-status="'.$post['post_status'].'" data-update="'.$updateUrl.'" /></td>';
											$m = (SP()->core->device=='mobile') ? esc_attr(__("Approve & Close", 'spab')) : esc_attr(sprintf(__("Mark %s Approved and Close", 'spab'), $label));
											echo '<td><input type="button" class="'.$controlClass.' spStackBtnLong spModeratePost" name="a0-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-url="" data-site="'.$site.'" data-removal="'.$removal.'" data-postid="'.$post['post_id'].'" data-forumid="'.$newpost['forum_id'].'" data-topicid="'.$topic['topic_id'].'" data-status="'.$post['post_status'].'" data-update="'.$updateUrl.'" /></td>';

											if (SP()->core->device=='mobile') echo '</tr></tr>';

											$m = (SP()->core->device=='mobile') ? esc_attr(__("Approve & Reply", 'spab')) : esc_attr(sprintf(__("Mark %s Approved and Quick Reply", 'spab'), $label));
											echo '<td><input type="button" class="'.$controlClass.' spStackBtnLong spReplyPost" name="q0-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-topicid="sfqform'.$topic['topic_id'].'" /></td>';
											$qaction = 0;
										} else {
											$site = $basesite.'&amp;targetaction=1&amp;canremove='.$canremove;
											$m = (SP()->core->device=='mobile') ? esc_attr(__("Mark Read & Load", 'spab')) : esc_attr(sprintf(__("Mark %s as Read and go to Topic", 'spab'), $label));
											echo '<td><input type="button" class="'.$controlClass.' spStackBtnLong spModeratePost" name="g1-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-url="'.$posturl.'" data-site="'.$site.'" data-removal="'.$removal.'" data-postid="'.$post['post_id'].'" data-forumid="'.$newpost['forum_id'].'" data-topicid="'.$topic['topic_id'].'" data-status="'.$post['post_status'].'" data-update="'.$updateUrl.'" /></td>';
											$m = (SP()->core->device=='mobile') ? esc_attr(__("Mark Read & Close", 'spab')) : esc_attr(sprintf(__("Mark %s as Read and Close", 'spab'), $label));
											echo '<td><input type="button" class="'.$controlClass.' spStackBtnLong spModeratePost" name="a1-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-url="" data-site="'.$site.'" data-removal="'.$removal.'" data-postid="'.$post['post_id'].'" data-forumid="'.$newpost['forum_id'].'" data-topicid="'.$topic['topic_id'].'" data-status="'.$post['post_status'].'" data-update="'.$updateUrl.'" /></td>';

											if (SP()->core->device=='mobile') echo '</tr></tr>';

											$m = (SP()->core->device=='mobile') ? esc_attr(__("Mark Read & Reply", 'spab')) : esc_attr(sprintf(__("Mark %s as Read and Quick Reply", 'spab'), $label));
											echo '<td><input type="button" class="'.$controlClass.' spStackBtnLong spReplyPost" name="a1-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-topicid="sfqform'.$topic['topic_id'].'" /></td>';
											$qaction = 1;
										}
									}

									if ($removal) {
										$remsite = $basesite.'&amp;targetaction=2&amp;canremove='.$canremove;
										$msg = esc_attr(__('Are you sure you want to delete this Post?', 'spab'));
										$m = (SP()->core->device=='mobile') ? esc_attr(__("Delete Post", 'spab')) : esc_attr(__("Delete this Post", 'spab'));
										echo '<td><input type="button" class="'.$controlClass.' spStackBtnLong spDeletePost" name="a2-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-msg="'.$msg.'" data-url="" data-site="'.$remsite.'" data-removal="'.$removal.'" data-postid="'.$post['post_id'].'" data-forumid="'.$newpost['forum_id'].'" data-topicid="'.$topic['topic_id'].'" data-status="'.$post['post_status'].'" data-update="'.$updateUrl.'" /></td>';

										if ($post['user_type']=='Member' && $is_spam) {
											$actionUrl = wp_nonce_url(SPAJAXURL.'remove-spam&amp;postid='.$post['post_id'].'&amp;userid='.$post['user_id'], 'remove-spam');
											$updateUrl = wp_nonce_url(SPAJAXURL.'admin-bar-update&amp;target=newposts', 'admin-bar-update');
											echo '</tr></tr>';
											$m = (SP()->core->device=='mobile') ? esc_attr(__("Remove Member & All Their Posts", 'spab')) : esc_attr(__("Remove this Member and All their Posts", 'spab'));
											echo '<td colspan="2"><input type="button" class="'.$controlClass.' spStackBtnLong spRemoveSpam" name="delSpam-'.$post['post_id'].'" value="'.$m.'" style="white-space: pre;" data-url="'.$actionUrl.'" data-update="'.$updateUrl.'" /></td>';
										}
									}

									echo '</tr></table>';

									# Quick Reply Form
									if ($lastpost) {
										$qsavesite = wp_nonce_url(SPAJAXURL."quickreply&amp;tid=".$topic['topic_id']."&amp;fid=".$newpost['forum_id'], 'quickreply');
										echo '<div id="sfqform'.$topic['topic_id'].'" class="spInlineSection">';
										echo '<form action="'.SP()->spPermalinks->get_url().'" method="post" class="'.$formClassHeader.' quickReplySubmit" name="addpost'.$topic['topic_id'].'" data-saveurl="'.$qsavesite.'", data-modurl="'.$site.'", data-postid="'.$post['post_id'].'", data-forumid="'.$newpost['forum_id'].'", data-topicid="'.$topic['topic_id'].'", data-poststatus="'.$post['post_status'].'", data-action="'.$qaction.'", data-refreshurl="'.$updateUrl.'">';
										echo '<textarea	 tabindex="1" class="'.$quickReplyBox.'" name="postitem'.$topic['topic_id'].'" id="postitem'.$topic['topic_id'].'" cols="60" rows="8"></textarea>';
										echo '<br /><input type="submit" tabindex="2" class="'.$controlClass.'" id="sfsave'.$topic['topic_id'].'" name="newpost'.$topic['topic_id'].'" value="'.esc_attr(__('Save New Post', 'spab')).'" />';

										do_action('sph_quickreply_form', $newpost, $topic, $post);

										echo '</form><br /></div>';
									}
								echo '</div>';
							}
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}
		}
		sp_SectionEnd('', 'AdminQueue');
	} else {
		echo "<div class='$tagClassHeader spMessage'>";
		echo __('There are no unread posts', 'spab').'</div>';
		echo "<div class='spInlineSection' id='spAdminQueueMsg'></div>\n";
	}
	sp_InsertBreak('spacer-4px');
}

function sp_AdminBarDashboardPosts() {
	$out = '';

	# New/Unread Admin Post List
	$options = SP()->options->get('spAdminBar');
	if ($options['dashboardposts']) {
		if (SP()->user->thisUser->admin) {
			$waiting = SP()->DB->count(SPPOSTS, 'post_status != 0');
			$unreads = sp_AdminBarGetUnreadForums();
			$out.= '<p>'.__('The Admin postbag', 'spab').'</p>';

			if ($unreads) {
				$out.='<table class="sfdashtable">';
				foreach ($unreads as $unread) {
					if (!empty($unread)) {
						$out.= '<tr>';
						if (SP()->user->thisUser->admin) {
							if ($unread->post_count == 1) {
								$mess = sprintf(__('There is %s new post', 'spb'), $unread->post_count);
							} else {
								$mess = sprintf(__('There are %s new posts', 'spab'), $unread->post_count);
							}
							$out.= '<td>'.$mess.' '.__('in the forum topic', 'spab').':&nbsp;&nbsp;'.sp_AdminBarDashboardUrl($unread->forum_slug, $unread->topic_id, $unread->post_id).'</td>';
						}
						$out.= '</tr>';
					}
				}
				$out.= '</table>';

				if ($waiting == 1) $out.= '<br /><table class="sfdashtable"><tr><td>'.__('There is 1 post awaiting approval', 'spab').'</td></tr></table>';
				if ($waiting > 1) $out.= '<br /><table class="sfdashtable"><tr><td>'.__('There are', 'spab').' '.$waiting.' '.__('posts awaiting approval', 'spab').'.</td></tr></table>';
			} else {
				$out.= '<p>'. __('There are no new forum posts', 'spab').'</p>';
			}
		}
	echo $out;
	}
}

# ------------------------------------------------------------------
# sp_AdminBarGetUnreadForums()
#
# Returns list from the waiting table (Admins queue) for dashboard
# ------------------------------------------------------------------
function sp_AdminBarGetUnreadForums() {
	return SP()->DB->select('SELECT topic_id, '.SPWAITING.'.forum_id, forum_slug, forum_name, group_id, '.SPWAITING.'.post_count, '.SPWAITING.'.post_id
			 FROM '.SPFORUMS.'
			 JOIN '.SPWAITING.' ON '.SPFORUMS.'.forum_id = '.SPWAITING.'.forum_id
			 WHERE '.SPWAITING.'.post_count > 0
			 ORDER BY forum_id, topic_id');
}

function sp_AdminBarDashboardUrl($forumslug, $topicid, $postid) {
	$out = '';
	$topic = SP()->DB->table(SPTOPICS, "topic_id='$topicid'");
	if (!empty($topic)) $out = '<a href="'.SP()->spPermalinks->build_url($forumslug, $topic[0]->topic_slug, 0, $postid).'"><img src="'. SPADMINIMAGES .'sp_AnnounceNew.png" alt="" />&nbsp;&nbsp;'.$topic[0]->topic_name.'</a>'."\n";
	return $out;
}

function sp_AdminBarEmail($msg, $newpost){
	$total = 0;
	$unread = SP()->DB->table(SPWAITING);
	if ($unread) {
		foreach ($unread as $entry) {
			$total+= $entry->post_count;
		}
	}

	$eol = "\r\n";
	$msg.= __('There are currently', 'spab').' '.$total.' '.__('post(s) in', 'spab').' '.count($unread).' '.__('topic(s) awaiting review', 'spab').$eol;
	return $msg;
}

# ------------------------------------------------------------------
# sp_get_topic_newpost_url()
#
# Builds the admin new post url
#	$forumslug:		forum slug
#	$topicslug:		topic slug
#	$topicname:		topic name
#	$postid:		if of post
#	$postindex:		index of post if known
# ------------------------------------------------------------------
function sp_get_topic_newpost_url($forumslug, $topicslug, $topicname, $postid, $postindex=0) {
	$out = '<a href="'.SP()->spPermalinks->build_url($forumslug, $topicslug, 0, $postid, $postindex).'">'.$topicname.'</a>'."\n";
	return $out;
}
