<?php

require_once( "utilities.php" );

require_administrator();

if( empty( $_GET[ 'filename' ] ) ) {
	redirect_main();
}

unlink( 'style.css' );
copy( $_GET[ 'filename' ], 'style.css' );
redirect( 'admin.php?success=1' );

?>
