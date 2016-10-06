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

if( get_bool( 'release' ) )
{
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
}

$operating_ids = array();
$query = 'SELECT user FROM DistributedClients';
$result = db_query( $conn, $query );

while( $row = $result->fetch_assoc() )
{
	if( !empty( $row[ 'user' ] ) )
	{
		array_push( $operating_ids, $row[ 'user' ] );
	}
}

$query = 'SELECT user, skipped FROM DistributedUsers';
$result = db_query( $conn, $query );

$found = FALSE;
while( $row = $result->fetch_assoc() )
{
	$user_id = $row[ 'user' ];
	$skipped = $row[ 'skipped' ];

	$query
		= 'SELECT username FROM Users WHERE id='
		. $user_id;

	$username = db_query( $conn, $query )->fetch_assoc()[ 'username' ];
	if
	( $skipped ||
	  in_array( $user_id, $operating_ids ) ||
	  db_already_voted_election( $conn, $username, $sys_config[ 'distributed_mode_election' ] )
	)
	{
		continue;
	}

	$found = TRUE;
	break;
}

if( !$found )
{
	db_disconnect( $conn );
	redirect( 'distributed.php?ran_out=1' );
}

$query
	= 'UPDATE DistributedClients SET user='
	. $user_id
	. ' WHERE id='
	. $id;

db_query( $conn, $query );

db_disconnect( $conn );
redirect( 'distributed.php?assignee=' . $user_id );

?>
