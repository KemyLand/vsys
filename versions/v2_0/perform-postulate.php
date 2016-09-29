<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_login();
require_non_hypervisor();
require_property( 'enable_proposals' );

if( empty( $_POST[ 'description' ] ) || empty( $_POST[ 'long_description' ] ) ) {
	redirect_main();
}

$user_id = $_SESSION[ 'id' ];
$description = $_POST[ 'description' ];
$long_description = $_POST[ 'long_description' ];
$proposal_date = formatted_date_now();

$conn = db_connect();

$query
	= 'SELECT last_vote FROM Users WHERE id='
	. $user_id;

$last_vote_date = unformat_date( db_query( $conn, $query )->fetch_assoc()[ 'last_vote' ] );
$vote_time_limit = $vote_limit_in_minutes * 60;
if( $_SESSION[ 'class' ] < 1 && time() - $vote_time_limit < $last_vote_date ) {
	db_disconnect( $conn );
	redirect( 'postulate.php?limit_reached=1' );
}

$query
	= 'INSERT INTO Proposals( proposing_student, total_votes, '
	. 'status, description, long_description, date ) VALUES( '
	. $user_id
	. ', 0, 0, "'
	. $description
	. '", "'
	. $long_description
	. '", "'
	. $proposal_date
	. '" )';

db_query( $conn, $query );

$query
	= 'UPDATE Users SET last_vote="'
	. $proposal_date
	. '" WHERE id='
	. $user_id;

db_query( $conn, $query );

$event
	= 'Propuesta #'
	. db_query_insert_id( $conn )
	. ' postulada por '
	. whole_name();

db_register_event( $conn, $event );

db_disconnect( $conn );
redirect_main( '?postulate_success=1' );

?>
