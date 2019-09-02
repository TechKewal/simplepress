<?php
/*
Simple:Press
Post by Email - Processing using IMAP
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_process_inbox_imap($mBox, $f, $mCount, $log) {
	# loop through the emails
	for($message_number = 1; $message_number <= $mCount; $message_number++) {
		$attachments = array();
		$subject = '';
		$slug = '';
		$content = '';
		$author = '';
		$attId = 0;
		$maybeNew = false;
	    $header = imap_headerinfo($mBox, $message_number);

		# Extract the Subject (Topic Name)
		$maybeNew = false;
		if(strpos($header->subject, '[')) {
			$subject = substr($header->subject, strpos($header->subject, '[')+1, -1);
		} else {
            $subject = $header->subject;
			$maybeNew = true;
		}

		$subject = trim(utf8_encode(quoted_printable_decode($subject)));

		$author = $header->reply_to[0]->mailbox.'@'.$header->reply_to[0]->host;
		$rawContent = imap_fetchbody($mBox,$message_number,1.2);

		if(!strlen($rawContent)>0){
	    	$rawContent = imap_fetchbody($mBox,$message_number,1);
		}

		#<cc20130206>
		# http://www.php.net/manual/en/function.imap-fetchstructure.php for reference, especially
		# the first example.
		# Note that according to the docs I believed this should work:
		# 	$body_structure = imap_bodystruct($mBox,$message_number,1.2);
		# But it does not, simply returning FALSE.

		# this gets the structure of the main part of the message.
		# In cases where the message itself is the main part, the encoding
		# given in this section works well.
		$body_structure = imap_fetchstructure($mBox,$message_number);

		if(sp_pbe_is_primary_body_type_multipart($body_structure)) {
			# multipart structure, so even though we have the correct $rawContent (by asking
			# for the TEXT/HTML body via the 1.2 designation), we do not yet have the right
			# body structure, so decoding will fail under various circumstances.
			#
			# Specifically, Outlook 2010 will send multipart messages whenever the user
			# chooses to send messages in HTML or Rich Text format. In both formats,
			# Outlook 2010 sends a multipart message with two parts: 1. HTML, and 2. PLAIN.
			# (Outlook 2010 converts the Rich Text to HTML).
			#
			# If an Outlook 2010 user chooses the Plain Text format, it will send a single
			# part message and the encoding will be on that main part.
			#
			# Let's find the HTML part if present, falling back to the PLAIN part if not.
			$body_structure = sp_pbe_body_structure_for_html_part($body_structure);
		}

		$rawContent = sp_pbe_decode_raw_content($rawContent, $body_structure->encoding);

		if (!$maybeNew) {
			$start = strpos($rawContent, '[--id=#');
			if($start) {
				$slug = substr($rawContent, ($start+7), (strpos($rawContent, '#--]')-($start+7)));
			}
		}

		$content = substr($rawContent, 0, strpos($rawContent, '[-- '));
		if(empty($content) && $maybeNew) $content = $rawContent;

		$content = trim(utf8_encode(quoted_printable_decode($content)));

		# Deal with double carriage return/line feed... use a random string of X's
		$content = str_replace(chr(13).chr(10).chr(13).chr(10), 'XXXXXXXX', $content);
		$content = str_replace(chr(13).chr(10), ' ', $content);
		$content = str_replace('XXXXXXXX', chr(13).chr(10).chr(13).chr(10), $content);

		# Apple Mail - closing angle bracket
		$content = rtrim($content, '&gt;');

		# test author and subject before we go further
		if(!empty($subject)) $log['eTopic'] = $subject;

		$user = array();
		$author = sanitize_email($author);
		$log['eUser'] = $author;
		$userdata = get_user_by('email', $author);
		if(empty($userdata)) {
			# try for alternate email address
			$userdata = sp_pbe_alt_email($author);
		}
		if(!empty($userdata)) {
			$user = SP()->memberData->get($userdata->ID);
		}

		if(empty($user)) {
			$log['eLog']=__('Invalid User Email Address', 'sp-pbe').'<br />('.$author.')';
			sp_pbe_log($log);
		} else {
			# check for attachments
		    $structure = imap_fetchstructure($mBox, $message_number);
			$flattenedParts = new stdClass();
			$flattenedParts = sp_flattenParts($structure->parts);

			foreach($flattenedParts as $partNumber => $part) {
				switch($part->type) {
					case 0:
						# the HTML or plain text part of the email
					break;

					case 1:
						# multi-part headers, can ignore
					break;
					case 2:
						# attached message headers, can ignore
					break;

					case 3: # application
					case 4: # audio
					case 5: # image
					case 6: # video
					case 7: # other
						$filename = sp_getFilenameFromPart($part);
						if($filename) {
							# it's an attachment
							$attachments[$attId]['filename'] = $filename;
							$attachments[$attId]['stream'] = sp_getPart($mBox, $message_number, $partNumber, $part->encoding);
							$attId ++;
						}
					break;
				}
			}
			sp_emailpost_save($f, $subject, $slug, $user, $author, $content, $log, $attachments);
			unset($attachments);
			unset($flattenedParts);
		}
		imap_delete($mBox, $message_number);
	}
	imap_delete($mBox,'1:*');
	imap_expunge($mBox);
	imap_close($mBox, CL_EXPUNGE);
}

function sp_pbe_is_primary_body_type_text($a_part) {
	return($a_part->type==0);
}

function sp_pbe_is_primary_body_type_multipart($a_part) {
	return($a_part->type==1);
}

function sp_pbe_is_mime_subtype_specified($a_part) {
	return($a_part->ifsubtype==1);
}

function sp_pbe_does_mime_subtype_match_given_string($a_part, $the_matching_string) {
	return($a_part->subtype==$the_matching_string);
}

function sp_pbe_is_mime_subtype_html($a_part) {
	return(sp_pbe_is_mime_subtype_specified($a_part) && sp_pbe_does_mime_subtype_match_given_string($a_part, "HTML"));
}

function sp_pbe_is_mime_subtype_plain($a_part) {
	return(sp_pbe_is_mime_subtype_specified($a_part) && sp_pbe_does_mime_subtype_match_given_string($a_part, "PLAIN"));
}

# tests the key parameters of the part and returns true if it looks like a properly formed HTML part.
function sp_pbe_is_part_a_valid_html_part($a_part) {
	return(sp_pbe_is_primary_body_type_text($a_part) && sp_pbe_is_mime_subtype_html($a_part));
}

# tests the key parameters of the part and returns true if it looks like a properly formed PLAIN part.
function sp_pbe_is_part_a_valid_plain_part($a_part) {
	return(sp_pbe_is_primary_body_type_text($a_part) && sp_pbe_is_mime_subtype_plain($a_part));
}

# given a structure that we expect to have multiple parts, this function chooses
# a default part as our best guess fallback case where we might find the right
# encoding information. It's a fallback because we expect to actually find
# the exact correct part and its matching encoding info, but just to be
# defensive in case something doesn't *quite* match, we'll have a good chance
# to find a useful value.
# internally used by sp_pbe_body_structure_for_html_part().
function sp_pbe_get_a_default_part($a_structure) {
	$result = $a_structure; # if for some reason we don't find a better part to assign, just use this top-level structure.
	if(is_array($a_structure->parts) && count($a_structure->parts) >= 1) {
		$result = $a_structure->parts[0];
	}
	return $result;
}

# works through the given parts in the structure to find
# a well-formed HTML part. Returns that part if it finds
# it. Returns null otherwise.
# internally used by sp_pbe_body_structure_for_html_part().
function sp_pbe_scan_for_html_part($the_parts_array) {
	$result = null;
	foreach($the_parts_array as $this_part) {
		if(sp_pbe_is_part_a_valid_html_part($this_part)) {
			$result = $this_part; # found our matching part
			break;
		}
	}
	return $result;
}

# works through the given parts in the structure to find
# a well-formed PLAIN part. Returns that part if it finds
# it. Returns null otherwise.
# internally used by sp_pbe_body_structure_for_html_part().
function sp_pbe_scan_for_plain_part($the_parts_array) {
	$result = null;
	foreach($the_parts_array as $this_part) {
		if(sp_pbe_is_part_a_valid_plain_part($this_part)) {
			return $this_part; # found our matching part
			break;
		}
	}
	return $result;
}

# gets the structure of the most appropriate part of a multipart
# message that we can find. We use this 'most appropriate part'
# to identify how the raw content of the message has been encoded.
function sp_pbe_body_structure_for_html_part($a_structure) {
	$parts = $a_structure->parts;

	$result = sp_pbe_scan_for_html_part($parts);
	if(is_null($result)) {
		# if we didn't find an HTML part, now fall back to looking for a PLAIN part
		$result = sp_pbe_scan_for_plain_part($parts);
		if(is_null($result)) {
			# still didn't find a matching part, so fall back to our best default part option
			$result = sp_pbe_get_a_default_part($a_structure);
		}
	}

	return $result;
}

# decodes based on the appropriate given encoding.
# really only base64 and quoted-printable need to be
# decoded. The other encodings seem to be handled by
# PHP strings without issue.
function sp_pbe_decode_raw_content($the_raw_content, $the_encoding) {
	$result = $the_raw_content;
	switch($the_encoding) {
		case 3:
			$result = base64_decode($the_raw_content);
			break;
		case 4:
			$result = quoted_printable_decode($the_raw_content);
			break;
	}
	return $result;
}


# Support Functions for attachments

function sp_flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true) {
	if($messageParts) {
		foreach($messageParts as $part) {
			$flattenedParts[$prefix.$index] = $part;
			if(isset($part->parts)) {
				if($part->type == 2) {
					$flattenedParts = sp_flattenParts($part->parts, $flattenedParts, $prefix.$index.'.', 0, false);
				}
				elseif($fullPrefix) {
					$flattenedParts = sp_flattenParts($part->parts, $flattenedParts, $prefix.$index.'.');
				}
				else {
					$flattenedParts = sp_flattenParts($part->parts, $flattenedParts, $prefix);
				}
				unset($flattenedParts[$prefix.$index]->parts);
			}
			$index++;
		}
	}
	return $flattenedParts;
}

function sp_getPart($connection, $message_number, $partNumber, $encoding) {
	$data = imap_fetchbody($connection, $message_number, $partNumber);
	switch($encoding) {
		case 0: return $data; # 7BIT
		case 1: return $data; # 8BIT
		case 2: return $data; # BINARY
		case 3: return base64_decode($data); # BASE64
		case 4: return quoted_printable_decode($data); # QUOTED_PRINTABLE
		case 5: return $data; # OTHER
	}
}

function sp_getFilenameFromPart($part) {
	$filename = '';
	if($part->ifdparameters) {
		foreach($part->dparameters as $object) {
			if(strtolower($object->attribute) == 'filename') {
				$filename = $object->value;
			}
		}
	}
	if(!$filename && $part->ifparameters) {
		foreach($part->parameters as $object) {
			if(strtolower($object->attribute) == 'name') {
				$filename = $object->value;
			}
		}
	}
	return $filename;
}
