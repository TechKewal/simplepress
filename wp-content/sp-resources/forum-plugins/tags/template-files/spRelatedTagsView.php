<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Related Tag View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'relate tags' template is used to display list of topics with related tags
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

		sp_SectionStart('tagClass=spPlainSection', 'related-tags');
           	if (isset($_GET['topic'])) $topic_id = SP()->filters->integer($_GET['topic']);
        	if (!empty($topic_id)) {
            	$tags = SP()->DB->select('SELECT tag_slug
            						FROM '.SPTAGS.'
            						JOIN '.SPTAGSMETA.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.".tag_id
            						WHERE topic_id=$topic_id");
            	if ($tags) {
            		$taglist = '';
            		foreach ($tags as $tag) {
            			if (empty($taglist)) {
            				$taglist = "('".$tag->tag_slug."'";
            			} else {
            				$taglist.= ",'".$tag->tag_slug."'";
            			}
            		}
            		$taglist.= ')';

            		# now grab the results
            		$query = new stdClass();
	            		$query->table	= SPTOPICS;
    	        		$query->fields	= 'topic_id';
        	    		$query->where	= SPTOPICS.'.topic_id IN (SELECT topic_id FROM '.SPTAGSMETA.' JOIN '.SPTAGS.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.".tag_id WHERE topic_id != $topic_id AND tag_slug IN $taglist)";
            			$query->orderby  = 'topic_id DESC';
            			$query->type		= 'col';
            			$query->resultType	=	ARRAY_A;
            			$query = apply_filters('sph_related_tags_query', $query);
            		$topics = SP()->DB->select($query);

            		if ($topics) {
            			SP()->forum->view->listTopics = new spcTopicList($topics);
            			sp_load_template('spListView.php');
            		} else {
            			echo '<div class="spMessage">';
            			echo __('No related topics found based on the tags for this topic', 'sp-tags');
            			echo '</div>';
            		}
            	} else {
            		echo '<div class="spMessage">';
            		echo __('This topic does not have any tags so cannot look for related topics', 'sp-tags');
            		echo '</div>';
            	}
            } else {
        		echo '<div class="spMessage">';
        		echo __('You have requested an invalid topic ID for related tags', 'sp-tags');
        		echo '</div>';
            }
		sp_SectionEnd('', 'related-tags');

	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'foot');
