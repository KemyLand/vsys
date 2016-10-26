<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_login();
require_property( 'enable_about_page' );

upper_header( 'Manual de referencia' );

function section( $title, $contents, ...$subsections )
{
	return (object)array
	(
		'title' => $title,
		'contents' => $contents,
		'subsections' => $subsections
	);
}

function show_entries_recursive( $printer, $index, $entries, $level )
{
	if( strlen( $index ) != 0 )
	{
		$index .= '.';
	}

	$counter = 1;
	foreach( $entries as $entry )
	{
		call_user_func( $printer, $entry, $index . $counter, $level );
		$counter++;
	}
}

function show_manual( $printer, $manual )
{
	show_entries_recursive( $printer, "", $manual, 0 );
}

$manual = array
(
	section
	(
		'Interfaz de usuario',
		array(),
		section
		(
			'Sesiones y permisos de usuario',
			array
			(
				'Si el modo distribuido no se encuentra habilitado, ' .
				'un usuario debe iniciar sesión por medio de un ' .
				'nombre de usuario y una contraseña antes de poder ' .
				'hacer uso del sistema. De lo contrario, si la máquina ' .
				'cliente del usuario se encuentra en la lista blanca ' .
				'de modo distribuido, se le asignará la sesión de un ' .
				'usuario automáticamente en función de los parámetros ' .
				'definidos en la configuración de modo distribuido. Si ' .
				'el nombre de usuario y/o contraseña no coinciden, o la ' .
				'máquina cliente no está autorizada a participar en modo ' .
				'distribuido, el intento de inicio de sesión es denegado.',
				'Cada usuario es clasificado según sus permisos como ' .
				'usuario común, moderador o administrador. Los moderadores ' .
				'tienen acceso al panel de moderación, y los administradores ' .
				'a su vez tienen acceso al panel de administración.',
				'Un usuario especial, el usuario hipervisor, posee permisos ' .
				'similares a los de un administrador. El usuario hipervisor ' .
				'no requiere ningún tipo de inicio de sesión, ya que esta es ' .
				'iniciada automáticamente según los parámetros del fichero de ' .
				'configuración.',
				'De ser permitido por el fichero de configuración, todos los ' .
				'usuarios, con excepción del usuario hipervisor, pueden cerrar ' .
				'sesión a través del panel superior.'
			)
		),
		section
		(
			'Panel superior',
			array
			(
				'El panel superior, de estar habilitado en el fichero de ' .
				'configuración, se encuentra ubicado en la parte superior ' .
				'de la pantalla. De estar habilitado cada uno, a todos los ' .
				'usuarios se le mostrarán enlances en el panel superior al ' .
				'registro de votos, al registro de propuestas, la sección ' .
				'"Acerca de" y el mecanismo de cierre de sesión. A los ' .
				'moderadores se les mostrará un enlace al panel de moderación, ' .
				'y a los administradores, además, se les mostrará un enlace al ' .
				'panel de administración.',
				'El panel superior también muestra el nombre y apellidos del ' .
				'usuario cuya sesión está activa, o "Usuario hipervisor" en el ' .
				'caso del usuario hipervisor.'
			)
		)
	),
	section
	(
		'Sistema de propuestas',
		array(),
		section
		(
			'Postulado de propuestas',
			array()
		),
		section
		(
			'Votación',
			array()
		),
		section
		(
			'Aprobación',
			array()
		),
		section
		(
			'Denegación',
			array()
		),
		section
		(
			'Propuestas mutuamente exclusivas y elecciones',
			array()
		)
	),
	section
	(
		'Modelo de seguridad',
		array()
	),
	section
	(
		'Panel de moderación',
		array()
	),
	section
	(
		'Panel de administración',
		array()
	)
);

?>

		<TABLE>
			<TR>
				<TH CLASS="borderless">Manual de referencia de VSYS</TH>
			</TR>
			<TR>
				<TD STYLE="text-align: left">
					<OL>

<?php

function show_index_entry( $entry, $index, $level )
{
	echo( '<LI>' );

	echo( '<A HREF="#' . $index . '" CLASS="raw">' . html( $entry->title ) . '</A>' );
	if( count( $entry->subsections ) != 0 )
	{
		echo( '<OL>' );
		show_entries_recursive( 'show_index_entry', $index, $entry->subsections, $level + 1 );
		echo( '</OL>' );
	}

	echo( '</LI>' );
}

show_manual( 'show_index_entry', $manual );

?>

					</OL>
				</TD>
			</TR>
		</TABLE>

<?php

function show_proper_entry( $entry, $index, $level )
{
	echo
	( '<DIV CLASS="separator"></DIV>'
	. '<A CLASS="raw" ID="' . $index . '"><H' . strval( $level + 2 ) . '>'
	. '&sect;&nbsp;' . $index . ' &mdash; ' . $entry->title
	. '</H' . strval( $level + 2 ) . '></A>'
	);

	foreach( $entry->contents as $paragraph )
	{
		echo( '<P>' . html( $paragraph ) . '</P>' );
	}

	show_entries_recursive( 'show_proper_entry', $index, $entry->subsections, $level + 1 );
}

show_manual( 'show_proper_entry', $manual );

lower_header();

?>
