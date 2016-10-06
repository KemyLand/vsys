<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_administrator();
require_property( 'enable_distributed_mode' );

if( !get_check( 'id' ) )
{
	redirect_main();
}

$id = $_GET[ 'id' ];

$conn = db_connect();

$query
	= 'SELECT user FROM DistributedClients WHERE id='
	. $id;

$user_id = db_query( $conn, $query )->fetch_assoc()[ 'user' ];
if( !empty( $user_id ) )
{
	$query
		= 'UPDATE DistributedUsers SET skipped=1 WHERE user='
		. $user_id;

	db_query( $conn, $query );

	$query
		= 'UPDATE DistributedClients SET user=NULL where id='
		. $id;

	db_query( $conn, $query );
}

db_disconnect( $conn );
redirect( 'distributed.php?success=1' );

?>
