<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'versions.php' );

db_require_administrator();
upper_header( 'Administrador de versiones' );

if( get_bool( 'post_install' ) )
{
	$changed_major_version = version_post_install();

	save_config();

	echo
	( '<DIV CLASS="infoMessage center">Versión '
	. $version_current[ 'name' ]
	. ' instalada correctamente.</DIV>'
	);

	if( $changed_major_version )
	{
		echo
		( '<DIV CLASS="infoMessage center">La instalación de '
		. $version_current[ 'name' ] . ' es una '
		. 'actualización mayor. Esta ha alterado la estructura '
		. 'de la base de datos, la cual ha sido restaurada a su '
		. 'configuración por defecto. Se ha almacenado una copia '
		. 'de seguridad de la base de datos antigua y se han removido '
		. 'versiones incompatibles del repositorio local.</DIV>'
		);
	}

	echo( '<DIV CLASS="separator"></DIV>' );
}

if( get_bool( 'up_to_date' ) )
{
	echo
	( '<DIV CLASS="infoMessage center">No se encontraron actualizaciones.</DIV>'
	. '<DIV CLASS="separator"></DIV>'
	);
}

if( get_bool( 'found_update' ) )
{
	echo
	( '<DIV CLASS="infoMessage center">Se encontraron actualizaciones. '
	. 'Puede instalarlas utilizando el formulario a continuación.</DIV>'
	. '<DIV CLASS="separator"></DIV>'
	);
}

?>

		<TABLE>
			<TR>
				<TH CLASS="borderless">Versión activa</TH>
				<TD CLASS="borderless"><?php echo( $version_current[ 'name' ] ); ?></TD>
			</TR>
			<TR>
				<TD CLASS="borderless">
					<A HREF="sync-versions.php">Buscar actualizaciones</A>
				</TD>
			</TR>
		</TABLE>
		<DIV CLASS="separator"></DIV>
		<FORM ACTION="register-user.php" METHOD="POST">
			<TABLE>
				<TR>
					<TH CLASS="borderless">Versiones disponibles</TH>
				</TR>
				<TR>
					<TH>Nombre</TH>
					<TH>Fecha</TH>
					<TH>Historial de cambios</TH>
				</TH>

<?php

foreach( $versions as $version_id => $version )
{
	echo
	( '<TR>'
	. '<TD>' . $version[ 'name' ] . '</TD>'
	. '<TD>' . $version[ 'date' ] . '</TD>'
	. '<TD>' . $version[ 'changelog' ] . '</TD>'
	);

	if( $version_id != $version_current_id )
	{
		echo
		( '<TD><A HREF="load-version.php?id='
		. $version_id
		. '">Instalar</A></TD>'
		);
	}

	echo( '</TR>' );
}

?>

			</TABLE>
		</FORM>

<?php lower_header(); ?>
