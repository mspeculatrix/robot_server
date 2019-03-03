<?php
/***
	std_library.php
***/

/**	\fn		clean_filename ( $filename, $allow_ext = FALSE )
	\brief	replaces '.' with '_' in a string, & uses strip_non_alphanum
	to get rid of problematic characters. Retains dot before extension
	if $allow_ext set to TRUE
	\param	$filename	- string containing filename, though it can be any text really
	\param	$allow_ext	(optional) - bool - retain full-stop before filename extension
	\return	string 	- changed name
*/
function clean_filename ( $filename, $allow_ext = FALSE ) {
	$newname = FALSE;
	$ext = FALSE;
	$filebits = explode ( '.', $filename );
	if ( $allow_ext && (count ( $filebits ) > 1 ) ) {
		$ext = array_pop ( $filebits  );
	}
	$newname = implode ( '_', $filebits );
	// we have now replaced all periods
	strip_non_alphanum ( $newname );
	if ( $ext ) { $newname .= '.'.$ext; }
	return $newname;
}

/**	\fn		clean_request ($key, $length=FALSE, $default=FALSE, $stripslash=FALSE, $stripmeta=FALSE, $striptags=TRUE)
	\param	$key	$_REQUEST key for desired data
	\param	$length		(optional) integer - def: FALSE - max number of characters allowed
	\param	$default	(optional) value - def: FALSE - value to return if nothing found in $_REQUEST
	\param	$stripslash	(optional) bool - def: FALSE - run through stripslashes()
	\param	$stripmeta	(optional) bool - def: FALSE - run through strip_meta()
	\param	$striptags	(optional) bool - def: TRUE - remove HTML code
	\brief	Gets the value of $key from $_REQUEST and cleans it
	If $length is specified, also trims it to that length.
	If $default is specified, this becomes the default and is returned if the array key isn't found.
	if $stripslash = TRUE, strips out slashes
	if $stripmeta = TRUE, strips out dubious chars.
	if $striptags = TRUE, removes HTML code
	Returns the value of the item, FALSE if not found or $default if specified
	\return	value of $_REQUEST item
*/
function clean_request ( 	$key,
							$length			= FALSE,
							$default		= FALSE,
							$stripslash		= FALSE,
							$stripmeta		= FALSE,
							$striptags		= FALSE,
							$strip_for_DB 	= FALSE ) {
	// First checks to see if $key item is in $_REQUEST.
	$request = $default;
	if ( array_key_exists ( $key, $_REQUEST ) ) {
		$request = trim ( $_REQUEST[$key] );
		if ( $length ) { $request = substr ( $request, 0, $length ); }
		if ( $stripslash ) { $request = stripslashes ( $request ); }
		if ( $stripmeta ) { strip_meta ( $request ); }
		if ( $striptags ) { strip_tags ( $request ); }
		if ( $strip_for_DB ) {
			// remove some of the characters that might be used for SQL insertion
			// this should be used only for simple stuff - eg, firstname, lastname etc
			$dbchars = array ( ';', '=', '(', ')' );
			$request = str_replace ( $dbchars, '', $request );
		}
		trim ( $request );
	}
	return $request;
}

function echoLogFile ( $file, $heading=false, $usetag=false,
						$formatTimestamp=true, $showErrors=false ) {
	$startTag = '';
	$endTag = '';
	if ($usetag) {
		$startTag ="<$usetag>";
		$endTag = "</$usetag>";
	}
	if ($heading) {
		echo $startTag.$heading.$endTag.NL;
	}
	if(file_exists($file)) {
		$fh = fopen($file, 'r');
			if ($fh) {
				while(($line = fgets($fh, 4096)) !== false) {
					$line = trim($line);
					echo $startTag;
					if ($formatTimestamp) {
						echo substr($line,0,4).'/'.substr($line,4,2).'/'.substr($line,6,2)
							.' '
							.substr($line,8,2).':'.substr($line,10,2).':'.substr($line,12,2)
							.substr($line,14);
					} else {
						echo $line;
					}
					echo $endTag.NL;
				}
			} else if($showErrors) {
				echo $startTag.'Error opening error file: $file'.$endTag.NL;
			}
		fclose($fh);
	} else if($showErrors) {
		echo $startTag.'-- File \''.$file.'\' not found.'.$endTag.NL;
	}
}

function module($modname) {
	// Look first to see if there's a module in a local dir.
	// We'll prioritise that to 'overload' any site-wide module
	$mod_pathFile = FALSE;
	if (file_exists('modules/'.$modname.'.php') ) {
		$mod_pathFile = 'modules/'.$modname.'.php';
	} elseif ( file_exists(DOC_ROOT_PATH.'modules/'.$modname.'.php' ) ) {
		// no local mod, so we'll check in the side-wide dir
		$mod_pathFile = DOC_ROOT_PATH.'modules/'.$modname.'.php';
	}
	// If nothing has been found, return the default module
	if (!$mod_pathFile) {
		echo '<p class="error">Missing module: '.$modname.'</p>'.NL;
		$mod_pathFile = DOC_ROOT_PATH.'modules/mod_not_found.php';
	}
	return $mod_pathFile;
}


/**	\name		pagemode ( )
	\param		$pagemode - default pagemode if none other found
	\return		string - pagemode
**/
function pagemode() {
	$pagemode = clean_request('pm', 20, FALSE, TRUE, TRUE, TRUE);
	if ($pagemode) {
		$pagemode = strtolower($pagemode);
	} else {
		$pagemode = 'index';	// default if nothing found
	}
	return $pagemode;
}

function readCfgFile($file) {
	$cfg = false;
	if(file_exists($file)) {
		$fh = fopen($file, 'r');
		if ($fh) {
			while(($line = fgets($fh, 4096)) !== false) {
				if( (substr($line,0,1) != '#')
					&& (strlen($line) > 3)
					&& (strpos($line, '=') !== false )) {
					$lineElements = explode('=', $line);
					$key = trim($lineElements[0]);
					$cfg[$key] = trim($lineElements[1]);
				}
			}
		} else {
			echo "Error opening file: $file".NL;
		}
		fclose($fh);
	}
	return $cfg;
}

function writeCfgFile($file, $dataArray, $comment=FALSE, $delim='=') {
	$fh = fopen($file, 'w');
	if($comment) { fwrite($fh, '# '.$comment."\n"); }
	foreach ($dataArray as $key => $value) {
		fwrite($fh, $key.$delim.$value."\n");
	}
	fclose($fh);
}

function writeMsgFile($file, $msg) {
	$fh = fopen($file, 'w');
	fwrite($msg."\n");
	fclose($fh);
}

/**	\name	redirect()
	\brief	redirect to another page
*/
function redirect ( $redirectpage = FALSE, $query = FALSE ) {

	$defaultpage = '/index.php'; // default to site home page

	/*	Priority is given to the values in the params.
		If we've not given a specific target & there are
		session settings, use the session vars */
	if ( !$redirectpage && !empty ( $_SESSION['ref_page'] ) ) {
		$redirectpage = $_SESSION['ref_page'] ;
		if ( !$query && !empty ( $_SESSION['ref_pagequery'] ) ) {
			$redirectpage .= '?'.$_SESSION['ref_pagequery'] ;
		}
	} elseif ( $redirectpage && $query ) {
		$redirectpage .= '?'.$query;
	}
	/*	At this point, if we've passed a specific page, then that
		will be used. If not, we've used the session var. If that
		doesn't exist, then $redirectpage is still FALSE, so
		set it to the default */
	if ( !$redirectpage ) $redirectpage = $defaultpage;
	header ( 'Location: http://'.$_SERVER['HTTP_HOST'].$redirectpage );
	exit;
}

function strip_meta ( &$rawdata ) {
	/*	NB: argument passed by reference.
		Strip out dubious characters */
	$metachars = array ( '*', '$', '|' );
	$rawdata = str_replace ( $metachars, '', $rawdata );
	return;
}

function strip_non_alphanum ( &$rawdata ) {
	$changed = FALSE;
	$orig = $rawdata;
	$badchars = array ( ' ', '+', '/', '\\', '&', '.', ',', '@', '#',
		'*', '^', '?', '!', '%20', '%2F', '%', '$', '£', '~', '[', ']',
		'(', ')', "'", '"', '<', '>' );
	$rawdata = str_replace ( $badchars, '', $rawdata );
	if ( $rawdata != $orig ) { $changed = TRUE; }
	return $changed;
}

function print_dict ($dict) {
	echo '<table class="dict">'.NL;
	foreach ( $dict as $key => $item ) {
		echo '<tr><td>'.$key.'</td><td>'.$item.'</td></tr>'.NL;
	}
	echo '</table>'.NL;
	return;
}

function println ($val) {
	echo '<p>'.$val.'</p>'.NL;
}

/**	\fn		sanitise (&$string, $maxlength=FALSE, $stripslash=FALSE, $stripmeta=FALSE, $striptags=TRUE)
	\brief	Similar to clean_request but cleans any string passed by ref
	Optionally runs the string through stripslashes(), strip_meta and strip_tags() and trims to
	specified length, according to arguments passed
	\param	&$string	string to be santised
	\param	$maxlength	(optional) integer - def: FALSE - max number of characters allowed
	\param	$stripslash	(optional) bool - def: FALSE - run through stripslashes()
	\param	$stripmeta	(optional) bool - def: FALSE - run through strip_meta()
	\param	$striptags	(optional) bool - def: TRUE - remove HTML code
	\todo	Should make more use of this function!!
	\return	void
**/
function sanitise ( &$string, $maxlength=FALSE, $stripslash=FALSE, $stripmeta=FALSE, $striptags=TRUE ) {
	$string = trim ( $string );
	if ( $stripslash ) { $string = stripslashes ( $string ); }
	if ( $stripmeta ) { strip_meta ( $string ); }
	if ( $striptags ) { $string = strip_tags ( $string ); }
	if ( $maxlength ) { $string = substr ( $string, 0, $maxlength ); }
	$string = trim ( $string );
	return;
}



/**	\fn		trim_text ($text, $length, $addellipsis=TRUE)
	\brief	Trims a given string to the specified length but without cutting into words.
	Also adds an ellipsis to the end (which can be switched off)
	\param	$text			string to be trimmed
	\param	$length			integer - max length of trimmed string
	\param	$addellipsis	(optional) bool - def:TRUE - add '...' to end of trimmed string
	\return	modified text if original was longer than trim length, otherwise returns original
**/
function trim_text ( $text, $length, $addellipsis=TRUE ) {
	/* this trims the text to the selected length, but without cutting through words */
	if ( strlen ( $text ) <= $length ) {
		return $text;
	} else {
		$words = explode ( ' ',$text );
		$newphrase = '';
		$finished = FALSE;
		while ( !$finished ) {
			$newphrase .= array_shift ( $words ).' ';
			// if $newphrase plus the next word in the list plus a space would be
			// longer than our target length, then finish.
			if ( strlen ( $newphrase.$words[0].' ' ) > $length ) { $finished = TRUE; }
		}
		trim ( $newphrase );	// get rid of any trailing space
		if ( $addellipsis ) { $newphrase .= '&nbsp;...'; }
		return $newphrase;
	}
}
?>
