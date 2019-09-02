<?php
/*
Simple:Press
Threading Plugin database Routine
$LastChangedDate: 2015-03-14 17:51:36 +0000 (Sat, 14 Mar 2015) $
$Rev: 12582 $
*/

# ----------------------------------------------------------------------------------------
# Prepare and populate the threading data for new post
function sp_threading_do_prepare_post_data($postData) {
	# save postindex as we need it below
	$postIndex = $postData['postindex'];

	# if ordinary new post - add to end of topic...
	if(empty($_POST['spEditorCustomValue'])) {
		$sql = "SELECT thread_index FROM ".SPPOSTS."
				WHERE topic_id = ".$postData['topicid']."
				ORDER BY thread_index DESC LIMIT 1";
		$last = SP()->DB->select($sql, 'var');
		$depth = explode('.', $last);
		$postData['thread_index'] = chr(254).str_pad((intval($depth[0])+1), 4, '0', STR_PAD_LEFT);
		$postData['control_index'] = $postIndex;
	} else {
		# So - it is a proper, new post in a threaded context.

		# get thread parent
		# new post will be one level deeper than spEditorCustomValue
		# check database to get thread-parent + dot and highest number
		# if none then this is first in new level.
		# if not then we need to determine value and add one for this level.

		$parent = SP()->filters->str($_POST['spEditorCustomValue']);
		$depth = explode('.', $parent);
		$levels = (count($depth)-1);

		$sql = "SELECT thread_index, post_id FROM ".SPPOSTS."
				WHERE topic_id = ".$postData['topicid']."
				AND thread_index LIKE '".$parent."%'";
		$posts = SP()->DB->select($sql);
		$target = 1;
		foreach($posts as $post) {
			$parts = explode('.', $post->thread_index);
			if(isset($parts[($levels+1)])) {
				$thisPart = intval($parts[($levels+1)]);
				If($thisPart >= $target) $target = ($thisPart+1);
			}
			# update parent flag of parent post
			if($parent == $post->thread_index) sp_threading_mark_parent($post->post_id, 1);
		}

		$thisThread = $parent.'.'.str_pad($target, 4, '0', STR_PAD_LEFT);
		$postData['thread_index'] = chr(254).$thisThread;
		$postData['control_index'] = $postIndex;
	}
	return $postData;
}

# ----------------------------------------------------------------------------------------
function sp_threading_mark_parent($postid, $flag) {
	$sql = "UPDATE ".SPPOSTS." SET thread_parent = ".$flag." WHERE post_id=".$postid;
	SP()->DB->execute($sql);
}

# ----------------------------------------------------------------------------------------
# process the delete thread action
function sp_threading_do_process_delete() {
	if (! isset($_POST['delthread'])) return;

	# if post has no children then delete and finish
	if (! $_POST['children']) {
		sp_delete_post($_POST['thepost'], $_POST['thetopic'], $_POST['theforum']);
		return;
	}

	# if parent is -1 (top of whole thread) then remove it all
	if ($_POST['parent'] == -1) {
		$sql = "SELECT post_id FROM ".SPPOSTS."
				WHERE topic_id = ".$_POST['thetopic']."
				AND thread_index LIKE '".$_POST['delthread']."%'
				ORDER BY post_id";
		$posts = SP()->DB->select($sql, 'col');

		if($posts) {
			foreach($posts as $post) {
				sp_delete_post($post, $_POST['thetopic'], $_POST['theforum']);
			}
		}
		return;
	}

	# so now we are deleting an individual post in the middle of a thread...
	sp_delete_post($_POST['thepost'], $_POST['thetopic'], $_POST['theforum']);

	# select all records in the thread that match this sub-thread (so all children)
	$sql = "SELECT post_id, thread_index, thread_parent FROM ".SPPOSTS."
			WHERE topic_id = ".$_POST['thetopic']."
			AND thread_index LIKE '".$_POST['delthread']."%'
			ORDER BY thread_index";
	$posts = SP()->DB->select($sql);

	# now to determine new thread indexing for the records
	if($posts) {
		$index = array();
		for ($post=0; $post < count($posts); $post++) {
			# the first post inherits the deleted post's thread index
			if ($post == 0) {
				$index[$posts[$post]->post_id] = SP()->filters->integer($_POST['delthread']);
			} elseif ($posts[$post]->thread_parent || $posts[($post-1)]->thread_parent) {
				# if post is a parent itself or
				# any first child - then replace with it's own parents index
				$index[$posts[$post]->post_id] = $posts[($post-1)]->thread_index;
			} else {
				# any other children - remove last but one index part
				$parts = explode('.', $posts[$post]->thread_index);
				$newThread = array();
				for ($x=0; $x < count($parts); $x++) {
					if ($x != (count($parts)-2)) {
						$newThread[] = $parts[$x];
					}
				}
				$index[$posts[$post]->post_id] = implode('.', $newThread);
			}
		}

		# update the records with new index
		foreach($posts as $post) {
			$sql = "UPDATE ".SPPOSTS." SET
					thread_index = '".$index[$post->post_id]."'
					WHERE post_id = ".$post->post_id;

			SP()->DB->execute($sql);
		}
	}
}

# ----------------------------------------------------------------------------------------
# process the rebuild post index for control_index
function sp_threading_do_rebuild_indexes($topicId) {
	# get topic posts is their post ID order
	$query = new stdClass();
		$query->table	= SPPOSTS;
		$query->fields	= 'post_id, control_index';
		$query->where	= 'topic_id='.$topicId;
		$query->orderby	= 'post_id ASC';
	$posts = SP()->DB->select($query);

	if ($posts) {
		$index = 1;
		foreach ($posts as $post) {
			# update the post_index for each post to set display order
			$query = new stdClass();
				$query->table	= SPPOSTS;
				$query->fields	= array('control_index');
				$query->data	= array($index);
				$query->where	= 'post_id='.$post->post_id;
			SP()->DB->update($query);
			$index++;
		}
	}
}
