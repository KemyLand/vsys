<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'versions.php' );

db_require_administrator();

$conn = db_connect();

upper_header( 'Panel de administración' );

if( get_bool( 'password_mismatch' ) )
{
	echo
	( '<DIV CLASS="errorMessage center">Las contraseñas no coinciden.</DIV>'
	. '<DIV CLASS="separator"></DIV>'
	);
}

if( get_bool( 'ran_out' ) )
{
	echo
	( '<DIV CLASS="errorMessage center">La lista de usuarios fue agotada. No se asignaron clientes.</DIV>'
	. '<DIV CLASS="separator"></DIV>'
	);
}

if( get_check( 'assignee' ) )
{
	$query
		= 'SELECT first_name, last_name FROM Users WHERE id='
		. $_GET[ 'assignee' ];

	$result = db_query( $conn, $query )->fetch_assoc();
	echo
	( '<DIV CLASS="infoMessage center">La máquina cliente fue asignada al usuario '
	. $result[ 'first_name' ] . ' ' . $result[ 'last_name' ]
	. '.</DIV><DIV CLASS="separator"></DIV>'
	);
}

?>

		<TABLE>
			<TR>
				<TH CLASS="borderless">
					Versión del sistema
				</TH>
				<TD CLASS="borderless">
					<?php echo( $version_current[ 'name' ] ); ?>
				</TD>
			</TR>
			<TR>
				<TD CLASS="borderless">
					<A HREF="version-manager.php">Administrador de versiones</A>
				</TD>
			</TR>
		</TABLE>
		<DIV CLASS="separator"></DIV>
		<TABLE>
			<TR>
				<TD CLASS="borderless">
					<A HREF="distributed.php">Modo distributivo</A>
				</TD>
			</TR>
		</TABLE>
		<DIV CLASS="separator"></DIV>
		<FORM ACTION="register-user.php" METHOD="POST">
			<TABLE>
				<TR>
					<TH CLASS="borderless">
						Registrar usuario
					</TH>
				</TR>
				<TR>
					<Th>
						Nombre de usuario
					</TH>
					<TD>
						<INPUT TYPE="text" name="username"/>
					</TD>
				</TR>
				<TR>
					<TH>
						Contraseña
					</TH>
					<TD>
						<INPUT TYPE="password" name="password"/>
					</TD>
				</TR>
				<TR>
					<TH>
						Repetir contraseña
					</TH>
					<TD>
						<INPUT TYPE="password" name="repeat_password"/>
					</TD>
				</TR>
				<TR>
					<TH>
						Nombre
					</TH>
					<TD>
						<INPUT TYPE="text" name="first_name"/>
					</TD>
				</TR>
				<TR>
					<TH>
						Apellidos
					</TH>
					<TD>
						<INPUT TYPE="text" name="last_name"/>
					</TD>
				</TR>
				<TR>
					<TH>
						Permisos
					</TH>
					<TD>
						<INPUT TYPE="radio" name="class" value="0" checked/>Estudiante
						<INPUT TYPE="radio" name="class" value="1"/>Moderador
						<INPUT TYPE="radio" name="class" value="2"/>Administrador
					</TD>
				</TR>
				<TR>
					<TH>
						<INPUT TYPE="submit" VALUE="Registrar"/>
					</TH>
				</TR>
			</TABLE>
		</FORM>
		<DIV CLASS="separator"></DIV>
		<TABLE>
			<TR>
				<TH CLASS="borderless">
					Tabla de usuarios
				</TH>
			</TR>
			<TR>
				<TH>Identificador</TH>
				<TH>Nombre de usuario</TH>
				<TH>Nombre</TH>
				<TH>Apellido</TH>
				<TH>Permisos</TH>
			</TR>

<?php

function show_row
(
	$id,
	$username,
	$first_name,
	$last_name,
	$user_class
)
{
	if( $user_class == 0 )
	{
		$formatted_class = 'Estudiante';
	} elseif( $user_class == 1 )
	{
		$formatted_class = 'Moderador';
	} elseif( $user_class == 2 )
	{
		$formatted_class = 'Administrador';
	}

	echo
	( '<TR><TD>'
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
while( $row = $result->fetch_assoc() )
{
	show_row
	( $row[ 'id' ],
	  $row[ 'username' ],
	  $row[ 'first_name' ],
	  $row[ 'last_name' ],
	  $row[ 'class' ]
	);
}

db_disconnect( $conn );

?>

		</TABLE>
		<DIV CLASS="separator"></DIV>
		<TABLE>
			<TR>
				<TH CLASS="borderless">
					Cambiar hoja de estilos
				</TH>
			</TR>

<?php

function show_css_switcher
(
	$name,
	$filename
)
{
	echo
	( '<TR>'
	  . '<TD CLASS="borderless">' . html( $name ) . '</TD>'
	  . '<TD CLASS="borderless">'
	  . '<A HREF="select-style.php?filename=' . $filename . '">Seleccionar</A>'
	  . '</TD></TR>'
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
					<TH CLASS="borderless">
						Propiedades de configuración
					</TH>
				</TR>
				<TR>
					<TH>Propiedad</TH>
					<TH>Valor</TH>
				</TR>

<?php

global $sys_config;
foreach( $sys_config as $k => $v )
{
	echo
	( '<TR>'
	. '<TD>' . html( $k ) . '</TD>'
	. '<TD><INPUT TYPE="text" NAME="' . $k . '" VALUE="' . html( $v ) . '"/></TD>'
	. '</TR>'
	);
}

?>

				<TR>
					<TH CLASS="borderless">
						<INPUT TYPE="submit" VALUE="Guardar configuración">
					</TH>
				</TR>
			</TABLE>
		</FORM>

<?php lower_header(); ?>
