<?php
	require "../../script/verifSesion.php";

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["carrera"]))
		exit;

	$carrera = $_POST["carrera"];

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["sede"]))
		exit;

	$sede = $_POST["sede"];

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["condicion"]))
		exit;

	$condicion = $_POST["condicion"];
?>

<option value="">Profesor</option>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
			join pertenece as per on per.\"idProfesor\"=prof.cedula
		where prof.condicion='$condicion' and per.\"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede') 
		order by p.apellido, p.nombre, p.cedula
	";
	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe))
		echo "<option value=\"$profesor->cedula\">$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";
?>