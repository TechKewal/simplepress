<?php
/*
Simple:Press
Akismet
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Main functions - run Akismet/mark spam
# ------------------------------------------------------
function sp_akismet($newpost) {
    if (SP()->auths->get('bypass_akismet', $newpost['forumid'])) return $newpost;

	if (function_exists('akismet_http_post') == false) return $newpost;

	$akismet = SP()->options->get('spAkismet');
	if (empty($akismet) || $akismet == 1) return $newpost;

	$spam = sp_check_akismet($newpost);

	if (true == $spam) {
		if ($akismet == 2) $newpost['poststatus'] = 2;
		if ($akismet == 3) {
			SP()->notifications->message(1, __('This post has been identified as spam and has been rejected', 'spab'));
			wp_redirect(SP()->spPermalinks->get_url());
			die();
		}
	}
	return $newpost;
}

function sp_check_akismet($newpost) {
	global $akismet_api_host;
	$data = array(
		'blog' => get_option('home'),
		'user_ip' => $newpost['posterip'],
		'user_agent' => $_SERVER['HTTP_USER_AGENT'],
		'permalink' => $newpost['url'],
		'comment_type' => 'forum',
		'comment_author' => $newpost['postername'],
		'comment_author_email' => $newpost['posteremail'],
		'comment_content' => $newpost['postcontent']
	);
	return sp_akismet_comment_check($akismet_api_host, $data);
}

function sp_akismet_comment_check($key, $data) {
    $request = 'blog='. urlencode($data['blog']) .
               '&user_ip='. urlencode($data['user_ip']) .
               '&user_agent='. urlencode($data['user_agent']) .
               '&permalink='. urlencode($data['permalink']) .
               '&comment_type='. urlencode($data['comment_type']) .
               '&comment_author='. urlencode($data['comment_author']) .
               '&comment_author_email='. urlencode($data['comment_author_email']) .
               '&comment_content='. urlencode($data['comment_content']);
    $host = $http_host = $key.'.rest.akismet.com';
    $path = '/1.1/comment-check';
    $port = 80;
    $akismet_ua = "WordPress/3.2.1 | Akismet/2.5.3";
    $content_length = strlen( $request );
    $http_request = "POST $path HTTP/1.0\r\n";
    $http_request.= "Host: $host\r\n";
    $http_request.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $http_request.= "Content-Length: {$content_length}\r\n";
    $http_request.= "User-Agent: {$akismet_ua}\r\n";
    $http_request.= "\r\n";
    $http_request.= $request;
    $response = '';
    if (false != ($fs = @fsockopen($http_host, $port, $errno, $errstr, 10))) {
        fwrite($fs, $http_request);
        while (!feof($fs))
            $response.= fgets($fs, 1160); # One TCP-IP packet
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);
    }

    if (!empty($response) && 'true' == $response[1]) {
        return true;
    } else {
        return false;
    }
}
