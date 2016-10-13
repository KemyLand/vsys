<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_administrator();

$conn = db_connect();
foreach( $_GET as $k => $v )
{
	$sys_config[ $k ] = $v;
}

save_config();

db_register_event( $conn, 'Configuración del sistema modificada' );
db_disconnect( $conn );
redirect( 'admin.php?success=1' );

?>
