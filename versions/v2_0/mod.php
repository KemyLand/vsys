<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_moderator();

$conn = db_connect();

upper_header( 'Panel de moderación' );

?>

		<TABLE>
			<TR>
				<TH CLASS="borderless">Eventos</TH>
			</TR>
			<TR>
				<TH>Fecha</TH>
				<TH>Evento</TH>
			</TR>

<?php

$order = ( get_check( 'order' ) ) ? $_GET[ 'order' ] : 'newest';
$order_query = filter_order( $order, 'datetime', NULL );
$query = 'SELECT datetime, event FROM Events ' . $order_query;

$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() )
{
	echo
	( '<TR>'
	. '<TD>' . $row[ 'datetime' ] . '</TD>'
	. '<TD>' . $row[ 'event' ] . '</TD>'
	. '</TR>'
	);
}

db_disconnect( $conn );
lower_header();

?>
