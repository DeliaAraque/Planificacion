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
?>

<option value="">Periodo académico</option>

<?php
	require "../../lib/conexion.php";

	$sql="
		select p.id as id
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\"
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
		where cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede' 
		group by p.id 
		order by p.id desc
	";
	$exe=pg_query($sigpa, $sql);

	while($periodo=pg_fetch_object($exe))
		echo "<option value=\"$periodo->id\">$periodo->id</option>";
?>