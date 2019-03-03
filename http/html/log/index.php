<?php
// ***** SHELDON/INDEX.PHP *****

// ***** PAGE SETTINGS *****
define ( 'APP_NAME', 'log' );
define ( 'PAGE_TYPE',	'col1' );
define ( 'LOGFILE_PATH', 'sheldon.log');
// ***** DO NOT EDIT *****
require_once ( $_SERVER['DOCUMENT_ROOT'].'/include/std_config.php' );

$pagemode = pagemode();
// ----- END OF DO NOT EDIT -----
$informMsg = '';
$updateBtnLabel = 'update';
switch($pagemode) {
	case 'del':
		$btnText = 'confirm deletion';
		$btnStyle = 'btn-danger';
		$btnTargetMode = 'delConfirm';
		$updateBtnLabel = 'cancel';
		break;
	case 'delconfirm':
		if(file_exists(LOGFILE_PATH)) {
			$deleted = unlink(LOGFILE_PATH);
			if($deleted) {
				$informMsg = 'log deleted';
			} else {
				$informMsg = 'failed to delete log';
			}
		} else {
			$informMsg = 'no log file found';
		}
	default:
		$btnText = 'delete log';
		$btnStyle = 'btn-warning';
		$btnTargetMode = 'del';
		break;
}


include_once(module('html_begin'));
include_once(module('head_begin'));
include_once(module('head_end'));
include_once(module('body_begin'));
include_once(module('navbar'));
include_once(module('content_begin'));
//include_once(module('masthead_begin'));
include_once(module('masthead_end'));
?>

<div id="header" class="container">
	<p>Sheldon log</p>
</div>

<div id="logSection" class="container">

<div id="logData" contenteditable>
	<pre>
<?php
	if(file_exists(LOGFILE_PATH)) {
		$fh = @fopen(LOGFILE_PATH, 'r');
		$temp = 'something';
		if($fh) {
			while(($line = fgets($fh, 4096)) != false) {
				echo $line;
			}
			if(!feof($fh)) {
				echo '<p class="warning">Unexpected bollocks with fgets()'.NL;
			}
			fclose($fh);
		}
	} else {
		echo '-- no log file --';
	}
?>	
</pre>
</div><!-- /logData -->

<div id="logButtons" class="container">
<?php
if(file_exists(LOGFILE_PATH)) {
	echo '<button id="clearBtn" type="button" class="btn btn-sm ';
	echo $btnStyle;
	echo '" onclick="window.location.href = \'';
	echo THIS_PAGE .  '?pm='. $btnTargetMode .'\'">';
	echo $btnText . '</button>'.NL;
}

	echo '<button id="updateBtn" type="button" class="btn btn-sm btn-primary" '
		.'onclick="window.location.href = \'' . THIS_PAGE .'\'">'
		.$updateBtnLabel . '</button>'.NL;

?>
</div>

</div><!-- /logSction -->


<div id="message" class="container">
	<p id="msgText"><?php echo $informMsg; ?></p>
</div>

<?php
include_once(module('content_end'));
include_once(module('body_end'));
include_once(module('html_end'));
?>
