<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'versions.php' );

require_administrator();

$conn = db_connect();

?>

<HTML>
	<HEAD>
		<META CHARSET="utf-8"/>
		<LINK REL="stylesheet" TYPE="text/css" HREF="style.css"/>
		<TITLE>Panel de administración</TITLE>
	</HEAD>
	<BODY>
		<?php upper_header(); ?>
	<DIV ID="content">

<?php

if( !empty( $_GET[ 'password_mismatch' ] ) && $_GET[ 'password_mismatch' ] == 1 ) {
	echo(
		'<DIV CLASS="errorMessage center">Las contraseñas no coinciden.</DIV>'
		. '<DIV CLASS="separator"></DIV>'
	);
}

if( !empty( $_GET[ 'ran_out' ] ) && $_GET[ 'ran_out' ] == 1 ) {
	echo(
		'<DIV CLASS="errorMessage center">La lista de usuarios fue agotada. No se asignaron clientes.</DIV>'
		. '<DIV CLASS="separator"></DIV>'
	);
}

if( !empty( $_GET[ 'assignee' ] ) ) {
	$query =
		'SELECT first_name, last_name FROM Users WHERE id='
		. $_GET[ 'assignee' ];

	$result = db_query( $conn, $query )->fetch_assoc();
	echo(
		'<DIV CLASS="infoMessage center">La máquina cliente fue asignada al usuario '
		. $result[ 'first_name' ] . ' ' . $result[ 'last_name' ]
		. '.</DIV><DIV CLASS="separator"></DIV>'
	);
}

?>

		<TABLE>
			<TR>
				<TH><A HREF="version-manager.php">Administrador de versiones</A></TH>
			</TR>
			<TR>
				<TH>Versión actual</TH>
				<TD><?php echo( $version_current[ 'name' ] ); ?></TD>
			</TR>
		</TABLE>
		<TABLE>
			<TR>
				<TH><A HREF="distributed.php">Modo distributivo</A></TH>
			</TR>
		</TABLE>
		<DIV CLASS="separator"></DIV>
		<FORM ACTION="register-user.php" METHOD="POST">
			<TABLE>
				<TR>
					<TH>Registrar usuario</TH>
				</TR>
				<TR>
					<TD>
						Nombre de usuario
					</TD>
					<TD>
						<INPUT TYPE="text" name="username"/>
					</TD>
				</TR>
				<TR>
					<TD>
						Contraseña
					</TD>
					<TD>
						<INPUT TYPE="password" name="password"/>
					</TD>
				</TR>
				<TR>
					<TD>
						Repetir contraseña
					</TD>
					<TD>
						<INPUT TYPE="password" name="repeat_password"/>
					</TD>
				</TR>
				<TR>
					<TD>
						Nombre
					</TD>
					<TD>
						<INPUT TYPE="text" name="first_name"/>
					</TD>
				</TR>
				<TR>
					<TD>
						Apellidos
					</TD>
					<TD>
						<INPUT TYPE="text" name="last_name"/>
					</TD>
				</TR>
				<TR>
					<TD>
						Rango
					</TD>
					<TD>
						<INPUT TYPE="radio" name="class" value="0" checked/>Estudiante
						<INPUT TYPE="radio" name="class" value="1"/>Moderador
						<INPUT TYPE="radio" name="class" value="2"/>Administrador
					</TD>
				</TR>
				<TR>
					<TD>
						<INPUT TYPE="submit" VALUE="Registrar"/>
					</TD>
				</TR>
			</TABLE>
		</FORM>
		<DIV CLASS="separator"></DIV>
		<TABLE>
			<TR>
				<TH>Tabla de usuarios</TH>
			</TR>
			<TR>
				<TH>Identificador</TH>
				<TH>Nombre de usuario</TH>
				<TH>Nombre</TH>
				<TH>Apellido</TH>
				<TH>Permisos</TH>
			</TR>

<?php

function show_row( $id, $username, $first_name, $last_name, $user_class ) {
	if( $user_class == 0 ) {
		$formatted_class = "Estudiante";
	} elseif( $user_class == 1 ) {
		$formatted_class = "Moderador";
	} elseif( $user_class == 2 ) {
		$formatted_class = "Administrador";
	}

	echo(
		'<TR><TD>'
		. $id
		. '</TD><TD>'
		. $username
		. '</TD><TD>'
		. $first_name
		. '</TD><TD>'
		. $last_name
		. '</TD><TD>'
		. $formatted_class
		. '</TD></TR>'
	);
}

$query = 'SELECT id, username, first_name, last_name, class FROM Users';
$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() ) {
	show_row( $row[ 'id' ], $row[ 'username' ], $row[ 'first_name' ], $row[ 'last_name' ], $row[ 'class' ] );
}

db_disconnect( $conn );

?>

		</TABLE>
		<DIV CLASS="separator"></DIV>
		<TABLE>
			<TR>
				<TH>Cambiar hoja de estilos</TH>
			</TR>

<?php

function show_css_switcher( $name, $filename )
{
	echo(
		'<TR>'
		. '<TD>' . html( $name ) . '</TD>'
		. '<TD><A HREF="select-style.php?filename=' . $filename . '">Seleccionar</TD>'
		. '</TR>'
	);
}

show_css_switcher( 'Predeterminado', 'default-style.css' );
show_css_switcher( 'Elecciones', 'election-style.css' );

?>

		</TABLE>
		<DIV CLASS="separator"></DIV>
		<FORM ACTION="save-config.php" METHOD="GET">
			<TABLE>
				<TR>
					<TH>Propiedades de configuración</TH>
				</TR>
				<TR>
					<TH>Propiedad</TH>
					<TH>Valor</TH>
				</TR>

<?php

global $sys_config;
foreach( $sys_config as $k => $v ) {
	echo(
		'<TR>'
		. '<TD>' . html( $k ) . '</TD>'
		. '<TD><INPUT TYPE="text" NAME="' . $k . '" VALUE="' . html( $v ) . '"/></TD>'
		. '</TR>'
	);
}

?>

				<TR>
					<TH><INPUT TYPE="submit" VALUE="Guardar configuración"></TH>
				</TR>
			</TABLE>
		</FORM>
	</DIV>
	</BODY>
</HTML>
