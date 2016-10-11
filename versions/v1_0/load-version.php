<?php

require_once( 'utilities.php' );
require_once( 'versions.php' );

require_administrator();

if( !get_check( 'id' ) || !in_array( $_GET[ 'id' ], array_keys( $versions ) ) )
{
	redirect_main();
}

$id = $_GET[ 'id' ];
$new_version = $versions[ $id ];

foreach( explode( ',', $version_current[ 'srcs' ] ) as $src )
{
	unlink( $src );
}

foreach( explode( ',', $new_version[ 'srcs' ] ) as $src )
{
	file_put_contents
	(
		$src,
		file_get_contents( $config_paths[ 'versions' ] . PATH_SEPARATOR . $id . PATH_SEPARATOR . $src )
	);
}

$version_current_id = $id;
$version_current = $versions[ $version_current_id ];
save_versions();

redirect( $new_version[ 'post_install' ] );

?>
