<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = htmlspecialchars($_POST["id"], ENT_QUOTES);
	$idECS = htmlspecialchars($_POST["ecs"], ENT_QUOTES);

	$sql = "delete from periodo where id='$id' and \"idECS\"='$idECS'";
	$exe = pg_query($sigpa, $sql);

	if($exe) {
		$sql2 = "
			select c.nombre as carrera, s.nombre as sede, e.nombre as estructura 
			from \"estructuraCS\" as ecs
				join estructura as e on e.id=ecs.\"idEstructura\"
				join \"carreraSede\" as cs on cs.id=ecs.\"idCS\"
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
			where ecs.id='$idECS'
		";
		$exe = pg_query($sigpa, $sql2);
		$ecs = pg_fetch_object($exe);

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se eliminó <strong>$id</strong> de <strong>$ecs->carrera - $ecs->sede ($ecs->estructura)</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

		echo "Se eliminó satisfactoriamente&&success";
	}

	else {
		echo "No se puede eliminar, es posible que algún dato dependa de este elemento&&error";
	}
?>