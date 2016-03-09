<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	function horasDisponibles($profesor, $periodo, $ecs, $mecs) {
		global $sigpa;

		$re = "[0-9]{7,}";

		if(! ereg("$re", $profesor))
			exit;

		$periodo = htmlspecialchars($periodo, ENT_QUOTES);

		if($ecs) {
			$ecs = htmlspecialchars($ecs, ENT_QUOTES);

			$sql = "
				select *
				from periodo 
				where id='$periodo'
				 and tipo='a'
				 and \"idECS\"='$ecs'
			";
		}

		else {
			$mecs = htmlspecialchars($mecs, ENT_QUOTES);

			$sql = "
				select *
				from periodo 
				where id='$periodo'
				 and tipo='a'
				 and \"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')
			";
		}

		$exe = pg_query($sigpa, $sql);
		$periodo = pg_fetch_object($exe);

		$sql = "
			select p.cedula as cedula, d.horas as dedicacion, p.condicion as condicion 
			from profesor as p 
				join dedicacion as d on d.id=p.dedicacion
			where p.cedula='$profesor'
		";
		$exe = pg_query($sigpa, $sql);
		$profesor = pg_fetch_object($exe);

		$sql = "
			select ucm.\"horasTeoricas\" as ht, ucm.\"horasPracticas\" as hp, c.\"dividirHT\" as \"dividirHT\", s.multiplicador as multiplicador, s.grupos as grupos, ucm.tipo as tipo, c.\"horasComunes\" as \"horasComunes\" 
			from carga as c 
				join seccion as s on s.\"ID\"=c.\"idSeccion\" 
				join periodo as p on p.\"ID\"=s.\"idPeriodo\"
				join \"mallaECS\" as mecs on mecs.id=s.\"idMECS\" 
				join \"ucMalla\" as ucm on ucm.\"idMalla\"=mecs.\"idMalla\" and ucm.\"idUC\"=c.\"idUC\" and ucm.periodo=s.\"periodoEstructura\" 
			where c.\"idProfesor\"='$profesor->cedula' and p.\"fechaFin\" >= '$periodo->fechaInicio' and p.\"fechaFin\" <= '$periodo->fechaFin'
		";

		if($p->condicion != 3)
			$sql .= " or c.\"idSuplente\"='$profesor->cedula'";

		$exe = pg_query($sigpa, $sql);

		$total = 0;

		while($horas = pg_fetch_object($exe)) {
			$ht = $horas->ht * $horas->multiplicador;
			$hp = $horas->hp * $horas->multiplicador;

			if($horas->tipo == "t") {
				if($horas->grupos == "t") {
					$hp *= 2;

					if($horas->dividirHT == "t")
						$ht *= 2;
				}
			}

			$descuento = $horas->horasComunes * $horas->multiplicador;
			$total += $ht + $hp - $descuento + $horas->horasComunes;
		}

		return $profesor->dedicacion - $total;
	}

	echo horasDisponibles($_POST["profesor"], $_POST["periodo"], $_POST["ecs"], $_POST["mecs"]);
?>