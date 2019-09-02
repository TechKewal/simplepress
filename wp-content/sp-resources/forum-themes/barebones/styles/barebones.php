<?php
header("Content-Type: text/css; charset: utf-8");
header("Expires: ".gmdate('D, d M Y H:i:s', (time()+900)) . " GMT");

# --------------------------------------------------------------------------------------
#
#	Simple:Press Template CSS
#	Theme		:	Barebones
#	Author		:	Simple:Press
#
#	This is the main CSS file for the Simple:Press 'Barebones' theme.
#	This file requires a Colour Overlay file to be included
#
#	************************************************************************************
#	WARNING: It is highly recommended that you do NOT edit this theme's files.	Since
#	it's one of the default themes supplied with Simple:Press, if you later update the
#	theme any changes you have made will be lost.  You should instead create your own
#	child theme and then make your edits and customisations there.
#	************************************************************************************
#
# --------------------------------------------------------------------------------------

# load the selected color overlay stylesheet
$overlay = dirname(__FILE__).'/../../'.$_GET['theme'].'/styles/overlays/'.$_GET['overlay'].'.php';
include($overlay);

# get display device type
$viewDevice = (empty($_GET['device'])) ? 'desktop' : $_GET['device'];

# load the reset css file
if (isset($_GET['rtl'])) {
	include('base-reset-rtl.css');
} else {
	include('base-reset.css');
}

?>
/*
# --------------------------------------------------------------------------------------
#
#	Simple:Press CSS File
#	Theme		:	Barebones
#	Author		:	Simple:Press
#
# --------------------------------------------------------------------------------------
*/

/*--------------------------------------------------------------------------- QUICK TEMPLATE CALLS */

#spMainContainer {
	font-size: 90%;
	width: 100%;
}

#spMainContainer .spBold {
	font-weight: bold;
}

/*--------------------------------------------------------------------------- LABELS */

#spMainContainer .spLabelSmall {
	color: <?php echo($subTitleFontColor); ?>;
	font-weight: <?php echo($subTitleFontWeight); ?>;
	font-size: <?php echo($subTitleFontSize); ?>;
	margin: 0px;
	padding: 0px 0px 0px 5px;
	line-height: 1.1em;
}

#spMainContainer .spLabelSmall.spRight {
	color: <?php echo($subTitleFontColor); ?>;
	font-weight: normal;
	font-size: <?php echo($subTitleFontSize); ?>;
	margin: 0px;
	padding: 5px 5px 0px 0px;
	line-height: 1.1em;
}

#spMainContainer .spHeadOne .spLabelSmall.spRight {
	padding: 1px 5px 0px 0px;
}

#spMainContainer .spLogLabelSmall {
	color: <?php echo($secLinkFontColor); ?>;
	font-weight: bold;
	font-size: <?php echo($subTitleFontSize); ?>;
	margin: 0px;
	padding: 1px 5px 0px 5px;
	line-height: 1.1em;
	border-left: <?php echo($solidBorder); ?>;
}

#spMainContainer #spUnreadCount {
	font-weight: <?php echo($subTitleFontWeight); ?>;
}

#spMainContainer .spLabel,
#spMainContainer .spLabelBordered {
	color: <?php echo($subTitleFontColor); ?>;
	font-size: inherit;
	line-height: 1.1em;
}

#spMainContainer .spLabelBordered {
	color: <?php echo($subTitleFontColor); ?>;
	font-size: inherit;
	line-height: 1.1em;
}

#spMainContainer .spRecentPostSection .spMessage {
	font-family: <?php echo($titleFontFamily); ?>;
	font-size: <?php echo($titleFontSize); ?>;
	font-weight: <?php echo($titleFontWeight); ?>;
	text-transform: <?php echo($titleFontTransform); ?>;
	color: <?php echo($titleFontColor); ?>;
	background: <?php echo($titleContainerBackground); ?>;
	border: none;
	margin: 0;
	padding: 15px 0;
}

#spMainContainer .spInRowLabel {
	font-size: 80%;
	color: <?php echo($contentFontColor); ?>;
	padding: 0;
	margin: 0;
}

#spMainContainer .spUniversalLabel .spInRowLabel {
	color: <?php echo($contentFontColor); ?>;
}

/*--------------------------------------------------------------------------- BUTTONS / LINKS / SUBMITS / INPUTS */

#spMainContainer a:link,
#spMainContainer a:active,
#spMainContainer a:visited,
#spMainContainer .spLink:link,
#spMainContainer .spLink:active,
#spMainContainer .spLink:visited {
	color: <?php echo($linkFontColor); ?>;
	outline: none;
}

#spMainContainer a:hover,
#spMainContainer .spLink:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spSearchLink,
#spMainContainer .spSearchLink a,
#spMainContainer .spSearchLink:link,
#spMainContainer .spSearchLink:active,
#spMainContainer .spSearchLink:visited {
	font-size: <?php echo($spButtonFontSize); ?>;
	font-weight: <?php echo($spButtonFontWeight); ?>;
	color: <?php echo($secLinkFontColor); ?>;
	background: <?php echo($spButtonBackground); ?>;
	border: <?php echo($spButtonBorder); ?>;
	vertical-align: middle;
	padding: 5px;
	margin: 5px 0px;
	cursor: pointer;
	outline: none;
}

#spMainContainer .spSearchLink:hover {
	color: <?php echo($secLinkFontColorHover); ?>;
	border: <?php echo($spButtonBorderHover); ?>;
	background: <?php echo($spButtonBackgroundHover); ?>;
}

#spMainContainer a.spButton {
	font-size: <?php echo($spButtonFontSize); ?>;
	font-weight: <?php echo($spButtonFontWeight); ?>;
	color: <?php echo($secLinkFontColor); ?>;
	background: <?php echo($spButtonBackground); ?>;
	border: <?php echo($spButtonBorder); ?>;
	vertical-align: middle;
	padding: 5px;
	margin: 5px 0px;
	cursor: pointer;
	outline: none;
}

#spMainContainer a.spButton:hover {
	color: <?php echo($secLinkFontColorHover); ?>;
	border: <?php echo($spButtonBorderHover); ?>;
	background: <?php echo($spButtonBackgroundHover); ?>;
}

#spMainContainer a.spButton img {
	vertical-align: middle;
	margin: 0 3px 2px 0;
	padding: 0;
}

#spMainContainer a.spButton span {
	font-weight: bold;
}

#spMainContainer .spButton {
	font-size: <?php echo($spButtonFontSize); ?>;
	font-weight: <?php echo($spButtonFontWeight); ?>;
	color: <?php echo($secLinkFontColor); ?>;
	background: <?php echo($spButtonBackground); ?>;
	border: <?php echo($spButtonBorder); ?>;
	vertical-align: middle;
	padding: 5px;
	margin: 5px 0px;
	cursor: pointer;
	outline: none;
}

#spMainContainer .spButton:hover {
	color: <?php echo($secLinkFontColorHover); ?>;
	border: <?php echo($spButtonBorderHover); ?>;
	background: <?php echo($spButtonBackgroundHover); ?>;
}

#spMainContainer .spButtonAsLabel {
	cursor: default;
	font-size: 80%;
	font-weight: normal;
	color: <?php echo($contentFontColor); ?>;
	background: none;
	border: none;
	width: auto;
	height: auto;
	text-align: left;
	vertical-align: middle;
	padding: 4px 5px 0px 5px;
	margin: 0px 4px 5px 2px;
	outline: none;
}

#spMainContainer input.spSubmit {
	width: auto;
	min-height: 32px;
	height: auto;
	text-align: center;
	padding: 0;
	margin: 0;
	font-size: 80%;
	text-decoration: none;
	outline-style: none;
	color: <?php echo($contentFontColor); ?>;
	cursor: pointer;
	position: relative;
	outline: none;
}

#spMainContainer .spSubmit:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer input, select, textarea {
	padding: 0;
	border-radius: 0;
}

#spMainContainer textarea.spControl,
#spMainContainer select.spControl,
#spMainContainer input.spControl {
	height: 27px;
	border: <?php echo($solidBorderLight); ?>;
	color: <?php echo($contentFontColor); ?>;
	padding: 0;
	margin: 5px 0;
	width: auto;
}

#spMainContainer textarea.spControl {
	height: auto;
	background: <?php echo ($whiteContainer); ?>;
	box-shadow: none;
	padding: 5px;
}

#spMainContainer #spPostForm .spEditor .spEditorTitle input {
	border: <?php echo($solidBorder); ?>;
}

#spMainContainer select.spSelect {
	font-size: 100%;
	border: <?php echo ($solidBorder); ?>;
	color: <?php echo ($contentFontColor); ?>;
	height: 24px;
	width: auto;
	line-height: 1.2em;
	vertical-align: middle;
	margin: 3px 10px;
	padding: 1px;
}

#spMainContainer select.spSelect option{
	background: <?php echo ($whiteContainer); ?>;
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer select.spSelect optgroup {
	background: <?php echo($titleColumnsBackground); ?>;
	outline: none;
	box-shadow: none;
}

#spMainContainer select.spSelect:hover {
	background: rgba(40, 40, 40, 0.1);
}

#spMainContainer #spPostForm select.spSelect {
	border: <?php echo ($solidBorder); ?>;
}

/*--------------------------------------------------------------------------- FIELDSET (GENERIC) */

#spMainContainer fieldset {
	background: <?php echo($formContainerBackground); ?>;
	color: <?php echo($contentFontColor); ?>;
	margin: 0px 0px;
	width: auto;
}

#spMainContainer fieldset .spLabel {
	color: <?php echo ($contentFontColor); ?>;
	font-size: 80%;
}

#spMainContainer fieldset legend {
	padding: 5px;
	margin: 0 0 7px 0;
	font-weight: normal;
	width: auto;
	color: <?php echo ($contentFontColor); ?>;
	font-weight: bold;
	line-height: 1.3em;
	font-size: 85%;
}

/*--------------------------------------------------------------------------- FORUM HEAD */

#spMainContainer .spHeadControlBar {
	color: <?php echo($contentFontColor); ?>;
	background: none;
	margin: 0 0 5px 0;
	padding: 0;
	width: 100%;
	height: auto;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	display: flex;
	display: -webkit-flex;
	flex-direction: row;
	-webkit-flex-direction: row;
	flex-wrap: wrap;
	-webkit-flex-wrap: wrap;
}

#spMainContainer .spHeadOne {
	margin: 0 0 -100px auto;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#spMainContainer .spHeadTwo {
	width: auto;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	margin: 2px 0 0 0;
}

#spMainContainer .spHeadThree {
	margin: 5px 0 0 0;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#spMainContainer .spHeadControls {
	background: <?php echo($headerContainerBackground); ?>;
	border: <?php echo($headerContainerBorder); ?>;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	display: inline-block;
	padding: 5px 10px;
}

#spMainContainer .spHeadControls .spButton {
	margin: 0px 0px 0px 10px;
}

/*--------------------------------------------------------------------------- LOGIN FORM */

#spMainContainer #spLoginForm {
	padding: 0;
	text-align: center;
	font-size: <?php echo($formFontSize); ?>;
	background: transparent;
}

#spMainContainer #spLoginForm label{
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer #spLoginForm h2 {
	margin: 0;
	padding: 0;
	clear: none;
}

#spMainContainer #spLoginForm fieldset.spControl {
	background: <?php echo ($titleContainerBackground); ?>;
	vertical-align: top;
	height: auto;
	width: 250px;
	border: 0px;
	padding: 10px 0 20px 0;
	margin: 10px 0 0 0;
	margin-left: auto;
	margin-right: auto;
}

#spMainContainer #spLoginForm form.spForm {
	margin: 0 auto;
	width: 200px;
	text-align: left;
	background: transparent;
}

#spMainContainer #spLoginForm form.spForm input {
	background: <?php echo ($inputFieldBackground); ?>;
	width: 200px;
	margin: 5px 0 0 0;
	padding: 0 5px;
	box-sizing: border-box;
}

#spMainContainer #spLoginForm .spLink {
	color: <?php echo ($linkFontColor); ?>;
	font-weight: bold;
}

#spMainContainer #spLoginForm .spLink:hover {
	color: <?php echo ($linkFontColorHover); ?>;
}

#spMainContainer #spLoginForm form.spForm input.spSubmit {
	width: auto;
	background: none;
	color: <?php echo ($linkFontColor); ?>;
	font-weight: bold;
	font-size: 100%;
}

#spMainContainer #spLoginForm form.spForm input.spSubmit:hover{
	color: <?php echo ($linkFontColorHover); ?>;
}

#spMainContainer #spLoginForm form.spForm p {
	padding: 5px 0;
	clear: both;
}

#spMainContainer #spLoginForm form.spForm p.spForm {
	text-align: center;
	background: none;
}

#spMainContainer #spMarkRead p,
#spMainContainer #spMarkReadForum p {
	padding: 20px 0 10px;
	text-align: center;
}

#spMainContainer .spForm {
	padding: 5px;
	background: <?php echo ($titleColumnsBackground); ?>;
}

/*--------------------------------------------------------------------------- SEARCH FORM */

#spMainContainer .spSearchForm input.spControl {
	font-size: <?php echo($contentFontSize); ?>;
	color: <?php echo ($contentFontColor); ?>;
	background: <?php echo($inputFieldBackground); ?>;
	border: <?php echo($solidBorderLight); ?>;
	line-height: 1em;
	margin: 0px 0px 0px 0px;
	padding: 0 5px;
	box-sizing: border-box;
}

#spMainContainer .spSearchForm input.spControl:hover {
	background: <?php echo ($inputFieldBackground); ?>;
}

#spMainContainer #spSearchFormAdvanced {
	display: none;
	font-size: 100%;
	margin: 0;
	padding: 0;
	text-align: center;
}

#spMainContainer #spSearchFormAdvanced	fieldset.spSearchMember {
	border: none;
}

#spMainContainer #spSearchFormAdvanced .spSearchMember .spSearchSectionUser img {
	display: none;
}

#spMainContainer .spSearchMember .spSearchSection {
	text-align: center;
}

#spMainContainer #spSearchFormAdvanced .spLabel,
#spMainContainer #spSearchFormAdvanced legend,
#spMainContainer #spSearchFormAdvanced p,
#spMainContainer #spSearchFormAdvanced b {
	display: inline;
	color: <?php echo($contentFontColor); ?>;
	font-size: <?php echo($contentFontSize); ?>;
}

#spMainContainer #spSearchFormAdvanced .spSubmit {
	font-size: <?php echo($spButtonFontSize); ?>;
	font-weight: <?php echo($spButtonFontWeight); ?>;
	color: <?php echo($secLinkFontColor); ?>;
	background: <?php echo($spButtonBackground); ?>;
	border: <?php echo($spButtonBorder); ?>;
	vertical-align: middle;
	padding: 0;
	margin: 0px 15px 0px 0px;
	cursor: pointer;
}

#spMainContainer #spSearchFormAdvanced .spSubmit:hover {
	color: <?php echo($secLinkFontColorHover); ?>;
	border: <?php echo($spButtonBorderHover); ?>;
	background: <?php echo($spButtonBackgroundHover); ?>;
}

#spMainContainer #spSearchFormAdvanced fieldset {
	background: <?php echo($formContainerBackground); ?>;
	border: <?php echo($formContainerBorder); ?>;
	padding: 10px;
	margin-top: 10px;
}

#spMainContainer #spSearchFormAdvanced h2 {
	margin: 0;
	padding: 0;
	clear: none;
}

#spMainContainer #spSearchFormAdvanced legend {
	background: <?php echo ($formContainerBackground); ?>;
	font-size: <?php echo($titleFontSize); ?>;
	font-weight: bold;
	display: none;
}

#spMainContainer .spSearchFormSubmit a {
	float: right;
	margin: 0;
	padding: 0 5px;
}

#spMainContainer .spSearchSection .spTopicListSection {
	display: flex;
	display: -webkit-flex;
	flex-direction: column;
	-webkit-flex-direction: column;
	font-size: 85%;
	padding: 10px;
	box-sizing: border-box;
}

#spMainContainer .spSearchMember .spSearchSection,
#spMainContainer .spSearchTopicStatus .spSearchSection {
	text-align: left;
}

#spMainContainer .spSearchSection .spRadioSection hr {
	display: none;
}

#spMainContainer p.spSearchForumScope,
#spMainContainer p.spSearchSiteScope,
#spMainContainer p.spSearchMatch,
#spMainContainer p.spSearchOptions {
	text-align: center;
	font-weight: bold;
	margin: 0 0 5px 0;
	padding: 0;
}

#spMainContainer p.spSearchSiteScope {
	margin-top: 20px;
}

/*--------------------------------------------------------------------------- QUICKLINKS */

#spMainContainer #spQuickLinksForumSelect_msdd,
#spMainContainer #spQuickLinksTopicSelect_msdd {
	width: 100% !important;
}

#spMainContainer #spQuickLinksForumSelect_child,
#spMainContainer #spQuickLinksTopicSelect_child {
	max-height: 550px !important;
	min-height: auto;
	width: 350px !important;
}

#spMainContainer .spQuickLinks {
	display: none;
	height: auto;
	width: auto;
	background: <?php echo($spButtonBackground); ?>;
	border: <?php echo($spButtonBorder); ?>;
	color: <?php echo($secLinkFontColor); ?>;
	font-size: <?php echo($spButtonFontSize); ?>;
	padding: 5px;
	margin-top: -7px;
	text-decoration: none;
	text-align:left;
	position: relative;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	cursor: pointer;
}

#spMainContainer .spQuickLinks:hover {
	background: <?php echo($greyContainer); ?>;
}

#spMainContainer .spQuickLinks .dd .ddTitle {
	padding:3px;
	margin-top: 5px;
	text-indent:0;
	cursor: pointer;
	overflow:hidden;
}

#spMainContainer .spQuickLinks .ddTitleText {
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spQuickLinks .ddTitleText span.ddlabel {
	font-weight: bold;
	cursor: pointer;
}

#spMainContainer .spQuickLinks .dd .ddChild {
	position:absolute;
	display:none;
	height: auto !important;
	width:	<?php echo($quickLinksListWidth); ?>;
	overflow:auto;
	overflow-x:hidden !important;
	border: <?php echo($solidBorder); ?>;
	background: <?php echo($greyContainer); ?>;
	padding: 10px;
	margin: 0 10px;
	z-index: 100 !important;
	cursor: pointer;
}

#spMainContainer .spQuickLinks .dd .ddChild ul li {
	background: <?php echo($greyContainer); ?>;
	margin: 0;
	padding: 4px 0 4px 15px;
	list-style-type:none;
}

#spMainContainer .spQuickLinks .dd .ddChild li .optgroupTitle {
	font-weight: bold;
}

#spMainContainer .spQuickLinks .dd .ddChild li img {
	border: 0 none;
	vertical-align: middle;
	float: left;
	margin-right: 10px;
	padding-top: 3px;
}

#spMainContainer #spQuickLinksTopic .ddChild li.spPostRead span {
	color: <?php echo($quickLinksTopicsPostRead); ?>;
}

#spMainContainer #spQuickLinksTopic .ddChild li.spPostNew span {
	color: <?php echo($quickLinksTopicsPostNew); ?>;
}

#spMainContainer #spQuickLinksTopic .ddChild li.spPostMod span {
	color: <?php echo($quickLinksTopicsPostMod); ?>;
}

/*--------------------------------------------------------------------------- BREADCRUMBS */

#spMainContainer .spCrumbHolder {
	display: block;
	margin: 0 0 1px 0;
	width: 100%;
}

#spMainContainer #spBreadCrumbs {
	overflow: hidden;
	height: auto;
	margin: 0 0 0 0;
	padding: 0 15px 0 0;
	vertical-align: middle;
}

#spMainContainer #spBreadCrumbs img {
	margin: 0 5px 2px 0;
	padding: 0;
	opacity: 0.7;
	min-width: 2px;
	min-height: 2px;
}

#spMainContainer #spBreadCrumbs a {
	color: <?php echo($subTitleFontColor); ?>;
	font-size: <?php echo($subTitleFontSize); ?>;
	padding: 5px;
}

#spMainContainer #spBreadCrumbs a:hover {
	color: <?php echo($linkFontColor); ?>;
	text-decoration: none;
}

#spMainContainer #spBreadCrumbs .spCurrentBreadcrumb{
	color: <?php echo ($linkFontColor); ?>;
	font-weight: <?php echo($subTitleFontWeight); ?>;
	text-decoration: none;
}

#spMainContainer #spBreadCrumbs .spCurrentBreadcrumb:hover{
	color: <?php echo ($linkFontColorHover); ?>;
}

/*--------------------------------------------------------------------------- HEADER CONTAINERS */

#spMainContainer .spFlexHeadContainer {
	display: flex;
	flex-direction: row;
}

#spMainContainer .spGroupViewSection .spGroupViewHeader,
#spMainContainer .spForumViewSection .spForumViewHeader,
#spMainContainer .spTopicViewSection .spTopicViewHeader {
	color: <?php echo($contentFontColor); ?>;
	background: <?php echo($titleContainerBackground); ?>;
	border: none;
	width: 100%;
	padding: 0;
	margin: -1px 0px 0px 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#spMainContainer .spTopicViewSection .spTopicViewHeader .spHeaderDescription {
	padding: 0;
}

#spMainContainer .spHeaderIcon {
	margin: 5px;
}

#spMainContainer .spHeaderName,
#spMainContainer .spHeaderName span {
	font-family: <?php echo($titleFontFamily); ?>;
	font-size: <?php echo($titleFontSize); ?>;
	font-weight: <?php echo($titleFontWeight); ?>;
	text-transform: <?php echo($titleFontTransform); ?>;
	color: <?php echo($titleFontColor); ?>;
	margin: 0;
	padding: 5px 10px 0px 0px;
}

#spMainContainer .spHeaderName p {
	color: <?php echo($titleFontColor); ?>;
	font-weight: <?php echo($titleFontWeight); ?>;
	font-size: <?php echo($titleFontSize); ?>;
}

#spMainContainer .spHeaderDescription {
	font-family: <?php echo($contentFontFamily); ?>;
	font-size: <?php echo($contentFontSize); ?>;
	font-weight: <?php echo($contentFontWeight); ?>;
	color: <?php echo($contentFontColor); ?>;
	margin: 0;
	padding: 2px 4px 0px 0px;
	opacity: 0.7;
	float: left;
	line-height: 1.4em;
}

#spMainContainer .spHeaderMessage {
	font-family: <?php echo($contentFontFamily); ?>;
	font-size: <?php echo($contentFontSize); ?>;
	font-weight: <?php echo($contentFontWeight); ?>;
	color: <?php echo($contentFontColor); ?>;
	margin: 0 5px 5px 5px;
	padding: 5px;
	opacity: 0.7;
}

#spMainContainer .spRowIcon {
	margin: 5px;
}

#spMainContainer .spRowIconHidden {
	margin: 5px;
	visibility: hidden;
	margin-top: -50%;
	margin-bottom: -50%;
}

#spMainContainer a.spRowName,
#spMainContainer .spRowName {
	font-size: <?php echo($contentFontSize); ?>;
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
	line-height: 1em;
}

#spMainContainer a.spRowName:hover,
#spMainContainer .spRowName:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spRowDescription {
	font-size: 80%;
	font-weight: normal;
	color: <?php echo($contentFontColor); ?>;
	margin: 0 0 0 0;
	padding: 0;
}

#spMainContainer .spRowDescriptionBold {
	font-size: 80%;
	font-weight: bold;
	color: <?php echo($contentFontColor); ?>;
	margin: 0 0 0 0;
	padding: 0;
}

#spMainContainer .spRowDescriptionBold span {
	font-weight: bold;
}

#spMainContainer .spHeaderAddButton {
	padding: 2px 0px 0px 0px;
	margin: 0;
	font-size: 80%;
	white-space: nowrap;
	font-weight: bold;
}

#spMainContainer .spTopicViewSection .spTopicViewHeader .spLink:link,
#spMainContainer .spForumViewSection .spForumViewHeader .spLink:link,
#spMainContainer .spGroupViewSection .spGroupViewHeader .spLink:link {
	font-size: 80%;
	font-weight: bold;
	padding: 5px 0 0 0;
	line-height: 1.1em;
}

#spMainContainer .spTopicViewSection .spTopicViewHeader a:link,
#spMainContainer .spForumViewSection .spForumViewHeader a:link,
#spMainContainer .spGroupViewSection .spGroupViewHeader a:link {
	font-weight: bold;
}

#spMainContainer .spSubButton,
#spMainContainer .spSubButton a {
	font-size: 80%;
	padding: 5px 0 0 0;
	font-weight: bold;
	line-height: 1.1em;
}

#spMainContainer .spHeaderButton,
#spMainContainer .spHeaderButton a {
	font-size: 80%;
	margin: 0 0 0 10px;
	line-height: 1.1em;
}

#spMainContainer .spReplyButton,
#spMainContainer .spReplyButton a {
	font-size: 80%;
	font-weight: bold;
	line-height: 1.1em;
	padding: 5px;
	margin: 5px 0;
}

/*--------------------------------------------------------------------------- HEADER COLUMNS */

#spMainContainer .spUniversalLabel {
	padding: 0;
}

#spMainContainer .spUniversalLabel .spColumnTitle {
	font-family: <?php echo($subTitleFontFamily); ?>;
	color: <?php echo($subTitleFontColor); ?>;
	font-size: <?php echo($subTitleFontSize); ?>;
	font-weight: <?php echo($subTitleFontWeight); ?>;
	float: left;
}

#spMainContainer .spUniversalLabel .spColumnTitleCentered {
	font-family: <?php echo($subTitleFontFamily); ?>;
	color: <?php echo($subTitleFontColor); ?>;
	font-size: <?php echo($subTitleFontSize); ?>;
	font-weight: <?php echo($subTitleFontWeight); ?>;
	float: left;
	width: 100%;
	text-align: center;
}

#spMainContainer .spTitleColumn {
	min-height: 1px;
	margin: 7px;
}

#spMainContainer .spTitleColumnHidden {
	min-height: 1px;
	margin: 7px;
	width: auto;
}

#spMainContainer .spTitleColumnTitle {
	float: none;
	min-height: 1px;
	padding: 7px 7px 7px 0px;
	margin: 0;
}

#spMainContainer .spTitleColumnTitle .spButton {
	margin: 0;
	padding: 0;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spTitleColumnTitle .spButton:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spHeadColumn3 {
	margin-left: auto;
	padding: 7px;
}

#spMainContainer .spCategoryLabels {
	width: 100%;
	padding: 0px;
	margin: 0px;
	display: flex;
	-moz-box-flex: 1;
	flex-direction: row;
	align-items: center;
	box-sizing: border-box;
	background: <?php echo($titleColumnsBackground); ?>;
}

#spMainContainer .spSpacerIcon {
	height: 0px;
}

/*--------------------------------------------------------------------------- GROUP, FORUM, TOPIC & MEMBER VIEW SECTIONS */

#spMainContainer .spPlainSection {
	color: inherit;
	background: transparent;
	border: none;
	padding: 0;
	width: auto;
	height: auto;
}

#spMainContainer .spInlineSection {
	display: none;
}

#spMainContainer .spGroupViewSection {
	color: <?php echo($contentFontColor); ?>;
	background: <?php echo($groupSectionBackground); ?>;
	border: <?php echo($groupSectionBorder); ?>;
	width: 100%;
	padding: 0px;
	margin: 0px;
}

#spMainContainer .spForumViewSection {
	color: <?php echo($contentFontColor); ?>;
	background: <?php echo($forumSectionBackground); ?>;
	border: <?php echo($forumSectionBorder); ?>;
	width: 100%;
	padding: 0px;
	margin: 0px;
}

#spMainContainer .spTopicViewSection {
	color: <?php echo($contentFontColor); ?>;
	background: <?php echo($topicSectionBackground); ?>;
	border: <?php echo($topicSectionBorder); ?>;
	width: 100%;
	padding: 0px;
	margin: 0px;
}

#spMainContainer .spMemberGroupsSection {
	color: <?php echo($contentFontColor); ?>;
	background: <?php echo($groupSectionBackground); ?>;
	border: <?php echo($groupSectionBorder); ?>;
	width: 100%;
	padding: 0px;
	margin: 0px;
}

#spMainContainer .spColumnCountViews,
#spMainContainer .spColumnCountReplies {
	display: flex;
	flex-direction: column;
	align-items: center;
	margin: 7px;
}

#spMainContainer .spForumViewSection .spForumViewHeader .spButton {
	margin: 8px 5px 5px 5px;
}

#spMainContainer .spForumViewSection .spForumViewHeader .spButton:hover {
	background: none;
}

#spMainContainer .spForumViewSection .spForumViewHeader .spIconNoAction{
	margin: 6px 4px 0 0;
}

#spMainContainer .spForumViewSection .spForumViewHeader .spIconNoAction:hover {
	background: transparent;
}

#spMainContainer .spForumViewSection .spForumViewHeader .spPageLinks span{
	border: none;
}

#spMainContainer .spActionsColumnSection {
	padding: 4px 4px 0 0;
}

#spMainContainer .spForumTopicContainer .spActionsColumnSection {
	padding: 0px;
	margin: 0 -4px 0 0;
}

#spMainContainer .spForumTopicSection a.spInRowLastPostLink {
	font-size: 100%;
}

#spMainContainer #spTopicHeaderShowBlogLink {
	border: none;
	background: none;
	margin: 0;
	padding: 0;
	color: <?php echo($linkFontColor); ?>;
	float: left;
}

#spMainContainer #spTopicHeaderShowBlogLink:hover {
	background: none;
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spTopicViewSection .spTopicViewHeader .spPageLinks  img {
	margin: 3px 0 5px 0;
}

#spMainContainer .spTopicViewSection .spTopicViewHeader .ShareThisTopic {
	margin: 5px -8px 5px 0;
	padding: 3px;
}

/*--------------------------------------------------------------------------- GROUP & FORUM VIEW INDEX ROWS */

#spMainContainer .spGroupForumSection.spOdd,
#spMainContainer .spForumTopicSection.spOdd {
	color: <?php echo($indexRowOddColor); ?>;
	background: <?php echo($indexRowOddBackGround); ?>;
	border: <?php echo($indexRowOddBorder); ?>;
}

#spMainContainer .spGroupForumSection.spOdd:hover,
#spMainContainer .spForumTopicSection.spOdd:hover {
	color: <?php echo($indexRowOddColorHover); ?>;
	background: <?php echo($indexRowOddBackGroundHover); ?>;
	border: <?php echo($indexRowOddBorderHover); ?>;
}

#spMainContainer .spGroupForumSection.spEven,
#spMainContainer .spForumTopicSection.spEven {
	background: <?php echo($indexRowEvenBackGround); ?>;
	border: <?php echo($indexRowEvenBorder); ?>;
}

#spMainContainer .spGroupForumSection.spEven:hover,
#spMainContainer .spForumTopicSection.spEven:hover {
	color: <?php echo($indexRowEvenColorHover); ?>;
	background: <?php echo($indexRowEvenBackGroundHover); ?>;
	border: <?php echo($indexRowEvenBorderHover); ?>;
}

#spMainContainer .spGroupForumSection,
#spMainContainer .spForumTopicSection {
	width: 100%;
	padding: 0px;
	margin: 0px;
	display: flex;
	display: -webkit-flex;
	-moz-box-flex:1.0; /* Firefox */
	-webkit-box-flex:1.0; /* Safari and Chrome */
	-ms-flex:1.0; /* Internet Explorer 10 */
	box-flex:1.0;
	flex-direction: row;
	-webkit-flex-direction: row;
	align-items: center;
	-webkit-align-items: center;
	box-sizing: border-box;
}

#spMainContainer .spTopicDescription {
	font-size: 75%;
	margin: 0;
	opacity: 0.8;
}

#spMainContainer .spInRowLastPostLink {
	font-size: <?php echo($linkFontSize); ?>;
	font-weight: <?php echo($subTitleFontWeight); ?>;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spInRowLastPostLink a{
	font-size: 100%;
}

#spMainContainer .spInRowLastPostLink:hover,
#spMainContainer .spInRowLastPostLink a:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spInRowPostLink a:link {
	font-size: 75%;
	font-weight: <?php echo($contentFontWeight); ?>;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spInRowPostLink a:link:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spInRowPostLink .spIcon {
	margin-top: 0px;
}

#spMainContainer .spInRowText,
#spMainContainer .spInRowRank,
#spMainContainer .spInRowDate,
#spMainContainer .spInRowNumber,
#spMainContainer .spForumModeratorList {
	font-size: 80%;
	line-height: 1.1em;
}

/*--------------------------------------------------------------------------- COLUMN SECTIONS - UNIVERSAL */

#spMainContainer .spIconColumnSection {
	margin: 7px;
	width: auto;
}

#spMainContainer .spIconColumnSectionTitle {
	margin: 7px 13px 7px 7px;
	width: auto;
}

#spMainContainer .spViewsLabel,
#spMainContainer .spPostsLabel {
	font-weight: bold;
	padding-left: 0px;
}

#spMainContainer .spColumnSection {
	margin: 7px;
}

#spMainContainer .spColumnSection a img{
	opacity: 1;
}

#spMainContainer .spColumnSection a img:hover{
	opacity: 1;
}

#spMainContainer .spColumnSection .spIconNoAction {
	opacity: 1;
}

#spMainContainer .spColumnSection .spButton{
	background: none;
	color: <?php echo($contentFontColor); ?>;
	margin: 0;
	padding: 9px 0px 4px 0;
	text-align: center;
	font-size: 75%;
	border: none;
}

#spMainContainer .spColumnSection img {
	justify-content: space-around;
	align-content: space-around;
}

/*--------------------------------------------------------------------------- SUB FORUMS - GROUP VIEW */

#spMainContainer .spInRowSubForums {
	font-size: <?php echo($contentFontSize); ?>;
	margin: 10px 0;
}

#spMainContainer .spInRowSubForums .spInRowLabel {
	font-size: 100%;
}

#spMainContainer .spInRowSubForums .spIconSmall {
	max-width: 16px;
	max-height: 16px;
}

#spMainContainer .spInRowSubForums .spInRowLabel ul,
#spMainContainer .spInRowSubForums .spInRowLabel li {
	list-style-type: none;
}

#spMainContainer .spInRowSubForums .spInRowLabel li {
	margin: 3px 0 0 0;
}

#spMainContainer .spInRowSubForumlink {
	margin: 0;
	font-weight: bold;
}

#spMainContainer .spInRowSubForums .spInRowLabel .spInRowSubForumlink {
	font-size: 100%;
}

#spMainContainer .spForumSubforumContainer .spColumnSection .spIcon {
	margin: 0;
}

#spMainContainer .spGroupForumSection .spInRowSubForums .spInRowLabel{
	font-size: 95%;
}

#spMainContainer .spGroupForumSection .spInRowSubForums .spInRowSubForumlink {
	font-weight: bold;
}

#spMainContainer .spGroupForumSection .spInRowSubForums img {
	max-width: 16px;
	max-height: 16px;
	padding: 0;
}

#spMainContainer .spForumSubforumContainer .spColumnSection .spInRowPostLink {
	margin: 0;
}

/*--------------------------------------------------------------------------- LIST SECTIONS / NEW POST LISTS */

#spMainContainer .spTopicListSection .spColumnSection  {
	display: flex;
}

#spMainContainer .spTopicListSection .spButton {
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
	font-size: 80%;
	padding: 0 0 0 10px;
	margin: 0;
	vertical-align: baseline;
}

#spMainContainer .spTopicListSection .spButton:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spListViewSection .spTopicListSection .spListTopicRowName{
	font-weight: <?php echo($subTitleFontWeight); ?>;
	font-size: 80%;
	padding: 0;
	margin: 1px 0 0 0;
}

#spMainContainer .spRecentPostSection .spTopicListSection .spListForumRowName {
	font-size: 80%;
}

#spMainContainer .spListForumRowName{
	color: <?php echo($linkFontColor); ?>;
	padding: 4px 4px 4px 8px;
	margin: 3px 0px 3px 0px;
	font-weight: bold;
	overflow: hidden;
	width: 100%;
	box-sizing: border-box;
}

#spMainContainer .spListSection .spListForumRowName {
	border: none;
	padding: 0;
	font-size: 80%;
}

#spMainContainer .spListForumRowName a:link {
	padding: 20px;
}

#spMainContainer .spTopicListSection {

	background: <?php echo($listRowBackGround); ?>;
	width: 100%;
	margin: 0px 0px 10px 0px;
	display: flex;
	display: -webkit-flex;
	-webkit-justify-content: space-around;
	flex-direction: row;
	-webkit-flex-direction: row;
	align-items: center;
	-webkit-align-items: center;
	box-sizing: border-box;
}

#spMainContainer .spTopicListSection:hover {
	background: <?php echo($listRowBackGroundHover); ?>;
}

#spMainContainer .spTopicListSection.spEven {
	background: <?php echo($indexRowEvenBackGround); ?>;
}

#spMainContainer .spListTopicRowName .spLink {
	font-weight: bold;
}

#spMainContainer .spUnreadPostsInfo {
	font-size: 70%;
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spUnreadPostsInfo img:hover {
	opacity: 0.8;
}

#spMainContainer #spUnreadPostsLink .spIcon {
	padding-left: 3px;
	color: <?php echo($contentFontColor); ?>;
}

/* -------------------
List View specific
----------------------*/

#spMainContainer .spInlineTopics .spTopicListSection .spListForumRowName,
#spMainContainer .spRecentPostSection .spTopicListSection .spListForumRowName,
#spMainContainer .spSearchSection .spTopicListSection .spListForumRowName {
	font-size:	80%;
	padding: 0;
	margin: 0;
}

#spMainContainer .spInlineTopics .spTopicListSection .spListTopicRowName,
#spMainContainer .spRecentPostSection .spTopicListSection .spListTopicRowName,
#spMainContainer .spSearchListSection .spTopicListSection .spListTopicRowName {
	font-size:	80%;
	padding: 0 0 3px 0;
}

#spMainContainer .spListViewSection .spTopicListSection .spListTopicRowName {
	padding: 0 0 3px 0;
}

#spMainContainer .spInlineTopics .spTopicListSection .spListLabel,
#spMainContainer .spInlineTopics .spTopicListSection .spListPostLink,
#spMainContainer .spRecentPostSection .spTopicListSection .spListLabel,
#spMainContainer .spRecentPostSection .spTopicListSection .spListPostLink,
#spMainContainer .spSearchListSection .spTopicListSection .spListLabel {
	font-size: 80%;
	clear: both;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spRecentPostSection .spTopicListSection .spListPostLink {
	font-size: 100%;
	width: 100%;
}

#spMainContainer .spRecentPostSection .spTopicListSection .spListPostLink .spProfilePopupLink {
	font-weight: bold;
}


#spMainContainer .spSearchListSection .spTopicListSection .spListPostLink {
	font-size: 100%;
}

#spMainContainer .spSearchListSection .spTopicListSection .spListPostLink .spProfilePopupLink {
	font-weight: bold;
}

#spMainContainer .spListPostLink {
	padding: 0 0 0 0px;
}

#spMainContainer .spListPostLink .spIcon {
	margin: 0px 4px 0px 0px;
}

#spMainContainer .spListIconSmall {
	border: none;
	vertical-align: top;
	padding: 0 0 3px 0;
	margin: 0 5px 0 0;
}

#spMainContainer .spInlineTopics {
	width: 100%;
	padding: 0;
}
#spMainContainer .spGroupForumContainer .spListViewSection {
	margin: 10px 0 20px 0;
}

#spMainContainer .spGroupForumContainer .spListViewSection .spTopicListSection {
	color: <?php echo($contentFontColor); ?>;
	border: none;
	width: 97%;
	margin: 0px 0px 10px 0px;
	margin-left: 3%;
	display: flex;
	display: -webkit-flex;
	justify-content: space-around;
	-webkit-justify-content: space-around;
	flex-direction: row;
	-webkit-flex-direction: row;
	align-items: center;
	-webkit-align-items: center;
	box-sizing: border-box;
}

#spMainContainer .spListSection {
	color: <?php echo($contentFontColor); ?>;
	background: inherit;
	border: none;
	padding: 0;
	width: 100%;
	display: flex;
	display: -webkit-flex;
	flex-direction: column;
	-webkit-flex-direction: column;
}

#spMainContainer .spListSection .spPageLinks .spIcon img{
	vertical-align: middle;
	padding: 0 5px 2px 0;
}

#spMainContainer .spListSection .spPageLinksBottom .spIconNoAction {
	margin: 0 0 0 0;
}

#spMainContainer .spListSection .spPageLinksBottom .spIconNoAction:hover {
	background: transparent;
}

#spMainContainer .spListSection .spButton:hover{
	background: none;
}

/*--------------------------------------------------------------------------- TOPIC VIEW / TOPIC POST SECTION */

#spMainContainer .spTopicPostSection {
	color: <?php echo($topicViewSectionColor); ?>;
	background: <?php echo($topicViewSectionBackGroundColor); ?>;
	border: <?php echo($topicViewSectionBorder); ?>;
	margin: 25px 0px 25px 0px;
	padding: 0px;
	position: relative;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	display: flex;
	display: -webkit-flex;
}

#spMainContainer .statusHolder {
	padding: 0 10px;
}

#spMainContainer .spTopicPostSection .spPostUserName,
#spMainContainer .spTopicPostSection .spPostUserLocation,
#spMainContainer .spTopicPostSection .spPostUserPosts,
#spMainContainer .spTopicPostSection .spPostUserRegistered,
#spMainContainer .spTopicPostSection .spPostUserRank,
#spMainContainer .spTopicPostSection .spPostUserStatus,
#spMainContainer .spTopicPostSection .spPostUserSpecialRank,
#spMainContainer .spTopicPostSection .spPostUserMemberships {
	font-size: 80%;
	margin: 0;
	padding: 3px;
	line-height: 1em;
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spTopicPostSection .spPostUserStatus .spIcon {
	margin: 0px 4px 3px 0px;
}

#spMainContainer .spTopicPostSection .spPostUserName,
#spMainContainer .spTopicPostSection .spPostUserName a {
	font-weight: bold;
}

#spMainContainer .spTopicPostSection .spPostUserRank img,
#spMainContainer .spTopicPostSection .spPostUserSpecialRank img {
	margin: auto;
}

#spMainContainer .spTopicPostSection .spPostUserAvatar img {
	margin: auto;
}

#spMainContainer .spTopicPostSection .spPostUserWebsite img,
#spMainContainer .spTopicPostSection .spPostUserTwitter img,
#spMainContainer .spTopicPostSection .spPostUserFacebook img,
#spMainContainer .spTopicPostSection .spPostUserGooglePlus img,
#spMainContainer .spTopicPostSection .spPostUserYouTube img,
#spMainContainer .spTopicPostSection .spPostUserLinkedIn img,
#spMainContainer .spTopicPostSection .spPostUserMySpace img {
	margin: 2px;
	max-width: 16px;
	max-height: 16px;
}

#spMainContainer .spTopicPostSection .spUserSection {
	padding: 0;
	margin: 0px 4px 0 0;
	background: <?php echo($topicUserSectionBackGround);?>;
}

#spMainContainer .spPostSection .spPostUserDate {
	font-size: 80%;
	margin: 0 2px 0 0;
	padding: 3px;
	line-height: 1em;
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spPostSection .spPostActionLabel {
	font-size: 80%;
	font-weight: bold;
	margin: 0 2px;
	padding: 3px;
	line-height: 1em;
	color: <?php echo ($linkFontColor); ?>;
}

#spMainContainer .spPostSection .spPostActionLabel:hover {
	color: <?php echo ($linkFontColorHover); ?>;
}

#spMainContainer .spPostSection .spButton {
	padding: 0;
	margin: 0 3px;
	border: none;
}

#spMainContainer .spPostSection .spLabelBordered {
	border: 0px;
	padding: 0;
	margin: 0px 5px 2px 0;
	line-height: 1em;
	font-weight: bold;
}

#spMainContainer .spTopicPostSection .spUserSection span {
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spTopicPostSection .spUserSection .spProfilePopupLink {
	color: <?php echo ($linkFontColor); ?>;
}

#spMainContainer .spTopicPostSection .spUserSection .spProfilePopupLink:hover {
	color: <?php echo ($linkFontColorHover); ?>;
}

#spMainContainer .spTopicPostSection .spPostActionSection {
	-moz-box-ordinal-group: 1;
	order: 0;
	-moz-box-flex: 0;
	flex: 0 0 auto;
	align-self: auto;
	-webkit-align-self: auto;
	height: 22px;
}

#spMainContainer .spTopicPostSection .spPostActionSection .spButton,
#spMainContainer .spTopicPostSection .spPostActionSection .spButton:hover {
	border: none;
}

#spMainContainer .spTopicPostSection .spPostActionSection img{
	min-width: 16px;
	min-height: 16px;
	max-width: 16px;
	max-height: 16px;
	opacity: 1;
}

#spMainContainer .spTopicPostSection .spPostActionSection img:hover{
	min-width: 16px;
	min-height: 16px;
	max-width: 16px;
	max-height: 16px;
	opacity: 0.5;
	border: none;
}

#spMainContainer .spPostContentSection {
	-moz-box-ordinal-group: 1;
	order: 0;
	-moz-box-flex: 1;
	flex: 1 0 auto;
	align-self: auto;
	-webkit-align-self: auto;
	background: <?php echo($postSectionBackGroundColor); ?>;
}

#spMainContainer .spPostContentSection .spProfilePopupLink {
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spPostContentSection .spProfilePopupLinkHover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent {
	color: <?php echo($contentFontColor); ?>;
	margin: 5px 0;
	padding: 10px;
	font-size: 85%;
	line-height: <?php echo($postLineHeight); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h1,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h2,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h3,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h4,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h5,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h6 {
	margin: 0;
	padding: 5px 0px;
	font-weight: bold;
	letter-spacing: 0;
	text-transform: none;
	line-height: 1em;
	word-wrap: break-word;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h1 {
	font-size:	<?php echo($postHeadingh1); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h2 {
	font-size:	<?php echo($postHeadingh2); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h3 {
	font-size:	<?php echo($postHeadingh3); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h4 {
	font-size:	<?php echo($postHeadingh4); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h5 {
	font-size:	<?php echo($postHeadingh5); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent h6 {
	font-size:	<?php echo($postHeadingh6); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent p {
	line-height: <?php echo($postLineHeight); ?>;
	padding-bottom: 1em;
	color: <?php echo($contentFontColor); ?>;
	word-wrap: break-word;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent blockquote {
	overflow: hidden;
	color: <?php echo($contentFontColor); ?>;
	border-left: <?php echo($thickBorder); ?>;
	margin: 0 0 1em 15px;
	padding: 10px;
	word-wrap: break-word;
	opacity: 0.7;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent blockquote blockquote{
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent a:link,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent a:active,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent a:visited {
	overflow: hidden;
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
	word-wrap: break-word;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent a:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent ul,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent ol {
	padding: 0 0 1em 2em;
	list-style-position: inside;
	word-wrap: break-word;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent ul li,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent ol li {
	padding-bottom: 0.5em;
	word-wrap: break-word;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent img {
	overflow: hidden;
	margin: 5px;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimageright {
	float: right;
	margin: 0;
	padding: 5px 0 5px 20px;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimageleft {
	float: left;
	margin: 0;
	padding: 5px 20px 5px 0;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimagecenter {
	display: block;
	margin: 0 auto;
	padding: 5px 20px;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimagebaseline {
	margin: 0;
	padding: 10px
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimagetop {
	vertical-align: top;
	margin: 0;
	padding: 10px
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimagemiddle {
	vertical-align: middle;
	margin: 0;
	padding: 10px
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimagebottom {
	vertical-align: bottom;
	margin: 0;
	padding: 10px
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimagetexttop {
	vertical-align: text-top;
	margin: 0;
	padding: 10px
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfimagetextbottom {
	vertical-align: text-bottom;
	margin: 0;
	padding: 10px
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfmouseright {
	float: right;
	margin-right: -39px;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfmouseleft {
	float: left;
	margin-left: -39px;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .sfmouseother {
	margin: 0 0 0 -34px;
	padding: 20px 0;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent pre {
	margin: 0;
	padding: 5px 0px;
	word-wrap: break-word;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent cite {
	overflow: hidden;
	background: <?php echo($whiteContainer); ?>;
	color: <?php echo($contentFontColor); ?>;
	padding: 2px 5px;
	font-style:;
	word-wrap: break-word;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent table {
	width: 100%;
	margin: 1em 0;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent table td {
	padding: 0.5em;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .spSpoiler {
	margin: 10px 0;
	padding: 0;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .spSpoilerContent {
	padding: 10px 10px 0;
	margin: 0 10px 10px;
	background: <?php echo($whiteContainer); ?>;
	color: <?php echo($contentFontColor); ?>;
	display: none;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent .spSpoiler .spReveal {
	padding: 10px 0;
	text-align: left;
	font-weight: bold;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent span.sfcode,
#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent div.sfcode {
	overflow: hidden;
	background: <?php echo($whiteContainer); ?>;
	color: #393B66;
	font-family: monospace, Courier;
	font-size: inherit;
	display: block;
	margin: 2em;
	padding: 0.5em;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent div.sfcode table.syntax {
	width: 99%;
	padding: 0;
	margin: 0;
}

#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent div.sfcode table.syntax td {
	padding: 0;
}

#spMainContainer .spPostContentSection fieldset{
	background: transparent;
	border: <?php echo($solidBorder); ?>;
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spPostContentSection fieldset legend{
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer #spAnswersTopicAnswer {
	padding: 0 10px;
	text-align: right;
	margin-top: -20px;
	background: <?php echo($postSectionBackGroundColor); ?>;
	width: auto;
	margin-left: auto;
}

#spMainContainer #spAnswersTopicAnswer .spInRowLabel {
	font-weight: bold;
}

#spMainContainer .spTopicPostSection .spPostButton {
	font-weight: bold;
	background: <?php echo($postSectionBackGroundColor); ?>;
	color: <?php echo($linkFontColor); ?>;
	font-size: <?php echo($linkFontSize); ?>;
	text-align: right;
	width: auto;
	margin-left: auto;
	padding: 0 10px;
}

#spMainContainer .spTopicPostSection .spPostButton:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spTopicPostSection .spPostContentHolder {
	background: <?php echo($postSectionBackGroundColor); ?>;
}

#spMainContainer .spTopicPostSection .spPostSection {
	padding: 0;
	 display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
	-webkit-box-direction: normal;
	-moz-box-direction: normal;
	-webkit-box-orient: vertical;
	-moz-box-orient: vertical;
	-webkit-flex-direction: column;
	-ms-flex-direction: column;
	flex-direction: column;
	-webkit-flex-wrap: wrap;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;
	-webkit-box-pack: justify;
	-moz-box-pack: justify;
	-webkit-justify-content: space-around;
	-ms-flex-pack: distribute;
	justify-content: space-around;
	-webkit-align-content: stretch;
	-ms-flex-line-pack: stretch;
	align-content: stretch;
	-webkit-box-align: stretch;
	-moz-box-align: stretch;
	-webkit-align-items: stretch;
	-ms-flex-align: stretch;
	align-items: stretch;
	width: 100%;
	box-sizing: border-box;
	flex-wrap: nowrap;
}

#spMainContainer .spPluginSection {
	width: 100%;
	height: auto;
}

#spMainContainer .spPluginSection .spButton{
	font-size: 70%;
	color: <?php echo ($contentFontColor); ?>;
	border: none;
}

#spMainContainer .spPluginSection .spButton:hover{
	font-size: 70%;
	color: <?php echo ($contentFontColor); ?>;
	border: none;
}

#spMainContainer .spPostSignatureSection {
	-moz-box-ordinal-group: 1;
	order: 0;
	-moz-box-flex: 0;
	flex: 0 0 auto;
	align-self: stretch;
	-webkit-align-self: stretch;
}

#spMainContainer .spPostUserSignature {
	font-size: 80%;
	background: <?php echo($postSectionBackGroundColor); ?>;
	border-top: <?php echo($dottedBorder); ?>;
	padding: 20px 5px;
	margin: 0;
	width: auto;
	text-align: center;
}

#spMainContainer .spacer {
	display: flex;
	display: -webkit-flex;
	flex:1;
	height: 5px;
	width: 100%;
	border: 1px solid blue;
}

#spMainContainer .spTopicViewSection .spPostContentSection .spPostIndexAttachments {
	margin-left: 20px;
}

#spMainContainer .spForumTopicSection .spButton,
#spMainContainer .spForumTopicSection .spButton:hover{
	border: none;
}

#spMainContainer .spForumTopicSection .spInRowPageLinks {
	margin: 5px 0 0 5px;
}

#spMainContainer .spForumTopicSection .spInRowPostLink img{
	margin: 4px 2px 0 0;
	max-height: 13px;
	max-width: 9px;
	min-height: 13px;
	min-width: 9px;
	opacity: 1;
}

#spMainContainer .spForumTopicSection .spInRowLabel {
	font-size: 75%;
}

/*--------------------------------------------------------------------------- POST EDITOR */

#spMainContainer #spPostForm {
	padding: 0 0 0px;
	color: <?php echo($contentFontColor); ?>;
	font-size: 80%;
	width: 100%;
}

#spMainContainer #spPostForm .spForm {
	padding: 0px;
	background: none;
}

#spMainContainer #spPostForm .spEditorFieldset {
	margin: 0px;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	padding: 5px;
}

#spMainContainer #spPostForm .spEditorFieldset legend{
	color: <?php echo($titleFontColor); ?>;
	margin: 0;
	padding: 0;
	width: 100%;
	text-align: center;
}

#spMainContainer #spPostForm .spEditorSection {
	background: transparent;
	color: <?php echo($contentFontColor); ?>;
	padding: 10px 10px;
	margin: 0px 0 0;
	width: auto;
}

#spMainContainer #spPostForm .spEditorSectionLeft {
	float: left;
	width: 45%;
}

#spMainContainer #spPostForm .spEditorSectionRight {
	float: right;
	width: 45%;
}

#spMainContainer #spPostForm .spEditorHeading {
	padding: 0 0 3px;
	margin: 0 0 7px;
	text-align: center;
	font-weight: <?php echo($titleFontWeight); ?>;
	color: <?php echo ($titleFontColor); ?>;
}

#spMainContainer #spPostForm .spEditorSection .spEditorMessage {
	margin: 5px 5px 15px;
	padding: 10px;
	text-align: center;
}

#spMainContainer #spPostForm .spEditorSection .spEditorMessage p {
	color: <?php echo($titleFontColor); ?>;
}

#spMainContainer #spPostForm .spEditor .spEditorTitle {
	color: <?php echo($titleFontColor); ?>;
	width: 98%;
	font-weight: bold;
	padding: 5px 0;
}

#spMainContainer #spPostForm .spEditor .spEditorTitle input {
	padding: 0 0 0 5px;
	width: 100%;
	margin: 5px 0;
	box-sizing: border-box;
}

#spMainContainer #spPostForm .spEditor p.spLabelSmall {
	padding: 10px 5px;
	text-align: center;
	font-size: 100%;
}

#spMainContainer #spPostForm #spEditorContent {
	clear: both;
	margin: 0;
	padding: 0 10px;
}

#spMainContainer #spPostForm .spEditorSmileys {
	padding: 0 10px;
}

#spMainContainer #spPostForm .spEditorSmileys img.spSmiley {
	padding: 3px 1px;
	cursor: pointer;
}

#spMainContainer #spPostForm .spEditorSmileys img.spSmiley:active {
	border: 1px solid transparent;
}

#spMainContainer #spPostForm .spEditorSubmit {
	text-align: center;
	padding: 15px 0 5px;
	clear: both;
}

#spMainContainer #spPostForm .spEditorSubmit .spEditorTitle {
	text-align: center;
	padding-top: 0;
}

#spMainContainer #spPostForm .spEditor .spEditorSubmit .spEditorSpam {
	padding: 0 0 5px;
}

#spMainContainer #spPostForm .spEditor .spEditorSubmit .spEditorSpam input {
	text-align: center;
}

#spMainContainer #spPostForm .spEditorSubmitButton {
	margin: 0 3px 0 0;
	padding: 0;
}

#spMainContainer #spPostForm .spEditorSubmitButton .spSubmit,
#spMainContainer #spPostForm .spEditorSection .spSubmit {
	margin: 0 15px;
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
	font-size: 100%;
}

#spMainContainer #spPostForm .spEditorSubmitButton .spSubmit:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer #spPostForm .spSubmit {
	margin: 0 3px;
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
}

#spMainContainer #spPostForm .spSubmit:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spEditor .spPtEditor {
	width: 98%;
	margin: 0 5px;
}

#spMainContainer #spProfileForumSubscriptions label,
#spMainContainer .spProfileManageWatches label.list,
#spMainContainer .spProfileTopicSubscriptions label.list {
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer #spRedirectDiv fieldset {
	background: none;
	padding: 10px 5px 5px 5px;
}

#spMainContainer #spRedirectDiv fieldset legend {
	display: none;
}

#spEditorContent .mce-panel {
	border: 0px solid #9E9E9E;
	background-color: transparent;
	background-image: linear-gradient(to bottom, #FDFDFD, #DDD);
	background-repeat: repeat-x;
}

#spMainContainer #spPostForm .spEditor .spEditorSection label.spCheckbox {
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer #spPostForm .spEditor .spEditorSection label.spCheckbox:focus {
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
}

#spMainContainer #spHiddenTimestamp {
	display: flex;
	display: -webkit-flex;
	display: none;
	text-align: center;
	font-size: 100%;
	line-height: 31px;
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer #spHiddenTimestamp select.spControl {
	margin-left: 15%;
}

#spMainContainer #spHiddenTimestamp .spControl	{
	text-align: center;
}

/*--------------------------------------------------------------------------- MEMBERS LIST */

#spMainContainer .spMemberListControlLeft {
	width: auto;
	float: left;
}

#spMainContainer .spMemberListControlRight {
	width: auto;
	padding: 10px;
	float: Right;
}

#spMainContainer #msearch {
	font-size: 80%;
}

#spMainContainer #membersearch,
#spMainContainer #allmembers {
	color: <?php echo($linkFontColor); ?>;
	margin: 0 0 0 10px;
	font-size: <?php echo($linkFontSize); ?>;
	border: none;
	font-weight: bold;
}

#spMainContainer #membersearch:hover,
#spMainContainer #allmembers:hover {
	color: <?php echo($linkFontColorHover); ?>;
	background: none;
}

#spMainContainer #spMembersListSearchForm .spForm fieldset {
	background: none;
	padding: 0px;
}

#spMainContainer #spMembersListSearchForm fieldset legend{
	display: none
}

#spMainContainer #spMembersListSearchForm .spForm {
	background: <?php echo($titleContainerBackground); ?>;
	padding: 10px;
	border: none;
	border-bottom: none;
}

#spMainContainer #spMembersListSearchForm .spForm input.spControl {
	background: <?php echo ($inputFieldBackground); ?>;
	height: 27px;
	margin: 0;
	padding: 0 0 0 5px;
}

#spMainContainer #spMembersListSearchForm .spForm input.spControl:hover {
	background: <?php echo ($inputFieldBackgroundHover); ?>;
}

#spMainContainer #spMembersListSearchForm .spSearchDetails {
	display: flex;
	font-size: 80%;
	margin: 5px 0 0 0;
}

#spMainContainer .spMemberListControl {
	background: <?php echo($titleContainerBackground); ?>;
	padding: 0;
}

#spMainContainer p.spSearchDetails {
	width: 100%;
	margin-top: 10px;
}

#spMainContainer .spSearchMember input.spControl {
	height: 27px;
	line-height: 1em;
	margin: 10px;
}

#spMainContainer .spUsergroupSelect select {
	font-size: 80%;
	border: none;
	color: <?php echo ($linkFontColor); ?>;
	height: 27px;
	line-height: 1.2em;
	vertical-align: middle;
	margin: 0;
	padding: 1px;
	background: <?php echo ($inputFieldBackground); ?>;
}

#spMainContainer .spUsergroupSelect select:hover,
#spMainContainer .spUsergroupSelect select:focus {
	background: <?php echo ($inputFieldBackgroundHover); ?>;
}

#spMainContainer .spUsergroupSelect option {
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spMemberGroupsSection .spMemberGroupsHeader {
	color: <?php echo($titleFontColor); ?>;
	background: <?php echo($titleColumnsBackground); ?>;
	width: 100%;
	padding: 10px;
	margin: 0;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	text-align: center;
}

#spMainContainer .spMemberGroupsSection .spMemberGroupsHeader .spHeaderName {
	padding: 0;
	line-height: 1.1em;
}

#spMainContainer .spMemberGroupsSection .spMemberGroupsHeader .spHeaderDescription {
	float: none;
	line-height: 1.1em;
	padding: 0;
}

#spMainContainer .spMemberListSection.spOdd {
	color: <?php echo($indexRowOddColor); ?>;
	background: <?php echo($indexRowOddBackGround); ?>;
	border: <?php echo($indexRowOddBorder); ?>;
}

#spMainContainer .spMemberListSection.spOdd:hover {
	color: <?php echo($indexRowOddColorHover); ?>;
	background: <?php echo($indexRowOddBackGroundHover); ?>;
	border: <?php echo($indexRowOddBorderHover); ?>;
}

#spMainContainer .spMemberListSection.spEven {
	background: <?php echo($indexRowEvenBackGround); ?>;
	border: <?php echo($indexRowEvenBorder); ?>;
}

#spMainContainer .spMemberListSection.spEven:hover {
	color: <?php echo($indexRowEvenColorHover); ?>;
	background: <?php echo($indexRowEvenBackGroundHover); ?>;
	border: <?php echo($indexRowEvenBorderHover); ?>;
}

#spMainContainer .spMemberListSection .spColumnSection img {
	max-height: 110px;
	max-width: 110px;
}

#spMainContainer .spMemberListSection a {
	font-size: 95%;
}

#spMainContainer .spMemberListSection {
	font-size: 95%;
	padding: 5px;
	display: flex;
	-moz-box-flex: 1;
	display: -webkit-flex;
}

#spMainContainer .spMemberListSection .spColumnSection{
	text-align: center;
	display: flex;
	align-content: center;
	justify-content: flex-start;
	flex-direction: column;
}

#spMainContainer .spMemberListSection .spRowName a{
	line-height: 1.0em;
	font-weight: bold !important;
}

#spMainContainer .spMemberListSection .spInRowDate {
	font-size: 80%;
}

/*--------------------------------------------------------------------------- FORUM FOOTER */

#spMainContainer .spFootContainer {
	background: <?php echo($footerContainerBackground); ?>;
	border: <?php echo($footerContainerBorder); ?>;
	padding: 0;
}

#spMainContainer .spFootContainer a.spButton span{
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spFootContainer .spGoToTop {
	padding: 0;
	margin: 0 -2px 0 0;
}

#spMainContainer .spFootContainer .spLink {
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spFootContainer .spButton a{
	border: none;
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spFootContainer label {
	color: <?php echo ($contentFontColor); ?>;
	font-weight: bold;
	font-size: 100%;
}

#spMainContainer .spFootContainer .spPlainSection {
	padding: 3px 0 0 0;
}

#spMainContainer .spFootContainer .spPlainSection.spCenter {
	border-top: none;
	padding: 3px 0 0 0;
	margin: 15px 0 0 0;
}

/*--------------------------------------------------------------------------- PAGELINKS */

#spMainContainer .spPageLinks {
	font-size: <?php echo($linkFontSize); ?>;
	color: <?php echo($linkFontColor); ?>;
	margin: 3px;
	padding: 8px 0 4px 0;
}

#spMainContainer .spPageLinksBottomSection {
	margin: 10px;
}

#spMainContainer .spPageLinksBottomSection .spButton {
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spPageLinksBottomSection .spButton:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spPageLinksBottom {
	box-sizing: border-box;
}

#spMainContainer a.spPageLinks {
	margin: 0px 2px;
	padding: 2px 6px;
	background: <?php echo($titleContainerBackground); ?>;
	color: <?php echo($secLinkFontColor); ?>;
	transition: none;
	font-weight: bold;
}

#spMainContainer a.spPageLinks:hover {
	background: none;
	color: <?php echo($secLinkFontColorHover); ?>;
	border: <?php echo($solidBorderLightThick); ?>;
	margin: 0px 2px;
	padding: 0px 4px;
	transition: none;
}

#spMainContainer a.spPageLinks.spCurrent {
	font-weight: bold;
	background: none;
	color: <?php echo($secLinkFontColorHover); ?>;
	border: <?php echo($solidBorderLightThick); ?>;
	margin: 0px 2px;
	padding: 0px 4px;
}

#spMainContainer .spPageLinks.spPageLinksBottom {
	padding: 7px 4px 0px 4px;
	margin: 0 0 -1px 0;
}

#spMainContainer .spFootContainer .spPageLinksBottom {
	background: none;
}

#spMainContainer .spFootContainer .spPageLinksBottom .spIcon {
	margin: 3px 6px;
	padding: 0;
}

#spMainContainer .spPageLinksBottom .spButton{
	margin: 3px 0px 3px 3px;
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spPageLinksBottom .spButton span{
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spPageLinksBottom	 .spButton:hover{
	background: none;
}

#spMainContainer .spPageLinksBottom	 .spButton.spIcon:hover{
	background: none;
}

#spMainContainer .spPageLinks a.spPageLinks.spIcon {
	border: none;
	padding: 0;
	margin: 0 0 5px 0;
}

#spMainContainer .spPageLinks img {
	border: none;
	padding: 0;
	margin: 0 0 4px 0;
}

#spMainContainer .spPageLinks .spHSpacer {
	color: <?php echo($contentFontColor); ?>;
	margin: 0 0.2em;
	padding: 3px 0px 2px 0px;
}

/*--------------------------------------------------------------------------- TIMEZONE SECTION */

#spMainContainer .spActionsBar {
	padding: 5px 10px;
	width: 100%;
	background: <?php echo($titleColumnsBackground); ?>;
	box-sizing: border-box;
}

#spMainContainer .spActionsBar .spFootButton,
#spMainContainer .spActionsBar .spFootButton a {
	font-weight: bold;
	color: <?php echo($secLinkFontColor); ?>;
	padding: 0;
	margin: 0 0 0 15px;
	font-size: 80%;
	line-height: inherit;
	cursor: pointer;
}

#spMainContainer .spActionsBar .spFootButton span {
	font-size: 100%;
	font-weight: bold;
}

#spMainContainer .spActionsBar .spFootButton:hover {
	color: <?php echo($secLinkFontColorHover); ?>;
}

#spMainContainer .spTimeZoneBar {
	margin-top: -5px;
	width: 100%;
	background: <?php echo($titleContainerBackground); ?>;
	padding: 6px 8px 6px 8px;
	box-sizing: border-box;
}

#spMainContainer .spForumTimeZone,
#spMainContainer .spUserTimeZone {
	width: auto;
	padding: 0 8px 0 0;
	border-bottom: none;
	font-size: <?php echo($contentFontSize); ?>;
	color: <?php echo ($contentFontColor); ?>;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#spMainContainer .spForumTimeZone {
	text-align: left;
}

#spMainContainer .spUserTimeZone {
	text-align: left;
}

#spMainContainer .spUserTimeZone span,
#spMainContainer .spForumTimeZone span {
	font-weight: <?php echo($subTitleFontWeight); ?>;
	color: <?php echo ($subTitleFontColor); ?>;
}

#spMainContainer .spFootContainer #sp_OpenCloseControl {
	font-size: 80%;
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
	line-height: 1.3em;
	margin: 0 5px 0 0;
	cursor: pointer;
}

#spMainContainer .spFootContainer #sp_OpenCloseControl:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

/*--------------------------------------------------------------------------- STATS SECTION */

#spMainContainer .spStatsSection {
	color: <?php echo($contentFontColor); ?>;
	padding: 5px 0 0 0;
	margin: 0;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#spMainContainer .spStatsSection span {
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spStatsSection .spLink {
	color: <?php echo ($linkFontColor) ?>;
}

#spMainContainer .spStatsSection .spLink:hover {
	color: <?php echo ($linkFontColorHover); ?>;
}

#spMainContainer .spUserGroupList {
	font-size: 80%;
	margin-top: 10px;
	padding: 5px 0 5px 7px;
	width: 100%;
}

#spMainContainer .spUserGroupListTitle span{
	font-weight: bold;
	padding: 8px 0px 5px;
}

#spMainContainer .spAllRSSButton {
	font-weight: bold;
	margin: 0 0 0 5px;
	font-size: 80%;
	line-height: 1.3em;
}

#spMainContainer .spStatsSection p {
	margin: 0;
	padding: 0;
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spStatsSection .spColumnSection {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#spMainContainer .spForumStatsTitle,
#spMainContainer .spMembershipStatsTitle,
#spMainContainer .spTopPosterStatsTitle,
#spMainContainer .spMostOnline span,
#spMainContainer .spCurrentBrowsing span {
	padding: 0 0 5px 0;
	font-weight: bold;
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer #spForumStatsHolder {
	margin: 0 0 10px 0;
}

#spMainContainer .spForumStats .spMembershipStatsTitle,
#spMainContainer .spForumStats .spTopPosterStatsTitle,
#spMainContainer .spForumStats .spForumStatsTitle {
	color: <?php echo ($contentFontColor); ?>;
}

#spMainContainer .spCurrentOnline span,
#spMainContainer .spDeviceStats span {
	font-weight: bold;
	color: <?php echo ($contentFontColor) ?>;
}

#spMainContainer .spCurrentOnline span.spOnlineUser {
	font-weight: normal;
}

#spMainContainer .spCurrentOnline .spProfilePopupLink,
#spMainContainer .spCurrentBrowsing .spProfilePopupLink {
	font-weight: normal;
}

#spMainContainer span.spNewMembersTitle,
#spMainContainer span.spModeratorsTitle,
#spMainContainer span.spAdministratorsTitle {
	font-weight: bold;
	color: <?php echo ($contentFontColor) ?>;
}

#spMainContainer .spAdministratorsTitle {
	text-align: left;
}

#spMainContainer .spAdminsMods {
	font-size: 100%;
	margin: 0 0 0 0;
	padding: 0 0 0 2px;
	color: <?php echo ($contentFontColor) ?>;
}

#spMainContainer .spOnlineStats {
	width: auto;
}

#spMainContainer .spOnlineStats,
#spMainContainer .spForumStats,
#spMainContainer .spMembershipStats,
#spMainContainer .spTopPosterStats,
#spMainContainer .spNewMembers,
#spMainContainer .spModerators,
#spMainContainer .spAdministrators {
	margin: 0 0 0 0;
	font-size: 80%;
	padding: 0 0 0 2px;
	color: <?php echo ($contentFontColor) ?>;
}

#spMainContainer .spNewMembers {
	width: 100%;
	padding: 4px 0;
	margin: 5px 0 0 0;
}

#spMainContainer .spNewMembers {
	margin-top: 10px;
}

#spMainContainer .spOnlineStats p,
#spMainContainer .spForumStats p,
#spMainContainer .spMembershipStats p,
#spMainContainer .spTopPosterStats p,
#spMainContainer .spNewMembers p,
#spMainContainer .spModerators p,
#spMainContainer .spAdministrators p {
	margin: 0 0 0 5px;
	padding: 0;
}

#spMainContainer .spTopPosterStats p {
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spOnlineStats p {
	margin-bottom: 5px;
}

/*--------------------------------------------------------------------------- ACKNOWLEDGEMENTS */

#spMainContainer .spFootInfo {
	display: flex;
	justify-content: center;
	padding: 10px 0;
}

#spMainContainer #spAck {
	padding: 0 75px;
	color: <?php echo ($subTitleFontColor); ?>;
	font-weight: <?php echo($subTitleFontWeight); ?>;
	font-size: 80%;
	display: inline;
	padding: 0 0 0 15px;
	line-height: 0em;
}

#spMainContainer #spAck a:link {
	margin: 0 5px 0 0;
}

#spMainContainer #spPolicyDoc,
#spMainContainer #spPrivacyDoc {
	line-height: 0;
}

#spMainContainer #spPolicyDoc a:link,
#spMainContainer #spPrivacyDoc a:link {
	color: <?php echo ($subTitleFontColor); ?>;
	margin: 0 0 0 5px
}

#spMainContainer #spPolicyDoc a:link:visited,
#spMainContainer #spPrivacyDoc a:link:visited,
#spMainContainer #spPolicyDoc a:link:active,
#spMainContainer #spPrivacyDoc a:link:active,
#spMainContainer #spPolicyDoc a:active,
#spMainContainer #spPrivacyDoc a:active,
#spMainContainer #spPolicyDoc a:visited,
#spMainContainer #spPrivacyDoc a:visited {
	color: <?php echo ($subTitleFontColor); ?>;
}

/*--------------------------------------------------------------------------- POPUPS / UI-DIALOG */

.div.modal-bg {
	background: black;
	position:fixed;
	top:0; left:0;
	width:100%;
	height:100%;
	z-index:10;
}

.ui-dialog {
	position: fixed !important;
	padding: 0;
	width: 300px;
	background: <?php echo($popupBackground); ?>;
	border: <?php echo($solidBorderLight); ?>;
	color: <?php echo($contentFontColor); ?>;
	z-index: 10;
	box-shadow: 8px 8px 0px rgba(0, 0, 0, 0.2);
	margin: 30px 0 0 0;
	outline: none;
}

.ui-dialog * {
	box-sizing: content-box;
}

.ui-dialog .ui-dialog-titlebar {
	background: <?php echo($popupBackground); ?>;
	padding: 10px;
	position: relative;
}

.ui-dialog .ui-dialog-title {
	color: <?php echo($popupHeaderColor); ?>;
	float: left;
	line-height: 18px;
	font-size: 90%;
	font-weight: bold;
	padding: 0 0 10px 0;
	width: 100%;
}

.ui-dialog .ui-dialog-titlebar-close:before {
	content: " ";
}

.ui-dialog-content {
	overflow: auto;
	padding: 20px;
	background: <?php echo($whiteContainer); ?>;
}

.ui-dialog .ui-dialog-titlebar-close {
	position: absolute;
	right: 9px;
	top: 19px;
	width: 18px;
	margin: -10px 0 0 0;
	padding: 0;
	height: 18px;
	background: #E85454;
	border-radius: 50%;
	background-image: <?php echo($ImageClose); ?> !important;
}

.ui-dialog .ui-dialog-titlebar-close span {
	display: block;
	margin: 1px;
}

.ui-dialog .ui-dialog-titlebar-close:hover,
#spMainContainer .ui-dialog .ui-dialog-titlebar-close:focus {
	padding: 0;
	background: #D80000;
}

.ui-dialog .ui-dialog-titlebar-close:focus,
#spMainContainer .ui-dialog .ui-dialog-titlebar-close:focus {
	padding: 0;
	background: <?php echo($popupBackground); ?>;
}


#dialog.ui-dialog-content a:hover {
	color: <?php echo($contentFontColor); ?>;
}

#dialog.ui-dialog-content {
	background: <?php echo($whiteContainer); ?>;
}

#dialog.ui-dialog-content,
#dialog.ui-dialog-content p,
#dialog.ui-dialog-content td {
	color: <?php echo($contentFontColor); ?>;
	font-weight: normal;
	border: 0;
	zoom: 1;
	margin: 0;
	clear: both;
}

#dialog.ui-dialog-content p {
	background: transparent;
	font-size: 80%;
}

#dialog.ui-dialog-content .spSubmit {
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
	line-height: 1.3em;
	margin: 0 20px;
	font-size: 100%;
}

#dialog.ui-dialog-content .spSubmit:hover {
	color: <?php echo($linkFontColorHover); ?>;
	font-weight: bold;
}

.ui-dialog .ui-dialog-buttonpane {
	text-align: left;
	border-width: 1px 0 0 0;
	margin: .5em 0 0 0;
	padding: .3em 1em .5em .4em;
}

.ui-dialog .ui-dialog-buttonpane button {
	float: right;
	margin: .5em .4em .5em 0;
	cursor: pointer;
	padding: .2em .6em .3em .6em;
	line-height: 1.4em;
	width: auto;
	overflow: visible;
}

.ui-dialog .ui-resizable-se {
	width: 11px;
	height: 11px;
	right: 3px !important;
	bottom: 3px;
	float: right;
	background-image: <?php echo($ImageResize); ?>;
}

.ui-draggable .ui-dialog-titlebar {
	cursor: move;
}

.ui-widget-header {
	background: <?php echo($greyContainer); ?>;
	color: <?php echo($contentFontColor); ?>;
	height: auto;
}

.ui-widget-overlay {
	background: <?php echo($Imagesp_ImageOverlay); ?> !important;
	opacity: .50 !important;
	filter: Alpha(Opacity=50) !important;
	z-index: 10 !important;
}

.ui-dialog .ui-dialog-content img.spPopupImg {
	width: 99%;
	height: 99%;
	margin: 1px 0 0 1px;
}

#dialog.ui-dialog-content .spTopicListSection .spListLabel {
	font-size: 75%;
}

#dialog.ui-dialog-content .spTopicListSection .spListViewSection {
	font-size: 75%;
}

.ui-dialog form#pagejump {
	text-align: center;
}

.ui-dialog form#pagejump label {
	color: <?php echo($contentFontColor); ?>;
	font-size: <?php echo($contentFontSize); ?>;
	font-family: <?php echo($baseFontFamily); ?>;
}

.ui-dialog form#pagejump input {
	padding: 5px;
	border: none;
	border-radius: 0px;
	background: none;
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
	margin: 10px 0 0 0;
}

.ui-dialog form#pagejump input:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

.ui-dialog form#pagejump input.spSubmit {
	font-size: <?php echo($linkFontSize); ?>;
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
	border: none;
	background: <?php echo($inputFieldBackground); ?>;
	display: block;
	margin-top: 15px;
	margin-left: auto;
	margin-right: auto;
	width: 30%
}

.ui-dialog form#pagejump input.spSubmit:hover {
	border: none;
	background: <?php echo($inputFieldBackground); ?>;
}

#spMainContainer.spForumToolsPopup form label {
	font-size: 80%;
	margin-top: 10px;
}

#spMainContainer.spForumToolsPopup form select.spSelect {
	border: <?php echo($solidBorder); ?>;
	width: 80%;
	font-size: 80%;
	height: auto;
}

#spMainContainer.spForumToolsPopup form select.spSelect optgroup {
	border: none;
}

#spMainContainer.spForumToolsPopup fieldset {
	background: none;
	color: <?php echo($contentFontColor); ?>;
	font-size: 90%;
}

#spMainContainer.spForumToolsPopup fieldset legend{
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer.spForumToolsPopup .spHeaderName {
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer form#printopts span {
	font-size: 80%;
}

#spMainContainer form#printopts .spSubmit {
	font-size: 80%;
}

#spMainContainer form#printopts input.spControl {
	font-size: 100%;
}

/*--------------------------------------------------------------------------- PROFILE POPUP */

#spMainContainer .spProfileShowSection {
	color: <?php echo($plainSectionColor); ?>;
	border: <?php echo($plainSectionBorder); ?>;
	width: 80%;
}

#spMainContainer .spProfileShowBasicSection,
#spMainContainer .spProfileShowDetailsSection,
#spMainContainer .spProfileShowPhotosSection,
#spMainContainer .spProfileShowSignatureSection {
	color: <?php echo($contentFontColor); ?>;
	width: auto;
	margin: 25px 10px;
}

#spMainContainer .spProfileShowBasicSection p {
	font-size: 80%;
}

#spMainContainer .spProfileShowBasicSection .spProfileShowAvatarSection .spAvatar img {
	margin-left: auto;
	margin-right: auto;
}

#spMainContainer .spProfileShowDetailsSection .spButton:hover {
	opacity: 1;
}

#spMainContainer .spProfileShowDetailsSection .spColumnSection {
	margin: 1px;
}

#spMainContainer .spProfileShowBasicSection .spColumnSection {
	margin: 1px;
}

#spMainContainer .spProfileShowBasicSection .spForumRank,
#spMainContainer .spProfileShowBasicSection .spSpecialRank {
	background: <?php echo ($plainSectionBackGround); ?>;
}

#spMainContainer .spProfileShowInfoSection {
	min-width: 49%;
	color: <?php echo($plainSectionColor); ?>;
	background: <?php echo ($plainSectionBackGround); ?>;
	border: <?php echo($plainSectionBorder); ?>;
	margin: 0px;
}

#spMainContainer .spProfileShowAvatarSection {
	width: 49%;
}


#spMainContainer .spProfileShowIdentitiesSection {
	width: 49%;
}

#spMainContainer .spProfileShowStatsSection {
	width: 49%;
}

#spMainContainer .spProfileShowStatsSection .spSubmit {
	background: transparent;
	font-size: 70%;
	border: <?php echo($solidBorder); ?>;
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spProfileShowStatsSection .spSubmit:hover {
	background: transparent;
	border: <?php echo($solidBorder); ?>;
}


#spMainContainer .spProfileShowPhotosSection .spImg {
	border: <?php echo($solidBorderLight); ?>;
	padding: 4px;
}

#spMainContainer .spProfileShowHeader {
	font-size: 100%;
	font-weight: bold;
	margin: 0 5px 0 0;
	color: <?php echo($contentFontColor); ?>;
	line-height: 0em;
}

#spMainContainer span.spProfileShowHeaderEdit {
	font-size: <?php echo($linkFontSize); ?>;
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer span.spProfileShowHeaderEdit:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer img.spOnlineStatus {
	padding-left: 5px;
}

#spMainContainer .spProfileShowSection hr {
	background: <?php echo($greyContainer); ?>;
	color: <?php echo($contentFontColor); ?>;
	border: none;
	height: 1px;
	margin: 5px 0 10px;
}

#spMainContainer .spProfileShowSection .spProfileLabel,
#spMainContainer .spProfileShowSection p.spProfileLabel {
	font-size: 80%;
	line-height: 1em;
	margin: 0;
	color: <?php echo($contentFontColor); ?>;
	padding: 0px 2px 4px 2px;
}

#spMainContainer .spProfileShowSection .spColumnSection	 {
	margin: 0;
}

#spMainContainer .spProfileShowSection .spProfileLabel p {
	font-size: 100%;
}

#spMainContainer .spProfileShowSection .spButton {
	border: none;
	padding: 0;
	margin: 0;
	font-size: 80%;
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
	line-height: 1.1em;
}

#spMainContainer .spProfileShowSection .spPmButton {
	border: none;
	padding: 0;
	margin: 0;
	font-size: 100%;
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spProfileShowSection .spPmButton:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spProfileShowSection .spButton:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spProfileShowSection .spProfileShowHeaderSection,
#spMainContainer .spProfileShowSection .spFlexSection {
	display: flex;
	justify-content: center;
}

#spMainContainer .spProfileShowSection .spPostedToSubmit {
	text-align: center;
}

#spMainContainer .spProfileShowSection .spPostedToSubmit .spSubmit {
	font-size: 75%;
	line-height: 1.2em;
	padding: 0 0 0 15px;
	min-height: 0px;
	border: none;
	background: none;
	color: <?php echo($linkFontColor); ?>;
	float: left;
	font-weight: bold;
}

#spMainContainer .spProfileShowSection .spPostedToSubmit .spSubmit:hover {
	border: none;
	background: none;
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spProfileShowSection .spButton:hover {
	border: none;
	background: none;
}

#spMainContainer .spProfileShowSection p.spProfileTitle {
	font-weight: bold;
	color: <?php echo($contentFontColor); ?>;
	font-size: 100%;
}

/*--------------------------------------------------------------------------- PROFILE PAGE */

#spMainContainer .spBodyContainer .spProfileShowSection {
	width: 100%;
}

#spMainContainer .spBodyContainer .spProfileShowSection .spProfileShowHeaderSection {
	padding: 10px 0;
	background: <?php echo($titleContainerBackground); ?>;
}

#spMainContainer .spProfileShowSection .spFlexSection .spLabel {
	font-size: 80%;
	clear: both;
}

#spMainContainer .spProfileShowSection .spPostedToSubmitInline {
	text-align: center;
}

#spMainContainer .spProfileShowSection .spPostedToSubmitInline .spSubmit {
	font-size: 75%;
	line-height: 1.2em;
	padding: 2px;
	min-height: 0px;
	border: none;
	background: none;
	color: <?php echo($linkFontColor); ?>;
	float: left;
	font-weight: bold;
	margin-left: 7px;
}

#spMainContainer .spProfileShowSection .spPostedToSubmitInline .spSubmit:hover {
	border: none;
	background: none;
	color: <?php echo($linkFontColorHover); ?>;
}

/*--------------------------------------------------------------------------- PROFILE OPTIONS PAGES */

#spMainContainer ul#spProfileTabs {
	margin: 0 0 -1px 0;
	padding: 10px 0;
	height: auto;
	background: <?php echo($titleContainerBackground); ?>;
	display: flex;
	display: -webkit-flex;
	justify-content: space-around;
	-webkit-justify-content: space-around;
	flex-wrap: wrap;
	-webkit-flex-wrap: wrap;
}

#spMainContainer ul#spProfileTabs li {
	float: left;
	padding: 0;
	margin: 0;
	list-style: none;
}

#spMainContainer ul#spProfileTabs a {
	float: left;
	font-size: 80%;
	font-weight: bold;
	display: block;
	text-decoration: none;
	color: <?php echo($linkFontColor); ?>;
	position: relative;
	outline: 0;
}

#spMainContainer ul#spProfileTabs a:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer ul#spProfileTabs a.current {
	color: <?php echo($linkFontColorHover); ?>;
	font-weight: bold;
}


#spMainContainer #spProfileMenu {
	float: left;
	margin: 0;
	width: 20%;
	clear: both;
}

#spMainContainer ul.spProfileMenuGroup {
	list-style: none;
	margin: 0;
	padding: 0;
}

#spMainContainer li.spProfileMenuItem {
	width: 98%;
	font-size: 90%;
	margin: 0 0 10px;
	color: <?php echo($secLinkFontColor); ?>;
	list-style: none;
	float: left;
}

#spMainContainer li.spProfileMenuItem:hover {
	color: <?php echo($spButtonFontColorHover); ?>;
}


#spMainContainer li.spProfileMenuItem.current a {
	color: <?php echo($spButtonFontColorHover); ?>;
	font-weight: bold;
}

#spMainContainer li.spProfileMenuItem a {
	padding: 0 5px;
	margin: 1px 0;
	color: <?php echo($contentFontColor); ?>;
	float: left;
	width: 100%;
}

#spMainContainer li.spProfileMenuItem:hover a {
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer #spProfileContent {
	color: <?php echo($contentFontColor); ?>;
	font-size: 90%;
}

#spMainContainer #spProfileHeader small {
	font-size: 100%;
	font-weight: bold;
	text-align: center;
}

#spMainContainer #spProfileHeader {
	width: 100%;
	padding: 10px 0;
	font-size: 100%;
	font-weight: bold;
	text-align: center;
	margin: 0 0 10px;
	background: <?php echo($headerContainerBackground); ?>;
}

#spMainContainer #spProfileData {
	float: left;
	margin: 1px 0 0 0;
	padding: 0;
	width: 80%;
}

#spMainContainer #spProfileFormPanel {
	padding: 0px 0px 0px 10px;
	font-size: 80%;
	border-left: <?php echo($solidBorder); ?>;
}

#spMainContainer #spProfileFormPanel hr {
	color: <?php echo($headerContainerBackground); ?>;
}

#spMainContainer #spProfileFormPanel .spColumnSection {
	margin: 2px;
}

#spMainContainer #spProfileFormPanel fieldset{
	background: transparent;
}

#spMainContainer #spProfileFormPanel fieldset legend{
	color: <?php echo ($contentFontColor); ?>;
	font-size: 100%;
}

#spMainContainer #spProfileFormPanel fieldset img {
	max-height: 100px;
	max-width: 100px;
}

#spMainContainer #spAvatarUpload .spSubmit,
#spMainContainer .spProfileFormSubmit .spSubmit{
	min-height: 0;
	margin: 5px 10px 5px 0;
	line-height: 1.6em;
	color: <?php echo($linkFontColor); ?>;
	background: none;
	font-weight: bold;
	list-style: none;
	font-size: 100%;
}

#spMainContainer #spAvatarUpload .spSubmit:hover,
#spMainContainer .spProfileFormSubmit .spSubmit:hover{
	color: <?php echo($linkFontColorHover); ?>;
	background: none;
}

#spMainContainer #spProfileFormPanel .spProfileOverview .spProfileFormSubmit {
	text-align: left;
}

#spMainContainer #spProfileFormPanel .spProfileSignature .spProfileFormSubmit,
#spMainContainer #spProfileFormPanel .spProfileAvatar .spProfileFormSubmit {
	text-align: center;
}

#spMainContainer #spProfileFormPanel .spProfileSignature .spProfileFormSubmit input.spSubmit {
	margin: 10px 10px 0 10px;
}

#spMainContainer #spProfileFormPanel .spProfileFormSubmit {
	text-align: right;
}

#spMainContainer .spProfileSignature .spEditorSection.sp_file_uploader a {
	padding: 0;
	min-height: 0;
	margin: 10px;
	line-height: 1.6em;
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
	list-style: none;
}

#spMainContainer .spProfileSignature .spEditorSection.sp_file_uploader a:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spProfileLeftCol {
	clear: both;
	float: left;
	text-align: left;
	width: 35%;
}

#spMainContainer .spProfileSpacerCol {
	float: left;
	text-align: left;
	width: 1%;
}

#spMainContainer .spProfileRightCol {
	float: left;
	text-align: left;
	width: 52%;
}

#spMainContainer .spProfileLeftHalf {
	color: <?php echo($contentFontColor); ?>;
	float: left;
	text-align: left;
	width: 47%;
}

#spMainContainer .spProfileRightHalf {
	color: <?php echo($contentFontColor); ?>;
	float: left;
	text-align: left;
	width: 47%;
}

#spMainContainer #spProfileFormPanel .spColumnSection {
	display: inline;
}

#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection {
	display: flex;
	display: -webkit-flex;
	flex-direction: column;
	-webkit-flex-direction: column;
	padding: 10px 0 0 0;
}

#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection img.spHeaderName  {
	display: none;
}

#spMainContainer #spProfileFormPanel .spColumnSection.spProfilePermissionIcon {
	display: none;
}

#spMainContainer #spProfileFormPanel .spColumnSection.spProfilePermissionForum {
	margin: 10px;
	padding: 5px 5px 5px 15px;
	color: <?php echo($contentFontColor); ?>;
	border-left: <?php echo($thickBorder); ?>;
}

#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection .spGroupForumSection.spOdd,
#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection .spGroupForumSection.spEven {
	background: none;
}

#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection .spHeaderName {
	font-size: 100%;
	margin: 0 0 15px 0;
	padding: 15px 0 0 0;
}

#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection .spHeaderDescription {
	display: none;
}

#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection .spRowName {
	font-size: 100%;
}

#spMainContainer #spProfileFormPanel .spProfileUserPermissions.spListSection .spRowName:hover {
	color: <?php echo($linkFontColor); ?>;
}

/*---------------------------- PROFILE FORM ELEMENTS */

#spMainContainer p.spProfileLabel {
	margin: 5px 0 0 0;
	padding: 0;
	line-height: 1.6em;
}

#spMainContainer span.spProfileRadioLabel {
	line-height: 22px;
}

#spMainContainer .spProfileForm label.list {
	float: left;
	padding-top: 4px
}

#spMainContainer .spProfileForm textarea {
	width: 90%;
	background: <?php echo($inputFieldBackground); ?>;
	font-size: 100%;
}

#spMainContainer #spavpool {
	font-size: 100%;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer #spavpool:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spProfileLabel select.spControl {
	background: <?php echo($inputFieldBackground); ?>;
	height: 25px;
	padding: 1px;
	margin: 0 0 12px 0;
}

#spMainContainer .spProfileForm .spControl[disabled] {
	background: none;
	border: none;
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spProfileForm input {
	width: 90%;
	vertical-align: top;
	padding: 0 0 0 8px;
	line-height: 1.6em;
	margin: 1px 0px 5px;
	box-sizing: border-box;
	background: <?php echo($inputFieldBackground); ?>;
}

#spMainContainer .spProfileForm input.spSubmit {
	width: auto;
}

#spMainContainer #spProfileForumSubscriptions label {
	margin: 2px 0;
}

#spMainContainer .spProfileUsergroupsMemberships,
#spMainContainer .spProfileUsergroupsNonMemberships {
	color: <?php echo($subTitleFontColor); ?>;
	padding: 5px;
	margin-top: 20px;
}

#spMainContainer .spProfileUsergroupsMemberships .spHeaderName,
#spMainContainer .spProfileUsergroupsNonMemberships .spHeaderName {
	font-size: 100%;
}

#spMainContainer .spProfileUsergroupsMemberships .spHeaderDescription,
#spMainContainer .spProfileUsergroupsNonMemberships .spHeaderDescription {
	font-size: 85%;
}

#spMainContainer .spProfileUsergroupsNonMemberships {
	margin-top: 30px;
}

#spMainContainer .spProfileUsergroup.spOdd {
	margin: 10px 0;
	padding: 5px 5px 5px 15px;
	color: <?php echo($contentFontColor); ?>;
	border-left: <?php echo($thickBorder); ?>;
}

#spMainContainer .spProfileUsergroup.spEven {
	margin: 10px 0;
	padding: 5px 5px 5px 15px;
	color: <?php echo($contentFontColor); ?>;
	border-left: <?php echo($thickBorder); ?>;
}

#spMainContainer .spProfileUsergroup .spHeaderName {
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer .spProfileUsergroup .spHeaderDescription {
	color: <?php echo($contentFontColor); ?>;
	padding-left: 0px;
}

#spMainContainer .spProfileUsergroup .spColumnSection {
	float: left;
	width: 70%;
}

#spMainContainer .spProfileUsergroup .spProfileMembershipsLeave,
#spMainContainer .spProfileUsergroup .spProfileMembershipsJoin {
	padding-top: 20px;
	float: right;
	width: 30%;
}

#spMainContainer .spProfileMembershipsLeave .spInRowSubForums,
#spMainContainer .spProfileMembershipsJoin .spInRowLabel {
	text-align: center;
	margin-left: auto;
	margin-right: auto;
}

#spMainContainer .spProfileForm .spProfileUsergroup label.list {
	text-align: left;
}

#spMainContainer .spProfileUserPermissions .spColumnSection.spProfilePermissionIcon {
	width: 9%;
	float: left;
}

#spMainContainer .spProfileUserPermissions .spColumnSection.spProfilePermissionForum {
	width: 75%;
	float: left;
	padding: 0px 0px 0px 0px;
}

#spMainContainer #spProfileFormPanel .spColumnSection.spProfilePermissionButton {
	margin-left: auto;
}

#spMainContainer #spProfileFormPanel .spColumnSection.spProfilePermissionButton .spSubmit {
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
	font-size: 100%;
}

#spMainContainer .spProfileUserPermissions .spProfilePermission .spAuthCat {
	clear: both;
	font-weight: bold;
	padding: 15px 0;
}

#spMainContainer .spProfileUserPermissions .spProfilePermission .spColumnSection {
	width: 49%;
	float: left;
	font-size: 0.9em;
	height: auto;
	vertical-align: middle;
}

#spMainContainer .spProfileUserPermissions .spProfilePermission p {
	clear: both;
	text-align: right;
	padding: 15px 0;
}

#spMainContainer .spProfileUserPermissions .spHiddenSection.spProfilePermission {
	margin: -20px 0 0 10px;
	padding: 5px 0px 5px 15px;
	color: <?php echo($contentFontColor); ?>;
	border-left: <?php echo($thickBorder); ?>;
	width: auto;
}

#spMainContainer .spProfileUserPermissions .spProfilePermission .spSubmit {
	font-weight: bold;
	color: <?php echo($linkFontColor); ?>;
	font-size: 100%;
}

/*--------------------------------------------------------------------------- SEARCH RESULTS VIEW */

#spMainContainer #spSearchHeaderName.spMessage {
	color: <?php echo ($titleFontColor); ?>;
	background: <?php echo($titleContainerBackground); ?>;
	margin: 0;
	font-weight: bold;
	font-size: 80%;
	padding: 10px;
	box-sizing: border-box;
}

#spMainContainer .spPageLinksSearchView,
#spMainContainer .spPageLinksSearchViewBottom {
	color: <?php echo ($contentFontColor); ?>;
	border: <?php echo($titleContainerBorder); ?>;
}

/*--------------------------------------------------------------------------- TOOL TIPS */

.ttip {
	color: <?php echo($toolTipsColor); ?>;
	font-family: <?php echo($toolTipsFontFamily); ?>;
	font-size: <?php echo($toolTipsFontSize); ?>;
	line-height: 1.1em;
	background: <?php echo($toolTipsBackground); ?>;
	padding: 5px;
	position: absolute;
	z-index: 999999;
	max-width: 300px;
}

body .ttip {
	border: <?php echo($toolTipsBorder); ?>;
}

/*--------------------------------------------------------------------------- FORUM TOOLS */

#spMainContainer a.spToolsButton,
#spMainContainer .spToolsButton {
	width: auto;
	text-align: right;
	font-weight: bold;
	font-size: <?php echo($linkFontSize); ?>;
	outline-style: none;
	color: <?php echo($linkFontColor); ?>;
	background: none;
	cursor: pointer;
	margin-left: 10px;
}

#spMainContainer a.spToolsButton:hover,
#spMainContainer .spToolsButton:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer a.spToolsButtonTopic {
	margin: 3px 0 0 1px;
}

#spMainContainer a.spToolsButtonMobile {
	margin-left: auto;
	margin-top: -4px;
	font-weight: bold;
	font-size: <?php echo($linkFontSize); ?>;
	outline-style: none;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer a.spToolsButtonTopicMobile {
	margin: 0 3px 0 0;
	position: relative;
}

#spMainContainer .spToolsButton img {
	vertical-align: middle;
	margin: 0;
	padding: 0;
}

#spMainContainer .spTopicPostSection .spPostActionSection .spToolsButton img {
	max-width: 20px;
	max-height: 20px;
	margin: 0px 3px;
}

#spMainContainer.spForumToolsPopup {
	padding: 0;
}

#spMainContainer .spForumToolsPopup .spIcon {
	vertical-align: bottom;
	margin: 10px 10px 0 3px;
}

#spMainContainer #spMobilePanel .spForumToolsPopup .spIcon {
	margin: 8px 10px 2px 3px;
}

#spMainContainer .spForumToolsHeader {
	padding: 0;
	margin-bottom: 15px;
}

#spMainContainer .spForumToolsHeader .spForumToolsHeaderTitle {
	color: <?php echo($titleFontColor); ?>;
	font-weight: bold;
	padding: 0;
	text-align: left;
	width: 90%;
	font-size: <?php echo($titleFontSize); ?>;
}

#spMainContainer.spForumToolsPopup .spIcon {
	margin: 3px 0;
}

#spMainContainer.spForumToolsPopup a {
	font-size: 80%;
	font-weight: normal;
	color: <?php echo($linkFontColor); ?>;
	margin: 0 0 5px 0;
}

#spMainContainer .spPopupTable {
	width: 100%;
}

#spMainContainer.spForumToolsPopup input.spControl,
#spMainContainer.spForumToolsPopup textarea.spControl {
	font-size: 80%;
	width: 85%;
	border: <?php echo($solidBorder); ?>;
	padding: 0 0 0 5px;
}

#spMainContainer .spPopupTable td {
	color: <?php echo($contentFontColor); ?>;
	padding: 5px 20px 5px 5px;
}

/*--------------------------------------------------------------------------- MISC */

#spMainContainer .spTransitionHoverContent {
	opacity: 0.2;
	height: 0;
}

#spMainContainer .spTransitionHover:hover .spTransitionHoverContent {
	opacity: 1;
	height: 0;
}

#spMainContainer .spTransitionHover:hover .spTransitionHoverContentHide {
	visibility: hidden;
}

UL.jqueryFileTree A {
	padding: 5px 0 5px 10px !important;
	font-weight: bold;
	border-left: <?php echo($thickBorder); ?>;
	outline: none;
}

UL.jqueryFileTree A:focus {
	background: #BBDDFF;
}

/* Base label styling */
#spMainContainer [type="checkbox"]:not(:checked),
#spMainContainer [type="checkbox"]:checked,
#spMainContainer [type="radio"]:not(:checked),
#spMainContainer [type="radio"]:checked {
	position: absolute;
	left: -9999px;
}

#spMainContainer [type="checkbox"]:not(:checked) + label,
#spMainContainer [type="checkbox"]:checked + label,
#spMainContainer [type="radio"]:not(:checked) + label,
#spMainContainer [type="radio"]:checked + label {
	position: relative;
	cursor: pointer;
	padding: 0 0 0 25px;
	line-height: 26px;
	font-size: 100%;
}

#spMainContainer [type="checkbox"]:checked + label,
#spMainContainer [type="radio"]:checked + label {
	font-weight: bold;
}

/* checkbox/radio aspect */
#spMainContainer [type="checkbox"]:not(:checked) + label:before,
#spMainContainer [type="checkbox"]:checked + label:before,
#spMainContainer [type="radio"]:not(:checked) + label:before,
#spMainContainer [type="radio"]:checked + label:before {
	content: '';
	position: absolute;
	left: 0;
	top: 0;
	width: 15px;
	height: 15px;
	border: <?php echo($solidBorder); ?>;
	background: <?php echo($checkBoxBackground); ?>;
}

/* checked mark aspect */
#spMainContainer [type="checkbox"]:not(:checked) + label:after,
#spMainContainer [type="checkbox"]:checked + label:after,
#spMainContainer [type="radio"]:not(:checked) + label:after,
#spMainContainer [type="radio"]:checked + label:after {
	content: '✔';
	position: absolute;
	top: -6px;
	left: 3px;
	font-size: 20px;
	color: <?php echo($checkBoxTickColor); ?>;
	transition: all .2s;
}

/* checked mark aspect changes */
#spMainContainer [type="checkbox"]:not(:checked) + label:after,
#spMainContainer [type="radio"]:not(:checked) + label:after {
	opacity: 0;
	transform: scale(0);
}

#spMainContainer [type="checkbox"]:checked + label:after,
#spMainContainer [type="radio"]:checked + label:after {
	opacity: 1;
	transform: scale(1);
}

/* disabled checkbox/radio */
#spMainContainer [type="checkbox"]:disabled:not(:checked) + label:before,
#spMainContainer [type="checkbox"]:disabled:checked + label:before,
#spMainContainer [type="radio"]:disabled:not(:checked) + label:before,
#spMainContainer [type="radio"]:disabled:checked + label:before {
	box-shadow: none;
	border: <?php echo($solidBorder); ?>;
}

#spMainContainer [type="checkbox"]:disabled:checked + label:after,
#spMainContainer [type="radio"]:disabled:checked + label:after {
	color: <?php echo($checkBoxDisabledColor); ?>;
}

#spMainContainer [type="checkbox"]:disabled + label,
#spMainContainer [type="radio"]:disabled + label {
	color: <?php echo($checkBoxDisabledColor); ?>;
}

/* hover style just for information */
#spMainContainer label:hover:before {
	border: <?php echo($solidBorder); ?> !important;
	opacity: 0.4;
}

#spMainContainer #sp_uploader_info table {
	color: <?php echo($contentFontColor); ?>;
}

/*---------------------------- NOTIFICATIONS */

#spMainContainer .spMessage,
#spMainContainer .spMessage p {
	color: <?php echo($linkFontColor); ?>;
	background: none;
	font-weight: bold;
	font-size: 90%;
	text-align: center;
	margin: 6px 0 9px 0;
	padding: 6px 0;
	width: 100%;
}

#spMainContainer .spMessage .spNoticeText {
	width: auto;
}

#spMainContainer .spMessage .spNoticeLink {
	font-weight: bold;
}

#spMainContainer .spMessage .spNoticeText .spLabelSmall {
	font-size: 100%;
	text-decoration: none;
}

#spMainContainer .spMessage .spNoticeText .spLabelSmall:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

#spMainContainer .spPostContentSection .spMessage,
#spMainContainer .spPostContentSection .spMessage p {
	color: <?php echo ($linkFontColor); ?>;
}

#spMainContainer .spMessage a {
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer .spRecentPostSection {
	margin: 0 0 20px 0;
}

#spMainContainer #spPostNotifications {
	display: none;
	font-weight: bold;
	font-size: 100%;
	background: none;
	color: <?php echo($contentFontColor); ?>;
	vertical-align: middle;
	padding: 2px 4px;
	height: auto;
	width: auto;
	text-align: center;
}

#spMainContainer #spPostNotifications strong {
	color: <?php echo($contentFontColor); ?>;
}

#spMainContainer #spPostMove input.spSubmit {
	color: <?php echo($linkFontColor); ?>;
	font-weight: bold;
	margin: 0 10px;
}

#spMainContainer #spPostMove input.spSubmit:hover {
	color: <?php echo($linkFontColorHover); ?>;
}

/*---------------------------- SUCCESS / FAIL MESSAGES */

#spMainContainer #spNotification {
	display: none;
	z-index: 9999999;
	height: auto;
	width: 300px;
	position: fixed;
	top: 125px;
	border: <?php echo($whiteBorder); ?>;
	background: <?php echo($toolTipsBackground); ?>;
	text-align: center;
}

#spMainContainer #spNotification p {
	color: <?php echo($toolTipsColor); ?>;
	font-weight: bold;
	font-size: 100%;
	vertical-align: middle;
	padding: 0 20px 10px 20px;
}

#spMainContainer #spNotification img {
	vertical-align: top !important;
	text-align: center !important;
	margin: 15px auto;
}

/*---------------------------- WP MEDIA EMBEDS */

#spMainContainer .mejs-container {
	background: #464646;
	vertical-align: top;
	font-family: Helvetica,Arial;
}

#spMainContainer .mejs-container * {
	float: left;
	font-size: 11px;
}

#spMainContainer .mejs-controls .mejs-time {
	padding: 8px 3px 0;
	line-height: 12px;
	color: #FFFFFF;
}

#spMainContainer .mejs-controls .mejs-time-rail {
	padding-top: 5px;
}

#spMainContainer .mejs-controls .mejs-time-rail span {
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
}

#spMainContainer .mejs-controls .mejs-time-rail .mejs-time-loaded {
	background: #21759B;
}

#spMainContainer .mejs-controls .mejs-time-rail .mejs-time-current {
	background: #D54E21;
}

#spMainContainer .mejs-controls .mejs-time-rail .mejs-time-total {
	margin: 5px;
}

#spMainContainer .mejs-controls .mejs-time-rail .mejs-time-handle {
	background: #ffffff;
	border: 2px solid #333333;
}

#spMainContainer .mejs-controls .mejs-time-rail .mejs-time-float {
	background: #eeeeee;
	border: 1px solid #333333;
	margin-left: -18px;
}

#spMainContainer .mejs-controls .mejs-time-rail .mejs-time-float-current {
	margin: 2px;
}

#spMainContainer .mejs-controls .mejs-time-rail .mejs-time-float-corner {
	line-height: 0;
	border: solid 5px #eee;
	border-color: #eee transparent transparent transparent;
	-webkit-border-radius: 0;
	-moz-border-radius: 0;
	border-radius: 0;
}

#spMainContainer .mejs-controls .mejs-volume-button .mejs-volume-slider {
	background: rgba(50, 50, 50, 0.7);
}

#spMainContainer .mejs-controls .mejs-volume-button .mejs-volume-total {
	background: #ddd;
	background: rgba(255, 255, 255, 0.5);
}

#spMainContainer .mejs-controls .mejs-volume-button .mejs-volume-current {
	background: #ddd;
	background: rgba(255, 255, 255, 0.9);
}

#spMainContainer .mejs-controls .mejs-volume-button .mejs-volume-handle {
	background: #ddd;
	background: rgba(255, 255, 255, 0.9);
}

#spMainContainer .spProfileAccount #pass-strength-result {
	background-color: #EEEEEE;
	border: 1px solid #DDDDDD;
	margin: 13px 5px 5px 1px;
	padding: 3px 5px;
	text-align: center;
	width: 200px;
}

#spMainContainer .spProfileAccount #pass-strength-result.short {
	background-color: #FFA0A0;
	border-color: #F04040;
}

#spMainContainer .spProfileAccount #pass-strength-result.bad {
	background-color: #FFB78C;
	border-color: #FF853C;
}

#spMainContainer .spProfileAccount #pass-strength-result.good {
	background-color: #FFEC8B;
	border-color: #FFCC00;
}

#spMainContainer .spProfileAccount #pass-strength-result.strong {
	background-color: #C3FF88;
	border-color: #8DFF1C;
}

#spMainContainer .spProfileAccount .indicator-hint {
	font-size: 90%;
	padding-top: 5px;
}

#spMainContainer .spPostContent iframe {
	max-width:100%;
}

#spMainContainer .spAlsoViewingContainer {
	background: transparent;
}

#spMainContainer .spAlsoViewingContainer .spBrowsingUserHolder {
	margin: 0 0 10px;
	font-size: 80%;
	padding: 0px;
	float: left;
	min-height: 35px;
	width: 33%;
}

#spMainContainer .spAlsoViewingContainer .spBrowsingUserHolder .spAvatar {
	width: 50px;
	float: left;
}

#spMainContainer .spAlsoViewingContainer .spBrowsingUserHolder .spBrowsingTopic a:link{
	font-weight: bold;
}

/*---------------------------- CORE AUTOCOMPLETE STYLES */

.ui-autocomplete {
	position: fixed;
	cursor: default;
	background: <?php echo($whiteContainer); ?>;
	border: <?php echo($solidBorderLight); ?>;
	color: <?php echo($linkFontColor); ?>;
	font-size: 80%;
	font-weight: bold;
	overflow: scroll;
	height: 200px;
}

.ui-front.ui-autocomplete {
	z-index: 9999999;
}

* html .ui-autocomplete {
	width: 1px;
}

.ui-menu {
	list-style: none;
	padding: 2px;
	margin: 0;
	display: block;
	float: left;
}

.ui-menu .ui-menu {
	margin-top: -3px;
}

.ui-menu .ui-menu-item {
	margin: 0;
	padding: 0;
	zoom: 1;
	float: left;
	clear: left;
	width: 100%;
}

.ui-menu .ui-menu-item:focus,
.ui-menu .ui-menu-item:active,
.ui-menu .ui-menu-item:hover {
	color: <?php echo($linkFontColorHover); ?>;
	background: <?php echo($greyContainer); ?>;
	border: none;
	font-weight: bold;
}

.ui-menu .ui-menu-item a {
	text-decoration: none;
	display: block;
	padding: .1em .4em;
	line-height: 1.0;
	zoom: 1;
}

.ui-menu .ui-menu-item a.ui-state-hover,
.ui-menu .ui-menu-item a.ui-state-active {
	font-weight: bold;
	margin: -1px;
}

.ui-helper-hidden-accessible {
	display: none;
}

/*---------------------------- UI DIALOG SLIDING PANEL */

#spMainContainer #spMobilePanel {
	display:none;
	position: fixed;
	bottom: 0px;
	right: 0px;
	left: 0px;
	width: 100%;
	height: auto;
	margin: 0 0 0 0;
	background: <?php echo($greyContainer); ?>;
	border-top: <?php echo($solidBorder); ?>;
	color: <?php echo($contentFontColor); ?>;
	z-index: 999999;
	padding: 0 0 8px 0;
	box-sizing: border-box;
}

#spMobilePanel #spPanelClose,
#spPanelClose {
	position: absolute;
	right: 10px;
	top: 20px;
	width: 19px;
	margin: -10px 0 0 0;
	padding: 0;
	height: 19px;
	background-image: <?php echo($ImageCloseMobile); ?>;
}

/*---------------------------- DIALOG AND PANEL GRID */

#spMainContainer #spGrid {
	width: 100%;
	display: flex;
	flex-flow: row wrap;
	padding: 0.5px;
	box-sizing: border-box;
	background: #C4C4C4;
}

#spMainContainer #spGrid .spGridCell {
	float: left;
	font-family: <?php echo($baseFontFamily); ?>;
	font-size: <?php echo($linkFontSize); ?>;
	font-weight: <?php echo($baseFontWeight); ?>;
	margin: 0.5px;
	padding: 10px 0;
	width: 33%;
	text-align: center;
	line-height: 1.0em;
	vertical-align: middle;
	/*border: <?php echo($solidBorderLight); ?>;*/
	background: <?php echo($whiteContainer); ?>;
}

#spMainContainer #spGrid .spGridCell:hover {
	background: <?php echo($inputFieldBackground); ?>;
}

#spMainContainer #spGrid .spGridCell .spIcon {
	display: block;
	margin: 0 0 5px 0;
	box-sizing: border-box;
	color: <?php echo($linkFontColor); ?>;
}

#spMainContainer #spMobilePanel #spGrid .spGridCell .spIcon {
	display: block;
	margin: 0 auto 5px auto;
	box-sizing: border-box;
}

#spMainContainer #spGrid .spGridCell p {
	margin: 0;
	font-weight: bold;
}

#spMainContainer #spGrid .spGridCell br {
	display: none;
}

#spMainContainer #spGrid .spGridCell a {
	font-weight: bold;
	font-size: 80%;
}

#spMainContainer .spAdminLinksPopup {
	width: 100%;
	box-sizing: border-box;
	padding: 0;
}

/*---------------------------- EDITOR ELEMENTS */

#spMainContainer .spEditor table,
#spMainContainer .spEditor tr,
#spMainContainer .spEditor td {
	margin: 0;
	padding: 0;
	text-align: left;
	line-height: 1em;
	width: auto;
	border: 0;
}

/*---------------------------- CENTER FORUM PAGE TITLE GRAPHIC REPLACEMENT */

img#sfbanner {
	display: block;
	margin-left: auto;
	margin-right: auto;
}

/*---------------------------- ABOUT */

#dialog #spAbout {
	text-align: center;
}

/*---------------------------- MISC */

#spMainContainer div.spGoToBottom {
	padding: 0 0 0 0;
	margin: 24px 0 0 0;
}

#spMainContainer #spUnreadPostsInfo img {
	margin: 4px 3px 0 4px;
}

#spMainContainer #spForumTop,
#spMainContainer #spForumBottom {
	line-height: 1px;
}

#spMainContainer #spLastVisitLabel {
	border-left: <?php echo ($whiteBorder); ?>;
	margin: 5px 6px 0 6px;
	padding: 0 0 0 6px;
}

#spMainContainer #spLastVisitLabel b{
	line-height: 1.2em;
	margin: 0;
	padding: 0;
	color: <?php echo ($contentFontColor); ?>;
	border-left: <?php echo ($whiteBorder); ?>;
}

#spMainContainer .spStatusIcon {
	margin: 0;
}

#spMainContainer .spStatusIcon:hover {
	cursor: default;
}

#spMainContainer .spStatusIcon .spIconNoAction {
	opacity: 0.6;
}

#spMainContainer .spStatusIcon .spIconNoAction:hover {
	color: <?php echo($contentFontColor); ?>;
	cursor: default;
}

#spMainContainer .spHiddenSection,
#spMainContainer .spInlineSection {
	color: <?php echo($plainSectionColor); ?>;
	background: <?php echo($plainSectionBackGround); ?>;
	border: <?php echo($plainSectionBorder); ?>;
	padding: 0;
	margin: 0;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

/*---------------------------- FORMS MISC */

#spMainContainer .spButton, #spMainContainer .spSubmit {
	white-space: normal;
}

#spMainContainer .spIconSmall {
	border: none;
	vertical-align: middle;
	padding: 0 5px 0 5px;
	margin: 0;
}

#spMainContainer .spImg {
	vertical-align: middle;
	padding: 0;
	border: none;
	border-radius: none;
	box-shadow: none;
}

#spMainContainer .spIcon {
	vertical-align: middle;
	margin: 0;
	padding: 0 3px;
	border: none;
}

#spMainContainer .spHeadContainer,
#spMainContainer .spBodyContainer,
#spMainContainer .spGroupForumContainer,
#spMainContainer .spFootContainer {
	width: 100%;
	height: auto;
	margin: 0;
}

#spMainContainer label.spRadio,
#spMainContainer label.spCheckbox,
#spMainContainer label.spSelect {
	margin: 0;
	padding: 5px 5px 5px 0px;
	background: transparent;
	line-height: 14px;
}

@media screen and (max-width: 4000px) and (min-width: 651px)  {
	#spMainContainer .spSearchSection .spRadioSection {
		padding: 0 0 0 5%;
		text-align: left;
		width: 33%;
		box-sizing: border-box;
	}

	#spMainContainer #spLoginForm {
		width: 100%;
		display: none;
	}

	#spMainContainer #spSearchFormAdvanced {
		border: transparent;
		color: <?php echo ($contentFontColor); ?>;
	}

	#spMainContainer .spSearchLinkSep {
		color: <?php echo ($contentFontColor); ?>;
	}
}

/* -------------------
Column Width IDs
----------------------*/

/* GROUP VIEW */

[id^="spColGroup1"] { width: <?php echo($ColGroup_1[0]); ?>; }
[id^="spColGroup2"] { width: <?php echo($ColGroup_2[0]); ?>; }
[id^="spColGroup3"] { width: <?php echo($ColGroup_3[0]); ?>; }
[id^="spColGroup4"] { width: <?php echo($ColGroup_4[0]); ?>; }
[id^="spColGroup5"] { width: <?php echo($ColGroup_5[0]); ?>; }

@media screen and (max-width: 800px) {
	[id^="spColGroup1"] { width: <?php echo($ColGroup_1[800]); ?>; }
	[id^="spColGroup2"] { width: <?php echo($ColGroup_2[800]); ?>; }
	[id^="spColGroup3"] { width: <?php echo($ColGroup_3[800]); ?>; }
	[id^="spColGroup4"] { width: <?php echo($ColGroup_4[800]); ?>; }
}
@media screen and (max-width: 720px) {
	[id^="spColGroup1"] { width: <?php echo($ColGroup_1[720]); ?>; }
	[id^="spColGroup2"] { width: <?php echo($ColGroup_2[720]); ?>; }
	[id^="spColGroup3"] { width: <?php echo($ColGroup_3[720]); ?>; }
	[id^="spColGroup4"] { width: <?php echo($ColGroup_4[720]); ?>; }
}
@media screen and (max-width: 600px) {
	[id^="spColGroup1"] { width: <?php echo($ColGroup_1[600]); ?>; }
	[id^="spColGroup2"] { width: <?php echo($ColGroup_2[600]); ?>; }
	[id^="spColGroup3"] { width: <?php echo($ColGroup_3[600]); ?>; }
	[id^="spColGroup4"] { width: <?php echo($ColGroup_4[600]); ?>; }
	[id^="spColGroup5"] { width: <?php echo($ColGroup_5[500]); ?>; }
}
@media screen and (max-width: 480px) {
	[id^="spColGroup1"] { width: <?php echo($ColGroup_1[480]); ?>; }
	[id^="spColGroup2"] { width: <?php echo($ColGroup_2[480]); ?>; }
	[id^="spColGroup3"] { width: <?php echo($ColGroup_3[480]); ?>; }
	[id^="spColGroup4"] { width: <?php echo($ColGroup_4[480]); ?>; }
}
@media screen and (max-width: 360px) {
	[id^="spColGroup1"] { width: <?php echo($ColGroup_1[360]); ?>; }
	[id^="spColGroup2"] { width: <?php echo($ColGroup_2[360]); ?>; }
	[id^="spColGroup3"] { width: <?php echo($ColGroup_3[360]); ?>; }
	[id^="spColGroup4"] { width: <?php echo($ColGroup_4[360]); ?>; }
}

/* FORUM VIEW */

[id^="spColForum1"] { width: <?php echo($ColForum_1[0]); ?>; }
[id^="spColForum2"] { width: <?php echo($ColForum_2[0]); ?>; }
[id^="spColForum3"] { width: <?php echo($ColForum_3[0]); ?>; }
[id^="spColForum4"] { width: <?php echo($ColForum_4[0]); ?>; }
[id^="spColForum5"] { width: <?php echo($ColForum_5[0]); ?>; }

@media screen and (max-width: 800px) {
	[id^="spColForum1"] { width: <?php echo($ColForum_1[800]); ?>; }
	[id^="spColForum2"] { width: <?php echo($ColForum_2[800]); ?>; }
	[id^="spColForum3"] { width: <?php echo($ColForum_3[800]); ?>; }
	[id^="spColForum4"] { width: <?php echo($ColForum_4[800]); ?>; }
	[id^="spColForum5"] { width: <?php echo($ColForum_5[800]); ?>; }
}
@media screen and (max-width: 720px) {
	[id^="spColForum1"] { width: <?php echo($ColForum_1[720]); ?>; }
	[id^="spColForum2"] { width: <?php echo($ColForum_2[720]); ?>; }
	[id^="spColForum3"] { width: <?php echo($ColForum_3[720]); ?>; }
	[id^="spColForum4"] { width: <?php echo($ColForum_4[720]); ?>; }
	[id^="spColForum5"] { width: <?php echo($ColForum_5[720]); ?>; }
}
@media screen and (max-width: 600px) {
	[id^="spColForum1"] { width: <?php echo($ColForum_1[600]); ?>; }
	[id^="spColForum2"] { width: <?php echo($ColForum_2[600]); ?>; }
	[id^="spColForum3"] { width: <?php echo($ColForum_3[600]); ?>; }
	[id^="spColForum4"] { width: <?php echo($ColForum_4[500]); ?>; }
	[id^="spColForum5"] { width: <?php echo($ColForum_5[500]); ?>; }
}
@media screen and (max-width: 480px) {
	[id^="spColForum1"] { width: <?php echo($ColForum_1[480]); ?>; }
	[id^="spColForum2"] { width: <?php echo($ColForum_2[480]); ?>; }
	[id^="spColForum3"] { width: <?php echo($ColForum_3[480]); ?>; }
	[id^="spColForum4"] { width: <?php echo($ColForum_4[480]); ?>; }
	[id^="spColForum5"] { width: <?php echo($ColForum_5[480]); ?>; }
}
@media screen and (max-width: 360px) {
	[id^="spColForum1"] { width: <?php echo($ColForum_1[360]); ?>; }
	[id^="spColForum2"] { width: <?php echo($ColForum_2[360]); ?>; }
	[id^="spColForum3"] { width: <?php echo($ColForum_3[360]); ?>; }
	[id^="spColForum4"] { width: <?php echo($ColForum_4[360]); ?>; }
	[id^="spColForum5"] { width: <?php echo($ColForum_5[360]); ?>; }
}

/* TOPIC VIEW */

[id^="spColTopic1"] { width: <?php echo($ColTopic_1[0]); ?>; }
[id^="spColTopic2"] { width: <?php echo($ColTopic_2[0]); ?>; }

@media screen and (max-width: 800px) {
	[id^="spColTopic1"] { width: <?php echo($ColTopic_1[800]); ?>; }
	[id^="spColTopic2"] { width: <?php echo($ColTopic_2[800]); ?>; }
}
@media screen and (max-width: 720px) {
	[id^="spColTopic1"] { width: <?php echo($ColTopic_1[720]); ?>; }
	[id^="spColTopic2"] { width: <?php echo($ColTopic_2[720]); ?>; }
}
@media screen and (max-width: 600px) {
	[id^="spColTopic1"] { width: <?php echo($ColTopic_1[600]); ?>; }
	[id^="spColTopic2"] { width: <?php echo($ColTopic_2[600]); ?>; }
}
@media screen and (max-width: 480px) {
	[id^="spColTopic1"] { width: <?php echo($ColTopic_1[480]); ?>; }
	[id^="spColTopic2"] { width: <?php echo($ColTopic_2[480]); ?>; }
}
@media screen and (max-width: 360px) {
	[id^="spColTopic1"] { width: <?php echo($ColTopic_1[360]); ?>; }
	[id^="spColTopic2"] { width: <?php echo($ColTopic_2[360]); ?>; }
}

/* -----------------------
Mobile overrides tablet specific
--------------------------*/

@media screen and (max-width: 650px) {
	#spMainContainer .spTitleColumnHidden,
	#spMainContainer .spIconColumnSection,
	#spMainContainer .spColumnCountViews,
	#spMainContainer .groupBreak {
		display: none;
	}
}


/* ----------------------
Tablet overrides
-------------------------*/

@media screen and (max-width: 599px) and (min-width: 499px)	 {
	#spMainContainer .spOnlineStats {
		width: 40%;
	}

	#spMainContainer .spForumStats {
		width: 40%;
	}

	#spMainContainer .spMembershipStats,
	#spMainContainer .spTopPosterStats {
		display: none;
	}
}

@media screen and (max-width: 699px) and (min-width: 499px) {
	#spMainContainer .spOnlineStats {
		width: 40%;
	}

	#spMainContainer .spForumStats {
		width: 25%;
	}

	#spMainContainer .spTopPosterStats {
		width: 25%;
	}

	#spMainContainer .spMembershipStats {
		display: none;
	}

	#spMainContainer .spFootContainer img {
		margin: 0 5px;
	}
}

@media screen and (min-width: 700px)  {
	#spMainContainer .spOnlineStats {
		width: auto;
	}

	#spMainContainer .spMembershipStats,
	#spMainContainer .spTopPosterStats {
		display: inline;
	}
}

/*	====================================================================================
	If you DO make style changes (see Warning above) then ensure they are made ABOVE
	this comment
	================================================================================= */

@font-face {
	font-family: 'Barebones';
	src:	url('fonts/Barebones.eot');
	src:	url('fonts/Barebones.eot?#iefix') format('embedded-opentype'),
		url('fonts/Barebones.ttf') format('truetype'),
		url('fonts/Barebones.woff') format('woff'),
		url('fonts/Barebones.svg?#Barebones') format('svg');
	font-weight: normal;
	font-style: normal;
}

#spMainContainer [class^="sp_"], #spMainContainer [class*=" sp_"] {
	/* use !important to prevent issues with browser extensions that change fonts */
	font-family: 'Barebones' !important;
	speak: none;
	font-style: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	line-height: 1;
	font-size: 16px;
	color: <?php echo($iconColor); ?>;

	/* Better Font Rendering =========== */
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}

#spMainContainer [class^="sp_"]:hover, #spMainContainer [class*=" sp_"]:hover {
		color: <?php echo($iconColorHover); ?>;
}

#spMainContainer .sp_ToolsBanOn:before {
	content: "\e913";
}
#spMainContainer .sp_ToolsSuspendOn:before {
	content: "\e914";
}
#spMainContainer .sp_ToolsWarnOn:before {
	content: "\e915";
}
#spMainContainer .sp_ManageWarnings:before {
	content: "\e916";
}
#spMainContainer .sp_ToolsAttachments:before {
	content: "\e11e";
}
#spMainContainer .sp_BlogLink:before {
	content: "\e111";
}
#spMainContainer .sp_Jump:before {
	content: "\e112";
}
#spMainContainer .sp_QLBalloonBlue:before {
	content: "\e113";
}
#spMainContainer .sp_QLBalloonRed:before {
	content: "\e114";
}
#spMainContainer .sp_ToolsLock:before {
	content: "\e115";
}
#spMainContainer .sp_ToolsPin:before {
	content: "\e116";
}
#spMainContainer .sp_ToolsEdit:before {
	content: "\e117";
}
#spMainContainer .sp_ToolsDelete:before {
	content: "\e118";
}
#spMainContainer .sp_ToolsMove:before {
	content: "\e119";
}
#spMainContainer .sp_ToolsTags:before {
	content: "\e11a";
}
#spMainContainer .sp_ToolsSort:before {
	content: "\e11b";
}
#spMainContainer .sp_ToolsWatches:before {
	content: "\e11c";
}
#spMainContainer .sp_ToolsProperties:before {
	content: "\e11d";
}
#spMainContainer .sp_ToolsUnapprove:before {
	content: "\e11f";
}
#spMainContainer .sp_ToolsEmail:before {
	content: "\e120";
}
#spMainContainer .sp_ToolsNotify:before {
	content: "\e121";
}
#spMainContainer .sp_ToolsPrivate:before {
	content: "\e122";
}
#spMainContainer .sp_ProfileFormList:before {
	content: "\e123";
}
#spMainContainer .sp_TopicsPosted:before {
	content: "\e124";
}
#spMainContainer .sp_TopicsStarted:before {
	content: "\e125";
}
#spMainContainer .sp_PmProfileBuddyAdversary:before {
	content: "\e126";
}
#spMainContainer .sp_PmDeleteBuddyAdversary:before {
	content: "\e127";
}
#spMainContainer .sp_PermissionYes:before {
	content: "\e128";
}
#spMainContainer .sp_PermissionNo:before {
	content: "\e129";
}
#spMainContainer .sp_Permalink:before {
	content: "\e12a";
}
#spMainContainer .sp_Information:before {
	content: "\e12b";
}
#spMainContainer .sp_PmSendPmButton:before {
	content: "\e12c";
}
#spMainContainer .sp_PolicyDoc:before {
	content: "\e12d";
}
#spMainContainer .sp_goNewPost:before {
	content: "\e12e";
}
#spMainContainer .sp_PlupAttachmentStatus:before {
	content: "\e12f";
}
#spMainContainer .sp_TopicStatusPin:before {
	content: "\e130";
}
#spMainContainer .sp_WatchesForumStatus:before {
	content: "\e131";
}
#spMainContainer .sp_SubscriptionsForumStatus:before {
	content: "\e132";
}
#spMainContainer .sp_TopicStatusPost:before {
	content: "\e133";
}
#spMainContainer .sp_TopicStatusLock:before {
	content: "\e134";
}
#spMainContainer .sp_GooglePlus:before {
	content: "\e135";
}
#spMainContainer .sp_LinkedIn:before {
	content: "\e136";
}
#spMainContainer .sp_MySpace:before {
	content: "\e137";
}
#spMainContainer .sp_Twitter:before {
	content: "\e138";
}
#spMainContainer .sp_Facebook:before {
	content: "\e139";
}
#spMainContainer .sp_YouTube:before {
	content: "\e13a";
}
#spMainContainer .sp_UserWebsite:before {
	content: "\e13b";
}
#spMainContainer .sp_ManageForums:before {
	content: "\e13c";
}
#spMainContainer .sp_ManageOptions:before {
	content: "\e13d";
}
#spMainContainer .sp_ManageComponents:before {
	content: "\e13e";
}
#spMainContainer .sp_ManageUsergroups:before {
	content: "\e13f";
}
#spMainContainer .sp_ManagePermissions:before {
	content: "\e140";
}
#spMainContainer .sp_ManageIntegration:before {
	content: "\e141";
}
#spMainContainer .sp_ManageProfiles:before {
	content: "\e142";
}
#spMainContainer .sp_ManageAdmins:before {
	content: "\e143";
}
#spMainContainer .sp_ManageUsers:before {
	content: "\e144";
}
#spMainContainer .sp_ManagePlugins:before {
	content: "\e145";
}
#spMainContainer .sp_ManageThemes:before {
	content: "\e146";
}
#spMainContainer .sp_ManageToolbox:before {
	content: "\e147";
}
#spMainContainer .sp_ManageEmails:before {
	content: "\e148";
}
#spMainContainer .sp_ManagePolls:before {
	content: "\e149";
}
#spMainContainer .sp_ManagePM:before {
	content: "\e14a";
}
#spMainContainer .sp_ManageTags:before {
	content: "\e14b";
}
#spMainContainer .sp_Warnings:before {
	content: "\e14c";
}
#spMainContainer .sp_Event:before {
	content: "\e14d";
}
#spMainContainer .sp_ToolsStatus:before {
	content: "\e14e";
}
#spMainContainer .sp_Log:before {
	content: "\e14f";
}
#spMainContainer .sp_question:before {
	content: "\e150";
}
#spMainContainer .sp_ManageReputation:before {
	content: "\e156";
}
#spMainContainer .sp_RepUserButton:before {
	content: "\e151";
}
#spMainContainer .sp_ToolsReassign:before {
	content: "\e152";
}
#spMainContainer .sp_TagsToolbar:before {
	content: "\e153";
}
#spMainContainer .sp_WriteDenied:before {
	content: "\e154";
}
#spMainContainer .sp_EditHistory:before {
	content: "\e155";
}
#spMainContainer .sp_PlupAttachmentEditor:before {
	font-size: 25px;
	content: "\e900";
}
#spMainContainer .sp_EditorCancel:before {
	font-size: 25px;
	content: "\e901";
}
#spMainContainer .sp_EditorOptions:before {
	font-size: 25px;
	content: "\e902";
}
#spMainContainer .sp_ToolsLink:before {
	content: "\e903";
}
#spMainContainer .sp_EditorSave:before {
	font-size: 25px;
	content: "\e904";
}
#spMainContainer .sp_EditorSmileys:before {
	font-size: 25px;
	content: "\e905";
}
#spMainContainer .sp_TopicExpireEd:before {
	font-size: 25px;
	content: "\e906";
}
#spMainContainer .sp_PostMultiple:before {
	font-size: 25px;
	content: "\e907";
}
#spMainContainer .sp_PollToolbar:before {
	font-size: 25px;
	content: "\e908";
}
#spMainContainer .sp_EditorPreview:before {
	font-size: 25px;
	content: "\e909";
}
#spMainContainer .sp_ToolsExpire:before {
	content: "\e90a";
}
#spMainContainer .sp_RateStarOff:before {
	content: "\e90b";
	color: #000000;
	opacity: 0.5;
}
#spMainContainer .sp_RateStarOn:before {
	content: "\e90c";
	color: #FFAE00;
}
#spMainContainer .sp_RateStarOver:before {
	content: "\e90d";
	color: #FF4D00;
}
#spMainContainer .sp_RateUp:before {
	content: "\e90e";
}
#spMainContainer .sp_RateUpGrey:before {
	content: "\e90f";
	opacity: 0.6;
}
#spMainContainer .sp_RateDown:before {
	content: "\e910";
}
#spMainContainer .sp_RateDownGrey:before {
	content: "\e911";
	opacity: 0.6;
}
#spMainContainer .sp_ForumStatusAdd:before {
	font-size: 12px;
	content: "\e912";
}

#spMainContainer a.spNewFlag,
#spMainContainer .spNewFlag {
	font-size: 70% !important;
	margin: 0 8px 0 0 !important;
	padding: 0 2px 0 2px;
	border-radius: 3px;
	display: inline;
	line-height: 1.4em;
}

<?php

# Load plugin file css (as .spcss files)
foreach (glob("desktop-css/*.spcss") as $f) {
	include $f;
}

# load mobile overrides
if ($viewDevice == 'mobile') {
	include('barebones-mobile.php');
	# Load plugin file css (as .spcss files)
	foreach (glob("mobile-css/*.spcss") as $f) {
		include $f;
	}
}

# load the rtl css file if needed
if (isset($_GET['rtl'])) {
	include('barebones-rtl.php');
}
