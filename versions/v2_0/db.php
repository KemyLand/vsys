<?php

require_once( 'utilities.php' );

function db_connect()
{
	global $config_paths;

	$db_cfg = parse_ini_file( $config_paths[ 'db' ] );
	$conn = new mysqli( $db_cfg[ 'address' ], $db_cfg[ 'username' ], $db_cfg[ 'password' ], $db_cfg[ 'db_name' ] );
	if( $conn->connect_error )
	{
		die( 'Database connection failed: ' . $conn->connect_error);
	}

	return $conn;
}

function db_disconnect
(
	$conn
)
{
	$conn->close();
}

function db_query
(
	$conn,
	$sql
)
{
	$result = $conn->query( $sql );
	if( !$result )
	{
		die( 'Error for SQL query `' . $sql . '`' );
	}

	return $result;
}

function db_query_insert_id
(
	$conn
)
{
	return $conn->insert_id;
}

function db_register_event
(
	$conn,
	$event
)
{
	$query
		= 'INSERT INTO Events( datetime, event ) VALUES( "'
		. formatted_date_now()
		. '", "'
		. $event
		. '" )';

	db_query( $conn, $query );
}

function db_already_voted
(
	$conn,
	$username,
	$id
)
{
	$hashee = $username . '::' . $id;

	$query
		= 'SELECT COUNT(*) FROM Votes WHERE data=SHA1("'
		. $hashee
		. '")';

	return db_query( $conn, $query )->fetch_row()[0] != 0;
}

function db_already_voted_election
(
	$conn,
	$username,
	$election_id,
	$proposal     = FALSE
)
{
	$query
		= 'SELECT proposals FROM Elections WHERE id='
		. $election_id;

	$parties = explode( ':', db_query( $conn, $query )->fetch_assoc()[ 'proposals' ] );
	foreach( $parties as $party_id )
	{
		if( $proposal && $party_id != $proposal )
		{
			continue;
		}

		foreach( $parties as $party_id )
		{
			if( db_already_voted( $conn, $username, $party_id ) )
			{
				return TRUE;
			}
		}
	}

	return FALSE;
}

function db_fake_authenticate
(
	$conn,
	$id
)
{
	$query
		= 'SELECT username, first_name, last_name, class FROM Users WHERE id="'
		. $id
		. '"';

	$result = db_query( $conn, $query )->fetch_assoc();

	session_name( 'szLogin' );
	session_start();

	$_SESSION[ 'login' ] = 1;
	$_SESSION[ 'id' ] = $id;
	$_SESSION[ 'username' ] = $result[ 'username' ];
	$_SESSION[ 'first_name' ] = $result[ 'first_name' ];
	$_SESSION[ 'last_name' ] = $result[ 'last_name' ];
	$_SESSION[ 'class' ] = $result[ 'class' ];

	$event
		= 'Sesión de '
		. whole_name()
		. ' asignada a '
		. $_SERVER[ 'REMOTE_ADDR' ]
		. ' implícitamente';

	db_register_event( $conn, $event );
}

function db_fake_authenticate_hypervisor
(
	$conn
)
{
	session_name( 'szLogin' );
	session_start();

	$_SESSION[ 'login' ] = 1;
	$_SESSION[ 'class' ] = 3;

	$event
		= 'Sesión del usuario hipervisor asignada a '
		. $_SERVER[ 'REMOTE_ADDR' ]
		. ' implícitamente';

	db_register_event( $conn, $event );
}

function db_require_login()
{
	global $sys_config;

	session_name( 'szLogin' );
	session_start();

	if( $_SESSION[ 'login' ] != 1 )
	{
		redirect( 'login.php' );
	} else if( $_SESSION[ 'class' ] < 3 && $sys_config[ 'enable_distributed_mode' ] == 1 )
	{
		$conn = db_connect();
		$query = 'SELECT user FROM DistributedClients';
		$result = db_query( $conn, $query );

		$distributed_users = array();
		while( $row = $result->fetch_assoc() )
		{
			array_push( $distributed_users, $row[ 'user' ] );
		}

		db_disconnect( $conn );
		if( !in_array( $_SESSION[ 'id' ], $distributed_users ) )
		{
			redirect( 'login.php' );
		}
	}
}

function db_require_moderator()
{
	db_require_login();
	if( $_SESSION[ 'class' ] < 1 )
	{
		redirect_main( '?denied=panel%20de%20moderaci%C3%B3n' );
	}
}

function db_require_administrator()
{
	db_require_login();
	if( $_SESSION[ 'class' ] < 2 )
	{
		redirect_main( '?denied=panel%20de%20administraci%C3%B3n' );
	}
}

?>
