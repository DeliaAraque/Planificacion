<?php
	require "../../script/verifSesion.php";

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["carrera"]))
		exit;

	$carrera = $_POST["carrera"];
?>

<option value="">Sede</option>

<?php
	require "../../lib/conexion.php";

	$sql="
		select s.id as id, s.nombre as nombre 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join sede as s on s.id=\"idSede\" 
		where p.tipo='a' and cs.\"idCarrera\"='$carrera' 
		group by s.id, s.nombre 
		order by s.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($sede=pg_fetch_object($exe))
		echo "<option value=\"$sede->id\">$sede->nombre</option>";
?>