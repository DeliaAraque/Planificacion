<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$cedula = htmlspecialchars($_POST["cedula"], ENT_QUOTES);

	$sql = "select cedula from profesor where cedula='$cedula'";
	$exe = pg_query($sigpa, $sql);
	$profesor = pg_fetch_object($exe);

	$sql = "select * from persona where cedula='$cedula'";
	$exe = pg_query($sigpa, $sql);
	$persona = pg_fetch_object($exe);

	if($persona->cedula) {
?>

{
	"cedula" : "<?= $profesor->cedula; ?>",
	"nombre" : "<?= $persona->nombre; ?>",
	"segundoNombre" : "<?= $persona->segundoNombre; ?>",
	"apellido" : "<?= $persona->apellido; ?>",
	"segundoApellido" : "<?= $persona->segundoApellido; ?>",
	"sexo" : "<?= $persona->sexo; ?>",
	"correo" : "<?= $persona->correo; ?>",
	"direccion" : "<?= $persona->direccion; ?>",
	"telefono" : "<?= $persona->telefono; ?>",
	"telefonoFijo" : "<?= $persona->telefonoFijo; ?>"
}

<?php
	}
?>