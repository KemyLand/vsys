<?php

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );
date_default_timezone_set( 'America/Costa_Rica' );

$config_paths = parse_ini_file( 'paths.ini' );
$sys_config = parse_ini_file( $config_paths[ 'sys' ] );

$ellipsis = '<SPAN CLASS="ellipsis">&#8230;</SPAN>';
$checkmark = '<SPAN CLASS="checkmark">&#10004;</SPAN>';
$crossmark = '<SPAN CLASS="crossmark">&#10008;</SPAN>';

$vote_limit_in_minutes = 30;

function get_check( $what )
{
	return isset( $_GET[ $what ] );
}

function get_bool( $what )
{
	return get_check( $what ) && $_GET[ $what ] == 1;
}

function post_check( $what )
{
	return isset( $_POST[ $what ] );
}

function post_bool( $what )
{
	return post_check( $what ) && $_POST[ $what ] == 1;
}

function html( $what )
{
	return str_replace( '\n', '<BR/>', htmlspecialchars( $what ) );
}

function upper_header( $title, $show_header = TRUE )
{
	global $sys_config;
	echo
	( '<!DOCTYPE html/>'
	. '<HEAD>'
	. '<META CHARSET="UTF-8"/>'
	. '<LINK REL="stylesheet" TYPE="text/css" HREF="style.css"/>'
	. '<TITLE>' . html( $title ) . '</TITLE>'
	. '</HEAD>'
	. '<BODY>'
	);

	if( $sys_config[ 'show_menubar' ] && $show_header )
	{
		echo( '<DIV ID="loginHeader">' );
		if( $_SESSION[ 'class' ] <= 2 )
		{
			echo
			( html( $_SESSION[ 'last_name' ] )
			. ', '
			. html( $_SESSION[ 'first_name' ] )
			);
		} else
		{
			echo( 'Usuario hipervisor' );
		}

		echo( '</DIV><BR/><UL ID="upperHeader">' );
		if( $sys_config[ 'enable_proposals' ] )
		{
			echo
			( '<LI><A CLASS="raw" HREF="votes.php">Registro de votos</A></LI>'
			. '<LI><A CLASS="raw" HREF="proposals.php">Propuestas</A></LI>'
			);
		}

		if( $sys_config[ 'enable_elections_page' ] )
		{
			echo( '<LI><A CLASS="raw" HREF="elections.php">Elecciones</A></LI>' );
		}

		if( $_SESSION[ 'class' ] >= 1 )
		{
			echo( '<LI><A CLASS="raw" HREF="mod.php">Panel de moderación</A></LI>' );
		}

		if( $_SESSION[ 'class' ] >= 2 )
		{
			echo( '<LI><A CLASS="raw" HREF="admin.php">Panel de administración</A></LI>' );
		}

		if( $sys_config[ 'enable_about_page' ] )
		{
			echo( '<LI><A CLASS="raw" HREF="about.php">Acerca de</A></LI>' );
		}

		if( $sys_config[ 'enable_unauthenticate' ] )
		{
			echo( '<LI><A CLASS="raw" HREF="unauthenticate.php">Salir</A></LI>' );
		}

		echo( '</UL><DIV CLASS="separator"></DIV>' );
	}

	echo( '<DIV ID="content">' );
	if( get_check( 'denied' ) )
	{
		echo
		( '<DIV CLASS="errorMessage center">Acceso denegado a '
		. html( $_GET[ 'denied' ] )
		. '.</DIV></DIV CLASS="separator"></DIV>'
		);
	}

	if( get_bool( 'function_disabled' ) )
	{
		echo
		( '<DIV CLASS="errorMessage center">Esta función ha sido deshabilitada.</DIV>'
		. '</DIV CLASS="separator"></DIV>'
		);
	}

	if( get_bool( 'requires_user' ) )
	{
		echo
		( '<DIV CLASS="errorMessage center">Esta operación no puede ser ejecutada por el usuario hipervisor.</DIV>'
		. '</DIV CLASS="separator"></DIV>'
		);
	}

	if( get_bool( 'already_voted' ) )
	{
			echo(
			'<DIV CLASS="errorMessage center">Usted ya ha votado por esta propuesta.</DIV>'
			. '<DIV CLASS="separator"></DIV>'
		);
	}

	if( get_bool( 'already_voted_election' ) )
	{
		echo
		( '<DIV CLASS="errorMessage center">Usted ya ha votado en esta elección.'
		     . '</DIV><DIV CLASS="separator"></DIV>'
		);
	}

	if( get_bool( 'vote_success' ) )
	{
		echo
		( '<DIV CLASS="infoMessage center">Se ha realizado la votación exitosamente.</DIV>'
		. '</DIV CLASS="separator"></DIV>'
		);
	}

	if( get_bool( 'postulate_success' ) )
	{
		echo
		( '<DIV CLASS="infoMessage center">Propuesta postulada exitosamente.</DIV>'
		. '<DIV CLASS="separator"></DIV>'
		);
	}

	if( get_bool( 'success' ) )
	{
		echo
		( '<DIV CLASS="infoMessage center">Operación realizada exitosamente.</DIV>'
		. '<DIV CLASS="separator"></DIV>'
		);
	}
}

function lower_header()
{
	echo( '</DIV></BODY></HTML>' );
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
	if( $_SESSION[ 'class' ] == '3' )
	{
		redirect_main( '?requires_user=1' );
	}
}

function require_property
(
	$property,
	$tester = TRUE
)
{
	global $sys_config;
	if( $sys_config[ $property ] != $tester )
	{
		redirect_main( '?function_disabled=1' );
	}
}

function filter_order
(
	$order,
	$date_column,
	$votes_column
)
{
	if( $order == 'most-popular' && $votes_column )
	{
		return 'ORDER BY ' . $votes_column . ' ASC';
	} elseif( $order == 'least-popular' && $votes_column )
	{
		return 'ORDER BY ' . $votes_column . ' DESC';
	} elseif( $order == 'oldest' )
	{
		return 'ORDER BY ' . $date_column . ' ASC';
	}

	return 'ORDER BY ' . $date_column . ' DESC';
}

function get_status_image
(
	$status
)
{
	global $ellipsis, $checkmark, $crossmark;

	if( $status == 0 )
	{
		return $ellipsis;
	} elseif( $status == 1 )
	{
		return $checkmark;
	} else
	{
		return $crossmark;
	}
}

function get_status_description
(
	$status
)
{
	switch( $status )
	{
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
	if( $_SESSION[ 'class' ] <= 2 )
	{
		return $_SESSION[ 'first_name' ] . ' '  . $_SESSION[ 'last_name' ];
	} else
	{
		return 'usuario hipervisor';
	}
}

function make_ini
(
	$what,
	$parent = array()
)
{
	$out = '';
	foreach( $what as $k => $v ) {
		if( is_array( $v ) )
		{
			$sec = array_merge( (array) $parent, (array) $k);
			$out .= '[' . join( '.', $sec ) . ']' . PHP_EOL;
			$out .= make_ini( $v, $sec );
		} else
		{
			$out .= $k . '="' . $v . '"' . PHP_EOL;
		}
	}

	return $out;
}

function save_config()
{
	global $config_paths, $sys_config;
	file_put_contents( $config_paths[ 'sys' ], make_ini( $sys_config ) );
	copy( $sys_config[ 'style' ], 'style.css' );
}

function logout_everyone()
{
	ini_set( 'session.gc_max_lifetime', 0 );
	ini_set( 'session.gc_probability', 1 );
	ini_set( 'session.gc_divisor', 1 );
}

?>
