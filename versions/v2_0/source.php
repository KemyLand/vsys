<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_login();
require_property( 'enable_about_page' );

upper_header( 'Código fuente' );

foreach( scandir( getcwd() ) as $path )
{
	if( $path[0] == '.' )
	{
		continue;
	}

	echo( '<TABLE>' );
	if( is_dir( $path ) )
	{
		echo
		( '<TR><TH CLASS="borderless"><I>'
		. html( $path )
		. '</I> es un directorio</TH></TR>'
		);
	} else if( !is_link( $path ) )
	{
		echo
		( '<TR><TH CLASS="borderless">Código fuente de <I>'
		. html( $path )
		. '</I></TH></TR>'
		. '<TR><TD CLASS="source"><PRE><CODE>'
		);

		$file = fopen( $path, 'r' );
		echo( html( fread( $file, filesize( $path ) ) ) );
		fclose( $file );

		echo( '</CODE></PRE></TD></TR>' );
	} else
	{
		echo
		( '<TR><TH CLASS="borderless"><I>'
		. html( $path )
		. '</I> es un enlace simbólico a <I>'
		. html( readlink( $path ) )
		. '</I></TH></TR>'
		);
	}

	echo( '</TABLE><DIV CLASS="separator"></DIV>' );
}

lower_header();

?>
