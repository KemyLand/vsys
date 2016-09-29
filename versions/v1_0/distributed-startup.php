<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_administrator();
require_property( 'enable_distributed_mode', FALSE );

$conn = db_connect();

$query = 'DELETE FROM DistributedClients';
db_query( $conn, $query );

$query = 'DELETE FROM DistributedUsers';
db_query( $conn, $query );

$addresses = explode( ",", $sys_config[ 'distributed_mode_clients' ] );
foreach( $addresses as $address ) {
	$query =
		'INSERT INTO DistributedClients( address, user ) VALUES( "'
		. $address
		. '", NULL )';

	db_query( $conn, $query );
}

$query = 'SELECT id, class FROM Users';
$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() ) {
	$query =
		'INSERT INTO DistributedUsers( user, skipped ) VALUES( '
		. $row[ 'id' ]
		. ', '
		. ( $row[ 'class' ] <= 1 ? '0' : '1' )
		. ' )';

	db_query( $conn, $query );
}

$sys_config[ 'enable_distributed_mode' ] = '1';
save_config();

logout_everyone();

db_disconnect( $conn );
redirect( 'distributed.php?success=1' );

?>
