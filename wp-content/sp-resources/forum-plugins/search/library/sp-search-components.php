<?php
/*
Simple:Press
Search Results List View Function Handler
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_search_do_old_results($args) {
    # if searching posts or posts and titles force search results to end without ouputting anything
    if ((SP()->forum->view->thisSearch->searchInclude == 1 || SP()->forum->view->thisSearch->searchInclude == 3) && SP()->forum->view->thisSearch->searchType < 4) {
        $args['get'] = 1;
    }

    return $args;
}

function sp_search_do_results() {
    require_once SPSEARCHTAGS.'sp-search-template-functions.php';

	echo "<div id='spSearchList' class='spSearchSection'>\n";
    $posts = (SP()->forum->view->thisSearch->searchData) ? implode(',', SP()->forum->view->thisSearch->searchData) : 0;
    SP()->forum->view->listPosts = new spcPostList(SPPOSTS.".post_id IN ($posts)", '', 0, 'post-content', 'search');
    $name = SP()->theme->find_template(SPSEARCHTEMP, 'spSearchListView.php');
	sp_load_template($name);
	echo "</div>\n";
}

function sp_search_do_query($query, $searchTerm, $searchType, $searchInclude) {
    if ($searchType == 1 || $searchType == 2 || $searchType == 3) {
        if ($searchInclude == 1 || $searchInclude == 3) {
            $query->fields = SPPOSTS.'.post_id';
            $query->orderby = SPPOSTS.'.post_id DESC';
        }
        if ($searchInclude == 3) {
            $query->join = '';
            $query->left_join = array(SPTOPICS.' ON '.SPPOSTS.'.topic_id = '.SPTOPICS.'.topic_id');
        }
    }

    return $query;
}

function sp_search_do_load_css() {
    $css = SP()->theme->find_css(SPSEARCHCSS, 'sp-search.css', 'sp-search.spcss');
    SP()->plugin->enqueue_style('sp-search', $css);
}

# Add the blog search checkbox to the search form
function sp_search_do_search_form($out) {
	$opts = SP()->options->get('search');
	$out.= '<hr /><input type="checkbox" id="spCheckBlog" name="blogsearchoption" value="0"'.(!empty(SP()->rewrites->pageData['newblogsearch']) ? ' checked="checked"' : '').' /><label class="spLabel spCheckBox" for="spCheckBlog">'.$opts['form'].'</label><br>';
	return $out;
}

# Add the blog search param to the serch url
function sp_search_do_prepare_url($params) {
	if (isset($_REQUEST['blogsearchoption'])) $params['blog'] = 1;
	return $params;
}

# Open the tabs on the forum search template
function sp_search_do_open_tabs() {
	$tab = (isset($_GET['tab'])) ? 1 : 0;
	$options = SP()->options->get('search');
?>
	<style>.spListSection {display:none;}</style>
	<script>
		(function(spj, $, undefined) {
			$(function() {
				$("ul#spSearchTabs").tabs("div.search-panes > div",{initialIndex: <?php echo($tab); ?>});
			});
			$(document).ready(function() {
				$('.spListSection').show();
			});
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php
	require_once SPSEARCHCLASS.'sp-search-blog-view-class.php';
	require_once SPSEARCHCLASS.'sp-search-blog-list-class.php';
	require_once SPSEARCHTAGS.'sp-search-template-functions.php';
?>
	<ul id="spSearchTabs">
		<li><a id="spFSButton" href="#" class="spButton"><?php echo($options['ftab']); ?></a></li>
		<li><a id="spBSButton" href="#" class="spButton"><?php echo($options['btab']); ?></a></li>
	</ul>
	<div class="search-panes">
		<div>
<?php
	return;
}

# Close the tabs on the forum search template
function sp_search_do_close_tabs() {
?>
		</div>
		<div>
<?php
    $template = SP()->theme->find_template(SPSEARCHTEMP, 'spSearchBlogView.php');
    require_once $template;
?>
		</div>
	</div>
<?php
	return;
}

# Add blog search param to main search url
function sp_search_do_add_search_param($params) {
	$params['blog'] = SP()->filters->integer($_GET['blog']);
	return $params;
}

# Add new blog search param to pageData
function sp_search_do_add_page_data($data) {
	if (isset($_GET['new']) && isset($_REQUEST['blog'])) {
		$data['newblogsearch'] = true;
	} else {
		$data['newblogsearch'] = false;
	}
	return $data;
}

# Add unique class to each row
function sp_search_do_fix_rowclass($rowClass) {
	global $spSearchBlogPostList, $spSearchThisBlogPost;
	if (isset($spSearchBlogPostList)) $rowClass.= ($spSearchBlogPostList->currentPost % 2) ? ' spOdd' : ' spEven';
	return $rowClass;
}

# Add unique id to each row
function sp_search_do_fix_rowid($rowId) {
	global $spSearchBlogPostList, $spSearchThisBlogPost;
	if (isset($spSearchThisBlogPost)) $rowId.= "listblogpost$spSearchThisBlogPost->ID";
	return $rowId;
}
