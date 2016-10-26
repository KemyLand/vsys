<?php

require_once( 'utilities.php' );
require_once( 'db.php' );

db_require_login();
require_property( 'enable_about_page' );

upper_header( 'Acerca de' );

?>

		<TABLE>
			<TR>
				<TH CLASS="borderless">Licencia y derechos de autor</TH>
			</TR>
			<TR>
				<TD CLASS="monospace borderless">
					<P>
						Copyright &copy; 2016: Alejandro Soto &lt;alejandrosotochacon@yahoo.es&gt;
					</P>
					<P>
						Este programa es software libre; usted puede redistribuirlo y/o modificarlo
						bajo los términos de la Licencia Pública General de GNU, publicada por la
						Fundación por el Software Libre, en su tercera versión, o, a su opinión,
						cualquier versión posterior.
					</P>
					<P>
						Este programa es distribuido en la expectativa de que le sea útil, pero
						SIN NINGUNA GARANTÍA; ni siquiera la garantía implícita de COMERCIABILIDAD
						o SITUABILIDAD PARA UN PROPÓSITO DADO. Vea la Licencia Pública General de GNU
						para más información.
					</P>
					<P>
						Usted debió haber recibido una copia de la Licencia Pública General de GNU
						junto con este programa; de no ser así, por favor visite
						<A HREF="http://www.gnu.org/licenses/">&lt;http://www.gnu.org/licenses/&gt;</A>.
					</P>
				</TD>
			</TR>
		</TABLE>
		<DIV CLASS="separator"></DIV>
		<TABLE>
			<TR>
				<TD CLASS="borderless">
					<A HREF="source.php">Código fuente</A>
				</TD>
			</TR>
		</TABLE>
		<DIV CLASS="separator"></DIV>
		<TABLE>
			<TR>
				<TD CLASS="borderless">
					<A HREF="manual.php">Manual de referencia</A>
				</TD>
			</TR>
		</TABLE>

<?php lower_header(); ?>
