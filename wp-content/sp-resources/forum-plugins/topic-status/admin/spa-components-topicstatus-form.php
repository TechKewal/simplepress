<?php
/*
Simple:Press
Admin Components Topic Status Form
$LastChangedDate: 2019-01-11 18:38:47 -0600 (Fri, 11 Jan 2019) $
$Rev: 15869 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_components_topicstatus_options() {
	$sfcomps = spa_get_topicstatus_data();

	spa_paint_options_init();

#== TOPIC STATUS Tab ============================================================

	spa_paint_open_tab(__('Components', 'sp-tstatus').' - '.__('Topic Status', 'sp-tstatus'), true);

	$count = (is_array($sfcomps['topic-status'])) ? count($sfcomps['topic-status']) + 1 : 1;
	for ($x=0; $x<$count; $x++) {
		$flag = 0;
		
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Topic Status Set', 'sp-tstatus'), !$x, 'topic-status-sets');

				if (isset($sfcomps['topic-status'][$x])) {
					spa_paint_hidden_input("sftopstatid[$x]", $sfcomps['topic-status'][$x]['meta_id']);

					echo "<tr><td class='sflabel' style='vertical-align:top !important;width:30%'>".__('Topic status set name', 'sp-tstatus').":";
					echo "<input class='sfpostcontrol' type'text' name='sftopstatname[$x]' value='".SP()->displayFilters->title($sfcomps['topic-status'][$x]['meta_key'])."' />";

					echo "<br /><br /><input type='submit' tabindex='51' name='sftopstatdel[$x]' id='sftopstatdel-$x' value='".__('Delete this topic status set', 'sp-tstatus')."' />";

					echo '<p><br />'.__('Enter the status data opposite. Sequence controls the order in which they will be shown. Give each status a unique key. Once created these keys should NEVER be changed.', 'sp-tstatus').'<br /><br /></p>';
					echo "</td>";

					echo "<td class='sflabel' colspan='1'>".__('Topic Status Values', 'sp-tstatus').":";

					echo "<table style='width:100%'>";
						echo "<tr>
						<td class='sflabel' style='width:10%'>".__('Sequence', 'sp-tstatus')."</td>
						<td class='sflabel' style='width:20%'>".__('Key', 'sp-tstatus')."</td>
						<td class='sflabel' style='width:20%'>".__('Status Label', 'sp-tstatus')."</td>
						<td class='sflabel' style='width:10%'>".__('Colors','sp-tstatus')."</td> 
						<td class='sflabel' style='width:10%'>".__('Lock','sp-tstatus')."</td>
						<td class='sflabel' style='width:10%'>".__('Default','sp-tstatus')."</td>
						<td class='sflabel' style='width:20%'>".__('User Group','sp-tstatus')."</td>
						</tr>";
						$set = $sfcomps['topic-status'][$x]['meta_value'];
						
						
						for($i=1; $i<count($set)+2; $i++) {
							echo "<tr><td style='width:10%'><input class='sfpostcontrol' type='text' name='seq[$x][$i]' value='$i' /></td>";
                            $key = (!empty($set[$i]['key'])) ? $set[$i]['key'] : '';
							echo "<td style='width:20%'><input class='sfpostcontrol' type='text' name='key[$x][$i]' value='".$key."' /></td>";
                            $status = (!empty($set[$i]['status'])) ? $set[$i]['status'] : '';
							echo "<td style='width:20%'><input class='sfpostcontrol' type='text' name='status[$x][$i]' value='".$status."' /></td>";

							/* Create Color Box */
							$status_color = (!empty($set[$i]['status_color'])) ? $set[$i]['status_color'] : '';
								echo "<td style='width:10%'><input type='color' name='status_color[$x][$i]'  class='my-color-field' value='".$status_color."'/></td>";

							/* Create Check Box To Define Which Wants To Be Lock */
							$is_locked = (!empty($set[$i]['is_locked'])) ? "checked=checked" : '';							
								echo "<td style='width:10%'><input type='checkbox' name='is_locked[$x][$i]' style='position:relative;left:0px' $is_locked/> </td>";
							
							/* Create Radio Button For Default Status */							
							$is_default = (!empty($set[$i]['is_default'])) ? "checked=checked" : '';
							if($i == 1){				
								for($j=1; $j<count($set)+2; $j++) { 
									if(!empty($set[$j]['is_default']) && $set[$j]['is_default'] == 'on'){
										$flag=1;
									}
								}
								if($flag==1){
									$is_default = $is_default;
								}else{
									$is_default = "checked=checked";
								}
							}
							echo "<td style='width:10%'><input type='radio' class='is_default_status default_$x' name='is_default[$x][$i]' style='position:relative;left:0px' $is_default/></td>";

							/* Create Multi Select Box For Usergroup  class='is_default_status' */
								echo "<td style='width:20%'><select name='usr_grp[$x][$i][]' multiple='multiple'>";
									global $wpdb;
									//$result = $wpdb->get_results ( "SELECT * FROM wp_sfusergroups" );
									$result = SP()->DB->table(SPUSERGROUPS);
									foreach ($result as $row) {
										$usr_grp =  "";
										$isSel = "";
										if(!empty($set[$i]['usr_grp'])){
											$user_rol = $set[$i]['usr_grp'];
											$usr_grp = explode(",", $user_rol);
											
											if($row->usergroup_id != ''){
												foreach($usr_grp as $is){
													if($is == $row->usergroup_id){
														$isSel = 'selected';
														break;
										            }
										        }
										    }
										}
										echo "<option value='".$row->usergroup_id."' $isSel >".$row->usergroup_name."</option>";
									}
									"</select></td></tr>";
						}
					echo "</table></td>";

				} else {
					spa_paint_hidden_input("sftopstatid[$x]", '');
					echo '<p><b>'. __('To create a new Topic Status Set enter the main details below and update the panel', 'sp-tstatus') .'</b></p>';
					spa_paint_input(__('Topic status set name', 'sp-tstatus'), "sftopstatname[$x]", '', false, false);
					spa_paint_input(__('Enter status phrases separated by commas. Enter them in the order they are to appear in the selection list', 'sp-tstatus'), "sftopstatwords[$x]", '', false, true);
				}

			spa_paint_close_fieldset();
		spa_paint_close_panel();
	}

	spa_paint_close_container();
}

function spa_get_topicstatus_data() {
	$sfcomps = array();

	$tsets = SP()->meta->get('topic-status-set', false);
	$sfcomps['topic-status'] = ($tsets) ? $tsets : 0;

	return $sfcomps;
}