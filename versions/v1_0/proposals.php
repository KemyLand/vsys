<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_login();
require_property( 'enable_proposals' );

function show_row( $conn, $id, $total_votes, $status, $description, $date ) {
	$proposal_link
		= '<A HREF="show-proposal.php?id='
		. $id
		. '">'
		. $description
		. '</A>';

	$status_image = get_status_image( $status );

	echo(
		'<TR>' .
			'<TD>' . $proposal_link . '</TD>' .
			'<TD>' . $total_votes . '</TD>' .
			'<TD>' . $status_image . '</TD>' .
			'<TD>' . $date . '</TD>' .
		'</TR>'
	);
}

?>

<HTML>
	<HEAD>
		<META CHARSET="utf-8"/>
		<LINK REL="stylesheet" TYPE="text/css" HREF="style.css"/>
		<TITLE>Registro de propuestas</TITLE>
	</HEAD>
	<BODY>
		<?php upper_header(); ?>
	<DIV ID="content">

<?php

$conn = db_connect();

?>

<?php if( $_SESSION[ 'class' ] <= 2 ): ?>
		<TABLE>
			<TR>
				<TH><A HREF="postulate.php">Postular propuesta</A></TH>
			</TR>
		</TABLE>
<?php endif; ?>

		<DIV CLASS="separator"></DIV>
		<TABLE ID="proposals">
			<TR>
				<TH>Tabla de propuestas</TH>
			</TR>
			<TR>
				<TH>Descripción</TH>
				<TH>Cuenta de votos</TH>
				<TH>Estado</TH>
				<TH>Fecha</TH>
			</TR>

<?php

$order = ( !empty( $_GET[ 'order' ] ) ) ? $_GET[ 'order' ] : 'newest';

$order_query = filter_order( $order, 'date', 'total_votes' );

$query = 'SELECT id, total_votes, status, description, date FROM Proposals ' . $order_query;

$result = db_query( $conn, $query );
while( $row = $result->fetch_assoc() ) {
	show_row( $conn, $row[ 'id' ], $row[ 'total_votes' ], $row[ 'status' ], $row[ 'description' ], $row[ 'date' ] );
}

db_disconnect($conn);

?>

		</TABLE>
	</DIV>
	</BODY>
</HTML>
