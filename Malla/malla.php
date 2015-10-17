<?php
	require "../../script/verifSesion.php";

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["carrera"])) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$carrera = $_POST["carrera"];

	if(! is_numeric($_POST["estructura"]))
		exit;

	$estructura = $_POST["estructura"];

	require "../../lib/conexion.php";

	$sql = "select * from \"unidadCurricular\" where \"idCarrera\"='$carrera' order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($uc = pg_fetch_object($exe))
		$ucOptions .= "
				<option value=\"$uc->id\">$uc->nombre ($uc->id)</option>";

	$sql = "select * from estructura where id='$estructura'";
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
?>

	<tr><td>
		<div class="form-group row">
			<div class="form-group col-xs-11">
				<select name="unidadCurricular<?= $n; ?>" class="form-control" onChange="noRepetida(this.value)" required="required">
					<option value="">Unidad Curricular</option>

					<?= $ucOptions; ?>
				</select>
				<input type="hidden" name="valorAnt" />
			</div>

			<div class="form-group col-xs-1">
				<i class="fa fa-trash-o fa-fw eliminar" title="Quitar unidad curricular" onClick="borrarUc(this)"></i>
			</div>
		</div>

		<div class="form-group">
			<label class="checkbox-inline"><input type="checkbox" name="tipo<?= $n; ?>" value="1"> Laboratorio </label>
		</div>

		<div class="row">
			<div class="form-group col-xs-6">
				<input type="text" name="ht<?= $n; ?>" placeholder="Horas teóricas" class="form-control" pattern="^[1-9]$" maxlength="1" required="required" />
				<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 2.</p>
			</div>

			<div class="form-group col-xs-6">
				<input type="text" name="hp<?= $n; ?>" placeholder="Horas prácticas" class="form-control" pattern="^[0-9]$" maxlength="1" required="required" />
				<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 3.</p>
			</div>
		</div>

		<input type="hidden" name="periodo<?= $n; ?>" value="<?= $periodo->id; ?>" />
	</td></tr>

	<tr>
		<td class="text-center" title="Nueva unidad curricular" onClick="nuevaUC(this.parentNode, 1, '<?= $periodo->id; ?>')" style="cursor: pointer;"><i class="fa fa-plus fa-fw agregar"></i></td>
	</tr>

<?php
			++$n;
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

	<tr><td>
		<div class="form-group row">
			<div class="form-group col-xs-11">
				<select name="unidadCurricular<?= $n; ?>" class="form-control" onChange="noRepetida(this.value)" required="required">
					<option value="">Unidad Curricular</option>

					<?= $ucOptions; ?>
				</select>
				<input type="hidden" name="valorAnt" />
			</div>

			<div class="form-group col-xs-1">
				<i class="fa fa-trash-o fa-fw eliminar" title="Quitar unidad curricular" onClick="borrarUc(this)"></i>
			</div>
		</div>

		<div class="form-group">
			<label class="checkbox-inline"><input type="checkbox" name="tipo<?= $n; ?>" value="1"> Laboratorio </label>
		</div>

		<div class="row">
			<div class="form-group col-xs-6">
				<input type="text" name="ht<?= $n; ?>" placeholder="Horas teóricas" class="form-control" pattern="^[1-9]$" maxlength="1" required="required" />
				<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 2.</p>
			</div>

			<div class="form-group col-xs-6">
				<input type="text" name="hp<?= $n; ?>" placeholder="Horas prácticas" class="form-control" pattern="^[0-9]$" maxlength="1" required="required" />
				<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 3.</p>
			</div>
		</div>

<?php
				if($nPeriodos > 1) {
?>

		<div class="form-group">
			<input type="number" name="duracion<?= $n; ?>" placeholder="Duración" value="1" class="form-control" pattern="^[0-9]+$" min="1" max="<?= $nPeriodos; ?>" onChange="duracionPeridos(this, '<?= "$periodo->id-$subperiodo->id"; ?>')" required="required" />
			<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Periodos</strong>. Ej: <?= $nPeriodos; ?>.</p>
		</div>

<?php
				}
?>


		<input type="hidden" name="periodo<?= $n; ?>" value="<?= "$periodo->id-$subperiodo->id"; ?>" />
	</td></tr>

	<tr>
		<td class="text-center" title="Nueva unidad curricular" onClick="nuevaUC(this.parentNode, <?= $nPeriodos ?>, '<?= "$periodo->id-$subperiodo->id"; ?>')" style="cursor: pointer;"><i class="fa fa-plus fa-fw agregar"></i></td>
	</tr>

<?php
				--$nPeriodos;
				++$n;
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
		nuc += "</select> <input type=\"hidden\" name=\"valorAnt\" /> </div> <div class=\"form-group col-xs-1\"> <i class=\"fa fa-trash-o fa-fw eliminar\" title=\"Quitar unidad curricular\" style=\"padding-top: 0.3em;\" onClick=\"borrarUc(this)\"></i> </div> </div> <div class=\"form-group\"> <label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"tipo" + cantidad.value + "\" value=\"1\"> Laboratorio </label> </div> <div class=\"row\"> <div class=\"form-group col-xs-6\"> <input type=\"text\" name=\"ht" + cantidad.value + "\" placeholder=\"Horas teóricas\" class=\"form-control\" pattern=\"^[1-9]$\" maxlength=\"1\" required=\"required\" /> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 2.</p> </div> <div class=\"form-group col-xs-6\"> <input type=\"text\" name=\"hp" + cantidad.value + "\" placeholder=\"Horas prácticas\" class=\"form-control\" pattern=\"^[0-9]$\" maxlength=\"1\" required=\"required\" /> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 3.</p> </div> </div>";

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
</script>