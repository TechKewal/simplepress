<?php
/*
Simple:Press
Topic Status plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_topicstatus_do_install() {
	# Check if already exists - i.e, this is an upgrade - look for table field
	$exists = SP()->DB->columnExists(SPFORUMS, 'topic_status_set');
	if (!$exists) {
		# Yes - Convert current sfmeta data to new format if any exists (sfmeta)
		$records = SP()->meta->get('topic-status');
		if ($records) {
			foreach ($records as $r) {
				$i = 1;
				$v = array();
				$values = explode(',', $r['meta_value']);
				if ($values) {
					foreach ($values as $value) {
						$v[$i]['key'] = $i;
						$v[$i]['status'] = trim($value);
						$i++;
					}
					SP()->meta->add('topic-status-set', $r['meta_key'], $v);
				}
				SP()->meta->delete($r['meta_id']);
			}
		}
	}

	# Now change the topic field to string if necessary
	if($exists)	{
		SP()->DB->execute("ALTER TABLE ".SPTOPICS." MODIFY topic_status_flag varchar(25) default NULL");
	} else {
		# No - add the two required table columns
		SP()->DB->execute("ALTER TABLE ".SPFORUMS. " ADD topic_status_set bigint(20) NOT NULL default 0");
		SP()->DB->execute("ALTER TABLE ".SPTOPICS. " ADD topic_status_flag varchar(25) default NULL");
	}

	$options = SP()->options->get('topicstatus');
	if (empty($options)) {
        $options['dbversion'] = SPTSDBVERSION;
        SP()->options->update('topicstatus', $options);
    }

	# add indexing to topic status columns
	SP()->DB->execute("ALTER TABLE ".SPFORUMS." ADD KEY topic_status_set_idx (topic_status_set)");
	SP()->DB->execute("ALTER TABLE ".SPTOPICS." ADD KEY topic_status_flag_idx (topic_status_flag)");

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

    # add new permissions into the auths table
	SP()->auths->add('change_topic_status', __('Can change the status of a topic', 'sp-tstatus'), 1, 1, 1, 0, 7);
    SP()->auths->activate('change_topic_status');
}

function sp_topicstatus_do_reset_permissions() {
	SP()->auths->add('change_topic_status', __('Can change the status of a topic', 'sp-tstatus'), 1, 1, 1, 0, 7);
}
