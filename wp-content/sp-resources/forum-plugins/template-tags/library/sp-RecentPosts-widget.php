<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

class sp_RecentPostsWidget extends WP_Widget {
	function __construct() {
		$widget_ops = array(
            'classname' => 'spRecentPostsWidget',
            'description' => __('A widget to list the latest Simple:Press topics with recent posts', 'sp-ttags'),
            'customize_selective_refresh' => true,
        );
		$control_ops = array(
            'width' => 400,
            'height' => 350,
        );
		parent::__construct(
            'spf',
            __('Recent Forum Posts', 'sp-ttags'),
            $widget_ops,
            $control_ops
        );
	}

	function widget($args, $instance) {
		extract($args);
		$title 			= empty($instance['title']) 		? __("Recent Forum Posts", 'sp-ttags')	: $instance['title'];
		$forumIds 		= empty($instance['forumIds']) 		? 0 									: $instance['forumIds'];
		$limit 			= empty($instance['limit']) 		? 5 									: $instance['limit'];
		$itemOrder 		= empty($instance['itemOrder']) 	? 'FTUD' 								: $instance['itemOrder'];
		$linkScope 		= empty($instance['linkScope'])		? 'forum' 								: $instance['linkScope'];
		$beforeForum	= empty($instance['beforeForum']) 	? __('Forum: ', 'sp-ttags') 			: $instance['beforeForum'];
		$afterForum 	= empty($instance['afterForum']) 	? '<br />' 								: $instance['afterForum'];
		$beforeTopic	= empty($instance['beforeTopic']) 	? __('Topic: ', 'sp-ttags')				: $instance['beforeTopic'];
		$afterTopic		= empty($instance['afterTopic']) 	? '<br />' 								: $instance['afterTopic'];
		$beforeUser 	= empty($instance['beforeUser']) 	? __('By: ', 'sp-ttags') 				: $instance['beforeUser'];
		$afterUser 		= empty($instance['afterUser']) 	? '' 									: $instance['afterUser'];
		$beforeDate 	= empty($instance['beforeDate']) 	? '&nbsp;-' 							: $instance['beforeDate'];
		$afterDate 		= empty($instance['afterDate']) 	? '' 									: $instance['afterDate'];
		$avatarSize 	= empty($instance['avatarSize'])	? 25 									: $instance['avatarSize'];
		$niceDate 		= empty($instance['niceDate']) 		? 1 									: $instance['niceDate'];
		$truncate 		= empty($instance['truncate']) 		? 1 									: $instance['truncate'];
		$postTip 		= empty($instance['postTip']) 		? 0 									: $instance['postTip'];

		# generate output
		echo $before_widget.$before_title.$title.$after_title;
		sp_RecentPostsTag($instance);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title']			= SP()->displayFilters->title($new_instance['title']);
		$instance['forumIds']		= esc_attr($new_instance['forumIds']);
		$instance['limit']			= (int) $new_instance['limit'];
		$instance['itemOrder']		= esc_attr($new_instance['itemOrder']);
		$instance['linkScope']		= esc_attr($new_instance['linkScope']);
		$instance['beforeForum']	= SP()->saveFilters->kses($new_instance['beforeForum']);
		$instance['afterForum']		= SP()->saveFilters->kses($new_instance['afterForum']);
		$instance['beforeTopic']	= SP()->saveFilters->kses($new_instance['beforeTopic']);
		$instance['afterTopic']		= SP()->saveFilters->kses($new_instance['afterTopic']);
		$instance['beforeUser']		= SP()->saveFilters->kses($new_instance['beforeUser']);
		$instance['afterUser']		= SP()->saveFilters->kses($new_instance['afterUser']);
		$instance['beforeDate']		= SP()->saveFilters->kses($new_instance['beforeDate']);
		$instance['afterDate']		= SP()->saveFilters->kses($new_instance['afterDate']);
		$instance['avatarSize']		= (int) $new_instance['avatarSize'];
		if (isset($new_instance['niceDate'])) {
			$instance['niceDate'] = 1;
		} else {
			$instance['niceDate'] = 0;
		}
		if (isset($new_instance['postTip'])) {
			$instance['postTip'] = 1;
		} else {
			$instance['postTip'] = 0;
		}
		$instance['truncate']		= esc_attr($new_instance['truncate']);
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args((array) $instance, array(
			'title' 		=> __('Recent Forum Posts', 'sp-ttags'),
			'forumIds'		=> 0,
			'limit'			=> 5,
			'itemOrder'		=> 'FTUD',
			'linkScope'		=> 'forum',
			'beforeForum'	=> __('Forum: ', 'sp-ttags'),
			'afterForum'	=> '<br />',
			'beforeTopic'	=> __('Topic: ', 'sp-ttags'),
			'afterTopic'	=> '<br />',
			'beforeUser'	=> __('By: ', 'sp-ttags'),
			'afterUser'		=> '',
			'beforeDate'	=> '&nbsp;-',
			'afterDate'		=> '',
			'avatarSize'	=> 25,
			'niceDate'		=> 1,
			'truncate'		=> 0,
			'postTip'		=> 1
		));
		extract($instance, EXTR_SKIP);
?>
		<table style='width:100%'>

		<!--title-->
		<tr><td><?php _e('Title', 'sp-ttags')?>:</td>
		<td><input style="width: 330px;" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title) ?>"/></td></tr>

		<!--forum id list (comma separated)-->
		<tr><td><?php _e('Forum IDs', 'sp-ttags')?>:</td>
		<td><input style="width: 330px;" type="text" id="<?php echo $this->get_field_id('forumIds'); ?>" name="<?php echo $this->get_field_name('forumIds'); ?>" value="<?php echo $forumIds ?>"/></td></tr>

		</table>

		<!-- limit, itemOrder, linkScope, Avatar Size -->
		<table style='width:100%'>
		<tr><td><?php _e('Limit To', 'sp-ttags')?>:</td><td><?php _e('Display Order', 'sp-ttags')?>:</td><td><?php _e('Scope of Link', 'sp-ttags')?>:</td><td><?php _e('Avatar Size', 'sp-ttags')?>:</td></tr>
		<tr>
		<td><input style="width: 50px;" type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo $limit ?>"/></td>
		<td><input style="width: 120px;" type="text" id="<?php echo $this->get_field_id('itemOrder'); ?>" name="<?php echo $this->get_field_name('itemOrder'); ?>" value="<?php echo $itemOrder ?>"/></td>
		<td><input style="width: 120px;" type="text" id="<?php echo $this->get_field_id('linkScope'); ?>" name="<?php echo $this->get_field_name('linkScope'); ?>" value="<?php echo $linkScope ?>"/></td>
		<td><input style="width: 50px;" type="text" id="<?php echo $this->get_field_id('avatarSize'); ?>" name="<?php echo $this->get_field_name('avatarSize'); ?>" value="<?php echo $avatarSize ?>"/>&nbsp;pixels</td>
		</tr>
		</table>

		<!-- before/after forum/topic/user/date -->
		<table style='width:100%'>
		<tr><td><?php _e('Before Forum Text', 'sp-ttags')?>:</td><td><?php _e('After Forum Text', 'sp-ttags')?>:</td></tr>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('beforeForum'); ?>" name="<?php echo $this->get_field_name('beforeForum'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($beforeForum)); ?>"/></td>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('afterForum'); ?>" name="<?php echo $this->get_field_name('afterForum'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($afterForum)); ?>"/></td>
		</tr>

		<tr><td><?php _e('Before Topic Text', 'sp-ttags')?>:</td><td><?php _e('After Topic Text', 'sp-ttags')?>:</td></tr>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('beforeTopic'); ?>" name="<?php echo $this->get_field_name('beforeTopic'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($beforeTopic)); ?>"/></td>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('afterTopic'); ?>" name="<?php echo $this->get_field_name('afterTopic'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($afterTopic)); ?>"/></td>
		<tr>

		<tr><td><?php _e('Before User Text', 'sp-ttags')?>:</td><td><?php _e('After User Text', 'sp-ttags')?>:</td></tr>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('beforeUser'); ?>" name="<?php echo $this->get_field_name('beforeUser'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($beforeUser)); ?>"/></td>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('afterUser'); ?>" name="<?php echo $this->get_field_name('afterUser'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($afterUser)); ?>"/></td>
		<tr>

		<tr><td><?php _e('Before Date Text', 'sp-ttags')?>:</td><td><?php _e('After Date Text', 'sp-ttags')?>:</td></tr>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('beforeDate'); ?>" name="<?php echo $this->get_field_name('beforeDate'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($beforeDate)); ?>"/></td>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('afterDate'); ?>" name="<?php echo $this->get_field_name('afterDate'); ?>" value="<?php echo esc_attr(SP()->displayFilters->stripslashes($afterDate)); ?>"/></td>
		<tr>

		<tr><td><?php _e('Truncate Titles (# chars)', 'sp-ttags')?>:</td><td></td></tr>
		<td><input style="width: 190px;" type="text" id="<?php echo $this->get_field_id('truncate'); ?>" name="<?php echo $this->get_field_name('truncate'); ?>" value="<?php echo $truncate ?>"/></td>
		<td></td>
		<tr>
		</table>

		<!-- niceDate and postTip -->
		<table style='width:100%'>
		<tr>
		<!--show as nice date-->
		<td><label for="sfforum-<?php echo $this->get_field_id('niceDate'); ?>"><?php _e('Show post date as the elapsed time since the post:', 'sp-ttags')?>
			<input type="checkbox" id="sfforum-<?php echo $this->get_field_id('niceDate'); ?>" name="<?php echo $this->get_field_name('niceDate'); ?>"
			<?php if($instance['niceDate'] == TRUE) {?> checked="checked" <?php } ?> />
		</label></td>

		<!--include post extract-->
		<td><label for="sfforum-<?php echo $this->get_field_id('postTip'); ?>"><?php _e('Show post extract as popup tooltip:', 'sp-ttags')?>
			<input type="checkbox" id="sfforum-<?php echo $this->get_field_id('postTip'); ?>" name="<?php echo $this->get_field_name('postTip'); ?>"
			<?php if($instance['postTip'] == TRUE) {?> checked="checked" <?php } ?> />
		</label></td>
		</tr>
		</table>

		<hr />

		<!-- Help Text -->
		<h3>Help</h3>
		<table style='width:100%'>
		<tr><td colspan='3'>
		<b><?php _e('Passing Forum IDs - limit the display to specified forums', 'sp-ttags')?></b><br />
		<small><?php _e("If specified, Forum ID's must be separated by commas. To use ALL permissable forums enter a value of zero", 'sp-ttags')?></small>
		</td></tr>

		<tr><td colspan='3'>
		<b><?php _e('Display Order and Inclusion', 'sp-ttags')?></b><br />
		<small><?php _e('This parameter controls both which components are displayed and also the order in which they are displayed. Use the following codes to construct this parameter. No spaces or other characters can be used', 'sp-ttags')?></small></td></tr>
		<tr><td style='width:33%'><small><b>F</b> - <?php _e('Displays forum name', 'sp-ttags')?></small></td>
		<td style='width:33%'><small><b>T</b> - <?php _e('Displays Topic name', 'sp-ttags')?></small></td>
		<td><small><b>A</b> - <?php _e('Displays Avatars', 'sp-ttags')?></small></td></tr>
		<tr><td><small><b>U</b> - <?php _e('Displays the Users name', 'sp-ttags')?></small></td>
 		<td><small><b>D</b> - <?php _e('Displays the post date', 'sp-ttags')?></small></td>
 		<td><small>(<?php _e('Defaults to', 'sp-ttags')?> <b>FTUD</b>)</small></td></tr>
 		</tr>

		<tr>
        <td colspan='3'>
		<b><?php _e('Link Scope - Controlling the Links', 'sp-ttags')?></b><br />
		<small><?php _e('This parameter controls what items are made into links. The following options are available. PLEASE NOTE that the Topic will ALWAYS be formed as a link', 'sp-ttags')?></small>
        </td>
        </tr>
		<tr><td><small><b>forum</b> - <?php _e('Make the Forum name a separate link', 'sp-ttags')?></small></td>
 		<td><small><b>all</b> - <?php _e('Make the entire entry a link to the Topic', 'sp-ttags')?></small></td>
 		<td><small>(<?php _e('Defaults to', 'sp-ttags')?> <b>forum</b>)</small></td></tr>
 		</tr>

		<tr>
        <td colspan='3'>
		<b><?php _e('Title Truncation', 'sp-ttags')?></b><br />
		<small><?php _e('This parameter controls how many characters of a forum and topic title will be shown.  0 means the no truncation and entire title is shown.  Can be used to fit in tight spaces', 'sp-ttags')?></small>
        </td>
        </tr>
		</table>

<?php
	}
}

add_action('widgets_init', 'sp_widget_init', 5);
function sp_widget_init() {
	new sp_RecentPostsWidget();
	register_widget('sp_RecentPostsWidget');
}
