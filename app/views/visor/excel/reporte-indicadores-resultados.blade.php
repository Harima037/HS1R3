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
		.sub-encabezado-tabla{
			font-family: Arial;
			font-size: 11;
			font-weight: bold;
			vertical-align: middle;
			color: #FFFFFF;
			background-color: #0050A0;
		}
		.texto-tabla{
			font-family: Arial;
			font-size: 10;
			vertical-align: middle;
		}
		.proyecto-no-encontrado{
			color: #FFFFFF;
			background-color: #C00000;
		}
		.tabla-datos td,th{
			border: 1 solid #000000;
			border-collapse: collapse;
			padding:1;
		}
		.tabla-datos td{
			vertical-align: top;
			padding: 5px;
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
			<tr height="27">
				<th class="encabezado-tabla" width="26">Clave Presupuestaria</th>
				<th colspan="4" class="encabezado-tabla">Nombre Técnico</th>
			</tr>
			<tr height="27">
				<th colspan="2" class="encabezado-tabla">Indicadores</th>
				<th class="encabezado-tabla" width="14">Meta</th>
				<th class="encabezado-tabla" width="14">Avance</th>
				<th class="encabezado-tabla" width="14">Porcentaje</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data as $tipo_proyecto => $proyectos)
				@if($tipo_proyecto == 1)
					<tr height="27"><th class="sub-encabezado-tabla" colspan="5">Proyectos Institucionales</th></tr>
				@else
					<tr height="27"><th class="sub-encabezado-tabla" colspan="5">Proyectos de Inversión</th></tr>
				@endif
				@foreach($proyectos as $proyecto)
					<tr height="25" class="texto-tabla negrita">
						<td class="subtitulo-tabla">{{$proyecto['clave']}}</td>
						<td class="subtitulo-tabla" colspan="4">{{$proyecto['nombre']}}</td>
					</tr>
					@foreach($proyecto['componentes'] as $componente)
						<tr height="25" class="texto-tabla">
							<td colspan="2">{{$componente['indicador']}}</td>
							<td>{{$componente['meta']}}</td>
							<td>{{$componente['avance']}}</td>
							<td>{{$componente['porcentaje']}}</td>
						</tr>
					@endforeach

					@foreach($proyecto['actividades'] as $actividad)
						<tr height="25" class="texto-tabla">
							<td colspan="2">{{$actividad['indicador']}}</td>
							<td>{{$actividad['meta']}}</td>
							<td>{{$actividad['avance']}}</td>
							<td>{{$actividad['porcentaje']}}</td>
						</tr>
					@endforeach
				@endforeach
			@endforeach
		</tbody>
	</table>
</body>
</html>