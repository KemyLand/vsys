<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_administrator();
require_property( 'enable_distributed_mode' );

$conn = db_connect();

$query = 'DELETE FROM DistributedClients';
db_query( $conn, $query );

$query = 'DELETE FROM DistributedUsers';
db_query( $conn, $query );

$sys_config[ 'enable_distributed_mode' ] = '0';
save_config();

logout_everyone();

db_disconnect( $conn );
redirect( 'distributed.php?success=1' );

?>
