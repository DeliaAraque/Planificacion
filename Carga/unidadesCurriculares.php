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
		select count(s.id) as n 
		from seccion as s 
			join periodo as p on p.\"ID\"=s.\"idPeriodo\"
		where p.id='$periodo' and s.\"periodoEstructura\"='$periodoEstructura'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);

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
		$nS = 0;
?>

	<tr>
		<th class="text-center" style="color: white; background-color: #00005b;" colspan="2">
			<?= "$uc->nombre ($uc->id)"; ?>
		</th>
	</tr>

<?php
		$sql = "
			select per.apellido as \"apellidoProfesor\", per.nombre as \"nombreProfesor\", c.\"idProfesor\" as profesor, string_agg(concat_ws('&', s.id, c.id, c.\"idSuplente\"), '&&' order by s.id) as seccion 
			from carga as c 
				join persona as per on per.cedula=c.\"idProfesor\" 
				join seccion as s on s.\"ID\"=c.\"idSeccion\" 
				join periodo as p on p.\"ID\"=s.\"idPeriodo\" 
			where c.\"idUC\"='$uc->id' and p.id='$periodo' 
			group by per.apellido, per.nombre, c.\"idProfesor\" 
			order by per.apellido, per.nombre, c.\"idProfesor\"
		";
		$exe2 = pg_query($sigpa, $sql);

		while($carga = pg_fetch_object($exe2)) {
?>

	<tr>
		<td><a href="javascript: moreInfo('moduloPlanificacion/Profesor/consultar.php', 'cedula=<?= $carga->profesor; ?>')"><?= "$carga->apellidoProfesor $carga->nombreProfesor ($carga->profesor)"; ?></a></td>
		<td>

<?php
			$secciones = explode("&&", $carga->seccion);
			$nS += count($secciones);

			if(!$nS)
				$nS = 0;

			foreach($secciones as $seccion) {
				list($seccion, $idCarga, $suplente) = explode("&", $seccion);

				echo "<span>$seccion";
				
				if($suplente) {
					$sql = "select apellido, nombre, cedula from persona where cedula='$suplente'";
					$exe3 = pg_query($sigpa, $sql);
					$suplente = pg_fetch_object($exe3);

					echo " - Suple <a href=\"javascript: moreInfo('moduloPlanificacion/Profesor/consultar.php', 'cedula=$suplente->cedula')\">$suplente->apellido $suplente->nombre ($suplente->cedula)</a>";
				}
?>

			&nbsp;<i class="fa fa-times fa-fw eliminar" onClick="if(confirm('¿Realmente desea desasignarle la sección <?= $seccion; ?> al profesor <?= "$carga->apellidoProfesor $carga->nombreProfesor ($carga->profesor)"; ?>?')) { sendReq('moduloPlanificacion/Carga/eliminar.php', 'id=<?= $idCarga ?>'); this.parentNode.parentNode.removeChild(this.parentNode); }" title="Desasignar"></i><br/></span>

<?php
			}
?>
		</td>
	</tr>

<?php
		}

		if($n->n > $nS) {
?>

	<tr onClick="moreInfo('moduloPlanificacion/Carga/form.php', 'id=<?= $uc->id; ?>&carrera=<?= $carrera; ?>&sede=<?= $sede; ?>&periodo=<?= $periodo; ?>&mecs=<?= $mecs; ?>&periodoEstructura=<?= $periodoEstructura; ?>')">
		<td class="text-center" style="color: #00005b; cursor: pointer; font-weight: bold;" colspan="2">
			Asignar profesor
		</td>
	</tr>

<?php
		}
	}
?>

</table>