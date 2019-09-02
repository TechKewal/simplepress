<?php
/*
Simple:Press
Tags plugin database routines
$LastChangedDate: 2018-10-24 06:19:24 -0500 (Wed, 24 Oct 2018) $
$Rev: 15767 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('SFMANAGETAGSNUM', 35);

function sp_tags_get_tags($order, $search, $page) {
    global $wpdb;

	# get ordering
	if ($order == 'natural') {
		$orderby = 'tag_name ASC';
	} elseif ($order == 'asc') {
		$orderby = 'tag_count ASC';
	} else {
		$orderby = 'tag_count DESC';
	}

	# search term requested?
	$like = '';
	if (!empty($search)) $like = "tag_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($search))."%'";

	# paging
	$limit = ($page * SFMANAGETAGSNUM).", ".SFMANAGETAGSNUM;
	$tags['tags'] = SP()->DB->table(SPTAGS, $like, '', $orderby, $limit);
	$tags['count'] = SP()->DB->count(SPTAGS);

	return $tags;
}

function sp_tags_get_topics($currentpage, $tpaged, $date, $forum, $search) {
	global $wpdb;

	if (!$tpaged) $tpaged=20;

	# how many topics per page?
	$startlimit = 0;
	if ($currentpage != 1) $startlimit = (($currentpage-1) * $tpaged);
	$limit = " LIMIT $startlimit, $tpaged";

	# build the where clause for specific forum
	$where = '';
	if (!empty($forum) && $forum != 0) $where.= ' WHERE '.SPTOPICS.".forum_id=$forum";

	# build the where clause for specific date
	if (!empty($date) && $date != 0) {
		$year = substr($date, 0, 4);
		$month = substr($date, 4, 2);
		if (empty($where)) {
			$where.= ' WHERE';
		} else {
			$where.= ' AND';
		}
		$where.= ' MONTH(topic_date)='.$month.' AND YEAR(topic_date)='.$year;
	}

	# build the where clause for topic title search term
	if (!empty($search)) {
		if (empty($where)) {
			$where.= ' WHERE';
		} else {
			$where.= ' AND';
		}
		$where.= " topic_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($search))."%'";
	}

	# retrieve topic records
	$sql = 'SELECT topic_id, topic_name, topic_slug, forum_slug, forum_name
			FROM '.SPTOPICS.'
		 	JOIN '.SPFORUMS.' ON '.SPFORUMS.'.forum_id = '.SPTOPICS.".forum_id
			$where
			ORDER BY topic_id DESC
			$limit";
	$records = SP()->DB->select($sql, 'set', ARRAY_A);

	$topics = array();
	if ($records) {
		$where = str_replace('WHERE ', '', $where);
		$topics['count'] = SP()->DB->count(SPTOPICS, $where);
		if (!$topics['count']) $topics['count'] = 0;
		foreach ($records as $index => $topic) {
			$topics['topic'][$index]['topic_id'] = $topic['topic_id'];
			$topics['topic'][$index]['topic_name'] = $topic['topic_name'];
			$topics['topic'][$index]['topic_slug'] = $topic['topic_slug'];
			$topics['topic'][$index]['forum_slug'] = $topic['forum_slug'];
			$topics['topic'][$index]['forum_name'] = $topic['forum_name'];

			# get tags for topic
			$tags = SP()->DB->select('SELECT tag_name, '.SPTAGS.'.tag_id
								FROM '.SPTAGS.'
								JOIN '.SPTAGSMETA.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.'.tag_id
								WHERE topic_id='.$topic['topic_id'], 'set', ARRAY_A);
			$topictags = array();
			$topicids = array();
			if ($tags) {
				foreach ($tags as $tag) {
					$topictags[] = $tag['tag_name'];
					$topicids[] = $tag['tag_id'];
				}
				$topics['topic'][$index]['tags']['list'] = implode(', ', $topictags);
				$topics['topic'][$index]['tags']['ids'] = implode(',', $topicids);
			}
		}
	}

	return $topics;
}

function sp_tags_edit_tags() {
    check_admin_referer('forum-adminform_sfedittags', 'forum-adminform_sfedittags');

    $topic_id_list = $_POST['topic_id']; # array - sanitised below before use
    $tag_id_list = $_POST['tag_id']; # array - sanitised below before use
    $tag_list = $_POST['tags']; # array - sanitised below before use

	# take the easy way out and remove all tags and then add back in the new list
	for ($x=0; $x<count($topic_id_list); $x++) {
		if (!empty($tag_id_list[$x])) { # if no tags originally, dont delete anything
			# grab all the tag rows and decrement the tag count
			$tags = SP()->DB->table(SPTAGS, 'tag_id IN ('.SP()->filters->str($tag_id_list[$x]).')');
			foreach ($tags as $tag) {
				# decrement tag count and delete if it gets to zero or update the new count
				$tag->tag_count--;
				if ($tag->tag_count == 0) {

					SP()->DB->execute('DELETE FROM '.SPTAGS." WHERE tag_id=$tag->tag_id"); # count is zero so delete
				} else {
					SP()->DB->execute('UPDATE '.SPTAGS." SET tag_count=$tag->tag_count WHERE tag_id=$tag->tag_id"); # update count
				}
			}

			# remove all the tag meta entries for the topic
			SP()->DB->execute('DELETE FROM '.SPTAGSMETA.' WHERE topic_id='.SP()->filters->integer($topic_id_list[$x]));
		}

		# now add the current tags back in for the topic
		if (!empty($tag_list[$x])) {
		    $tags = trim(SP()->filters->str($tag_list[$x]));
		    $tags = trim($tags, ',');  # no extra commas allowed
			$tags = explode(',', $tags);
			$tags = array_unique($tags);  # remove any duplicates
			$tags = array_values($tags);  # put back in order
			if (SP()->core->forumData['display']['topics']['maxtags'] > 0 && count($tags) > SP()->core->forumData['display']['topics']['maxtags']) {
				$tags = array_slice($tags, 0, SP()->core->forumData['display']['topics']['maxtags']);  # limit to maxt tags opton
			}
			$mess = sp_tags_new_tags(SP()->filters->integer($topic_id_list[$x]), $tags);
			if ($mess != '') return $mess;
		}
	}

    do_action('sph_tags_edit');

	$mess = __('Tags updated', 'sp-tags');
	return $mess;
}

function sp_tags_rename_tags() {
    check_admin_referer('forum-adminform_sfrenametags', 'forum-adminform_sfrenametags');

    $otags = trim(SP()->filters->str($_POST['renametag_old']));
    $tags  = trim(SP()->filters->str($_POST['renametag_new']));

    if (empty($otags) || empty($tags)) {
    	$mess = __('Renaming tags requires old tag(s) and new tags(s) entries.  No tags renamed', 'sp-tags');
		return $mess;
    }

    # prep the new tags
    $tags = trim($tags, ',');  # no extra commas allowed
	$tags = explode(',', $tags);
	$tags = array_unique($tags);  # remove any duplicates
	$tags = array_values($tags);  # put back in order

    # prep the old tags
    $otags = trim($otags, ',');  # no extra commas allowed
	$otags = explode(',', $otags);
	$otags = array_unique($otags);  # remove any duplicates
	$otags = array_values($otags);  # put back in order
    foreach ($otags as $tag) {
		$tagslug = sp_create_slug($tag, false);
		$tagid = SP()->DB->table(SPTAGS, "tag_slug='$tagslug'", 'tag_id');
		if ($tagid) {
			# delete tag itself
			SP()->DB->execute('DELETE FROM '.SPTAGS." WHERE tag_id=$tagid");

			# find the topics that use this tag
			$topics = SP()->DB->table(SPTAGSMETA, "tag_id=$tagid");
			if ($topics) {
				foreach ($topics as $topic) {
					# delete tag metas for this topic
					SP()->DB->execute('DELETE FROM '.SPTAGSMETA." WHERE topic_id=$topic->topic_id AND tag_id=$tagid");

					# add in the new tags
					sp_tags_new_tags($topic->topic_id, $tags);
				}
			}
		}
    }

    do_action('sph_tags_renamed');

	$mess = __('Tags renamed or merged', 'sp-tags');
	return $mess;
}

function sp_tags_delete_tags() {
    check_admin_referer('forum-adminform_sfdeletetags', 'forum-adminform_sfdeletetags');

    $tags = trim(SP()->filters->str($_POST['deletetag_name']));

    if (empty($tags)) {
    	$mess = __('Deleting tags requires tags(s) entry.  No Tags deleted', 'sp-tags');
		return $mess;
    }

	$deleted = 0; # indicate nothing deleted
    # loop through tags and delete the tag and the tag metas
    $tags = trim($tags, ',');  # no extra commas allowed
	$tags = explode(',', $tags);
	$tags = array_unique($tags);  # remove any duplicates
	$tags = array_values($tags);  # put back in order
    foreach ($tags as $tag)  {
		$tagslug = sp_create_slug($tag, false);
		$tagid = SP()->DB->table(SPTAGS, "tag_slug='$tagslug'", 'tag_id');
		if ($tagid) {
			# delete tag metas with this tag id
			SP()->DB->execute('DELETE FROM '.SPTAGSMETA." WHERE tag_id=$tagid");

			# delete tag itself
			SP()->DB->execute('DELETE FROM '.SPTAGS." WHERE tag_id=$tagid");

			# indicate at least some tags deleted
			if ($deleted == 0) $deleted = 1;
		} else {
			if ($deleted == 1) $deleted = 2; # indicate some deleted but some not found
		}
    }

    # output deletion results message
    switch ($deleted) {
    	case 0:
			$mess = __('No tags matched for deletion', 'sp-tags');
			break;
    	case 1:
            do_action('sph_tags_del');
			$mess = __('Tags successfully deleted', 'sp-tags');
			break;
    	case 2:
            do_action('sph_tags_del');
			$mess = __('Some tags deleted, but others not found', 'sp-tags');
			break;
    }

	return $mess;
}

function sp_tags_add_tags() {
    check_admin_referer('forum-adminform_sfaddtags', 'forum-adminform_sfaddtags');

    $mtags = trim(SP()->filters->str($_POST['addtag_match']));
    $tags  = trim(SP()->filters->str($_POST['addtag_new']));

    if (empty($tags)) {
    	$mess = __('Adding tags requires new tags(s) entry.  No tags added', 'sp-tags');
		return $mess;
    }

	# prep the new tags
    $tags = trim($tags, ',');  # no extra commas allowed
	$tags = explode(',', $tags);
	$tags = array_unique($tags);  # remove any duplicates
	$tags = array_values($tags);  # put back in order
	if (SP()->core->forumData['display']['topics']['maxtags'] > 0 && count($tags) > SP()->core->forumData['display']['topics']['maxtags']) {
		$tags = array_slice($tags, 0, SP()->core->forumData['display']['topics']['maxtags']);  # limit to max tags opton
	}

    # if not match tags, add the new tags to all topics
    if (empty($mtags)) {
    	# get topics
		$topics = SP()->DB->table(SPTOPICS);
		if ($topics) {
			foreach ($topics as $topic) {
				# now add the tags
				sp_tags_new_tags($topic->topic_id, $tags);
			}

            do_action('sph_tags_add');

			$mess = __('Tags added to all topics', 'sp-tags');
			return $mess;
		} else {
	    	$mess = __('No topics to add tags to.  No tags added', 'sp-tags');
			return $mess;
		}
    }

	# alrighty, so need to match tags before we add the new ones
	# prep the match tags
    $mtags = trim($mtags, ',');  # no extra commas allowed
	$mtags = explode(',', $mtags);
	$mtags = array_unique($mtags);  # remove any duplicates
	$mtags = array_values($mtags);  # put back in order
	if ($mtags) {
		$mtag_list = '(';
		$first = true;
		# Now put the tags back together in list
		foreach ($mtags as $mtag) {
			# convert to a tag slug and build slug list
			$mtagslug = sp_create_slug($mtag, false);
			if ($first) {
				$mtag_list.= "'".$mtagslug."'";
				$first = false;
			} else {
				$mtag_list.= ",'".$mtagslug."'";
			}
		}
		$mtag_list.= ')';

		# grab any topics that have a matching slug
		$tagids = SP()->DB->table(SPTAGS, "tag_slug IN $mtag_list");
		if ($tagids) {
			# now find the topics with these matched tags and add the new tags
			foreach ($tagids as $tagid) {
				$topics = SP()->DB->table(SPTAGSMETA, "tag_id = $tagid->tag_id");
				if ($topics) {
					foreach ($topics as $topic) {
						# now add the tags
						sp_tags_new_tags($topic->topic_id, $tags);
					}
				}
			}

            do_action('sph_tags_add');

			$mess = __('Tags added to topics with matched tags', 'sp-tags');
			return $mess;
		} else {
			$mess = __('No tags matched', 'sp-tags');
			return $mess;
		}
	} else {
		$mess = __('Invalid matching tags entry.  No tags added', 'sp-tags');
		return $mess;
	}

	$mess = __("Oh Oh.  This shouldn't happen", 'sp-tags');
}

function sp_tags_cleanup_tags() {
    check_admin_referer('forum-adminform_sfcleanup', 'forum-adminform_sfcleanup');

	# remove orphaned tags
	$tagids = SP()->DB->table(SPTAGS);
	if ($tagids) {
		foreach ($tagids as $tagid) {
			$meta = SP()->DB->table(SPTAGSMETA, "tag_id=$tagid->tag_id");
			if (!$meta) SP()->DB->execute('DELETE FROM '.SPTAGS." WHERE tag_id=$tagid->tag_id");
		}
	}

	# remove orphaned tag meta
	$tagids = SP()->DB->table(SPTAGSMETA);
	if ($tagids) {
		foreach ($tagids as $tagid) {
			$tags = SP()->DB->table(SPTAGS, "tag_id=$tagid->tag_id");
			if (!$tags) SP()->DB->execute('DELETE FROM '.SPTAGSMETA." WHERE tag_id=$tagid->tag_id");
		}
	}

	# clean up the tag counts
	$tagids = SP()->DB->table(SPTAGS);
	if ($tagids) {
		foreach ($tagids as $tagid) {
			# get the number of topics using this tag
			$count = SP()->DB->count(SPTAGSMETA, "tag_id=$tagid->tag_id");

			# set the count to number of topics using
			SP()->DB->execute('UPDATE '.SPTAGS." SET tag_count=$count WHERE tag_id=$tagid->tag_id");
		}
	}

    do_action('sph_tags_cleanup');

	$mess = __('Tags database cleaned up', 'sp-tags');
	return $mess;
}

function sp_change_topic_tags($topicid, $newtags) {
	if (!$topicid) return '';
	$thistopic = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'row');

	if (!SP()->auths->get('edit_tags', $thistopic->forum_id)) die();

	# remove any existing tags for the topic
	$oldtags = SP()->DB->table(SPTAGSMETA, "topic_id=$topicid");
	foreach ($oldtags as $oldtag) {
		# grab all the tag rows and decrement the tag count
		$tagcount = SP()->DB->table(SPTAGS, "tag_id=$oldtag->tag_id", 'tag_count');

		# decrement tag count and delete if it gets to zero or update the new count
		$tagcount--;
		if ($tagcount == 0) {
			SP()->DB->execute('DELETE FROM '.SPTAGS." WHERE tag_id=$oldtag->tag_id"); # count is zero so delete
		} else {
			SP()->DB->execute('UPDATE '.SPTAGS." SET tag_count=$tagcount WHERE tag_id=$oldtag->tag_id"); # update count
		}

		# remove all the tag meta entries for the topic
		SP()->DB->execute('DELETE FROM '.SPTAGSMETA." WHERE topic_id=$topicid");
	}

	# now add in the updated tags
	if (!empty($newtags)) {
		$tags = trim($newtags);
		$tags = trim($tags, ',');  # no extra commas allowed
		$tags = explode(',', $tags);
		$tags = array_unique($tags);  # remove any duplicates
		$tags = array_values($tags);  # put back in order

		$tagsopt = SP()->options->get('tags');
		if ($tagsopt['maxtags'] > 0 && count($tags) > $tagsopt['maxtags']) $tags = array_slice($tags, 0, $tagsopt['maxtags']);
		sp_tags_new_tags($topicid, $tags);
	}

	SP()->notifications->message(0, __('Topic tags updated', 'sp-tags'));
}

function sp_tags_new_tags($topicid, $tags) {
	if ($tags) {
		foreach ($tags as $tag) {
			$tagid = '';

			$tagname = SP()->saveFilters->title(trim($tag));
			$tagslug = sp_create_slug($tag, false);

			#check if tag already exists
			$tagcheck = SP()->DB->table(SPTAGS, "tag_slug='$tagslug'", 'row');
			if ($tagcheck) {
				#is it already tied to this topic?
				$topictag = SP()->DB->table(SPTAGSMETA, "tag_id=$tagcheck->tag_id AND topic_id=$topicid", 'topic_id');
				if (empty($topictag)) {
					# tag exists, but not on this topic so increment the tag count
					$count = $tagcheck->tag_count + 1;
					SP()->DB->execute('UPDATE '.SPTAGS." SET tag_count=$count WHERE tag_id=$tagcheck->tag_id");

					# use current tag id
					$tagid = $tagcheck->tag_id;
				}
			} else {
				# new tag, so create the
				SP()->DB->execute('INSERT INTO '.SPTAGS." (tag_name, tag_slug, tag_count) VALUES ('$tagname', '$tagslug', 1)");

				# get new tag id
				$tagid = SP()->rewrites->pageData['insertid'];
			}

			# now save the tag meta info if it didnt exist for this topic
			if ($tagid) {
				$forumid = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'forum_id');
				SP()->DB->execute('INSERT INTO '.SPTAGSMETA." (tag_id, topic_id, forum_id) VALUES ($tagid, $topicid, $forumid)");
			}
		}
	}
}
