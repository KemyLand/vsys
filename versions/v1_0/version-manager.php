<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'versions.php' );

require_administrator();

?>

<HTML>
	<HEAD>
		<META CHARSET="utf-8"/>
		<LINK REL="stylesheet" TYPE="text/css" HREF="style.css"/>
		<TITLE>Administrador de versiones</TITLE>
	</HEAD>
	<BODY>
		<?php upper_header(); ?>
	<DIV ID="content">

<?php

if( !empty( $_GET[ 'post_install' ] ) && $_GET[ 'post_install' ] == 1 ) {
	echo(
		'<DIV CLASS="infoMessage center">Versión '
		. $versions_current[ 'name' ]
		. ' instalada correctamente.</DIV><DIV CLASS="separator"></DIV>'
	);
}

if( !empty( $_GET[ 'up_to_date' ] ) && $_GET[ 'up_to_date' ] == 1 ) {
	echo(
		'<DIV CLASS="infoMessage center">No se encontraron actualizaciones.</DIV>'
		. '<DIV CLASS="separator"></DIV>'
	);
}

if( !empty( $_GET[ 'found_update' ] ) && $_GET[ 'found_update' ] == 1 ) {
	echo(
		'<DIV CLASS="infoMessage center">Se encontraron actualizaciones. '
		. 'Puede instalarlas utilizando el formulario a continuación.</DIV>'
		. '<DIV CLASS="separator"></DIV>'
	);
}

?>

		<TABLE>
			<TR>
				<TH>Versión activa</TH>
				<TD><?php echo( $version_current[ 'name' ] ); ?></TD>
			</TR>
			<TR>
				<TH><A HREF="sync-versions.php">Buscar actualizaciones</A></TH
			</TR>
		</TABLE>
		<DIV CLASS="separator"></DIV>
		<FORM ACTION="register-user.php" METHOD="POST">
			<TABLE>
				<TR>
					<TH>Versiones disponibles</TH>
				</TR>
				<TR>
					<TH>Nombre</TH>
					<TH>Fecha</TH>
					<TH>Historial de cambios</TH>
				</TH>

<?php

foreach( $versions as $version_id => $version ) {
	echo(
		'<TR>'
		. '<TD>' . $version[ 'name' ] . '</TD>'
		. '<TD>' . $version[ 'date' ] . '</TD>'
		. '<TD>' . $version[ 'changelog' ] . '</TD>'
	);

	if( $version_id != $version_current_id ) {
		echo(
			'<TD><A HREF="load-version.php?id='
			. $version_id
			. '">Instalar</A></TD>'
		);
	}

	echo( '</TR>' );
}

?>

			</TABLE>
		</FORM>
	</DIV>
	</BODY>
</HTML>
