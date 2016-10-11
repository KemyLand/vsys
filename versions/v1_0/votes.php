<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_login();
require_property( 'enable_proposals' );

function show_row
(
	$conn,
	$id,
	$proposal_id,
	$date
)
{
	$proposal_description_query
		= 'SELECT description FROM Proposals WHERE id='
		. $proposal_id;

	$proposal_description = db_query
	(
		$conn,
		$proposal_description_query
	)->fetch_assoc()[ 'description' ];

	$vote_link
		= '<A HREF="show-vote.php?id='
		. $id
		. '">#'
		. $id
		. '</A>';

	$proposal_link
		= '<A HREF="show-proposal.php?id='
		. $proposal_id
		. '">'
		. $proposal_description
		. '</A>';

	echo
	( '<TR>'
	. '<TD>' . $vote_link . '</TD>'
	. '<TD>' . $proposal_link . '</TD>'
	. '<TD>' . $date . '</TD>'
	. '</TR>'
	);
}

upper_header( 'Registro de votos' );

?>

		<TABLE ID="votes">
			<TR>
				<TH>Registro de votos</TH>
			</TR>
			<TR>
				<TH>Identificador de voto</TH>
				<TH>Propuesta</TH>
				<TH>Fecha</TH>
			</TR>

<?php

$conn = db_connect();

$order = ( !empty( $_GET[ 'order' ] ) ) ? $_GET[ 'order' ] : 'newest';
$order_query = filter_order( $order, 'date', NULL );

$query = 'SELECT id, proposal_id, date FROM Votes ' . $order_query;

$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() )
{
	show_row( $conn, $row[ 'id' ], $row[ 'proposal_id' ], $row[ 'date' ] );
}

db_disconnect( $conn );

?>

		</TABLE>

<?php lower_header(); ?>
