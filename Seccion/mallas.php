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

<option value="">Malla</option>

<?php
	require "../../lib/conexion.php";

	$sql="
		select mecs.id as id, m.id as nombre 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join \"mallaECS\" as mecs on mecs.\"idECS\"=ecs.id and mecs.estado is true 
			join malla as m on m.id=mecs.\"idMalla\" 
		where p.id='$periodo' and p.tipo='a' and cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede' 
		group by mecs.id, m.id
		order by m.id desc
	";
	$exe=pg_query($sigpa, $sql);

	while($malla=pg_fetch_object($exe))
		echo "<option value=\"$malla->id\">$malla->nombre</option>";
?>