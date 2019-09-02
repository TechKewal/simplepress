<?php
/*
jQuery Fancy Captcha
www.webdesignbeach.com
Created by Web Design Beach.
Copyright 2009 Web Design Beach. All rights reserved.
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/
session_start(); /* starts session to save generated random number */

$rand = rand(0,4);
$_SESSION['postvalue'] = $rand;
echo $rand;
