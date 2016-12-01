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
				<th colspan="11">SEGUIMIENTO A ASPECTO SUSCEPTIBLES DE MEJORA CLASIFICADOS COMO ESPECIFICOS, DERIVADOS DE INFORMES DE RENDICIÓN DE CUENTAS</th>
			</tr>
			<tr height="40">
				<th class="encabezado-tabla">NO.</th>
				<th class="encabezado-tabla" width="20">ASPECTOS<br>SUSCEPTIBLES<br>DE MEJORA </th>
				<th class="encabezado-tabla" width="30">ACTIVIDADES</th>
				<th class="encabezado-tabla" width="20">AREA<br>RESPONSABLE</th>
				<th class="encabezado-tabla" width="20">GRUPO DE<br>TRABAJO</th>
				<th class="encabezado-tabla" width="15">FECHA DE<br>NOTIFICACIÓN</th>
				<th class="encabezado-tabla" width="20">RESULTADO<br>ESPERADO</th>
				<th class="encabezado-tabla" width="20">PRODUCTOS<br>Y/O<br>EVIDENCIAS</th>
				<th class="encabezado-tabla">% AVANCE</th>
				<th class="encabezado-tabla" colspan="2">IDENTIFICACIÓN<br>DEL DOCUMENTO<br>PROBATORIO</th>
			</tr>
		</thead>
		<tbody data-row="{{$row = 3}}">
			@foreach($data as $index => $plan)
				<tr height="25" class="texto-tabla">
					<td>{{$index+1}}</td>
					<td>{{$plan->indicador}}</td>
					<td>{{$plan->actividades}}</td>
					<td>{{$plan->areaResponsable}}</td>
					<td>{{$plan->grupoTrabajo}}</td>
					<td>{{$plan->fechaNotificacion}}</td>
					<td>{{$plan->accionMejora}}</td>
					<td>{{$plan->analisisResultados}}</td>
					<td>{{$plan->avance/$plan->meta}}</td>
					<td></td>
					<td></td>
				</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>