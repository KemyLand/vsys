<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

require_login();
require_property( 'enable_proposals' );

$id = $_GET[ 'id' ];

$conn = db_connect();

$vote_query = 'SELECT proposal_id, data, date FROM Votes WHERE id=' . $id;
$row = db_query( $conn, $vote_query )->fetch_assoc();
$proposal_id = $row[ 'proposal_id' ];
$data = $row[ 'data' ];
$date = $row[ 'date' ];

$proposal_query = 'SELECT description FROM Proposals WHERE id=' . $proposal_id;
$row = db_query( $conn, $proposal_query )->fetch_assoc();
$proposal_description = $row[ 'description' ];

?>

<HTML>
	<HEAD>
		<META CHARSET="utf-8"/>
		<LINK REL="stylesheet" TYPE="text/css" HREF="style.css"/>
		<TITLE>Voto #<?php echo( $id ); ?> a favor de "<?php echo( html( $proposal_description ) ); ?>"</TITLE>
	</HEAD>
	<BODY>
		<?php upper_header(); ?>
	<DIV ID="content">
		<TABLE ID="vote">
			<TR>
				<TH>Voto #<?php echo( $id ); ?></TH>
			</TR>
			<TR>
				<TH>A favor de</TH>
				<TD>
					<A HREF="show-proposal.php?id=<?php echo( $id ); ?>">
						<?php echo( html( $proposal_description ) ); ?>
					</A>
				</TD>
			</TR>
			<TR>
				<TH>Datos</TH>
				<TD><?php echo( html( $data ) ); ?></TD>
			</TR>
			<TR>
				<TH>Fecha</TH>
				<TD><?php echo( html( $date ) ); ?></TD>
			</TR>

<?php

db_disconnect($conn);

?>

		</TABLE>
	</DIV>
	</BODY>
</HTML>
