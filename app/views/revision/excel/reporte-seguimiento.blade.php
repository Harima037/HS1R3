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
		.tabla-datos td.encabezado{
			border:none !important;
			font-size:10; 
			font-weight:bold;
			text-align: center;
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
		<tr>
			<td colspan="7" class="encabezado">GOBIERNO DEL ESTADO DE CHIAPAS</td>
		</tr>
		<tr>
			<td colspan="7" class="encabezado">Instituto de Salud</td>
		</tr>
		<tr>
			<td colspan="7" class="encabezado">Reporte de Seguimiento de Proyectos {{$tipo_proyectos}} {{$ejercicio}}</td>
		</tr>
		
		<tr>
			<th class="encabezado-tabla">Num</th>
			<th class="encabezado-tabla">Clave Presupuestaria</th>
			<th class="encabezado-tabla">Nombre Técnico</th>
			<th class="encabezado-tabla">Dirección</th>
			<th class="encabezado-tabla">Enlace</th>
			<th class="encabezado-tabla">Revisor</th>
			<th class="encabezado-tabla">Estatus Seguimiento</th>
		</tr>
	
		@foreach ($datos as $row => $item)
			<tr height="15">
				<td>{{$row+1}}</td>
				<td>{{$item->ClavePresupuestaria}}</td>
				<td>{{$item->nombreTecnico}}</td>
				<td>{{$item->descripcionUnidadResponsable}}</td>
				<td>{{$item->nombreEnlace or 'Sin Enlace'}}</td>
				<td>{{$item->nombreRevisor or 'Sin Revisor'}}</td>
				<td>
				@if($item->estatusAvance)
					{{$item->estatusAvance}}
				@else
					@if((
						((isset($item->componentesMetasMes[0]))?$item->componentesMetasMes[0]->totalMeta:0) + 
						((isset($item->actividadesMetasMes[0]))?$item->actividadesMetasMes[0]->totalMeta:0) 
						) > 0)
						Inactivo
					@else
						No Programado
					@endif
				@endif
				</td>
			</tr>
		@endforeach
	</table>
</body>
</html>