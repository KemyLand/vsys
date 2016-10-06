<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_login();
require_non_hypervisor();
require_property( 'enable_voting' );

if( !get_check( 'id' ) )
{
	redirect_main();
}

$id = $_GET[ 'id' ];

$conn = db_connect();

if( db_already_voted( $conn, $_SESSION[ 'username' ], $id ) )
{
	db_disconnect( $conn );
	redirect_main( '?already_voted=1' );
}

$query = 'SELECT id FROM Elections';
$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() )
{
	$election_id = $row[ 'id' ];
	if( db_already_voted_election( $conn, $_SESSION[ 'username' ], $election_id, $id ) )
	{
		db_disconnect( $conn );
		redirect_main( '?already_voted_election=1' );
	}
}

$query
	= 'INSERT INTO Votes( proposal_id, data, date ) VALUES( '
	. $id
	. ', SHA1("'
	. $_SESSION[ 'username' ] . '::' . $id
	. '"), "'
	. formatted_date_now()
	. '" )';

db_query( $conn, $query );

$query = 'UPDATE Proposals SET total_votes = total_votes + 1 WHERE id=' . $id;
db_query( $conn, $query );

$event = 'Voto a favor de propuesta #' . $id;

db_register_event( $conn, $event );
db_disconnect( $conn );

redirect_main( '?vote_success=1' );

?>
