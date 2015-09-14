<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

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

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["mecs"]))
		exit;

	$mecs = $_POST["mecs"];

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+(\-[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)?$";

	if(! ereg("$re", $_POST["periodoEstructura"]))
		exit;

	$periodoEstructura = $_POST["periodoEstructura"];

	$sql = "
		select uc.id as id, uc.nombre nombre 
		from \"mallaECS\" as mecs 
			join malla as m on m.id=mecs.\"idMalla\" 
			join \"ucMalla\" as ucm on ucm.\"idMalla\"=m.id 
			join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" 
		where mecs.id='$mecs' and ucm.periodo='$periodoEstructura' and uc.\"idCarrera\"='$carrera' 
		order by uc.nombre
	";
	$exe = pg_query($sigpa, $sql);
?>

<table class="table">

<?php
	while($uc = pg_fetch_object($exe)) {
?>

	<tr>
		<th class="text-center" style="color: white; background-color: #00005b;">
			<?= "$uc->nombre ($uc->id)"; ?>
		</th>
	</tr>

	<tr onClick="moreInfo('moduloPlanificacion/Carga/form.php', 'id=<?= $uc->id; ?>&carrera=<?= $carrera; ?>&sede=<?= $sede; ?>&periodo=<?= $periodo; ?>&mecs=<?= $mecs; ?>&periodoEstructura=<?= $periodoEstructura; ?>')">
		<td class="text-center" style="color: #00005b; cursor: pointer; font-weight: bold;">
			Asignar profesor
		</td>
	</tr>

<?php
	}
?>

</table>