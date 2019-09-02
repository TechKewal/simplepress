<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_pm_is_pmview()

	returns true if the current page being viewed is a spf private messaging page
	===================================================================================*/
function sp_pm_do_is_pmview() {
	return (SP()->rewrites->pageData['pageview'] == 'pm' || SP()->rewrites->pageData['pageview'] == 'pmthread');
}
