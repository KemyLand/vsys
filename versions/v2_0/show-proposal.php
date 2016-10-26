<?php

require_once( 'utilities.php' );
require_once( 'db.php' );
require_once( 'meta.php' );

db_require_login();
require_property( 'enable_proposals' );

function denegate_link
(
	$reason,
	$code
)
{
	global $id;

	echo
	( '<TR><TD><A HREF="denegate.php?id='
	. $id
		. '&code='
	. $code
	. '">'
	. html( $reason )
	. '</A></TD></TR>'
	);
}

if( !isset( $sys_config[ 'sticky_proposal_id' ] ) )
{
	if( !get_check( 'id' ) )
	{
		redirect_main();
	}

	$id = $_GET[ 'id' ];
} else
{
	$id = $sys_config[ 'sticky_proposal_id' ];
}

$conn = db_connect();
$query
	= 'SELECT proposing_student, total_votes, status, description, long_description, '
	. 'date FROM Proposals WHERE id='
	. mysqli_real_escape_string( $conn, $id );

$row = db_query( $conn, $query )->fetch_assoc();
$proposing_student = $row[ 'proposing_student' ];
$total_votes = $row[ 'total_votes' ];
$status = $row[ 'status' ];
$description = $row[ 'description' ];
$long_description = $row[ 'long_description' ];
$date = $row[ 'date' ];

$formatted_status = get_status_image( $status ) . ' ' . get_status_description( $status );

upper_header( $description );

?>

		<TABLE ID="proposal">
			<TR>
				<TH CLASS="borderless">Propuesta #<?php echo( html( $id ) ); ?></TH>
			</TR>
			<TR>
				<TH>Descripción</TH>
				<TD><?php echo( html( $long_description ) ); ?></TD>
			</TR>
			<TR>
				<TH>Cuenta de votos</TH>
			<TD><?php echo( $total_votes ); ?></TD>
			</TR>
			<TR>
				<TH>Estado</TH>
				<TD><?php echo( $formatted_status ); ?></TD>
			</TR>
			<TR>
				<TH>Fecha</TH>
				<TD><?php echo( html( $date ) ); ?></TD>
			</TR>

<?php

foreach( get_proposal_meta( $id ) as $k => $v )
{
	echo
	( '<TR><TH>'
	. html( $k )
	. '</TH><TD>'
	. $v
	. '</TD></TR>'
	);
}

if( $_SESSION[ 'class' ] != 2 )
{
	echo
	( '<TR><TH CLASS="borderless"><A HREF="perform-vote.php?id='
	. $id . '">Votar a favor</A></TH></TR>'
	);
}

?>

		</TABLE>

<?php

if( $_SESSION[ 'class' ] >= 1 )
{
	echo
	( '<DIV CLASS="separator"></DIV>'
	. '<TABLE><TR><TH CLASS="borderless">Denegar por</TH></TR>'
	);

	denegate_link( "Irrelevancia de contexto", 3 );
	denegate_link( "Incumplimiento del reglamento", 4 );
	denegate_link( "Ambigüedad o incertidumbre", 5 );
	denegate_link( "Orden administrativa miscelánea", 6 );

	echo( '</TABLE>' );
}

db_disconnect( $conn );
lower_header();

?>
