<?php

require_once( 'utilities.php' );

$version_file = parse_ini_file( $config_paths[ 'versions' ] . '/versions.ini', TRUE );
$version_current_id = $version_file[ 'current' ];

$versions = array();
unset( $version_file[ 'current' ] );
foreach( $version_file as $k => $v ) {
	$versions[ $k ] = array(
		'name' => $v[ 'name' ],
		'date' => $v[ 'date' ],
		'changelog' => $v[ 'changelog' ]
	);
}

$version_current = $versions[ $version_current_id ];

?>
