<?php
/*
Search Widget
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


class SP_Widget_SPSearch extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'search', 'description' => __('Search blog posts and forums', 'sp-search'));
		parent::__construct('sp-search', __('Blog & Forum Search', 'sp-search'), $widget_ops);
		$this->alt_option_name = 'widget_blog_forum_search';
	}

	function widget($args, $instance) {
		extract($args);
		$title = (!empty( $instance['title'] ) ) ? $instance['title'] : __('Search blog posts and forums', 'sp-search');
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		# generate output
		echo $before_widget.$before_title.$title.$after_title;

		$form = '<form role="search" method="get" name="sfblogsearch" class="searchform" action="'.esc_url(SP()->spPermalinks->get_url('/')).'">
			<input type="text" class="field" placeholder="'.__('Search...', 'sp-search').'" value="" name="value" title="" />
			<input type="hidden" name="search" value="1" />
			<input type="hidden" name="new" value="1" />
			<input type="hidden" name="forum" value="all" />
			<input type="hidden" name="include" value="3" />
			<input type="hidden" name="blog" value="1" />
			<input type="hidden" name="bswidget" value="'.SP()->isForum.'" />
			<input type="submit" class="submit" value="'.__( 'Search', 'sp-search').'" /><br />
			<input type="radio" id="bsearchr1" name="type" value="1" checked="checked" />&nbsp;<label for="bsearchr1">'.__('any word', 'sp-search').'</label>&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" id="bsearchr2" name="type" value="2" />&nbsp;<label for="bsearchr2">'.__('all words', 'sp-search').'</label>&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" id="bsearchr3" name="type" value="3" />&nbsp;<label for="bsearchr3">'.__('phrase', 'sp-search').'</label>';

		# are the search results we can return to?
		if (!isset($_GET['search'])) {
			$r = SP()->cache->get('search');
			if ($r) {
				$p = $r[0]['page'];
				$url = $r[0]['url']."&amp;search=$p";
				$form.= "<p><a class='' rel='nofollow' href='$url'>".__('Display Last Search Results', 'sp-search')."</a></p>";
			}
		}

		$form.= '</form>';

		echo $form;
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form($instance) {
		$title = isset($instance['title']) ? esc_attr( $instance['title'] ) : '';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}
}

add_action('widgets_init', 'sp_search_init_widget', 5);
function sp_search_init_widget() {
	new SP_Widget_SPSearch();
	register_widget('SP_Widget_SPSearch');
}
