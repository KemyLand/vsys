<?php

require_once( "utilities.php" );
require_once( "db.php" );

db_require_administrator();

if
( !post_check( 'username' ) ||
  !post_check( 'password' ) ||
  !post_check( 'repeat_password' ) ||
  !post_check( 'first_name' ) ||
  !post_check( 'last_name' ) ||
  !post_check( 'class' ) )
{
	redirect_main();
}

$username = $_POST[ 'username' ];
$password = $_POST[ 'password' ];
$repeat_password = $_POST[ 'repeat_password' ];
$first_name = $_POST[ 'first_name' ];
$last_name = $_POST[ 'last_name' ];
$user_class = $_POST[ 'class' ];

if( $password != $repeat_password )
{
	redirect( 'admin.php?password_mismatch=1' );
}

$conn = db_connect();
$query
	= 'INSERT INTO Users( username, password, first_name, last_name, class ) '
	. 'VALUES( "'
	. $username
	. '", SHA1("'
	. $password
	. '"), "'
	. $first_name
	. '", "'
	. $last_name
	. '", '
	. $user_class
	. ' )';

db_query( $conn, $query );

$event
	= 'Nuevo usuario '
	. $username
	. ' (' . $first_name . ' ' . $last_name . ') registrado con clase '
	. $user_class;

db_register_event( $conn, $event );
db_disconnect( $conn );

redirect( 'admin.php?success=1' );

?>
