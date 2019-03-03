<?php
// ***** PAGE SETTINGS *****
define ( 'APP_NAME', 'remconfig' );
define ( 'PAGE_TYPE',	'col1' );

// ***** DO NOT EDIT *****
require_once ( $_SERVER['DOCUMENT_ROOT'].'/include/std_config.php' );
$pagemode = pagemode();
// ----- END OF DO NOT EDIT -----

include_once(module('html_begin'));
include_once(module('head_begin'));
include_once(module('head_end'));
include_once(module('body_begin'));
include_once(module('navbar'));
include_once(module('content_begin'));
//include_once(module('masthead_begin'));
include_once(module('masthead_end'));
?>

<!-- div id="hostip"><?php echo $_SERVER['SERVER_ADDR'] ?></div -->

<div id="remcfg" class="container">
	<form id="remSvrForm" >
		<div class="section row">Robot</div>
		<div id="webhost" class="row">
			<div class="col col-6">API IP: <input type="text" class="form-control col-md-11" id="robotIPField" size="15" /></div>
			<div class="col col-6">port: <input type="text" class="form-control col-md-11" id="robotPortField" size="15" /></div>
		</div>
		<div>Address: <span id="robotAddr"></span></div>
		<button id="setRobotCfg" type="button" class="btn btn-sm btn-warning">set &raquo;</button>
		
		<hr />
		
		<div class="section row">Remote server</div>
		<div id="webhost" class="row">
			<div class="col col-6">IP: <input type="text" class="form-control col-md-11" id="remWebSvrIPField" size="15" /></div>
			<div class="col col-6">port: <input type="text" class="form-control col-md-11" id="remWebSvrPortField" size="15" /></div>
		</div>

		<div id="webhost" class="row">
			<div class="col col-6">Websockets port: <input type="text" class="form-control col-md-11" id="remWebSvrWSPortField" size="15" /></div>
			<div class="col col-6">API port: <input type="text" class="form-control col-md-11" id="remWebSvrAPIPortField" size="15" /></div>
		</div>

		<div id="svrResponse">Response: <span id="respStatus"></span>&nbsp;<span id="respRecvd"></span></div>

		<button id="submitCfg" type="button" class="btn btn-sm btn-warning">send settings &raquo;</button>
	</form>
</div><!-- /remcfg -->

<?php
include_once(module('content_end'));
include_once(module('body_end'));
include_once(module('html_end'));
?>
