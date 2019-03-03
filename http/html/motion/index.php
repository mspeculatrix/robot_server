<?php
// ***** SHELDON/INDEX.PHP *****

// ***** PAGE SETTINGS *****
define ( 'APP_NAME', 'motion' );
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

<div id="motionControls">
<div id="headingCtrl" class="container controlContainer">
	<div class="motionRow row">
		<div class="col col-4"><button id="veerLeftBtn" type="button" class="motionBtn btn btn-sm btn-warning">veer<br>left</button></div>
		<div class="col col-4"><button id="fwdBtn" type="button" class="motionBtn btn btn-sm btn-success">forward</button></div>
		<div class="col col-4"><button id="veerRightBtn" type="button" class="motionBtn btn btn-sm btn-warning">veer<br>right</button></div>
	</div>
	<div class="motionRow row">
		<div class="col col-4"><button id="rotateLeftBtn" type="button" class="motionBtn btn btn-sm btn-primary">< rotate</button></div>
		<div class="col col-4"><button id="startStopBtn" type="button" class="motionBtn btn btn-sm btn-warning">START</button></div>
		<div class="col col-4"><button id="rotateRightBtn" type="button" class="motionBtn btn btn-sm btn-primary">rotate ></button></div>
	</div>
	<div class="motionRow row">
		<div class="col col-4"></div>
		<div class="col col-4"><button id="revBtn" type="button" class="motionBtn btn btn-sm btn-primary">reverse</button></div>
		<div class="col col-4"></div>
	</div>
</div>

<div id="speedCtrl" class="container controlContainer">
	<div class="motionRow row">
		<div class="col col-3"><button id="crawlBtn" type="button" class="speedBtn btn btn-sm btn-secondary">crawl</button></div>
		<div class="col col-3"><button id="slowBtn" type="button" class="speedBtn btn btn-sm btn-secondary">slow</button></div>
		<div class="col col-3"><button id="normalBtn" type="button" class="speedBtn btn btn-sm btn-secondary">normal</button></div>
		<div class="col col-3"><button id="fastBtn" type="button" class="speedBtn btn btn-sm btn-secondary">fast</button></div>
	</div>
</div>
</div><!-- /motionControls -->

<div id="msgsent">--</div>

<!-- <div id="state">
	<p><span id="startStopVal"></span>
		<span class="dataSep">|</span>
		<span id="headingVal"></span>
		<span class="dataSep">|</span>
		<span id="speedVal"></span>
	</p>
</div> -->

<div id="connect">
	<button id="connectBtn" type="button" class="btn btn-sm btn-warning">connect</button>
	<span id="msg"></span>
	<button id="pingBtn" type="button" class="btn btn-sm btn-warning">ping</button>
</div>

<div id="warning">
	<p id="warningMsg"></p>
	<button id="clrWarningBtn" type="button" class="btn btn-sm btn-warning">clear</button>
</div>

<div id="ip">
	<p id="currIP">Robot's IP: <span id="sheldonIP"></span><button id="changeIPBtn" type="button" class="btn btn-sm btn-link">change</button></p>
	<form id="ipForm">
		<div id="ipData">
			<input type="text" class="form-control col-md-11" id="ipField" size="15" />
			<button id="submitIPBtn" type="button" class="btn btn-sm btn-warning">set &raquo;</button>
			<button id="cancelIPBtn" type="button" class="btn btn-sm btn-secondary">cancel</button>
		</div>
	</form>
</div>


<?php
include_once(module('content_end'));
include_once(module('body_end'));
include_once(module('html_end'));
?>
