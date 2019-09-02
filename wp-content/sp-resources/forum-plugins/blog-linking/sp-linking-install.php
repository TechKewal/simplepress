<?php
/*
Simple:Press
Blog Linking plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_linking_do_install() {
	# Check if already exists - i.e, this is an upgrade
	$sfpostlinking = SP()->options->get('sfpostlinking');
	if(empty($sfpostlinking)) {
		# New Install
		# Create blog linking table
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFLINKS." (
				id bigint(20) NOT NULL auto_increment,
				post_id bigint(20) default '0',
				forum_id bigint(20) default '0',
				topic_id bigint(20) default '0',
				syncedit smallint(1) default '0',
				PRIMARY KEY (id),
				KEY post_id_idx (post_id),
				KEY forum_id_idx (forum_id),
				KEY topic_id_idx (topic_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		# Add column and index to the topics table
		SP()->DB->execute("ALTER TABLE ".SPTOPICS. " ADD blog_post_id bigint(20) NOT NULL default 0");

		# Create the options array
		$sfpostlinking = array();

		$sfpostlinking['sflinkexcerpt']		= 1;
		$sfpostlinking['sflinkwords']		= 100;
		$sfpostlinking['sflinkblogtext']	= '%ICON% Join the forum discussion on this post';
		$sfpostlinking['sflinkabove']		= false;
		$sfpostlinking['sflinkcomments']	= 1;
		$sfpostlinking['sfhideduplicate']	= true;
		$sfpostlinking['sfpostcomment']		= false;
		$sfpostlinking['sfkillcomment']		= false;
		$sfpostlinking['sfeditcomment']		= false;
		$sfpostlinking['sflinksingle']		= false;
		$sfpostlinking['sfuseautolabel']	= true;
		$sfpostlinking['sfautoupdate']		= true;
		$sfpostlinking['sfautocreate']		= false;
		$sfpostlinking['sfautoforum']		= '';
		$sfpostlinking['sflinkurls']		= 1; # each get their own canonical url
		$sfpostlinking['posttypes']['post'] = true;
		$sfpostlinking['posttypes']['page'] = true;
        $sfpostlinking['dbversion'] = SPLINKINGDBVERSION;
		SP()->options->add('sfpostlinking', $sfpostlinking);
	} else {
		# Upgrade
		# Changes to V5.0.0 Only
		SP()->DB->execute("ALTER TABLE ".SFLINKS." MODIFY id bigint(20) auto_increment;");
		SP()->DB->execute("ALTER TABLE ".SFLINKS." MODIFY forum_id bigint(20) default 0, MODIFY topic_id bigint(20) default 0, MODIFY post_id bigint(20) default 0;");
		SP()->DB->execute("ALTER TABLE ".SFLINKS." ADD KEY post_idx (post_id), ADD KEY forum_idx (forum_id), ADD KEY topic_idx (topic_id);");

		SP()->DB->execute("ALTER TABLE ".SPTOPICS." ADD KEY blog_post_idx (blog_post_id);");

		$sfpostlinking = array();
		$sfpostlinking = SP()->options->get('sfpostlinking');
		$sflinkposttype = array();
		$sflinkposttype = SP()->options->get('sflinkposttype');
		if ($sflinkposttype) {
			foreach($sflinkposttype as $key=>$value) {
				$sfpostlinking['posttypes'][$key] = $sfpostlinking[$value];
			}
		} else {
    		$sfpostlinking['posttypes']['post'] = true;
    		$sfpostlinking['posttypes']['page'] = true;
		}
        $sfpostlinking['dbversion'] = SPLINKINGDBVERSION;
		SP()->options->update('sfpostlinking', $sfpostlinking);
		SP()->options->delete('sflinkposttype');
	}

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables && !in_array(SFLINKS, $tables)) {
        $tables[] = SFLINKS;
        SP()->options->update('installed_tables', $tables);
    }

	# New items required by Version 5.0.0
    # add new permissions into the auths table
	SP()->auths->add('create_linked_topics', __('Can create linked blog post and forum topics', 'sp-linking'), 1, 1, 1, 0, 3);
    SP()->auths->activate('create_linked_topics');

	SP()->auths->add('break_linked_topics', __('Can break link between blog posts and forum topics', 'sp-linking'), 1, 1, 1, 0, 7);
    SP()->auths->activate('break_linked_topics');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

function sp_linking_do_reset_permissions() {
	SP()->auths->add('create_linked_topics', __('Can create linked blog post and forum topics', 'sp-linking'), 1, 1, 1, 0, 3);
	SP()->auths->add('break_linked_topics', __('Can break link between blog posts and forum topics', 'sp-linking'), 1, 1, 1, 0, 7);
}
