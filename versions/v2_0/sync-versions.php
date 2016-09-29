<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'curl.php' );
require_once( 'versions.php' );

require_administrator();

$repo = $config_paths[ 'repo' ];
$remote_version_file = parse_ini_string( do_curl( $repo . '/versions.ini' ), TRUE );
unset( $remote_version_file[ 'current' ] );

$updated = FALSE;
foreach( $remote_version_file as $k => $v ) {
	if( !in_array( $k, array_keys( $versions ) ) ) {
		$versions[ $k ] = $v;
		$path = $config_paths[ 'versions' ] . '/' . $k;
		mkdir( $path );
		foreach( explode( ',', $v[ 'srcs' ] ) as $src ) {
			file_put_contents( $path . '/' . $src, do_curl( $repo . '/' . $k . '/' . $src ) );
		}
		$updated = TRUE;
	}
}

save_versions();

if( $updated ) {
	redirect( 'version-manager.php?found_update=1' );
} else {
	redirect( 'version-manager.php?up_to_date=1' );
}

?>
