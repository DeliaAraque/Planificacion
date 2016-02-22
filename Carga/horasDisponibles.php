<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$re = "[0-9]{7,}";

	if(! ereg("$re", $_POST["profesor"]))
		exit;

	$profesor = $_POST["profesor"];

	$periodo = htmlspecialchars($_POST["periodo"], ENT_QUOTES);

	$sql = "
		select p.cedula as cedula, d.horas as dedicacion, p.condicion as condicion 
		from profesor as p 
			join dedicacion as d on d.id=p.dedicacion
		where p.cedula='$profesor'
	";
	$exe = pg_query($sigpa, $sql);
	$profesor = pg_fetch_object($exe);

	$sql = "
		select ucm.\"horasTeoricas\" as ht, ucm.\"horasPracticas\" as hp, c.\"dividirHT\" as \"dividirHT\", s.multiplicador as multiplicador, s.grupos as grupos, ucm.tipo as tipo 
		from carga as c 
			join seccion as s on s.\"ID\"=c.\"idSeccion\" 
			join periodo as p on p.\"ID\"=s.\"idPeriodo\" 
			join \"mallaECS\" as mecs on mecs.id=s.\"idMECS\" 
			join \"ucMalla\" as ucm on ucm.\"idMalla\"=mecs.\"idMalla\" and ucm.\"idUC\"=c.\"idUC\" and ucm.periodo=s.\"periodoEstructura\" 
		where p.id='$periodo' and c.\"idProfesor\"='$profesor->cedula'
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

		$total += $ht + $hp;
	}

	echo $profesor->dedicacion - $total;
?>