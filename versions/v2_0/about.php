<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_login();
require_property( 'enable_about_page' );

upper_header( 'Acerca de VSYS' );

?>

		<TABLE>
			<TR>
				<TH>Licencia y derechos de autor</TH>
			</TR>
			<TR>
				<TD CLASS="monospace">
					<P>
						Copyright &copy; 2016: Alejandro Soto &lt;alejandrosotochacon@yahoo.es&gt;
					</P>
					<P>
						Este programa es software libre; usted puede redistribuirlo y/o modificarlo
						bajo los términos de la Licencia Pública General de GNU, publicada por la
						Fundación por el Software Libre, en su tercera versión, o, a su opinión,
						cualquier versión posterior.
					</P>
					<P>
						Este programa es distribuido en la expectativa de que le sea útil, pero
						SIN NINGUNA GARANTÍA; ni siquiera la garantía implícita de COMERCIABILIDAD
						o SITUABILIDAD PARA UN PROPÓSITO DADO. Vea la Licencia Pública General de GNU
						para más información.
					</P>
					<P>
						Usted debió haber recibido una copia de la Licencia Pública General de GNU
						junto con este programa; de no ser así, por favor visite
						<A HREF="http://www.gnu.org/licenses/">&lt;http://www.gnu.org/licenses/&gt;</A>.
					</P>
				</TD>
			</TR>

<?php

foreach( scandir( getcwd() ) as $path )
{
	if( $path[0] == '.' )
	{
		continue;
	}

	if( is_dir( $path ) )
	{
		echo
		( '<TR><TH><I>'
		. html( $path )
		. ' es un directorio</I></TH></TR>'
		);
	} else if( !is_link( $path ) )
	{
		echo
		( '<TR><TH>Código fuente de <I>'
		. html( $path )
		. '</I></TH></TR>'
		. '<TR><TD CLASS="source"><CODE>'
		);

		$file = fopen( $path, 'r' );
		echo( html( fread( $file, filesize( $path ) ) ) );
		fclose( $file );

		echo( '</CODE></TD></TR>' );
	} else
	{
		echo
		( '<TR><TH><I>'
		. html( $path )
		. '</I> es un enlace simbólico a <I>'
		. html( readlink( $path ) )
		. '</I></TH></TR>'
		);
	}
}

?>

		</TABLE>

<?php lower_header(); ?>
