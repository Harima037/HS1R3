<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		.titulo1{
			font-weight: bold;
			font-family: Arial;
			font-size: 18;
		}
		.titulo2{
			font-weight: bold;
			font-family: Arial;
			font-size: 16;
		}
		.titulo3{
			font-weight: bold;
			font-family: Arial;
			font-size: 14;
		}
		.texto{
			font-family: Arial;
			font-size: 12;
		}
		.negrita{
			font-weight: bold;
		}
		.linea-firma{
			border-bottom: 1 solid #000000;
		}
		.texto-medio{
			vertical-align: middle;
		}
		.texto-centro{
			text-align: center;
		}
		.texto-derecha{
			text-align: right;
		}
		.texto-izquierda{
			text-align: left;
		}
		.encabezado-tabla{
			font-family: Arial;
			font-size: 11;
			font-weight: bold;
			text-align: center;
			vertical-align: middle;
			color: #FFFFFF;
			background-color: #0070C0;
		}
		.tabla-datos td,th{
			border: 1 solid #000000;
			border-collapse: collapse;
			padding:1;
		}
		.subtitulo-tabla{
			font-weight: bold;
			background-color: #DDDDDD;
		}
		.nota-titulo{
			font-family: Arial;
			font-size:8;
			font-weight: bold;
		}
		.nota-contenido{
			font-family: Arial;
			font-size:8;
		}
	</style>
</head>
<body>
	<table class="tabla-datos">
		<thead>
			<tr>
				<th class="encabezado-tabla">Departamento</th>
				<th class="encabezado-tabla">Nombre</th>
				<th class="encabezado-tabla">Cargo</th>
				<th class="encabezado-tabla">Email</th>
				<th class="encabezado-tabla">Telefono</th>
				<th class="encabezado-tabla">Unidad</th>
				<th class="encabezado-tabla">Usuario</th>
				<th class="encabezado-tabla">Estatus</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($datos as $item)
				<tr>
					<td>{{$item->departamento}}</td>
					<td>{{$item->nombre}}</td>
					<td>{{$item->cargo}}</td>
					<td>{{$item->email}}</td>
					<td>{{$item->telefono}}</td>
					<td>{{$item->claveUnidad}}</td>
					<td>{{$item->username}}</td>
					<td>{{$item->activated}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>