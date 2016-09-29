<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_moderator();

$conn = db_connect();

?>

<HTML>
	<HEAD>
		<META CHARSET="utf-8"/>
		<LINK REL="stylesheet" TYPE="text/css" HREF="style.css"/>
		<TITLE>Panel de moderación</TITLE>
	</HEAD>
	<BODY>
		<?php upper_header(); ?>
	<DIV ID="content">
		<TABLE>
			<TR>
				<TH>Fecha</TH>
				<TH>Evento</TH>
			</TR>

<?php

$order = ( !empty( $_GET[ 'order' ] ) ) ? $_GET[ 'order' ] : 'newest';
$order_query = filter_order( $order, 'datetime', NULL );
$query =
	'SELECT datetime, event FROM Events ' . $order_query;

$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() ) {
	echo(
		'<TR>'
		. '<TD>' . $row[ 'datetime' ] . '</TD>'
		. '<TD>' . $row[ 'event' ] . '</TD>'
		. '</TR>'
	);
}

db_disconnect( $conn );

?>

		</TABLE>
	</DIV>
	</BODY>
</HTML>
