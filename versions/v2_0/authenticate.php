<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_property( 'enable_distributed_mode', FALSE );

if( session_status() == PHP_SESSION_ACTIVE )
{
	session_unset();
	session_destroy();
}

if( !post_check( 'username' ) || !post_check( 'password' ) )
{
	redirect( 'login.php' );
}

$username = mysqli_real_escape_string( $_POST[ 'username' ] );
$password = mysqli_real_escape_string( $_POST[ 'password' ] );

$conn = db_connect();
$query
	= 'SELECT COUNT(*) FROM Users WHERE username="'
	. $username
	. '" AND password=SHA1("'
	. $password
	. '")';

if( db_query( $conn, $query )->fetch_row()[0] == 0 )
{
	$event
		= 'Fallo de inicio de sesión con nombre de usuario '
		. $username
		. ' desde '
		. $_SERVER[ 'REMOTE_ADDR' ];

	db_register_event( $conn, $event );
	db_disconnect( $conn );

	redirect( 'login.php?failed=1' );
}

$query
	= 'SELECT id, first_name, last_name, class FROM Users WHERE username="'
	. $username
	. '"';

$row = db_query( $conn, $query )->fetch_assoc();

$id = $row[ 'id' ];
$first_name = $row[ 'first_name' ];
$last_name = $row[ 'last_name' ];
$user_class = $row[ 'class' ];

session_name( 'szLogin' );
session_start();

$_SESSION[ 'login' ] = 1;
$_SESSION[ 'id' ] = $id;
$_SESSION[ 'username' ] = $username;
$_SESSION[ 'first_name' ] = $first_name;
$_SESSION[ 'last_name' ] = $last_name;
$_SESSION[ 'class' ] = $user_class;

$event
	= 'Inicio de sesión por '
	. $first_name
	. ' '
	. $last_name
	. ' desde '
	. $_SERVER[ 'REMOTE_ADDR' ];

db_register_event( $conn, $event );
db_disconnect( $conn );

redirect_main();

?>
