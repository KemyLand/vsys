<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_login();
require_property( 'enable_elections_page' );

function show_row
(
	$conn,
	$id,
	$proposals,
	$status,
	$name
)
{
	$election_link
		= '<A HREF="show-election.php?id='
		. $id
		. '">'
		. $name
		. '</A>';

	$status_image = get_status_image( $status );

	echo
	( '<TR>'
	. '<TD>' . $election_link . '</TD>'
	. '<TD>' . $status_image . '</TD>'
	. '<TD>' . $proposals . '</TD>'
	. '</TR>'
	);
}

$conn = db_connect();

upper_header( 'Registro de elecciones' );

?>

		<TABLE>
			<TR>
				<TH CLASS="borderless">Registro de elecciones</TH>
			</TR>
			<TR>
				<TH>Descripción</TH>
				<TH>Estado</TH>
				<TH>Participantes</TH>
			</TR>

<?php

$order = ( get_check( 'order' ) ) ? $_GET[ 'order' ] : 'newest';

$order_query = filter_order( $order, 'id', 'total_votes' );

$query = 'SELECT id, proposals, status, name FROM Elections ' . $order_query;

$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() )
{
	show_row( $conn, $row[ 'id' ], $row[ 'proposals' ], $row[ 'status' ], $row[ 'name' ] );
}

db_disconnect($conn);
lower_header();

?>
