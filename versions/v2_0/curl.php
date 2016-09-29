<?php

require_once( 'utilities.php' );

function do_curl( $url )
{
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
	$output = curl_exec( $ch );
	curl_close( $ch );
	return $output;
}

?>
