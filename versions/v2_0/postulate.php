<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_login();
require_property( 'enable_proposals' );

upper_header( 'Postular propuesta' );

if( get_bool( 'limit_reached' ) )
{
	$conn = db_connect();
	$query = 'SELECT last_vote FROM Users WHERE id=' . $_SESSION[ 'id' ];

	$last_vote_date = unformat_date( db_query( $conn, $query )->fetch_assoc()[ 'last_vote' ] );
	db_disconnect( $conn );

	$minutes_ellapsed = intdiv( time() - $last_vote_date, 60 );

	echo
	( '<DIV CLASS="errorMessage center">Usted postuló una propuesta por última vez '
	. 'hace ' . $minutes_ellapsed . ' minutos. Solo puede postular una '
	. 'vez cada ' . $vote_limit_in_minutes . ' minutos.</DIV>'
	. '<DIV CLASS="separator"></DIV>'
	);
}

?>

		<FORM ACTION="perform-postulate.php" METHOD="POST">
			<TABLE>
				<TR>
					<TH>Postular propuesta</TH>
				</TR>
				<TR>
					<TH>Título</TH>
					<TD><INPUT TYPE="text" NAME="description"/></TD>
				</TR>
				<TR>
					<TH>Descripción</TH>
					<TD><INPUT TYPE="text" NAME="long_description"/></TD>
				</TR>
				<TR>
					<TD><INPUT TYPE="submit" VALUE="Postular"/></TD>
				</TR>
			</TABLE>
		</FORM>

<?php lower_header(); ?>
