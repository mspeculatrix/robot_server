<?php
/**
	std_config.php
**/

define ( 'DOC_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/' );
define ( 'INC_PATH', DOC_ROOT_PATH.'include/' );
define ( 'MOD_PATH', DOC_ROOT_PATH.'modules/' );
define ( 'LIB_PATH', DOC_ROOT_PATH.'library/' );
require_once(DOC_ROOT_PATH.'site_config.php');
require_once ( INC_PATH.'std_library.php' );
require_once ( INC_PATH.'db_library.php' );

define ( 'THIS_PAGE', $_SERVER['PHP_SELF'] );
define ( 'NL',	"\n" );
define ( 'EOL',	"\r\n" );
define ( 'TAB',	'    ' );
define ( 'DIVIDER', '<span class="menu_divider"> | </span>' );
define ( 'MENU_DIVIDER', '<span class="menu_divider"> | </span>' );
define ( 'DIV_CLEAR_BOTH', '<div class="clear" style="clear:both;"></div>'.NL );
define ( 'DIV_CLEAR_LEFT', '<div class="clear" style="clear:left;"></div>'.NL );
define ( 'DIV_CLEAR_RIGHT', '<div class="clear" style="clear:right;"></div>'.NL );

define ( 'BROWSER', $_SERVER['HTTP_USER_AGENT'] );
define ( 'USER_IP', $_SERVER['REMOTE_ADDR'] );

?>
