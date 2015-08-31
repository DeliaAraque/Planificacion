<?php
	require "../../script/verifSesion.php";

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["carrera"]))
		exit;

	$carrera = $_POST["carrera"];
?>

<option value="">Estructura</option>

<?php
	require "../../lib/conexion.php";

	$sql="
		select e.id as id, e.nombre as nombre 
		from \"carreraSede\" as cs 
			join \"estructuraCS\" as ecs on ecs.\"idCS\"=cs.id 
			join estructura as e on e.id=ecs.\"idEstructura\" 
		where cs.\"idCarrera\"='$carrera'
		group by e.id, e.nombre 
		order by e.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($estructura=pg_fetch_object($exe))
		echo "
<option value=\"$estructura->id\">$estructura->nombre</option>";

?>