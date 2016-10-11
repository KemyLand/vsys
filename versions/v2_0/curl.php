<?php

require_once( 'utilities.php' );

function do_curl
(
	$url
)
{
	$handler = curl_init();
	curl_setopt( $handler, CURLOPT_URL, $url );
	curl_setopt( $handler, CURLOPT_RETURNTRANSFER, TRUE );

	$output = curl_exec( $handler );
	curl_close( $handler );

	return $output;
}

?>
