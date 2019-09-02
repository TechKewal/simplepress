<?php
/*
Simple:Press
Tags plugin ajax
$LastChangedDate: 2015-11-26 12:31:04 -0800 (Thu, 26 Nov 2015) $
$Rev: 13615 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('tags-ajax')) die();

$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'related') {
	if (isset($_GET['topicid'])) $topic_id = SP()->filters->integer($_GET['topicid']);
	if (empty($topic_id)) die();

	echo '<div id="spMainContainer">';

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
			$query->type 	= 'col';
			$query->resultType	= ARRAY_A;
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

	echo '</div>';

	die();
}

if ($action == 'edit-tags') {
    $topicid = SP()->filters->integer($_GET['topicid']);
    if (empty($topicid)) die();

	$thistopic = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'row');
	if (!SP()->auths->get('edit_tags', $thistopic->forum_id)) die();

	$thisforum = SP()->DB->table(SPFORUMS, "forum_id=".$thistopic->forum_id, 'row');
	$tags = SP()->DB->select('SELECT tag_name, '.SPTAGS.'.tag_id
						FROM '.SPTAGS.'
						JOIN '.SPTAGSMETA.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.".tag_id
						WHERE topic_id=$topicid");

    $postid = SP()->filters->integer($_GET['postid']);
    $page = SP()->filters->integer($_GET['page']);

	$curtags = '';
	if ($tags) {
		foreach ($tags as $tag) {
			if (empty($curtags)) {
				$curtags = $tag->tag_name;
			} else {
				$curtags.= ', '.$tag->tag_name;
			}
		}
	}

    # if we dont have a post id, then just go to forum view
    $topic_slug = (!empty($postid)) ? $thistopic->topic_slug : '';

	echo '<div id="spMainContainer" class="spForumToolsPopup">';
	echo '<form action="'.SP()->spPermalinks->build_url($thisforum->forum_slug, $topic_slug, $page, $postid).'" method="post" name="edittags" id="edittags">';
	echo '<input type="hidden" name="topicid" value="'.$topicid.'" />';
	echo '<div class="spHeaderName">'.__('Tags', 'sp-tags').':</div>';
	echo '<div><textarea class="spControl" name="topictags" rows="2">'.esc_textarea($curtags).'</textarea></div>';
	echo '<div class="spCenter"><br />';
	echo '<input type="submit" class="spSubmit" name="maketagsedit" value="'.esc_attr(__('Update Tags', 'sp-tags')).'" />';
	echo '<input type="button" class="spSubmit spCancelScript" name="cancel" value="'.esc_attr(__('Cancel', 'sp-tgs')).'" />';
	echo '</div>';
	echo '</form>';
	echo '</div>';

	die();
}

if ($action == 'tags_from_yahoo') {
	# Send good header HTTP
	status_header( 200 );
	header('Content-Type: text/javascript; charset='.get_bloginfo('charset'));

	# Get topic name and data
	$content = SP()->filters->str($_POST['content']) .' '. SP()->filters->str($_POST['title']);
	$content = trim($content);
	if (empty($content)) {
		echo '<p>'.__('You need to create a topic title and post content before tags can be suggested', 'sp-tags').'</p>';
		exit();
	}

	# Application entrypoint -> http://code.google.com/p/simple-tags/
	# Yahoo ID : h4c6gyLV34Fs7nHCrHUew7XDAU8YeQ_PpZVrzgAGih2mU12F0cI.ezr6e7FMvskR7Vu.AA--
	$yahoo_id = 'h4c6gyLV34Fs7nHCrHUew7XDAU8YeQ_PpZVrzgAGih2mU12F0cI.ezr6e7FMvskR7Vu.AA--';

	# Build params
	$param = 'appid='.$yahoo_id; # Yahoo ID
	$param .= '&context='.urlencode($content); # Post content
	if (!empty($_POST['tags'])) {
		$param .= '&query='.urlencode($_POST['tags']);  # Existing tags
	}
	$param .= '&output=php'; # Get PHP Array !

	$data = array();
	$reponse = wp_remote_post('http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction?'.$param);
	if (!is_wp_error($reponse) && $reponse != null) {
		$code = wp_remote_retrieve_response_code($reponse);
		if ($code == 200) $data = maybe_unserialize(wp_remote_retrieve_body($reponse));
	}

	if (empty($data) || empty($data['ResultSet']) || is_wp_error($data)) {
		echo '<p>'.__('No suggested tags from Yahoo service.', 'sp-tags').'</p>';
		exit();
	}

	# Get result value
	$data = (array) $data['ResultSet']['Result'];

	# Remove empty terms
	$data = array_filter($data, 'sp_delete_empty_element');
	$data = array_unique($data);
	foreach ((array) $data as $tag) {
		echo '<a class="spButton">'.$tag.'</a>';
	}
	echo '<div class="clear"></div>';
	exit();
}

if ($action == 'tags_from_tagthenet') {
	# Send good header HTTP
	status_header( 200 );
	header('Content-Type: text/javascript; charset='.get_bloginfo('charset'));

	# Get topic name and data
	$content = SP()->filters->str($_POST['content']) .' '. SP()->filters->str($_POST['title']);
	$content = trim($content);
	if (empty($content)) {
		echo '<p>'.__('You need to create a topic title and post content before tags can be suggested', 'sp-tags').'</p>';
		exit();
	}

	# Get Tag This Net tags
	$data = '';
	$reponse = wp_remote_post('http://tagthe.net/api/?text='.urlencode($content).'&view=json&count=200');
	if (!is_wp_error($reponse) ) {
		$code = wp_remote_retrieve_response_code($reponse);
		if ($code == 200) $data = maybe_unserialize(wp_remote_retrieve_body($reponse));
	}

    if ($data) {
    	$data = json_decode($data);
    	$data = $data->memes[0];
    	$data = $data->dimensions;
    }

	if (!isset($data->topic) && !isset($data->location) && !isset($data->person)) {
		echo '<p>'.__('No suggested tags from Tag the Net service', 'sp-tags').'</p>';
		exit();
	}

	$tags = array();
	# Get all topics
	foreach ((array) $data->topic as $topic) {
		$tags[] = '<a class="spButton">'.$topic.'</a>';
	}

	# Get all locations
	foreach ( (array) $data->location as $location )  {
		$tags[] = '<a class="spButton">'.$location.'</a>';
	}

	# Get all persons
	foreach ((array) $data->person as $person) {
		$tags[] = '<a class="spButton">'.$person.'</a>';
	}

	# Remove empty terms
	$tags = array_filter($tags, 'sp_delete_empty_element');
	$tags = array_unique($tags);
	echo implode("\n", $tags);
	echo '<div class="clear"></div>';
	exit();
}

if ($action == 'tags_from_local_db') {
	# Send good header HTTP
	status_header(200);
	header('Content-Type: text/javascript; charset='.get_bloginfo('charset'));

	# Get existing tags
	$tags  = SP()->DB->select("SELECT DISTINCT tag_name FROM ".SPTAGS, 'col');
	if (empty($tags)) {  # No tags to suggest
		echo '<p>'.__('There are no tags for Simple:Press', 'sp-tags').'</p>';
		exit();
	}

	# Get topic name and data
	$content = SP()->filters->str($_POST['content'] .' '. $_POST['title']);
	$content = trim($content);

	if (empty($content)) {
		echo '<p>'.__('You need to create a topic title and post content before tags can be suggested', 'sp-tags').'</p>';
		exit();
	}

	$found = 0;
	foreach ((array) $tags as $tag) {
		$tag = $tag;
		if (is_string($tag) && !empty($tag) && stristr($content, $tag)) {
			echo '<a class="spButton">'.$tag.'</a>';
			$found = 1;
		}
	}

	if (!$found) {
		echo '<p>'.__('No suggested tags from existing Simple:Press tags', 'sp-tags').'</p>';
		exit();
	}

	echo '<div class="clear"></div>';
	exit();
}

die();

function sp_delete_empty_element(&$element) {
	$element = $element;
	$element = trim($element);
	if (!empty($element)) return $element;
}
