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

	$re = "^[A-ZÁÉÍÓÚÑ0-9\-]+$";

	if(! ereg("$re", $_POST["periodo"]))
		exit;

	$periodo = $_POST["periodo"];
?>

<option value="">Estructura</option>

<?php
	require "../../lib/conexion.php";

	$sql="
		select e.id as id, e.nombre as nombre
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
		where p.id='$periodo' and p.tipo='a' and cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede' 
		group by e.id, e.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($estructura=pg_fetch_object($exe))
		echo "<option value=\"$estructura->id\">$estructura->nombre</option>";

?>