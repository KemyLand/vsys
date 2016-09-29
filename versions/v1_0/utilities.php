<?php

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );
date_default_timezone_set( 'America/Costa_Rica' );

$config_paths = parse_ini_file( 'paths.ini' );
$sys_config = parse_ini_file( $config_paths[ 'sys' ] );

$ellipsis
	= '<SPAN CLASS="ellipsis">&#8230;</SPAN>';

$checkmark
	= '<SPAN CLASS="checkmark">&#10004;</SPAN>';

$crossmark
	= '<SPAN CLASS="crossmark">&#10008;</SPAN>';

$vote_limit_in_minutes = 30;

function upper_header()
{
	global $sys_config;
	if( $sys_config[ 'show_menubar' ] ) {
		echo(
			'<UL ID="upperHeader">'
		);

		if( $_SESSION[ 'class' ] <= 2 ) {
			echo(
				'<LI>'
				. html($_SESSION[ "last_name" ])
				. ', '
				. html($_SESSION[ "first_name" ])
				. '</LI>'
			);
		} else {
			echo( '<LI>Usuario hipervisor</LI>' );
		}

		if( $sys_config[ 'enable_proposals' ] ) {
			echo(
				'<LI><A HREF="votes.php">Registro de votos</A></LI>'
				. '<LI><A HREF="proposals.php">Propuestas</A></LI>'
			);
		}

		if( $sys_config[ 'enable_elections_page' ] ) {
			echo( '<LI><A HREF="elections.php">Elecciones</A></LI>' );
		}

		if( $_SESSION[ 'class' ] >= 1 ) {
			echo( '<LI><A HREF="mod.php">Panel de moderación</A></LI>' );
		}

		if( $_SESSION[ 'class' ] >= 2 ) {
			echo( '<LI><A HREF="admin.php">Panel de administración</A></LI>' );
		}

		if( $sys_config[ 'enable_about_page' ] ) {
			echo( '<LI><A HREF="about.php">Acerca de</A></LI>' );
		}

		if( $sys_config[ 'enable_unauthenticate' ] ) {
			echo( '<LI><A HREF="unauthenticate.php">Salir</A></LI>' );
		}

		echo( '</UL><DIV CLASS="separator"></DIV>' );
		if( !empty( $_GET[ 'denied' ] ) ) {
			echo(
				'<DIV CLASS="errorMessage center">Acceso denegado a '
				. html( $_GET[ 'denied' ] )
				. '.</DIV></DIV CLASS="separator"></DIV>'
			);
		}

		if( !empty( $_GET[ 'function_disabled' ] ) && $_GET[ 'function_disabled' ] == 1 ) {
			echo(
				'<DIV CLASS="errorMessage center">Esta función ha sido deshabilitada.</DIV>'
				. '</DIV CLASS="separator"></DIV>'
			);
		}

		if( !empty( $_GET[ 'requires_user' ] ) && $_GET[ 'requires_user' ] == 1 ) {
			echo(
				'<DIV CLASS="errorMessage center">Esta función no puede ser ejecutada por el usuario hipervisor.</DIV>'
				. '</DIV CLASS="separator"></DIV>'
			);
		}

		if( !empty( $_GET[ 'already_voted' ] ) && $_GET[ 'already_voted' ] == 1 ) {
			echo(
				'<DIV CLASS="errorMessage center">Usted ya ha votado por esta propuesta.</DIV>'
				. '<DIV CLASS="separator"></DIV>'
			);
		}

		if( !empty( $_GET [ 'already_voted_election' ] ) && $_GET[ 'already_voted_election' ] == '1' ) {
			echo(
			    '<DIV CLASS="errorMessage center">Usted ya ha votado en esta elección.'
		        . '</DIV><DIV CLASS="separator"></DIV>'
		    );
		}

		if( !empty( $_GET[ 'vote_success' ] ) && $_GET[ 'vote_success' ] == 1 ) {
			echo(
				'<DIV CLASS="infoMessage center">Se ha realizado la votación exitosamente.</DIV>'
				. '</DIV CLASS="separator"></DIV>'
			);
		}

		if( !empty( $_GET[ 'postulate_success' ] ) && $_GET[ 'postulate_success' ] == 1 ) {
			echo(
				'<DIV CLASS="infoMessage center">Propuesta postulada exitosamente.</DIV>'
				. '<DIV CLASS="separator"></DIV>'
			);
		}

		if( !empty( $_GET[ 'success' ] ) && $_GET[ 'success' ] == 1 ) {
			echo(
				'<DIV CLASS="infoMessage center">Operación realizada exitosamente.</DIV>'
				. '<DIV CLASS="separator"></DIV>'
			);
		}
	}
}

function html( $what )
{
	return str_replace( "\n", "<BR/>", htmlspecialchars( $what ) );
}

function redirect( $url )
{
	header( 'Location: ' . $url );
	exit();
}

function redirect_main( $addend = '' )
{
	global $sys_config;

	redirect( $sys_config[ 'main_page' ] . $addend );
}

function require_non_hypervisor()
{
	if( $_SESSION[ 'class' ] == '3' ) {
		redirect_main( '?requires_user=1' );
	}
}

function require_login()
{
	global $sys_config;

	session_name( "szLogin" );
	session_start();
	if( $_SESSION[ 'login' ] != 1 ) {
		redirect( 'login.php' );
	}
}

function require_moderator()
{
	require_login();
	if( $_SESSION[ 'class' ] < 1 ) {
		redirect_main( '?denied=panel%20de%20moderaci%C3%B3n' );
	}
}

function require_administrator()
{
	require_login();
	if( $_SESSION[ 'class' ] < 2 ) {
		redirect_main( '?denied=panel%20de%20administraci%C3%B3n' );
	}
}

function require_property( $property, $tester = TRUE )
{
	global $sys_config;
	( $sys_config[ $property ] == $tester ) or redirect_main( '?function_disabled=1' );
}

function filter_order( $order, $date_column, $votes_column )
{
	if( $order == 'most-popular' && $votes_column ) {
		return 'ORDER BY ' . $votes_column . ' ASC';
	} elseif( $order == 'least-popular' && $votes_column ) {
		return 'ORDER BY ' . $votes_column . ' DESC';
	} elseif( $order == 'oldest' ) {
		return 'ORDER BY ' . $date_column . ' ASC';
	}

	return 'ORDER BY ' . $date_column . ' DESC';
}

function get_status_image( $status )
{
	global $ellipsis, $checkmark, $crossmark;

	if( $status == 0 ) {
		return $ellipsis;
	} elseif( $status == 1 ) {
		return $checkmark;
	} else {
		return $crossmark;
	}
}

function get_status_description( $status )
{
	switch( $status ) {
		case 0:
			return 'Requiere mayoría absoluta para ser aprobada.';

		case 1:
			return 'Aprobada por mayoría absoluta. En proceso de revisión.';

		case 2:
			return 'Aprobada y en ejecución.';

		case 3:
			return 'Denegada por irrelevencia de contexto.';

		case 4:
			return 'Denegada por incumplimiento del reglamento.';

		case 5:
			return 'Denegada por ambigüedad o incertidumbre.';

		case 6:
			return 'Denegada por orden administrativa miscelánea.';
	}
}

function query_security_assert( $condition )
{
	if( !$condition ) {
		die( $condition );
	}
}

function formatted_date_now()
{
	return date( 'Y-m-d H:i:s' );
}

function unformat_date( $formatted )
{
	return strtotime( $formatted );
}

function whole_name()
{
	if( $_SESSION[ 'class' ] <= 2 ) {
		return $_SESSION[ 'first_name' ] . ' '  . $_SESSION[ 'last_name' ];
	} else {
		return 'usuario hipervisor';
	}
}

function make_ini( $a, $parent = array() )
{
	$out = '';
	foreach( $a as $k => $v ) {
		if( is_array( $v ) ) {
			$sec = array_merge( (array) $parent, (array) $k);
			$out .= '[' . join( '.', $sec ) . ']' . PHP_EOL;
			$out .= make_ini( $v, $sec );
		} else {
			$out .= "$k=$v" . PHP_EOL;
		}
	}

	return $out;
}

function save_config()
{
	global $config_paths, $sys_config;
	file_put_contents( $config_paths[ 'sys' ], make_ini( $sys_config ) );
}

function logout_everyone()
{
	ini_set( 'session.gc_max_lifetime', 0 );
	ini_set( 'session.gc_probability', 1 );
	ini_set( 'session.gc_divisor', 1 );
}

?>
