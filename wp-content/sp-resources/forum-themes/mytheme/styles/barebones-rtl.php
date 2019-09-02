<?php
header("Content-Type: text/css; charset: utf-8");
header("Expires: ".gmdate('D, d M Y H:i:s', (time()+900)) . " GMT");

# --------------------------------------------------------------------------------------
#
#	Simple:Press Template CSS
#	Theme		:	Barebones
#	Author		:	Simple:Press
#
#	This is the RTL language CSS file for the Simple:Press 'Reboot' theme.
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
?>

#spMainContainer #spLastVisitLabel {
	border-right: <?php echo ($universalBreakBorder); ?>;
	border-left: none;
	margin: 5px 6px 0 6px;
	padding: 0 6px 0 0;
}

#spMainContainer .spLabelSmall {
	margin: 5px 0px 0px 0px;
	padding: 0px 10px 0px 0px;
}

#spMainContainer a.spButton {
	color: <?php echo($buttonColor); ?>;
}

#spMainContainer a.spButton img {
	margin: 0 0 2px 3px;
}

#spMainContainer .spSearchLink,
#spMainContainer .spSearchLink a,
#spMainContainer .spSearchLink:link,
#spMainContainer .spSearchLink:active,
#spMainContainer .spSearchLink:visited {
	color: <?php echo ($buttonColor ); ?>;
}

#spMainContainer .spSearchForm a.spButton {
	margin: 0 5px 0 0;
}

#spMainContainer .spSearchForm input.spControl {
	margin: 0 0 5px 4px;
}

#spMainContainer .spAdminBar a.spButton {
	margin: 6px 0 6px 6px;
}

#spMainContainer #spUnreadPostsInfo {
	margin: -3px 10px 0 0;
}

#spMainContainer #spUnreadPostsLink .spIcon {
	padding: 0 3px 0 0;
}

#spMainContainer #spSearchFormAdvanced .spLabel {
	float: left;
}

#spMainContainer .spAdminQueueSection {
	width: 100%;
}

#spMainContainer .spAdminQueueForum a {
	color: <?php echo($lightFont); ?>;
}

#spMainContainer #spQuickLinksTopic .dd .ddTitle span.arrow,
#spMainContainer #spQuickLinksForum .dd .ddTitle span.arrow {
	float: left;
}

#spMainContainer #spQuickLinksTopic .dd .ddChild .opta span,
#spMainContainer #spQuickLinksForum .dd .ddChild .opta span,
#spMainContainer #spQuickLinksTopic .ddChild a,
#spMainContainer #spQuickLinksForum .ddChild a,
#spMainContainer #spQuickLinksTopic .dd .ddTitle,
#spMainContainer #spQuickLinksForum .dd .ddTitle {
	text-align: right;
}

#spMainContainer .stButton .stTwbutton, #spMainContainer .stButton .stMainServices {
	height: auto;
}

#spMainContainer .spHeaderIcon {
	margin: 5px 2px 2px 8px;
}

#spMainContainer .spActionsColumnSection {
	padding: 4px 0 0 4px;
}

#spMainContainer .spInRowSubForums {
	padding: 4px 5px 4px 0;
}

#spMainContainer .spInRowPostLink,
#spMainContainer .spInRowPostLink a {
	text-align: right;
}

#spMainContainer .spUserTimeZone {
	text-align: left;
}

#spMainContainer .spForumTimeZone {
	text-align: right;
}

#spMainContainer .spColumnSection {
	text-align: right;
}

#spMainContainer .spForumTopicSection .spInRowPostLink {
	float: right;
}

#spMainContainer .spForumTopicSection .spInRowPostLink img {
	margin: 4px 0 0 2px;
}

#spMainContainer a.spPageLinks {
	color: <?php echo($pageLinkColor); ?>;
}

#spMainContainer .spTopicViewStatus a {
	color: <?php echo($lightFont); ?>;
}

#spMainContainer .spTopicViewStatus a:hover {
	color: <?php echo($fontColor1); ?>;
}

#spMainContainer #spPostForm .spEditor .spEditorSection {
	padding: 0 10px;
}

#spMainContainer #membersearch, #spMainContainer #allmembers {
	margin: 0 10px 0 0;
}

#spMainContainer .spProfileLeftCol {
	float: right;
	text-align: left;
}

#spMainContainer .spProfileRightCol {
	float: left;
	text-align: right;
}

#spMainContainer #spProfileMenu {
	float: right;
}

#spMainContainer #spProfileFormPanel {
	border-right: <?php echo($profileBorderMenu); ?>;
	border-left: none;
	padding: 0px 10px 0px 0px;
	margin: 11px 10px 0 0px;
}

#spMainContainer .spColumnSection a img {
        -moz-transform: scaleX(-1);
        -o-transform: scaleX(-1);
        -webkit-transform: scaleX(-1);
        transform: scaleX(-1);
        filter: FlipH;
        -ms-filter: "FlipH";
}

#spMainContainer .spPmThreadSection .spLabelSmall {
	color: <?php echo($controlColor); ?>;
}

#spMainContainer #spPmQuickLinksThreads .dd .ddTitle {
	text-align: right;
}

#spMainContainer #spPmQuickLinksThreads .dd .ddTitle span.arrow {
	float: left;
}

@media screen and (max-width: 2000px) {

	#spMainContainer #spQuickLinksTopicMobile {
		height: auto;
	}
}

/*-------------------------------------------------- ADMIN BAR */

#spMainContainer #spUnread .spUnreadRead,
#spMainContainer #spNeedModeration .spModRead,
#spMainContainer #spSpam .spSpamRead,
#spMainContainer #spUnread .spUnreadUnread,
#spMainContainer #spNeedModeration .spModUnread,
#spMainContainer #spSpam .spSpamUnread {
	display: inline-block;
}

#spMainContainer .spAdminQueuePost p {
    border-right: 5px solid #BFCBC5;
	border-left: none;
    margin: 5px 0px 5px 15px;
    padding: 10px;
}

#spMainContainer .spAdminBar a.spButton {
	margin: 0;
}

/*---------------------------------------------------- HEAD */

#spMainContainer .spHeadOne .spLabelSmall.spRight {
	padding: 1px 0px 0px 5px;
}

#spMainContainer #spUnreadPostsInfo {
    margin: 0px 0px 0px 5px;
}

#spMainContainer .spSearchSection .spRadioSection {
	text-align: right;
	padding: 0 5px 0 0;
}

#spMainContainer [type="checkbox"]:not(:checked),
#spMainContainer [type="checkbox"]:checked,
#spMainContainer [type="radio"]:not(:checked),
#spMainContainer [type="radio"]:checked {
    right: -9999px;
}

#spMainContainer #spSearchFormAdvanced .spLabel {
	float: none;
}

#spMainContainer [type="checkbox"]:not(:checked) + label,
#spMainContainer [type="checkbox"]:checked + label,
#spMainContainer [type="radio"]:not(:checked) + label,
#spMainContainer [type="radio"]:checked + label {
    padding: 0px 25px 0px 0px;
}

/*---------------------------------------------------- GROUP */

#spMainContainer .spHeadColumn3 {
    margin-left: 0;
	margin-right: auto;
    padding: 7px;
}

#spMainContainer .spHeaderDescription {
    padding: 2px 0px 0px 4px;
    float: right;
}

#spMainContainer .spHeaderName, #spMainContainer .spHeaderName span {
    padding: 5px 0px 0px 10px;
}

#spMainContainer .spIconColumnSectionTitle {
    margin: 7px 7px 7px 13px;
}

#spMainContainer .spTitleColumnTitle {
    padding: 7px 0px 7px 7px;
}

#spMainContainer #spBreadCrumbs {
    padding: 0px 0px 0px 15px;
}

/*---------------------------------------------------- FOOT */

#spMainContainer .spAllRSSButton {
	margin: 0 5px 0 0;
}

#spMainContainer .spStatsSection .spOnlinePageLink {
    float: right;
    padding: 0px 5px 0px 0px;
}

#spMainContainer .spOnlineStats,
#spMainContainer .spForumStats,
#spMainContainer .spMembershipStats,
#spMainContainer .spTopPosterStats,
#spMainContainer .spNewMembers,
#spMainContainer .spModerators,
#spMainContainer .spAdministrators {
	margin: 0 2px 0 0;
}

#spMainContainer #spBirthdays .spBirthdaysHeader {
	padding: 0 5px 0 0;
}