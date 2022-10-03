<?php include 'funciones/conexion.php' ?>
<?php function funcionMaster($idFiltro, $campoFiltro, $campo, $tabla) {
	include 'funciones/conexion.php';
	$queryMaster = mysqli_query($conexion, "SELECT * from $tabla where $campoFiltro = '$idFiltro'") or die (mysqli_error($conexion));
	$fetchMaster = mysqli_fetch_array($queryMaster);
	return($fetchMaster[$campo]);
} ?>
<?php if ($_GET['accion'] == 1) { ?>
	<?php mysqli_query($conexion, "CREATE table if not exists {$_GET['tabla']} (id int auto_increment, fechaRegistroFR datetime default current_timestamp, primary key(id))") ?>
	<?php  $valores = '' ?>
	<?php foreach ($_POST['datos'] as $key => $value) {
		mysqli_query($conexion, "ALTER table {$_GET['tabla']} add column if not exists $key text default null");
		$valores .= "$key = '$value', ";
	} ?>
	<?php $valores = substr($valores, 0, -2) ?>
	<?php $valoresFile = '' ?>
	<?php $countSize = 0 ?>
	<?php foreach ($_FILES['archivos']['name'] as $key => $value) {
		$llave = explode("|", $key);
		mysqli_query($conexion, "ALTER table {$_GET['tabla']} add column if not exists $llave[0] text default null");
		$valoresFile .= "$llave[0] = '".rand(1, 999)."$value', ";
		$countSize += $_FILES['archivos']['size'][$key];
		$valoresFile2 = explode(" = ", $valoresFile);
		if (!file_exists($llave[1])) {
			mkdir($llave[1], 0777, true);
		}
		move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $llave[1].str_replace("'", "", str_replace(", ", "", $valoresFile2[1])));
	} ?>
	<?php $valoresFile = substr($valoresFile, 0, -2) ?>
	<?php $validar = mysqli_query($conexion, "INSERT into {$_GET['tabla']} set $valores ".(!empty($countSize) ? ", $valoresFile" : "")."") ?>
	<!--Response-->
	<?php if ($validar) {
		echo "recargar";
	} else {
		echo "false";
	} ?>
<?php } else if ($_GET['accion'] == 2) { ?>
	<?php $campos = explode("/", $_POST['campos']) ?>
	<?php $queryManual = mysqli_query($conexion, "SELECT * from {$_POST['tabla']}") or die (mysqli_error($conexion)) ?>
	<?php $countManual = mysqli_num_rows($queryManual) ?>
	<?php if (!empty($countManual)) { ?>
		<?php foreach ($queryManual as $datManual) { ?>
			<tr>
				<?php foreach (explode("/", $_POST['campos']) as $key => $value) { ?>
					<?php $valores = explode("|", str_replace(" ", "", $value)) ?>
					<?php $count = count($valores) ?>
					<?php $formatos_de_imagen = explode("/", ".jpeg/.jpg") ?>
					<?php $banderaIMG = false ?>
					<?php foreach ($formatos_de_imagen as $key => $value) {
						if ((strpos($datManual[$valores[0]], $value)) == true) {
							$banderaIMG = true;
							break;
						} else {
							$banderaIMG = false;
						}
					} ?>
					<<?= (empty($datManual[$valores[0]]) ? "th class = 'text-danger'" : "td") ?>><?= ($count == 2 && $banderaIMG == true ? "<img class='rounded mx-auto' style='width: 250px; height: auto' src='$valores[1]/{$datManual[$valores[0]]}'>" : ($count == 1 ? $datManual[$valores[0]] : ($count == 4 ? funcionMaster($datManual[$valores[0]], $valores[1], $valores[2], $valores[3]) : (empty($datManual[$valores[0]]) ? "No registra" : "Error de validación")))) ?></<?= (empty($datManual[$valores[0]]) ? "th class = 'text-danger'" : "td") ?>>
				<?php } ?>
			</tr>
		<?php } ?>
	<?php } ?>
<?php } else if ($_GET['accion'] == 3) {
	mysqli_query($conexion, "TRUNCATE table {$_GET['tabla']}");
} ?>