<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	spRelatedTopicsTag($limit=10, $topic_id, $listtags=true, $forum=true, $echo=false)

	displays related topics

	parameters:

		$limit			How many tags to show in the list				number			10 (default)
		$topic_id		the topic id for which to find related topics	number			topic id
		$listtags		Wrap in <li> tags (li only)						true/false		true
		$forum			display forum name of related topics			true/false		true
		echo			write display (returns it if false)				true/false		true
 	===================================================================================*/
function spRelatedTopicsTag($limit=10, $topic_id, $listtags=true, $forum=true, $echo=true) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $topic_id = SP()->filters->integer($topic_id);
    if (empty($topic_id)) return;

	$out = '';
	$tags = SP()->DB->select('SELECT tag_slug
						FROM '.SPTAGS.'
						JOIN '.SPTAGSMETA.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.".tag_id
						WHERE topic_id=$topic_id");
	if ($tags) {
		# build list of tags for the topic id
		$taglist = '';
		foreach ($tags as $tag) {
			if ($taglist == '') {
				$taglist = "('".$tag->tag_slug."'";
			} else {
				$taglist.= ",'".$tag->tag_slug."'";
			}
		}
		$taglist.= ')';

		# now grab the results
		$LIMIT = ' LIMIT '.$limit;
		$ORDER = ' ORDER BY topic_id DESC';
		$WHERE = SPTOPICS.'.topic_id IN (SELECT topic_id FROM '.SPTAGSMETA.' JOIN '.SPTAGS.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.".tag_id WHERE tag_slug IN $taglist)";
		$topics = SP()->DB->select('SELECT SQL_CALC_FOUND_ROWS DISTINCT
				 '.SPTOPICS.'.topic_id, topic_name, topic_slug, '.SPTOPICS.'.forum_id, forum_name, forum_slug
				 FROM '.SPTOPICS.'
				 JOIN '.SPFORUMS.' ON '.SPTOPICS.'.forum_id = '.SPFORUMS.'.forum_id
				 JOIN '.SPPOSTS.' ON '.SPTOPICS.'.topic_id = '.SPPOSTS.".topic_id
				 WHERE $WHERE$ORDER$LIMIT", 'set', ARRAY_A);

		# now output the related topics
		if ($topics) {
			foreach ($topics as $topic) {
				if (SP()->auths->can_view($topic['forum_id'], 'topic-title')) {
					$p = false;

					# Start contruction
					if ($listtags) $out.= '<li class="sftagli">';
					$out.= SP()->spPermalinks->get_topic_url($topic['forum_slug'], $topic['topic_slug'], $topic['topic_name']);

					if ($forum) {
						$out.= '<p class="sftagp">'.__('posted in forum', 'sp-tags').' '.SP()->displayFilters->title($topic['forum_name']).'&nbsp;';
						$p = true;
					}

					if ($p) $out.= '</p>';
					if ($listtags) $out.= '</li>';
				}
			}
		} else {
			$out.= '<li class="sftagli">';
			$out.= __('No related topics', 'sp-tags');
			$out.= '</li>';
		}
	} else {
		$out.= '<li class="sftagli">';
		$out.= __('No related topics', 'sp-tags');
		$out.= '</li>';
	}

	if ($echo) {
		echo $out;
		return;
	} else {
		return $out;
	}
}
