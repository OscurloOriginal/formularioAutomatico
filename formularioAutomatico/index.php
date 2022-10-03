<?php include 'funciones/conexion.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="style/css/bootstrap.min.css">
	<link rel="stylesheet" href="style/css/jquery.dataTables.min.css">
</head>
<body>
	<div class="container">
		<form action="javascript:cargarDatos(formAuto, 'datos', 'index.php', 'resTable')" id="formAuto" enctype="multipart/form-data">
			<div class="row">
				<div class="col-12 mb-3">
					<h1>Datos de prueba</h1>
				</div>
				<div class="col-6 mb-3">
					<label>Nombre</label>
					<input type="text" class="form-control" name="datos[nombre]">
				</div>
				<div class="col-6 mb-3">
					<label>Apellido</label>
					<input type="text" class="form-control" name="datos[apellido]">
				</div>
				<div class="col-6 mb-3">
					<label>Edad</label>
					<input type="number" class="form-control" name="datos[edad]">
				</div>
				<div class="col-6 mb-3">
					<label>Sexo</label>
					<select class="form-control" name="datos[id_sexo]">
						<option value="">Seleccione</option>
						<?php $querySexo = mysqli_query($conexion, "SELECT * from sexo order by descripcion asc") ?>
						<?php foreach ($querySexo as $datSexo) { ?>
							<option value="<?= $datSexo['id'] ?>"><?= $datSexo['descripcion'] ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-12 mb-3">
					<label>Subir archivo</label>
					<input type="file" class="form-control" name="archivos[foto|fotos/]" accept="image/*">
				</div>
				<div class="col-6 mb-3">
					<button class="btn btn-success" type="submit">Guardar</button>
					<button class="btn btn-danger" type="button" onclick="limpiarDatos('datos')">Vaciar Datos</button>
				</div>
			</div>
		</form>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<table class="table" id="myTable">
					<thead>
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th>Apellido</th>
							<th>Edad</th>
							<th>Sexo</th>
							<th>Img</th>
						</tr>
					</thead>
					<tbody id="resTable"></tbody>
					<tfoot>
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th>Apellido</th>
							<th>Edad</th>
							<th>Sexo</th>
							<th>Img</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</body>
<script src="style/js/jquery-3.6.1.min.js"></script>
<script src="style/js/jquery.dataTables.min.js"></script>
<script>
	function cargarDatos(form, tabla, paDondeVamos, dondeLoCargo) {
		$.ajax("ajax_formularioAutomatico.php?accion=1&tabla="+tabla, {
			type: "POST",
			data: new FormData(form),
			processData: false,
			cache: false,
			contentType: false,
			success: function(response) {
				validar = response.replace(/\s+/g, '').split("<!--Response-->");
				if (validar[1] == "redi") {
					window.location.replace(paDondeVamos);
				} else if (validar[1] == "recargar") {
					console.log(response);
					mostrarDatos(tabla, dondeLoCargo);
				} else if (validar[1] == "false" || validar[1] == "") {
					alert("ha ocurrido un error a la hora de insertar los datos");
				}
			}
		})
	}
</script>
<script>
	function mostrarDatos(tabla, dondeLoCargo) {
		$.ajax("ajax_formularioAutomatico.php?accion=2", {
			type: "POST",
			data: { tabla: tabla, campos: "id / nombre / apellido / edad / id_sexo|id|descripcion|sexo / foto|fotos" },
			success: function(response) {
				$('#myTable').DataTable().clear().draw(); // Limpio la tabla ya existente
				$("#myTable").dataTable().fnDestroy(); // Destruyo
				$("#"+dondeLoCargo).html(response); // cargo los datos
				$('#myTable').DataTable(); // cargo el DataTable
			}
		})
	}
	mostrarDatos('datos', 'resTable');
</script>
<script>
	function limpiarDatos(tabla) { // los borra todos por que si :)
		$.ajax("ajax_formularioAutomatico.php?accion=3&tabla="+tabla, {
			success: function(response) {
				mostrarDatos('datos', 'resTable');
			}
		});
	}
</script>
<script>
	$(document).ready( function () {
		$('#myTable').DataTable();
	} );
</script>
</html>