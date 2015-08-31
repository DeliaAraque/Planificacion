<?php
	require "../../script/verifSesion.php";

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["carrera"])) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$carrera = $_POST["carrera"];
?>

Sedes:<br/>

<?php
	require "../../lib/conexion.php";

	$sql="
		select s.id as id, s.nombre as nombre
		from \"carreraSede\" as cs
			join sede as s on s.id=\"idSede\" 
		where cs.\"idCarrera\"='$carrera'
		order by s.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($sede=pg_fetch_object($exe))
		echo "
<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"sede[]\" value=\"$sede->id\"> $sede->nombre </label>";

?>

<p class="help-block">Debe seleccionar al menos una sede.</p>