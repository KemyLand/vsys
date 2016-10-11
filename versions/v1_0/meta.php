<?php

require_once( 'utilities.php' );

function get_proposal_meta
(
	$id
)
{
	$meta_ref_filename = 'meta' . PATH_SEPARATOR . $id . '.ini';
	if( !file_exists( $meta_ref_filename ) )
	{
		return array();
	}

	$output = array();
	$meta_ref = parse_ini_file( $meta_ref_filename, TRUE );
	foreach( explode( ',', $meta_ref[ 'FIELDS' ] ) as $meta_field )
	{
		$meta_title = $meta_ref[ 'TITLE' ][ $meta_field ];
		$meta_data = $meta_ref[ 'DATA' ][ $meta_field ];
		switch( $meta_ref[ 'TYPE' ][ $meta_field ] )
		{
			case 'RAW':
				$output[ $meta_title ] = html( $meta_data );
				break;

			case 'IMG':
				$output[ $meta_title ] = '<IMG SRC="meta/' . $id . '-' . $meta_data . '"/>';
				break;
		}
	}

	return $output;
}

?>
