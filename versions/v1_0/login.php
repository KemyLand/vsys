<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

$conn = db_connect();

$remote_addr = $_SERVER[ 'REMOTE_ADDR' ];
if( in_array( $remote_addr, explode( ',', $sys_config[ 'hypervisor_bypass' ] ) ) )
{
	db_fake_authenticate_hypervisor( $conn );

	db_disconnect( $conn );
	redirect_main();
}

$is_distributed_mode = $sys_config[ 'enable_distributed_mode' ];
if( $is_distributed_mode && ( empty( $_GET[ "failed" ] ) || $_GET[ "failed" ] != '1' ) )
{
	$query = 'SELECT address, user FROM DistributedClients';
	$result = db_query( $conn, $query );
	while( $row = $result->fetch_assoc() )
	{
		$address = $row[ 'address' ];
		$user = $row[ 'user' ];

		if( $address == $remote_addr )
		{
			if( empty( $user ) )
			{
				break;
			}

			db_fake_authenticate( $conn, $user );

			db_disconnect( $conn );
			redirect_main();
		}
	}
}

upper_header( $is_distributed_mode ? 'Access denegado' : 'Ingresar', FALSE );

if( get_bool( 'failed' ) )
{
	if( !$is_distributed_mode )
	{
		$message = 'Ha fallado la autenticación: Nombre de usuario y/o contraseña incorrectos.';
	} else
	{
		$message = 'Esta máquina cliente no está autorizada para participar en modo distribuido.';
	}

	echo
	( '<DIV CLASS="errorMessage center">'
	. html( $message )
	. '</DIV>'
	);
}

?>

<?php if( !$is_distributed_mode ): ?>
		<TABLE>
			<TR>
				<TD>
					<FORM ACTION="authenticate.php" METHOD="POST">
						Nombre de usuario<BR/><INPUT TYPE="text" name="username"/><BR/><BR/>
						Contraseña<BR/><INPUT TYPE="password" name="password"/><BR/><BR/>
						<INPUT TYPE="submit" VALUE="Ingresar"/>
					</FORM>
				</TD>
			</TR>
		</TABLE>
<?php endif; ?>

<?php

db_disconnect( $conn );
lower_header();

?>
