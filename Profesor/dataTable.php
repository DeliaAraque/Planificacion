<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";
?>

{
	"data": [

<?php
	if($_SESSION["nivel"] < 3) {
		$sql = "
			select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido, p.telefono as telefono, p.correo as correo, p.direccion as direccion, prof.categoria as categoria, prof.condicion as condicion, prof.dedicacion as dedicacion, prof.profesion as profesion 
			from persona as p 
				join profesor as prof on prof.cedula=p.cedula 
			order by p.apellido, p.nombre, p.cedula
		";
	}

	else {
		$sql = "
			select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido, p.telefono as telefono, p.correo as correo, p.direccion as direccion, prof.categoria as categoria, prof.condicion as condicion, prof.dedicacion as dedicacion, prof.profesion as profesion 
			from persona as p 
				join profesor as prof on prof.cedula=p.cedula 
				join pertenece as per on per.\"idProfesor\" = prof.cedula
			where per.\"idCS\" = (select id from \"carreraSede\" where \"idCarrera\" = '$_SESSION[carreraCoord]' and \"idSede\" = $_SESSION[sedeCoord])
			order by p.apellido, p.nombre, p.cedula
		";
	}

	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe)) {
?>

		[

<?php
		$sql = "select count(\"idProfesor\") as n from pertenece where \"idProfesor\"='$profesor->cedula'";
		$exe2 = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe2);
		$n = $n->n;

		if(($profesor->correo == "Sin asignar") || ($profesor->direccion == "Sin asignar") || ($profesor->telefono == "Sin asignar") || ($profesor->categoria == "No") || ($profesor->condicion == "0") || ($profesor->dedicacion == "No") || ($profesor->profesion == "0") || ($n == "0")) {
?>

			"<i class=\"fa fa-exclamation-triangle alerta\" onClick=\"embem('moduloPlanificacion/Profesor/editar.php', '#page-wrapper', 'cedula=<?= $profesor->cedula ?>')\" title=\"Este profesor necesita ser completado\"></i>",

<?php
		}

		else {
?>

			"",

<?php
		}
?>

			"<?= "$profesor->apellido $profesor->nombre"; ?>",
			"<?= $profesor->cedula; ?>",
			"<?= $profesor->telefono; ?>",
			"<div class=\"row\"> <div class=\"col-xs-7 col-sm-7 col-md-6 col-lg-7\"> <?= $profesor->correo; ?> </div> <div class=\"col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center\"> <i class=\"fa fa-search fa-fw consultar\" title=\"Mas información\" onClick=\"moreInfo('moduloPlanificacion/Profesor/consultar.php', 'cedula=<?= $profesor->cedula ?>')\"></i> <i class=\"fa fa-pencil fa-fw editar\" title=\"Editar\" onClick=\"embem('moduloPlanificacion/Profesor/editar.php', '#page-wrapper', 'cedula=<?= $profesor->cedula ?>')\"></i> <i class=\"fa fa-trash-o fa-fw eliminar\" onClick=\"if(confirm('¿Realmente desea eliminar a <?= "$profesor->apellido $profesor->nombre ($profesor->cedula)"; ?>?')) sendReq('../../script/eliminar.php', 'tabla=persona&campo=cedula&valor=<?= $profesor->cedula ?>', 'moduloPlanificacion/Profesor/index.php')\" title=\"Eliminar\"></i> </div> </div>"
		],

<?php
	}
?>

		["", "", "", "", ""]

	]
}