<?php

require_once( 'utilities.php' );

$version_file = parse_ini_file( $config_paths[ 'versions' ] . DIRECTORY_SEPARATOR . 'versions.ini', TRUE );
$version_current_id = $version_file[ 'current' ];

unset( $version_file[ 'current' ] );

$versions = array();
foreach( $version_file as $k => $v )
{
	$versions[ $k ] = array
	(
		'srcs' => $v[ 'srcs' ],
		'name' => $v[ 'name' ],
		'date' => $v[ 'date' ],
		'changelog' => $v[ 'changelog' ],
		'post_install' => $v[ 'post_install' ]
	);
}

$version_current = $versions[ $version_current_id ];

function save_versions()
{
	global $config_paths, $versions, $version_current_id;
	file_put_contents
	(
		$config_paths[ 'versions' ] . DIRECTORY_SEPARATOR . 'versions.ini',
		'current=' . $version_current_id . PHP_EOL . make_ini( $versions )
	);
}

?>
