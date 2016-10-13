<?php

require_once( 'utilities.php' );

function do_curl
(
	$url
)
{
	return file_get_contents( $url );
}

?>
