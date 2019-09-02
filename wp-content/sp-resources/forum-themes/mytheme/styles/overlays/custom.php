<?php

# --------------------------------------------------------------------------------------
/*
Simple:Press Template Colour Attribute File
Theme		:	Barebones
Color		:	Custom
Author		:	Simple:Press
*/
# --------------------------------------------------------------------------------------

# -----------------------------------------------------------------------------------------------------------------------------------

# adjust path as needed for multisite
$site = $_GET['site'];
if (empty($site)) {
    # standard wp
    $rel_path = '../../../../'; # custom settings in wp-content
} else {
    # multisite
    # need to handle old style blogs.dir storage and wp 3.5+ storage
    if (isset($_GET['oldstore'])) {
        # old style blogs.dir storage
        $rel_path = '../../../../'; # custom settings in blogs.dir/xx/ directory
    } else {
        # wp 3.5+ storage
        if ($site == 1) {
            $rel_path = '../../../../'; # custom settings in wp-content/uploads directory for main site
        } else {
            $rel_path = "../../../../sites/$site/"; # custom settings in wp-content/uploads/sites/xx directory for network sites
        }
    }
}

# check for custom settings
if (isset($_GET['sp-customizer-test'])) {
	require_once $rel_path.'sp-custom-settings/sp-barebones-test-settings.php';
} else {
	require_once $rel_path.'sp-custom-settings/sp-barebones-custom-settings.php';
}

# ------------------------------------------------------------------------------------------------------------------------------------
# BASE
# ====================================================================================================================================

	# COLOUR PALETTE
	# The basic colour palette used throughout the theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$stockColor1						=		$ops['C1'];					# Black
		$stockColor2						=		$ops['C2'];					# White
		$stockColor3						=		$ops['C3'];					# Colour 1 light
		$stockColor4						=		$ops['C4'];					# Colour 2 medium
		$stockColor5						=		$ops['C5'];					# Colour 3 dark
		$stockColor6						=		$ops['C6'];					# Grey
		$stockColor7						=		$ops['C7'];					# Glyph colour
		$stockColor8						=		$ops['C8'];					# Glyph hover colour
		$stockColor9						=		$ops['C9'];					# Primary link text hover
		$stockColor10						=		$ops['C10'];				# Secondary link text hover
		$stockColor11						=		$ops['C11'];				# Secondary link text hover

	# BASE FONT RULES
	# Base font styles used throughout the theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$baseFontFamily						=		$ops['FN'];					# Font family (as default inherits from WP theme page wrapper)
		$baseFontWeight						=		'normal';					# Font weight
		$baseFontSize						=		$ops['F1'].'%';				# Font size relative to WP theme page wrapper

	# GLYPH / ICON COLOURS
	# Glyph / Icon colour palette
	# --------------------------------------------------------------------------------------------------------------------------------
		$iconColor							=		$stockColor7;				# Glyph colour
		$iconColorHover						=		$stockColor8;				# Glyph hover colour

	# BASE LINK TEXT
	# Rules for controlling text links throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$linkFontColor						=		$stockColor5;				#
		$linkFontColorHover					=		$stockColor9;				#
		$secLinkFontColor					=		$stockColor10;				#
		$secLinkFontColorHover				=		$stockColor11;				#
		$linkFontSize						=		'80%';						#
		$linkFontWeight						=		'normal';					#

	# BASE/RESET STYLES
	# Base generic styles used throughout the theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$resetTextColor						=	'inherit';
		$resetBackGroundColor				=	'transparent';
		$resetBorderColor					=	'transparent';
		$resetLinkColor						=	$stockColor5;
		$resetLinkHover						=	$stockColor1;
		$resetFontFamily					=	$baseFontFamily;
		$resetFontSize						=	$baseFontSize;
		$resetFontWeight					=	$baseFontWeight;
		$resetFontSizeSmall					=	'90%';
		$resetLineHeight					=	'1.1em';
		$resetBorder						=	'none';
		$resetLinkDecoration				=	'none';

# ------------------------------------------------------------------------------------------------------------------------------------
# UNIVERSAL TEXT
# ====================================================================================================================================

	# TITLE / HEADING TEXT
	# Rules for controlling text in headings and titles throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$titleFontFamily					=		'inherit';					#
		$titleFontColor						=		$stockColor1;				#
		$titleFontSize						=		'80%';						#
		$titleFontWeight					=		'bold';						#
		$titleFontTransform					=		'none';						#
		$titleFontVariant					=		'none';						#

	# SUB TITLE / SUB HEADING TEXT
	# Rules for controlling sub text in headings and titles throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$subTitleFontFamily					=		'inherit';					#
		$subTitleFontColor					=		$stockColor1;				#
		$subTitleFontSize					=		'80%';						#
		$subTitleFontWeight					=		'bold';						#
		$subTitleFontTransform				=		'none';						#
		$subTitleFontVariant				=		'none';						#

	# CONTENT TEXT
	# Rules for controlling general content text throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$contentFontFamily					=		'inherit';					#
		$contentFontColor					=		$stockColor1;				#
		$contentFontSize					=		'80%';						#
		$contentFontWeight					=		'normal';					#

	# FORM TEXT
	# Rules for controlling form text
	# --------------------------------------------------------------------------------------------------------------------------------
		$formFontColor						=		$stockColor1;				#
		$formFontSize						=		'75%';						#
		$formFontWeight						=		'normal';					#

	# INFO TEXT
	# Rules for controlling info text
	# --------------------------------------------------------------------------------------------------------------------------------
		$infoFontColor						=		$stockColor1;				#
		$infoFontSize						=		'75%';						#
		$infoFontWeight						=		'normal';					#

# ------------------------------------------------------------------------------------------------------------------------------------
# BUTTONS / INPUTS / SELECTS ETC..
# ====================================================================================================================================
		$spButtonFontColor					=		$stockColor1;				#
		$spButtonFontColorHover				=		$stockColor5;				#
		$spButtonFontSize					=		'80%';						#
		$spButtonFontWeight					=		'bold';					#
		$spButtonBorder						=		'none';						#
		$spButtonBorderHover				=		'none';						#
		$spButtonBackground					=		'none';						#
		$spButtonBackgroundHover			=		'none';						#

		$inputFieldBackground				=		$stockColor6;				#
		$inputFieldBackgroundHover			=		$stockColor2;				#
		$inputFieldBorder					=		'none';						#

# ------------------------------------------------------------------------------------------------------------------------------------
# UNIVERAL BORDERS
# ====================================================================================================================================
		$dottedBorder						=		"1px dotted $stockColor4";	#
		$solidBorder						=		"1px solid $stockColor1";	#
		$solidBorderLight					=		"1px solid $stockColor3";   #
		$solidBorderLightThick				=		"2px solid $stockColor4";   #
		$thickBorder						=		"5px solid $stockColor3";	#
		$whiteBorder						=		"1px solid $stockColor2";	#
# ------------------------------------------------------------------------------------------------------------------------------------
# SECTION CONTAINERS
# ====================================================================================================================================

	# HEADER / FOOTER CONTAINERS
	# Rules for controlling header & footer containers throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$headerContainerBackground			=		$stockColor6;				#
		$headerContainerBorder				=		'none';						#
		$footerContainerBackground			=		$stockColor3;				#
		$footerContainerBorder				=		'none';						#

	# TITLE CONTAINERS
	# Rules for controlling title containers throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$titleContainerBackground			=		$stockColor3;				#
		$titleContainerBorder				=		'none';						#
		$titleColumnsBackground				=		$stockColor4;				#

	# VIEW SECTION CONTAINERS
	# Rules for controlling display sections
	# --------------------------------------------------------------------------------------------------------------------------------
		$groupSectionBackground				=		'transparent';				#
		$groupSectionBorder					=		'none';						#
		$forumSectionBackground				=		'transparent';				#
		$forumSectionBorder					=		'none';						#
		$topicSectionBackground				=		'transparent';				#
		$topicSectionBorder					=		'none';						#

	# FORM CONTAINERS
	# Rules for controlling form containers throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$formContainerBackground			=		'none';						#
		$formContainerBorder				=		"1px solid $stockColor4";	#
		$whiteContainer						=		$stockColor2;				#
		$greyContainer						=		$stockColor6;				#

	# PLAIN SECTION
	# Rules for controlling plain section containers throughout theme
	# --------------------------------------------------------------------------------------------------------------------------------
		$plainSectionColor					=		'inherit';					#
		$plainSectionBackGround				=		'transparent';				#
		$plainSectionBorder					=		'none';						#
# ------------------------------------------------------------------------------------------------------------------------------------
# FORUM / TOPIC / LIST ROW CONTAINERS
# ====================================================================================================================================

	# FORUM / TOPIC ROW CONTAINERS
	# Rules for controlling forum and topic containers in 'Group View' & 'Forum View'
	# --------------------------------------------------------------------------------------------------------------------------------
		$indexRowOddBackGround				=		$stockColor2; 				#
		$indexRowOddBackGroundHover			=		$stockColor2;				#
		$indexRowOddBorder					=		'none';						#
		$indexRowOddBorderHover				=		'none';						#

		$indexRowEvenBackGround				=		$stockColor6; 				#
		$indexRowEvenBackGroundHover		=		$stockColor6; 				#
		$indexRowEvenBorder					=		'none';						#
		$indexRowEvenBorderHover			=		'none';						#

	# FORUM / TOPIC ROW TEXT
	# Rules for controlling forum and topic containers in 'Group View' & 'Forum View'
	# --------------------------------------------------------------------------------------------------------------------------------
		$indexRowOddColor					=		'inherit';					#
		$indexRowOddColorHover				=		'inherit';					#
		$indexRowEvenColor					=		'inherit';					#
		$indexRowEvenColorHover				=		'inherit';					#

	# LIST ROW CONTAINERS
	# Rules for controlling topic list containers in new posts views
	# --------------------------------------------------------------------------------------------------------------------------------
		$listRowBackGround					=		$stockColor2; 				#
		$listRowBackGroundHover				=		$stockColor2; 				#

# ------------------------------------------------------------------------------------------------------------------------------------
# TOPIC VIEW
# ====================================================================================================================================

	# CONTAINERS
	# Rules for controlling post container styles in 'Topic View'
	# --------------------------------------------------------------------------------------------------------------------------------
		$topicViewSectionColor				=	$stockColor1; 					#
		$topicViewSectionBackGroundColor	=	'none';							#
		$topicViewSectionBorder				=	'none'	;						#

	# USER PANE
	# Rules for controlling post container styles in 'Topic View'
	# --------------------------------------------------------------------------------------------------------------------------------
		$topicUserSectionBackGround			=	'none'; 						#

	# POST PANE
	# Rules for controlling post container styles in 'Topic View'
	# --------------------------------------------------------------------------------------------------------------------------------
		$postSectionBackGroundColor			=	$stockColor6; 					#
		$postCiteBackGroundColor			=	$stockColor2; 					#
		$postCodeBackGroundColor			=	$stockColor2; 					#

		$postHeadingh1						=	'1.6em';						#
		$postHeadingh2						=	'1.5em';						#
		$postHeadingh3						=	'1.4em';						#
		$postHeadingh4						=	'1.3em';						#
		$postHeadingh5						=	'1.2em';						#
		$postHeadingh6						=	'1.1em';						#

		$postLineHeight						= 	'1.3em';
# ------------------------------------------------------------------------------------------------------------------------------------
# BACKGROUND IMAGES
# ====================================================================================================================================
		$ImageClose							='url("images/close.gif")';
		$ImageCloseMobile					='url("images/closeDark.gif")';
		$ImageResize						='url("images/resize.gif")';
		$Imagedd_arrow						='url("images/dd_arrow.gif") no-repeat 0 0';
		$Imagesp_ImageOverlay				='#666666 url("images/sp_ImageOverlay.png") 50% 50% repeat';

# ------------------------------------------------------------------------------------------------------------------------------------
# MISCELLANEOUS
# ====================================================================================================================================

	# UI-DIALOG
	# Rules for controlling popup dialog styles
	# --------------------------------------------------------------------------------------------------------------------------------
		$popupHeaderColor					=	$stockColor1;					#
		$popupBackground					=	$stockColor4;					#

	# TOOLTIPS
	# Rules for controlling tooltip styles
	# --------------------------------------------------------------------------------------------------------------------------------
		$toolTipsColor						=	$stockColor2;					#
		$toolTipsFontFamily					=	'inherit';						#
		$toolTipsFontSize					=	$contentFontSize;				#
		$toolTipsBackground					=	'#5C5C5C';						#
		$toolTipsBorder						=	'none';							#

	# CHECK BOXES
	# Rules for controlling checkbox styles
	# --------------------------------------------------------------------------------------------------------------------------------
		$checkBoxBackground					=	$stockColor2;	# Used for check boxes and radio controls
		$checkBoxDisabledBackground			=	$stockColor6;	# Used for disabled check boxes and radio controls
		$checkBoxTickColor					=	$stockColor5;	# Used for checkbox and radio control ticks
		$checkBoxDisabledColor				=	$stockColor6;	# Used for disabled check boxes and radio controls

	# QuickLinks
	# Possibly remove as no QuickLinks exist
	# --------------------------------------------------------------------------------------------------------------------------------
		$quickLinksTopicsPostNew			=	'#488ccc';		# Used for New Topics (Posts) in QuickLinks (Topics)
		$quickLinksTopicsPostMod			=	'#F26565';		# Used for New Topics (Posts awaiting Moderation) in QUickLinks (Topics)
		$quickLinksTopicsPostRead			=	'#777777';		# Used for Read Topics in QuickLinks (Topics)

		$quickLinksSelectWidth				=	'250px';
		$quickLinksListWidth				=	'350px';
		$quickLinksSelectMobileWidth		=	'200px';
		$quickLinksSelectMobileWidthSmall	=	'160px';
		$quickLinksListMobileWidth			=	'260px';
		$quickLinksListMobileWidthSmall		=	'248px';

# ------------------------------------------------------------------------------------------------------------------------------------
# COLUMN WIDTHS FOR VIEWS
# ====================================================================================================================================

# GROUP VIEW ========================

$ColGroup_1 = array();
$ColGroup_2 = array();
$ColGroup_3 = array();
$ColGroup_4 = array();

$ColGroup_1[0]		= '5%';
$ColGroup_1[800]	= '6%';
$ColGroup_1[720]	= '7%';
$ColGroup_1[600]	= '0%';
$ColGroup_1[480]	= '0%';
$ColGroup_1[360]	= '0%';

$ColGroup_2[0]		= '45%';
$ColGroup_2[800]	= '45%';
$ColGroup_2[720]	= '45%';
$ColGroup_2[600]	= '48%';
$ColGroup_2[480]	= '48%';
$ColGroup_2[360]	= '48%';

$ColGroup_3[0]		= '10%';
$ColGroup_3[800]	= '46%';
$ColGroup_3[720]	= '44%';
$ColGroup_3[600]	= '48%';
$ColGroup_3[480]	= '48%';
$ColGroup_3[360]	= '48%';

$ColGroup_4[0]		= '30%';
$ColGroup_4[800]	= '35%';
$ColGroup_4[720]	= '35%';
$ColGroup_4[600]	= '35%';
$ColGroup_4[480]	= '35%';
$ColGroup_4[360]	= '35%';

$ColGroup_5[0]		= '5%';
$ColGroup_5[800]	= '3%';
$ColGroup_5[720]	= '6%';
$ColGroup_5[500]	= '6%';
$ColGroup_5[480]	= '6%';
$ColGroup_5[360]	= '3%';

# FORUM VIEW ========================

$ColForum_1 = array();
$ColForum_2 = array();
$ColForum_3 = array();
$ColForum_4 = array();

$ColForum_1[0]		= '5%';
$ColForum_1[800]	= '5%';
$ColForum_1[720]	= '5%';
$ColForum_1[600]	= '5%';
$ColForum_1[480]	= '5%';
$ColForum_1[360]	= '5%';

$ColForum_2[0]		= '50%';
$ColForum_2[800]	= '50%';
$ColForum_2[720]	= '50%';
$ColForum_2[600]	= '50%';
$ColForum_2[480]	= '50%';
$ColForum_2[360]	= '50%';

$ColForum_3[0]		= '25%';
$ColForum_3[800]	= '25%';
$ColForum_3[720]	= '25%';
$ColForum_3[600]	= '25%';
$ColForum_3[480]	= '25%';
$ColForum_3[360]	= '25%';

$ColForum_4[0]		= '8%';
$ColForum_4[800]	= '8%';
$ColForum_4[720]	= '8%';
$ColForum_4[500]	= '8%';
$ColForum_4[480]	= '8%';
$ColForum_4[360]	= '8%';

$ColForum_5[0]		= '8%';
$ColForum_5[800]	= '8%';
$ColForum_5[720]	= '8%';
$ColForum_5[500]	= '8%';
$ColForum_5[480]	= '8%';
$ColForum_5[360]	= '8%';

# TOPIC VIEW ================

$ColTopic_1 = array();
$ColTopic_2 = array();

$ColTopic_1[0]		= '15%';
$ColTopic_1[800]	= '15%';
$ColTopic_1[720]	= '16%';
$ColTopic_1[600]	= '16%';
$ColTopic_1[480]	= '16%';
$ColTopic_1[360]	= '17%';

$ColTopic_2[0]		= '84%';
$ColTopic_2[800]	= '84%';
$ColTopic_2[720]	= '82%';
$ColTopic_2[600]	= '82%';
$ColTopic_2[480]	= '82%';
$ColTopic_2[360]	= '81%';
