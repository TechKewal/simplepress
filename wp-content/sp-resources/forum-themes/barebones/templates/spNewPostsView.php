<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	New Posts View
#	Author		:	Simple:Press
#
#	The 'new posts view' template is used to display a list of unread posts
#
# --------------------------------------------------------------------------------------

	# Load the forum header template - normally first thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadContainer', 'head');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

		sp_SectionStart('tagId=spRecentPostList&tagClass=spRecentPostSection');

			echo '<div class="spMessage">'.__sp('Most recent topics with unread posts').'</div>';

    		# Start the 'searchView' section
    		# ----------------------------------------------------------------------
            $first = SP()->filters->integer($_GET['first']);
            $group = SP()->filters->integer($_GET['group']);
			$id = SP()->filters->integer($_GET['id']);
			if (!empty($id)) {
				$topics = array();
				for ($x=0; $x<count(SP()->user->thisUser->newposts['forums']); $x++) {
					if (SP()->user->thisUser->newposts['forums'][$x] == $id) $topics[] = SP()->user->thisUser->newposts['topics'][$x];
				}
			} else {
				$topics = SP()->user->thisUser->newposts['topics'];
			}

            if(isset($count) ? $count = SP()->filters->integer($_GET['count']) : $count = 0);
			SP()->forum->view->listTopics = new spcTopicList($topics, $count, $group, $id, $first, 0, 'all unread posts');
    		sp_load_template('spListView.php');

    	sp_SectionEnd();

	sp_SectionEnd('', 'body');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');
