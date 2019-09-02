 <?php
/*
Simple:Press
Barebones SP Theme Custom Test AHAH call
$LastChangedDate: 2014-09-12 07:30:12 +0100 (Fri, 12 Sep 2014) $
$Rev: 11958 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


$url = SP()->spPermalinks->get_url().'?sp-customizer-test=on';
echo '<iframe height="100%" width="100%" src="'.$url.'"><iframe>';
die();
