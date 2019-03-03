<!-- mod::head_begin (zola) -->
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
	if(defined('APP_NAME') && defined('SITE_NAME')) {
		echo TAB.'<title>'.SITE_NAME.' : '.APP_NAME.'</title>'.NL;
	}
?>
	<!-- ----- CSS ----------------------------------------------- -->
	<link href="/css/bootstrap.css" rel="stylesheet">
	<link href="/css/bootstrap-grid.css" rel="stylesheet">
	<link href="/css/bootstrap-reboot.css" rel="stylesheet">
	<link rel="stylesheet" id="site-css" type="text/css" media="all" href="/site.css" />
<?php
	if(file_exists('local.css')) {
		echo TAB.'<link rel="stylesheet" id="local-css" '
			.'type="text/css" media="all" '
			.'href="local.css" />'.NL;
	}
?>
	<!-- ----- JAVASCRIPT ---------------------------------------- -->
	<script src="/js/jquery-3.3.1.min.js"></script>
	<script src="/js/bootstrap.js"></script>
	<script src="/js/bootstrap.bundle.js"></script>
	<script src="/js/smd_jslib.js"></script>
	<script src="/js/fontawesome-5.3.1-all.js"></script>
<?php
	if(file_exists('local.js')) {
		echo TAB.'<script src="local.js"></script>'.NL;
	}
	if(file_exists(APP_NAME.'.js')) {
		echo TAB.'<script src="'.APP_NAME.'.js"></script>'.NL;
	}
?>
