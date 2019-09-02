<?php
/*
Simple:Press
Admin Options Save Options Support Functions
$LastChangedDate: 2019-01-11 18:38:47 -0600 (Fri, 11 Jan 2019) $
$Rev: 15869 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_topicstatus_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# If nothing in element 0 then there is nothing to do as there are no sets...
	if (empty($_POST['sftopstatname'][0])) {
		_e('There is nothing to update', 'sp-tstatus');
		return;
	}

	for ($x=0; $x<count($_POST['sftopstatid']); $x++) {
		# let's get any deletions out of the way first
		if (isset($_POST['sftopstatdel'][$x])) {
			SP()->meta->delete(SP()->filters->integer($_POST['sftopstatid'][$x]));
		} else {
			# so something to update perhaps
			$metatype = 'topic-status-set';
			$metakey  = SP()->saveFilters->title(trim($_POST['sftopstatname'][$x]));

			# Is it an existing record or a new one?
			if (!empty($_POST['sftopstatid'][$x])) {
				# existing to update
				$metaid = SP()->filters->integer($_POST['sftopstatid'][$x]);
				$v = array();

				for ($i=1; $i<count($_POST['seq'][$x])+1; $i++) {
					# Does it have a value
					if(!empty($_POST['status'][$x][$i])) {

						$sequence = SP()->filters->integer($_POST['seq'][$x][$i]);
						$getInput = (isset($_POST['usr_grp'][$x][$sequence]))?$_POST['usr_grp'][$x][$sequence]:'';
						
						$selectedOption = "";
						if(!empty($getInput)){
							foreach ($getInput as $option => $value) {
								$selectedOption .= $value.',';
							}
						}

							$key	  = SP()->filters->str($_POST['key'][$x][$sequence]);
							$status   = SP()->saveFilters->title($_POST['status'][$x][$sequence]);
							$status_color   = SP()->saveFilters->title($_POST['status_color'][$x][$sequence]);
							$is_locked = SP()->saveFilters->title(isset($_POST['is_locked'][$x][$sequence])?$_POST['is_locked'][$x][$sequence]:'');
							$is_default = SP()->saveFilters->title(isset($_POST['is_default'][$x][$sequence])?$_POST['is_default'][$x][$sequence]:'');	
							$usr_grp = SP()->saveFilters->title($selectedOption);
							$v[$sequence]['key'] = $key;
							$v[$sequence]['status'] = $status;
							$v[$sequence]['status_color'] = $status_color;
							$v[$sequence]['is_locked'] = $is_locked;
							$v[$sequence]['is_default'] = $is_default;
							$v[$sequence]['usr_grp'] = $usr_grp;
					}
				}
				SP()->meta->update($metatype, $metakey, $v, $metaid);
			} else {
				# we have a new one
				$v = array();
				$i = 1;
				$values = explode(',', $_POST['sftopstatwords'][$x]);
				if ($values) {
					foreach ($values as $value) {
						$v[$i]['key'] = $i;
						$v[$i]['status'] = SP()->filters->str(trim($value));
						$i++;
					}
					SP()->meta->add($metatype, $metakey, $v);
				}
			}
		}
	}
	$mess = __('Topic status component updated', 'sp-tstatus');
	return $mess;
}
