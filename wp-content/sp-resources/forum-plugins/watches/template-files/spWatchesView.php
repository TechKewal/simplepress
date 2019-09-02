<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Watches View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'watches' template is used to display list of users unread topics they are watching
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

	sp_SectionStart('tagClass=spHeadContainer', 'head');
		sp_load_template('spHead.php');
	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

		sp_SectionStart('tagClass=spPlainSection', 'subs');
            add_action('sph_ListViewBodyEnd', 'sp_watches_list_button');
        	if (!empty(SP()->user->thisUser->watches)) {
            	echo '<div class="spStopWatchingAll">';
            	echo '<form action="'.SP()->spPermalinks->get_url().'" method="get" name="endallwatches">';
            	echo '<input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.SP()->user->thisUser->ID.'" />';
            	echo '<input type="submit" class="spSubmit" name="endallwatches" value="'.esc_attr(__('Remove All Watches', 'sp-watches')).'" />';
            	echo '</form>';
            	echo '</div>';

                $first = SP()->filters->integer($_GET['first']);
                SP()->forum->view->listTopics = new spcTopicList(SP()->user->thisUser->watches, 0, true, '', $first, 1, 'watches');

                sp_load_template('spListView.php');
            } else {
        		echo '<div class="spMessage">';
        		echo '<p>'.__('You are not currently watching any topics', 'sp-watches').'</p>';
        		echo '</div>';
            }
		sp_SectionEnd('', 'subs');

	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'foot');

##################################

    function sp_watches_list_button() {
        $site = wp_nonce_url(SPAJAXURL.'watches-manage&amp;targetaction=remove-watch&amp;topic='.SP()->forum->view->thisListTopic->topic_id.'&amp;user='.SP()->user->thisUser->ID, 'watches-manage');
    	echo '<a rel="nofollow" class="spButton spRight spWatchEndButton spWatchesEndButton" title="'.__('Stop Watching Topic', 'sp-watches').'" data-target="listtopic'.SP()->forum->view->thisListTopic->topic_id.'" data-site="'.$site.'">';
    	echo SP()->theme->paint_icon('spIcon', WIMAGES, 'sp_WatchesStopWatch.png').__('Stop Watching', 'sp-watches');
    	echo '</a>';
    }
