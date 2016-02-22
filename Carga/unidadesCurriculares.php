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
		where s.\"idPeriodo\"=(select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')) and s.\"periodoEstructura\"='$periodoEstructura' and s.\"idMECS\"='$mecs' 
	";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);

	$sql = "select \"fechaFin\" from periodo where id='$periodo' and tipo='p' and \"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')";
	$exe = pg_query($sigpa, $sql);
	$fechaFin = pg_fetch_object($exe);
	$fechaFin = $fechaFin->fechaFin;

	$sql = "
		select uc.id as id, uc.nombre nombre, ucm.tipo as tipo 
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

<?php
	echo "$uc->nombre ($uc->id)";

	if($uc->tipo == "t")
		echo " <i class=\"fa fa-fw fa-flask\" title=\"Laboratorio\"></i>";
?>

		</th>
	</tr>

<?php
		$sql = "
			select s.\"ID\" as \"ID\", s.id as id, s.grupos as grupos, s.turno as turno 
			from seccion as s 
			where s.\"idPeriodo\"=(select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')) and s.\"periodoEstructura\"='$periodoEstructura' and s.\"idMECS\"='$mecs' 
			order by s.id
		";
		$exe2 = pg_query($sigpa, $sql);

		while($seccion = pg_fetch_object($exe2)) {
?>

	<tr>
		<td>

<?php
			echo $seccion->id;

			if($seccion->grupos == "t")
				echo " <i class=\"fa fa-fw fa-users\" title=\"Se divide en grupos\"></i>";

			if($seccion->turno == "n")
				echo " <i class=\"fa fa-fw fa-moon-o\" title=\"Nocturna\"></i>";
?>

		</td>
		<td>

<?php
			$sql = "
				select per.apellido as \"apellido\", per.nombre as \"nombre\", c.\"idProfesor\" as cedula, c.id as carga, c.\"idSuplente\" as suplente 
				from carga as c 
					join persona as per on per.cedula=c.\"idProfesor\" 
					join seccion as s on s.\"ID\"=c.\"idSeccion\" 
				where c.\"idUC\"='$uc->id' and c.\"idSeccion\"='$seccion->ID'
			";
			$exe3 = pg_query($sigpa, $sql);
			$profesor = pg_fetch_object($exe3);

			if($profesor) {
				++$nS;
?>

			<a href="javascript: moreInfo('moduloPlanificacion/Profesor/consultar.php', 'cedula=<?= $profesor->cedula; ?>')"><?= "$profesor->apellido $profesor->nombre ($profesor->cedula)"; ?></a>

<?php
				if($profesor->suplente) {
					$sql = "select apellido, nombre, cedula from persona where cedula='$profesor->suplente'";
					$exe3 = pg_query($sigpa, $sql);
					$suplente = pg_fetch_object($exe3);

					echo " - Suple <a href=\"javascript: moreInfo('moduloPlanificacion/Profesor/consultar.php', 'cedula=$suplente->cedula')\">$suplente->apellido $suplente->nombre ($suplente->cedula)</a>";
				}

				if($fechaFin >= date("Y-m-d"))
					echo "&nbsp;<i class=\"fa fa-times fa-fw eliminar\" onClick=\"if(confirm('¿Realmente desea desasignarle la sección $seccion->id al profesor $profesor->apellido $profesor->nombre ($profesor->cedula)?')) { sendReq('moduloPlanificacion/Carga/eliminar.php', 'id=$profesor->carga'); unidadesCurriculares(); }\" title=\"Desasignar\"></i>";
			}

			else
				echo "<b style=\"color: red;\">No se ha asignado un profesor a esta sección</b>";
?>

		</td>
	</tr>

<?php
		}

		if($n->n > $nS) {
			if($fechaFin >= date("Y-m-d")) {
?>

	<tr onClick="moreInfo('moduloPlanificacion/Carga/form.php', 'id=<?= $uc->id; ?>&carrera=<?= $carrera; ?>&sede=<?= $sede; ?>&periodo=<?= $periodo; ?>&mecs=<?= $mecs; ?>&periodoEstructura=<?= $periodoEstructura; ?>')">
		<td class="text-center" style="color: #00005b; cursor: pointer; font-weight: bold;" colspan="2">
			Asignar profesor
		</td>
	</tr>

<?php
			}
		}
	}
?>

</table>