<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_login();
require_property( 'enable_unauthenticate' );

if( $_SESSION[ 'login' ] == 1 ) {
	$conn = db_connect();
	if( $_SESSION[ 'class' ] <= 2 ) {
		$event =
			'Cierre de sesión por '
			. whole_name()
			. ' desde '
			. $_SERVER[ 'REMOTE_ADDR' ];
	} else {
		$event =
			'Intento de cierre de sesión por usuario hipervisor desde '
			. $_SERVER[ 'REMOTE_ADDR' ];
	}

	db_register_event( $conn, $event );
	db_disconnect( $conn );

	$_SESSION[ 'login' ] = 0;
	session_unset();
	session_destroy();
}

redirect( 'login.php' );

?>
