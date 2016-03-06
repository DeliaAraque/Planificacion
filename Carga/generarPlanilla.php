<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$periodo = htmlspecialchars($_POST["periodo"], ENT_QUOTES);
	$mecs = htmlspecialchars($_POST["mecs"], ENT_QUOTES);

	$sql = "
		select per.id as periodo, per.\"fechaInicio\" as \"fechaInicio\", per.\"fechaFin\" as \"fechaFin\", e.nombre as estructura, c.id as \"idCarrera\", c.nombre as carrera, s.id as \"idSede\", s.nombre as sede 
		from periodo as per 
			join \"estructuraCS\" as ecs on ecs.id=per.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\"
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\"
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where per.id='$periodo' and per.tipo='a' and per.\"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')
	";
	$exe = pg_query($sigpa, $sql);
	$header = pg_fetch_object($exe);

	$sql = "
		select m.id as id
		from \"mallaECS\" as mecs 
			join malla as m on m.id = mecs.\"idMalla\"
		where mecs.id='$mecs'
	";
	$exe = pg_query($sigpa, $sql);
	$malla = pg_fetch_object($exe);

	$header->fechaInicio = explode("-", $header->fechaInicio);
	$header->fechaInicio = $header->fechaInicio[2] . "/" . $header->fechaInicio[1] . "/" . $header->fechaInicio[0];

	$header->fechaFin = explode("-", $header->fechaFin);
	$header->fechaFin = $header->fechaFin[2] . "/" . $header->fechaFin[1] . "/" . $header->fechaFin[0];

	$nombre = str_replace(" ", "\\ ", "$header->periodo $header->carrera $header->sede $malla->id");

	$cmd = "wkhtmltopdf -O Landscape -T 30mm -B 40mm --header-spacing 10 --header-html \"http://localhost/moduloPlanificacion/Carga/header.php?carrera=$header->carrera&sede=$header->sede&malla=$malla->id&periodo=$header->periodo&fechaInicio=$header->fechaInicio&fechaFin=$header->fechaFin\" --footer-html \"http://localhost/moduloPlanificacion/Carga/footer.php?carrera=$header->idCarrera&sede=$header->idSede\" \"http://localhost/moduloPlanificacion/Carga/planilla.php?mecs=$mecs&periodo=$periodo\" /var/www/html/moduloPlanificacion/Carga/Planillas/$nombre.pdf";
	exec($cmd);
	echo "Compruebe que se haya generado la planilla en la sección <strong><a href=\"javascript: embem('moduloPlanificacion/Carga/planillas.php', '#page-wrapper')\">Planillas</a></strong>&&success";

	$nombre = str_replace("\\", "", $nombre);

	$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se creó la planilla <strong>$nombre.pdf</strong>', 'No SQL')";
	$exe = pg_query($sigpa, $sql);
?>