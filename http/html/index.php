<?php
// ***** PAGE SETTINGS *****
define ( 'APP_NAME', 'home' );
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
include_once(module('masthead_begin'));
include_once(module('masthead_end'));

//print_dict($_SERVER);
?>
<div class="container">
	<div class="row">
		<p>Docker version of robot server.</p>
	</div>
</div>
<?php
include_once(module('content_end'));
include_once(module('body_end'));
include_once(module('html_end'));
?>
