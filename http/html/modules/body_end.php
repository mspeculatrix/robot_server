<!-- mod::body_end (std) -->
</body>
<?php
if(defined('APP_NAME') && file_exists(APP_NAME.'.js')) {
	// There's a local javascript file for this app
	echo TAB.'<script src="'.APP_NAME.'.js"></script>'.NL;
}
?>
