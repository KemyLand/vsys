<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'curl.php' );

require_administrator();

$repo = $config_paths[ 'repo' ];
$remote_version_file = parse_ini_string( do_curl( $repo . '/versions.ini' ), TRUE );
unset( $remote_version_file[ 'current' ] );

$updated = FALSE;
foreach( $remote_version_file as $k => $v ) {
	if( !in_array( $k, $versions ) ) {
		$versions[ $k ] = $v;
		$updated = TRUE;
	}
}

save_versions();

die('123');

if( $updated ) {
	redirect( 'version-manager.php?found_update=1' );
} else {
	redirect( 'version-manager.php?up_to_date=1' );
}

?>
