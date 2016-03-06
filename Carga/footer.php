<?php
	require "../../lib/conexion.php";

	$carrera = htmlspecialchars($_GET["carrera"], ENT_QUOTES);

	$sql = "select * from carrera where id = '$carrera'";
	$exe = pg_query($sigpa, $sql);
	$carrera = pg_fetch_object($exe);

	$sede = htmlspecialchars($_GET["sede"], ENT_QUOTES);

	$sql = "select * from sede where id = '$sede'";
	$exe = pg_query($sigpa, $sql);
	$sede = pg_fetch_object($exe);

	$sql = "select * from firma order by orden";
	$exe = pg_query($sigpa, $sql);
?>

<style>
	.firmas {
		text-align: center;
	}

	.firma {
		border-top: 1pt solid;
		display: inline-block;
		font-size: 8pt;
		padding: 0.5em 3em;

		margin: 0em 1em;
	}
</style>

<br/><br/><br/>

<div class="firmas">

<?php
	while($firma = pg_fetch_object($exe)) {
		$sql = "
			select pro.cedula as cedula, p.apellido as apellido, p.nombre as nombre
			from profesor as pro
				join persona as p on p.cedula = pro.cedula
			where pro.cedula = '$firma->idProfesor'
		";
		$exe2 = pg_query($sigpa, $sql);
		$profesor = pg_fetch_object($exe2);
?>

	<span class="firma"><?= "$profesor->apellido $profesor->nombre<br/>$firma->cargo"; ?></span>

<?php
	}

	if($carrera->idCoordinadorInst) {
		$sql = "select cedula, nombre, apellido from persona where cedula='$carrera->idCoordinadorInst'";
		$exe = pg_query($sigpa, $sql);
		$coordinador = pg_fetch_object($exe);

		echo "<span class=\"firma\">$coordinador->apellido $coordinador->nombre<br/>Coordinador Institucional</span>";
	}

	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido
		from \"carreraSede\" as cs 
			join persona as p on p.cedula=cs.\"idCoordinador\" 
		where cs.\"idCarrera\"='$carrera->id' and cs.\"idSede\"='$sede->id'
	";
	$exe = pg_query($sigpa, $sql);
	$coordinador = pg_fetch_object($exe);

	if($coordinador->cedula)
		echo "<span class=\"firma\">$coordinador->apellido $coordinador->nombre<br/>Coordinador</span>";
?>

</div>