<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_administrator();

$conn = db_connect();

upper_header( 'Modo distribuido' );


if( get_bool( 'ran_out' ) )
{
	echo
	( '<DIV CLASS="errorMessage center">La lista de usuarios fue agotada. No se asignaron clientes.'
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
				<TH>Modo distributivo</TH>
			</TR>
			<TR>
				<TH>
					<DIV CLASS="infoMessage center">
<?php if( !$sys_config[ 'enable_distributed_mode' ] ): ?>
						El modo distributivo está deshabilitado.
<?php else: ?>
						El modo distributivo está habilitado.
<?php endif; ?>
					</DIV>
				</TH>
			</TR>
			<TR>
<?php if( !$sys_config[ 'enable_distributed_mode' ] ): ?>
				<TH><A HREF="distributed-startup.php">Habilitar modo distributivo</A></TH>
<?php else: ?>
				<TH><A HREF="distributed-shutdown.php">Deshabilitar modo distributivo</A></TH>
<?php endif; ?>
			</TR>
		</TABLE>

<?php

if( $sys_config[ 'enable_distributed_mode' ] ) {
	echo
	( '<DIV CLASS="separator"></DIV><TABLE>'
	. '<TR><TH>Máquinas cliente</TH></TR>'
	. '<TR><TH>Dirección</TH><TH>Estado</TH></TR>'
	);

	$query = 'SELECT id, address, user FROM DistributedClients';
	$result = db_query( $conn, $query );
	while( $row = $result->fetch_assoc() )
	{
		$id = $row[ 'id' ];
		$address = $row[ 'address' ];
		$user = $row[ 'user' ];

		echo
		( '<TR><TD>'
		. $address
		. '</TD><TD>'
		);

		if( !empty( $user ) )
		{
			$query
				= 'SELECT first_name, last_name FROM Users WHERE id='
				. $user;

			$subresult = db_query( $conn, $query )->fetch_assoc();
			echo
			( 'Asignado a usuario '
			. $subresult[ 'first_name' ] . ' ' . $subresult[ 'last_name' ]
			);

			$is_allocated = TRUE;
		} else
		{
			echo( 'Inactivo' );
			$is_allocated = FALSE;
		}

		echo( '</TD>' );
		if( $is_allocated )
		{
			echo
			( '<TD><A HREF="distributed-assign.php?id='
			. $id
			. '&release=1">Reasignar</A></TD>'
			. '<TD><A HREF="distributed-release.php?id='
			. $id
			. '">Liberar</A></TD>'
			);
		} else
		{
			echo
			( '<TD><A HREF="distributed-assign.php?id='
			. $id
			. '">Asignar</A></TD>'
			);
		}

		echo( '</TR>' );
	}

	echo( '</TABLE>' );
}

db_disconnect( $conn );
lower_header();

?>
