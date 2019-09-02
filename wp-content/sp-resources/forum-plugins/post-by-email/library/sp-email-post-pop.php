<?php
/*
Simple:Press
Post by Email - Processing using POP3
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_process_inbox_pop($mBox, $f, $mCount, $log) {
	# loop through the emails
	for($i = 1; $i <= $mCount; $i++) {
		$message = $mBox->get($i);
		$phone_delim = '::';
		$bodysignal = false;
		$boundary = '';
		$charset = '';
		$subject = '';
		$slug = '';
		$content = '';
		$content_type = '';
		$content_transfer_encoding = '';
		$post_author = '';
		$author_found = false;

		# loop through the message by line
		foreach($message as $line) {
			# body signal
			if(strlen($line) < 3) {
				$bodysignal = true;
			}
			if($bodysignal) {
				$content .= $line;
			} else {
				if(preg_match('/Content-Type: /i', $line)) {
					$content_type = trim($line);
					$content_type = substr($content_type, 14, strlen($content_type) - 14);
					$content_type = explode(';', $content_type);
					if (!empty($content_type[1])) {
						$charset = explode('=', $content_type[1]);
						$charset = (!empty($charset[1])) ? trim($charset[1]) : '';
					}
					$content_type = $content_type[0];
				}
				if(preg_match('/Content-Transfer-Encoding: /i', $line)) {
					$content_transfer_encoding = trim($line);
					$content_transfer_encoding = substr($content_transfer_encoding, 27, strlen($content_transfer_encoding) - 27);
					$content_transfer_encoding = explode(';', $content_transfer_encoding);
					$content_transfer_encoding = $content_transfer_encoding[0];
				}
				if(($content_type == 'multipart/alternative') && (false !== strpos($line, 'boundary="')) && ('' == $boundary)) {
					$boundary = trim($line);
					$boundary = explode('"', $boundary);
					$boundary = $boundary[1];
				}
				if(preg_match('/Subject: /i', $line)) {
					$subject = trim($line);
					$subject = substr($subject, 9, strlen($subject) - 9);
					$subject = rawurldecode($subject);
					# Captures any text in the subject before $phone_delim as the subject
					if(function_exists('iconv_mime_decode')) {
						$subject = iconv_mime_decode($subject, 2, get_option('blog_charset'));
					} else {
						$subject = wp_iso_descrambler($subject);
					}
					$subject = explode($phone_delim, $subject);
					$subject = $subject[0];
					$subject = trim($subject);

					# Extract the Subject (Topic Name)
					$maybeNew = false;
					if(strpos($subject, '[')) {
						$subject = substr($subject, strpos($subject, '[')+1, -1);
					} else {
						$maybeNew = true;
					}

					$subject = trim(utf8_encode(quoted_printable_decode($subject)));

					$log['eTopic'] = $subject;
				}
				# Set the author using the email address (From or Reply-To)
				if(preg_match('/(From): /', $line)) {
					if( preg_match('|[a-z0-9_.-]+@[a-z0-9_.-]+(?!.*<)|i', $line, $matches)) {
						$author = $matches[0];
					} else {
						$author = trim($line);
					}
					$author = sanitize_email($author);
					$log['eUser'] = $author;
					if(is_email($author)) {
						$userdata = get_user_by('email', $author);
						if(empty($userdata)) {
							# try for alternate email address
							$userdata = sp_pbe_alt_email($author);
						}
						if(empty($userdata)) {
							$author_found = false;
						} else {
							$post_author = $userdata->ID;
							$author_found = true;
						}
					} else {
						$author_found = false;
					}
				}
			}
		}

		# If we have an author then soldier on...
		if($author_found) {
			if($content_type == 'multipart/alternative') {
				$content = explode('--'.$boundary, $content);
				$content = $content[2];
				# match case-insensitive content-transfer-encoding
				if(preg_match('/Content-Transfer-Encoding: quoted-printable/i', $content, $delim)) {
					$content = explode($delim[0], $content);
					$content = $content[1];
				}
				$content = strip_tags($content, '<p><br><i><b><u><em><strong><strike><font><span><div>');
			}
			$content = trim($content);
			if(false !== stripos($content_transfer_encoding, "quoted-printable")) {
				$content = quoted_printable_decode($content);
			}
			if(function_exists('iconv') && ! empty( $charset)) {
				if($charset != get_option('blog_charset')) {
					$content = iconv($charset, get_option('blog_charset'), $content);
				}
			}
			# Captures any text in the body after $phone_delim as the body
			$content = explode($phone_delim, $content);
			$content = empty($content[1]) ? $content[0] : $content[1];
			$content = trim($content);

			# test for slug
			if (!$maybeNew) {
				$start = strpos($content, '[--id=#');
				if($start) {
					$slug = substr($content, ($start+7), (strpos($content, '#--]')-($start+7)));
				}
			}

			# Extract and clean up the content
			$excontent = substr($content, 0, strpos($content, '[-- '));
			if(empty($excontent) && $maybeNew) $excontent = $content;
			$content = $excontent;

			$content = trim(utf8_encode(quoted_printable_decode($content)));

			# Deal with double carriage return/line feed... use a random string of X's
			$content = str_replace(chr(13).chr(10).chr(13).chr(10), 'XXXXXXXX', $content);
			$content = str_replace(chr(13).chr(10), ' ', $content);
			$content = str_replace('XXXXXXXX', chr(13).chr(10).chr(13).chr(10), $content);

			# Apple Mail - closing angle bracket
			$content = rtrim($content, '&gt;');

			# Now we can find out whether we have someone and something we can actually use...
			$user = SP()->memberData->get($post_author);
			if(empty($user) || empty($subject)) {
				$mBox->delete($i);
			} else {
				sp_emailpost_save($f, $subject, $slug, $user, $author, $content, $log);
			}
		} else {
			$log['eLog']=__('Invalid User Email Address', 'sp-pbe').'<br />('.$author.')';
			sp_pbe_log($log);
		}

		# delete the email
		if(!$mBox->delete($i)) {
			$mBox->reset();
		}
	}
}
