<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$ID = htmlspecialchars($_POST["ID"], ENT_QUOTES);

	$sql = "
		select sec.\"ID\" as \"ID\", p.id as periodo, sec.id as id, sec.turno as turno, sec.multiplicador as multiplicador, sec.grupos as grupos, c.id as carrera, s.id as sede, e.id as estructura, sec.\"periodoEstructura\" as \"periodoEstructura\", sec.\"idMECS\" as malla 
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\"
		where sec.\"ID\"='$ID' 
		order by p.id, sec.id, c.nombre, s.nombre, sec.\"periodoEstructura\"
	";
	$exe = pg_query($sigpa, $sql);
	$seccion = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Editar sección</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="seccion" method="POST" action="moduloPlanificacion/Seccion/modificar.php" data-exe="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Sección:
				<input type="text" name="id" placeholder="Sección" value="<?= $seccion->id; ?>" class="form-control" pattern="^[A-Z]$" required="required" />
				<input type="hidden" name="idAnt" value="<?= $seccion->id; ?>" />
				<p class="help-block">Solo está permitido un caracterer alfabético en mayusculas. Ej: A.</p>
			</div>

			<div class="form-group"> 
				Turno:
				<div class="radio-inline">
					<label class="radio-inline"><input type="radio" name="turno" value="d" onClick="document.seccion.multiplicador.value='1';" <?php if($seccion->turno == "d") echo "checked=\"checked\""; ?> required="required"> Diurno </label>
					<label class="radio-inline"><input type="radio" name="turno" value="n" onClick="document.seccion.multiplicador.value='1.5';" <?php if($seccion->turno == "n") echo "checked=\"checked\""; ?> required="required"> Nocturno </label>
				</div>
			</div>

			<div class="form-group">
				Multiplicador:
				<input type="text" name="multiplicador" placeholder="Multiplicador" value="<?= $seccion->multiplicador; ?>" class="form-control" pattern="^[0-9]+(\.[0-9])*$" required="required" />
				<p class="help-block">El multiplicador determina si las horas de las unidades curriculares deben aumentar o disminuir según el turno. Ej: 1.5.</p>
			</div>

			<div class="form-group">
				<label class="checkbox-inline"><input type="checkbox" name="grupos" value="1" <?php if($seccion->grupos == "t") echo "checked=\"checked\""; ?>> Dividir en grupos </label>
			</div>

			<div class="form-group text-center">
				<input type="hidden" name="ID" value="<?= $seccion->ID; ?>" />
				<input type="hidden" name="carrera" value="<?= $seccion->carrera; ?>" />
				<input type="hidden" name="sede" value="<?= $seccion->sede; ?>" />
				<input type="hidden" name="periodo" value="<?= $seccion->periodo; ?>" />
				<input type="hidden" name="malla" value="<?= $seccion->malla; ?>" />
				<input type="hidden" name="periodoEstructura" value="<?= $seccion->periodoEstructura; ?>" />
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>