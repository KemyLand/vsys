<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'meta.php' );

require_login();
require_property( 'enable_elections' );

if( empty( $sys_config[ 'sticky_election_id' ] ) )
{
	if( !get_check( 'id' ) )
	{
		redirect_main();
	}

	$id = $_GET[ 'id' ];
} else
{
	$id = $sys_config[ 'sticky_election_id' ];
}

query_security_assert( filter_var( $id, FILTER_VALIDATE_INT ) );

$conn = db_connect();
$query = 'SELECT proposals, status, name FROM Elections WHERE id=' . $id;

$row = db_query( $conn, $query )->fetch_assoc();

$parties = explode( ':', $row[ 'proposals' ] );
$election_status = $row[ 'status' ];
$election_name = $row[ 'name' ];

upper_header( $election_name );

?>

		<H1 CLASS="center"><?php echo( html( $election_name ) ); ?></H1>
		<DIV CLASS="separator"></DIV>
		<TABLE>

<?php

$ids = array();
$descriptions = array();
$metas = array();

foreach( $parties as $party )
{
	$query
		= 'SELECT description FROM Proposals WHERE id='
		. $party;

	$description = db_query( $conn, $query )->fetch_assoc()[ 'description' ];
	$descriptions[ $party ] = $description;
	array_push( $ids, $party );

	$meta = get_proposal_meta( $party );
	foreach( $meta as $k => $v )
	{
		if( isset( $metas[ $k ] ) )
		{
			$metas[ $k ][ $party ] = $v;
		} else
		{
			$metas[ $k ] = array( $party => $v );
		}
	}
}

echo( '<TR><TD></TD>' );
foreach( $ids as $id )
{
	echo( '<TH>' . html( $descriptions[ $id ] ) . '</TH>' );
}

echo( '</TR>' );

foreach( $metas as $k => $v )
{
	echo( '<TR><TH>' . $k . '</TH>' );
	foreach( $ids as $id )
	{
		if( isset( $v[ $id ] ) )
		{
			echo( '<TD>' . $v[ $id ] . '</TD>' );
		} else
		{
			echo( '<TD></TD>' );
		}
	}

	echo( '</TR>' );
}

echo( '<TR><TD></TD>' );
foreach( $ids as $id )
{
	echo
	( '<TD><A HREF="perform-vote.php?id='
	. $id
	. '">Votar a favor</A></TD>'
	);
}

echo( '</TR>' );

?>

		</TABLE>
		<DIV CLASS="separator"></DIV>
<?php if( $_SESSION[ 'class' ] >= 2 ): ?>
		<TABLE ID="user_votes">
			<TR>
				<TH>Estado de usuarios</TH>
			</TR>
			<TR>
				<TH>Nombre</TH>
				<TH>Estado</TH>
			</TR>
<?php endif; ?>

<?php

if( $_SESSION[ 'class' ] >= 2 )
{
	if( !$sys_config[ 'enable_distributed_mode' ] || $id != $sys_config[ 'distributed_mode_election' ] )
	{
		$query = 'SELECT username, first_name, last_name, class FROM Users';
		$result = db_query( $conn, $query );
		while( $row = $result->fetch_assoc() )
		{
			$username = $row[ 'username' ];
			$first_name = $row[ 'first_name' ];
			$last_name = $row[ 'last_name' ];
			$user_class = $row[ 'class' ];

			echo
			( '<TR><TD>'
			. html( $first_name )
			. ' '
			. html( $last_name )
			. '</TD>'
			);

			if( $user_class <= 1 )
			{
				$formatted_status = get_status_image( 0 );
				foreach( $parties as $party_id )
				{
					if( db_already_voted( $conn, $username, $party_id ) )
					{
						$formatted_status = get_status_image( 1 );
						break;
					}
				}
			} else
			{
				$formatted_status = get_status_image( 2 );
			}

			echo
			( '<TD>'
			. $formatted_status
			. '</TD></TR>'
			);
		}
	} else
	{
		$query = 'SELECT user, skipped FROM DistributedUsers';
		$result = db_query( $conn, $query );
		while( $row = $result->fetch_assoc() )
		{
			$user_id = $row[ 'user' ];
			$skipped = $row[ 'skipped' ];

			$query
				= 'SELECT username, first_name, last_name FROM Users WHERE id='
				. $user_id;

			$subresult = db_query( $conn, $query )->fetch_assoc();
			$username = $subresult[ 'username' ];
			$first_name = $subresult[ 'first_name' ];
			$last_name = $subresult[ 'last_name' ];

			echo
			( '<TR><TD>'
			. html( $first_name )
			. ' '
			. html( $last_name )
			. '</TD>'
			);

			$already_voted = db_already_voted_election( $conn, $username, $id );
			if( $already_voted )
			{
				$formatted_status = get_status_image( 1 );
			} elseif( $skipped )
			{
				$formatted_status = get_status_image( 2 );
			} else
			{
				$formatted_status = get_status_image( 0 );
			}

			echo
			( '<TD>'
			. $formatted_status
			. '</TD></TR>'
			);
		}
	}
}

db_disconnect( $conn );

?>

<?php if( $_SESSION[ 'class' ] >= 2 ): ?>
		</TABLE>
<?php endif; ?>
<?php lower_header(); ?>
