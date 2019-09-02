<?php
/*
Simple:Press
Gravatar Cache - engine
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_get_gravatar($who, $size, $userid, $data) {
	global $memCache;

	if (empty($data)) $data = array();

	# if empty or already cached in this session return accordingly
	if(empty($who)) return;
	if(isset($memCache[$who])) return $memCache[$who];
	if(!empty($data['uploaded']) || isset($data['default']) && $data['default'] == 1) return;

	# If we can't write to the disk return
	if(!is_writable(SPGCSTOREDIR)) return;

	# see if we can get hold of a file
	$md5 = md5($who);
	$isCached = sp_gravatar_check($md5.'.jpeg', $size);

	if(false === $isCached) {
		$memCache[$who] = '';
		if (!isset($data['default']) || (!empty($data['default']) && $data['default'] == 0)) {
			$data['default'] = 1;
			SP()->memberData->update($userid, 'avatar', $data);
		}
		return;
	} else {
		$url = parse_url(SPGCSTOREURL.'/'.$md5.'.jpeg');
		$memCache[$who] = $url['path'];
		if ((!isset($data['default']) || (!empty($data['default']) && $data['default'] == 1))) {
			$data['default'] = 0;
			SP()->memberData->update($userid, 'avatar', $data);
		}
		return $url['path'];
	}
}

function sp_gravatar_check($md5, $size) {
	$isCached = false;
	if(file_exists(SPGCSTOREDIR.'/'.$md5)) {
		$isCached = true;
	} else {
		# so let's see if we can go and get one it is exists
		$isCached = sp_gravatar_cache_image($md5, $size);
	}
	return $isCached;
}

function sp_gravatar_cache_image($md5, $size) {
	$path = SPGCSTOREDIR.'/';
	# go get gravatar
	$gravatar = sp_gravatar_query($md5, $size);
	$cached = sp_gravatar_copy($gravatar, "$path/$md5");
	if (! $cached) {
		# looks like the copy failed, delete the TMP
		if (is_file("$path/$md5")) {
			unlink("$path/$md5");
		}
	} elseif (filesize("$path/$md5") < 50) {
		# check filesize for bogus image from gravatar.com
		if (is_file("$path/$md5")) {
			unlink("$path/$md5");
		}
		$cached = false;
//	} else {
//		# we copied successfully
//		$cached = rename("$path/$md5.jpeg", "$path/$md5");
	}
	return $cached;
}

function sp_gravatar_query($md5, $size) {
	# prepare the query to gravatar.com
	$spAvatars = SP()->options->get('sfavatars');
	# get rating (default pg). get option set size setting for the actual gravatar download
	$rating = (isset($spAvatars['sfgmaxrating'])) ? $spAvatars['sfgmaxrating'] : 2;
	$size = (isset($spAvatars['sfavatarsize'])) ? $spAvatars['sfavatarsize'] : '50';

	switch ($rating) {
		case 1:
			$grating = 'g';
			break;
		case 2:
			$grating = 'pg';
			break;
		case 3:
			$grating = 'r';
			break;
		case 4:
		default:
			$grating = 'x';
			break;
	}
	$gravatar = "https://www.gravatar.com/avatar/$md5?d=404&size=$size&rating=$grating";
	return $gravatar;
}

function sp_gravatar_copy($url, $filename) {
	$result = false;
	$data = sp_gravatar_get_url($url);
	if (!empty($data)) {
		$result = sp_gravatar_write_file($filename, $data);
	}
	return $result;
}

function sp_gravatar_get_url($url) {
	$options = array(
		'timeout' => 10,
	);
	$response = wp_remote_get($url, $options);
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) return '';
    $body = wp_remote_retrieve_body($response);
    return $body;
}

function sp_gravatar_write_file($filename, $data) {
	$result = false;
	$handle = fopen($filename, "wb");
	if ($handle) {
		$result = fwrite($handle, $data);
	}
	fclose($handle);
	return $result;
}
