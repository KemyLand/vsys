<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_administrator();

if( !get_check( 'filename' ) )
{
	redirect_main();
}

$sys_config[ 'style' ] = $_GET[ 'filename' ];
save_config();

redirect( 'admin.php?success=1' );

?>
