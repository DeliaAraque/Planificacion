<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = $_POST["id"];

	$sql = "
		select m.id as id, m.fecha as fecha, e.id as \"estructuraID\", e.estructura as \"estructuraEst\", c.id as carrera, array_to_string(array_agg(s.id), '&') as sedes
		from malla as m 
			join \"mallaECS\" as mecs on mecs.\"idMalla\"=m.id 
			join \"estructuraCS\" as ecs on ecs.id=mecs.\"idECS\" 
			join \"estructura\" as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where m.id='$id' 
		group by m.id, m.fecha, e.id, e.estructura, c.id
	";
	$exe = pg_query($sigpa, $sql);
	$malla = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Nueva malla <small>(Usando como base <?= $malla->id; ?>)</small></h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="malla" method="POST" action="moduloPlanificacion/Malla/reutilizar.php" data-exe="embem('moduloPlanificacion/Malla/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="id" placeholder="Código" class="form-control" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+(\-[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$" onKeyUp="Verif(this)" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos y guiones. Ej: PNFI-2012.</p>
			</div>

			<div class="form-group">
				<input type="text" name="fecha" placeholder="Fecha de creación" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
				<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 27/04/2011.</p>
			</div>

			<div class="form-group" id="sedes">
				Sedes:<br/>

<?php
	$sql="
		select s.id as id, s.nombre as nombre
		from \"carreraSede\" as cs
			join sede as s on s.id=\"idSede\" 
		where cs.\"idCarrera\"='$malla->carrera'
		order by s.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($sede = pg_fetch_object($exe)) {
?>

					<label class="checkbox-inline"><input type="checkbox" name="sede[]" value="<?= $sede->id ?>" <?php if(in_array($sede->id, $sedes)) echo "checked=\"checked\""; ?>> <?= $sede->nombre ?> </label>

<?php
	}
?>

				<p class="help-block">Debe seleccionar al menos una sede.</p>

			</div>

			<div class="form-group" id="malla">

<?php
	$sql = "select * from \"unidadCurricular\" where \"idCarrera\"='$malla->carrera' order by nombre";
	$exe = pg_query($sigpa, $sql);

	$optionsBan = "null";

	while($uc = pg_fetch_object($exe))
		$ucOptions .= "
				<option value=\"$uc->id\">$uc->nombre ($uc->id)</option>";

	$sql = "select * from estructura where id='$malla->estructuraID'";
	$exe = pg_query($sigpa, $sql);
	$estructura = pg_fetch_object($exe);
	$estructuraJson = base64_encode($estructura->estructura);
	$estructura = json_decode($estructura->estructura);
?>

				<table class="table">

<?php
	/*
		Propiedades del objeto Periodo:
			· Identificador (id)
			· Nombre (nombre)
			· Duración (duracion)
			· Subperiodos (subperiodos)
	*/

	$n = 0;

	foreach($estructura->periodos as $periodo) {
?>

					<tr id="<?= $periodo->id; ?>"><th class="text-center" style="color: white; background-color: #00005b;"><?= "$periodo->nombre ($periodo->id)"; ?></th></tr>

<?php
		if(!$periodo->subperiodos) {
			$sql = "
				select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable, ucm.\"horasTeoricas\" as \"horasTeoricas\", ucm.\"horasPracticas\" as \"horasPracticas\", ucm.tipo as tipo 
				from \"ucMalla\" as ucm
					join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" 
				where ucm.periodo='$periodo->id' and ucm.\"idMalla\"='$malla->id' 
				order by uc.nombre
			";
			$exe = pg_query($sigpa, $sql);

			while($unidadCurricular = pg_fetch_object($exe)) {
				$optionsBan .= ",\"$unidadCurricular->id\"";
?>

					<tr class="<?= $unidadCurricular->id; ?>"><td>
						<div class="form-group row">
							<div class="form-group col-xs-11">
								<strong><?= "$unidadCurricular->nombre ($unidadCurricular->id)"; ?></strong>
								<input type="hidden" name="unidadCurricular<?= $n; ?>" value="<?= $unidadCurricular->id; ?>" />
							</div>

							<div class="form-group col-xs-1">
								<i class="fa fa-trash-o fa-fw eliminar" title="Quitar unidad curricular" onClick="borrarUc2(this)"></i>
							</div>
						</div>

						<div class="form-group">
							<label class="checkbox-inline"><input type="checkbox" name="tipo<?= $n; ?>" value="1" <?php if($unidadCurricular->tipo == "t") echo "checked=\"checkad\""; ?>> Laboratorio </label>
						</div>

						<div class="row">
							<div class="form-group col-xs-6">
								<input type="text" name="ht<?= $n; ?>" placeholder="Horas teóricas" value="<?= $unidadCurricular->horasTeoricas; ?>" class="form-control" pattern="^[0-9]+$" required="required" />
								<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 2.</p>
							</div>

							<div class="form-group col-xs-6">
								<input type="text" name="hp<?= $n; ?>" placeholder="Horas prácticas" value="<?= $unidadCurricular->horasPracticas; ?>" class="form-control" pattern="^[0-9]+$" required="required" />
								<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 3.</p>
							</div>
						</div>

						<input type="hidden" name="periodo<?= $n; ?>" value="<?= $periodo->id; ?>" />
					</td></tr>

<?php
				++$n;
			}
?>

					<tr>
						<td class="text-center" title="Nueva unidad curricular" onClick="nuevaUC(this.parentNode, 1, '<?= $periodo->id; ?>')" style="cursor: pointer;"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>

<?php
		}

		else {
			/*
				Propiedades del objeto Subperiodos:
					· Identificador (id)
					· Nombre (nombre)
					· Duración (duracion)
			*/

			$nPeriodos = count($periodo->subperiodos);  // Cantidad de subperiodos

			foreach($periodo->subperiodos as $subperiodo) {
?>

					<tr id="<?= "$periodo->id-$subperiodo->id"; ?>"><th class="text-center" style="color: #00005b;"><?= "$subperiodo->nombre ($subperiodo->id)"; ?></th></tr>

<?php

				$sql = "
					select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable, ucm.\"horasTeoricas\" as \"horasTeoricas\", ucm.\"horasPracticas\" as \"horasPracticas\", ucm.tipo as tipo 
					from \"ucMalla\" as ucm
						join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" 
					where ucm.periodo='$periodo->id-$subperiodo->id' and ucm.\"idMalla\"='$malla->id' 
					order by uc.nombre
				";
				$exe = pg_query($sigpa, $sql);

				while($unidadCurricular = pg_fetch_object($exe)) {
					$optionsBan .= ",\"$unidadCurricular->id\"";
?>

					<tr class="<?= $unidadCurricular->id; ?>"><td>
						<div class="form-group row">
							<div class="form-group col-xs-11">
								<strong><?= "$unidadCurricular->nombre ($unidadCurricular->id)"; ?></strong>
								<input type="hidden" name="unidadCurricular<?= $n; ?>" value="<?= $unidadCurricular->id; ?>" />
							</div>

							<div class="form-group col-xs-1">
								<i class="fa fa-trash-o fa-fw eliminar" title="Quitar unidad curricular" onClick="borrarUc2(this)"></i>
							</div>
						</div>

						<div class="form-group">
							<label class="checkbox-inline"><input type="checkbox" name="tipo<?= $n; ?>" value="1" <?php if($unidadCurricular->tipo == "t") echo "checked=\"checkad\""; ?>> Laboratorio </label>
						</div>

						<div class="row">
							<div class="form-group col-xs-6">
								<input type="text" name="ht<?= $n; ?>" placeholder="Horas teóricas" value="<?= $unidadCurricular->horasTeoricas; ?>" class="form-control" pattern="^[0-9]+$" required="required" />
								<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 2.</p>
							</div>

							<div class="form-group col-xs-6">
								<input type="text" name="hp<?= $n; ?>" placeholder="Horas prácticas" value="<?= $unidadCurricular->horasPracticas; ?>" class="form-control" pattern="^[0-9]+$" required="required" />
								<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 3.</p>
							</div>
						</div>

						<input type="hidden" name="periodo<?= $n; ?>" value="<?= "$periodo->id-$subperiodo->id"; ?>" />
					</td></tr>

<?php
					++$n;
				}
?>

					<tr>
						<td class="text-center" title="Nueva unidad curricular" onClick="nuevaUC(this.parentNode, <?= $nPeriodos ?>, '<?= "$periodo->id-$subperiodo->id"; ?>')" style="cursor: pointer;"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>

<?php
				--$nPeriodos;
			}
		}
	}
?>
				</table>

				<input type="hidden" name="cantidad" value="<?= $n; ?>" />

				<select name="unidadCurricularPlantilla" style="display: none;" disabled="disabled">
					<option value="">Unidad Curricular</option>

					<?= $ucOptions; ?>
				</select>

				<script>
					var optionsBan = new Array(<?= $optionsBan; ?>);

					for(var i = 0; i < optionsBan.length; ++i)
						$("select[name^='unidadCurricular'] option[value='" + optionsBan[i] + "']").attr("disabled", "disabled");

					function noRepetida(uc) {
						$("." + $("select:focus + input").attr("value")).css("display", "none");

						$("select[name^='unidadCurricular'] option[value='" + $("select:focus + input").attr("value") + "']").removeAttr("disabled");

						if(uc)
							$("select[name^='unidadCurricular'] option[value='" + uc + "']").attr("disabled", "disabled");

						$("select:focus option[value='" + uc + "']").removeAttr("disabled");
						$("select:focus + input").attr("value", uc);
					}

					function duracionPeridos(duracion, periodo) {
						var codigoUC = duracion.parentNode.parentNode.querySelector("input[type='hidden']").value;
						var tipo = (duracion.parentNode.parentNode.querySelector("input[name^='tipo']").checked) ? 1 : 0;
						var ht = duracion.parentNode.parentNode.querySelector("input[name^='ht']").value;
						var hp = duracion.parentNode.parentNode.querySelector("input[name^='hp']").value;

						if((! codigoUC) || (! ht) || (! hp)) {
							popUp("Debe indicar la unidad curricular, las horas teoricas y las horas practicas antes de asignar la duración", "info");
							return false;
						}

						$("." + codigoUC).css("display", "none");
						$("." + codigoUC + " input").attr("disabled", "disabled");

						periodo = periodo.split("-");
						var subperiodo = periodo[1];
						periodo = periodo[0];

						if(duracion.value == 1)
							return false;

						var nombreUC = document.querySelector("select[name^='unidadCurricular'] option[value='" + codigoUC + "']").innerHTML;

						var estructura = JSON.parse(atob("<?= $estructuraJson; ?>"));
						var periodos = estructura.periodos;
						var lista = new Array;

						for(var i = 0; i < periodos.length; ++i) {
							if(periodos[i].id == periodo) {
								var subperiodos = periodos[i].subperiodos;

								for(var i = 0; i < subperiodos.length; ++i)
									lista[lista.length] = subperiodos[i].id;

								break;
							}
						}

						for(var i = 1; i < duracion.value; ++i) {
							var cantidad = document.malla.cantidad;

							var contenido = "";
							contenido += "<input type=\"hidden\" name=\"unidadCurricular" + cantidad.value + "\" value=\"" + codigoUC + "\" />";

							if(tipo)
								contenido += "<input type=\"hidden\" name=\"tipo" + cantidad.value + "\" value=\"" + tipo + "\" />";

							contenido += "<input type=\"hidden\" name=\"ht" + cantidad.value + "\" value=\"" + ht + "\" />";
							contenido += "<input type=\"hidden\" name=\"hp" + cantidad.value + "\" value=\"" + hp + "\" />";
							contenido += "<input type=\"hidden\" name=\"periodo" + cantidad.value + "\" value=\"" + periodo + "-" + lista[lista.indexOf(subperiodo) + i] + "\" />";

							var tr = document.createElement("tr");
							tr.className = codigoUC;
							tr.innerHTML = "<td class=\"text-center\"><strong>" + nombreUC + contenido + "</strong></td>";

							$(tr).insertAfter("#" + periodo + "-" + lista[lista.indexOf(subperiodo) + i]);

							cantidad.value = parseInt(cantidad.value) + 1;
						}
					}

					function nuevaUC(tr, n, periodo) {
						var cantidad = document.malla.cantidad;
						var uc = document.createElement("tr");
						var nuc = "";

						nuc += "<td> <div class=\"form-group row\"> <div class=\"form-group col-xs-11\"> <select name=\"unidadCurricular" + cantidad.value + "\" class=\"form-control\" onChange=\"noRepetida(this.value)\" required=\"required\">";
						nuc += $("select[name='unidadCurricularPlantilla']").html();
						nuc += "</select> <input type=\"hidden\" name=\"valorAnt\" /> </div> <div class=\"form-group col-xs-1\"> <i class=\"fa fa-trash-o fa-fw eliminar\" title=\"Quitar unidad curricular\" style=\"padding-top: 0.3em;\" onClick=\"borrarUc(this)\"></i> </div> </div> <div class=\"form-group\"> <label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"tipo" + cantidad.value + "\" value=\"1\"> Laboratorio </label> </div> <div class=\"row\"> <div class=\"form-group col-xs-6\"> <input type=\"text\" name=\"ht" + cantidad.value + "\" placeholder=\"Horas teóricas\" class=\"form-control\" pattern=\"^[0-9]+$\" required=\"required\" /> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 2.</p> </div> <div class=\"form-group col-xs-6\"> <input type=\"text\" name=\"hp" + cantidad.value + "\" placeholder=\"Horas prácticas\" class=\"form-control\" pattern=\"^[0-9]+$\" required=\"required\" /> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 3.</p> </div> </div>";

						if(n > 1) {
							nuc += "<div class=\"form-group\"> <input type=\"number\" name=\"duracion" + cantidad.value + "\" placeholder=\"Duración\" value=\"1\" class=\"form-control\" pattern=\"^[0-9]+$\" min=\"1\" max=\"";
							nuc += n;
							nuc += "\" onChange=\"duracionPeridos(this, '" + periodo + "')\" required=\"required\" /> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Periodos</strong>. Ej: ";
							nuc += n;
							nuc += ".</p> </div>";
						}

						nuc += "<input type=\"hidden\" name=\"periodo" + cantidad.value + "\" value=\"";
						nuc += periodo;
						nuc += "\" /> </td>";

						uc.innerHTML += nuc;

						tr.parentNode.insertBefore(uc, tr);

						cantidad.value = parseInt(cantidad.value) + 1;
					}

					function borrarUc(btn) {
						if(btn.parentNode.parentNode.querySelector("select").value) {
							$("." + btn.parentNode.parentNode.querySelector("input[type='hidden']").value).css("display", "none");
							$("." + btn.parentNode.parentNode.querySelector("input[type='hidden']").value + " input").attr("disabled", "disabled");
							$("select[name^='unidadCurricular'] option[value='" + btn.parentNode.parentNode.querySelector("input[type='hidden']").value + "']").removeAttr("disabled");
						}

						btn.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(btn.parentNode.parentNode.parentNode.parentNode);
					}

					function borrarUc2(btn) {
						$("." + btn.parentNode.parentNode.querySelector("input[type='hidden']").value).css("display", "none");
						$("." + btn.parentNode.parentNode.querySelector("input[type='hidden']").value + " input").attr("disabled", "disabled");
						$("select[name^='unidadCurricular'] option[value='" + btn.parentNode.parentNode.querySelector("input[type='hidden']").value + "']").removeAttr("disabled");
						btn.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(btn.parentNode.parentNode.parentNode.parentNode);
					}
				</script>

			</div>

			<input type="hidden" name="carrera" value="<?= $malla->carrera ?>" />
			<input type="hidden" name="estructura" value="<?= $malla->estructuraID ?>" />
			<input type="hidden" name="malla" value="<?= $malla->id ?>" />

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Malla/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>

<script>
	function selectES(carrera) {
		if(! carrera) {
			document.malla.estructura.innerHTML = "<option value=\"\">Estructura</option>";
			$("#sedes").html("");
			$("#malla").html("");
			return false;
		}

		embem('moduloPlanificacion/Malla/estructuras.php', document.malla.estructura, "carrera=" + carrera);
		embem('moduloPlanificacion/Malla/sedes.php', "#sedes", "carrera=" + carrera);
	}

	function estructuraMalla(carrera, estructura) {
		if(! estructura) {
			$("#malla").html("");
			return false;
		}

		embem('moduloPlanificacion/Malla/malla.php', "#malla", "carrera=" + carrera + "&estructura=" + estructura);
	}
</script>