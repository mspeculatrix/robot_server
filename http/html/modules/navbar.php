<!-- mod::navbar (std) -->
<?php
$navItems = array (
	'home' 		=> array (  'url' => '/index.php',
							'icon'	=> 'fa fa-home',
							'label' => '<' ),
	'motion' 	=> array ( 	'url' => '/motion/index.php',
							'icon'	=> 'fas fa-truck-monster',
							'label' => 'motion' ),
	'remconfig'	=> array (  'url' => '/remconfig/index.php',
							'icon'	=> 'fas fa-cog',
							'label' => 'config'),
	'log' 		=> array ( 	'url' => '/log/index.php',
							'icon'	=> 'fas fa-file-alt',
							'label' => 'log' )
);

echo '<div class="container">'.NL;

echo '<ul class="nav nav-tabs" role="tablist">'.NL;

foreach ( $navItems as $app => $details ) {
	echo '<li role="presentation" class="nav-item">';
	echo '<a href="'
		.$details['url'].'" title="'.$details['label'].'" class="nav-link';
	if ( APP_NAME == $app ) echo ' active';
	echo '">';
	if ($details['icon']) {
		echo '<i class="'.$details['icon'].'"></i> ';
	}
	echo $details['label'].'</a></li>'.NL;
}

echo '</ul>'.NL;

echo '</div><!-- .container -->'.NL;
?>
