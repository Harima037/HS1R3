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
			<tr>
				<th class="encabezado-tabla">Clave Presupuestaria</th>
				<th width="50" class="encabezado-tabla">Nombre Técnico</th>
				<th class="encabezado-tabla">Estatus Proyecto</th>
				<th class="encabezado-tabla">Presupuesto Aprobado</th>
				<th class="encabezado-tabla">Modificacion Neta</th>
				<th class="encabezado-tabla">Presupuesto Modificado</th>
				<th class="encabezado-tabla">Presupuesto Liberado</th>
				<th class="encabezado-tabla">Presupuesto Por Liberar</th>
				<th class="encabezado-tabla">Presupuesto Ministrado</th>
				<th class="encabezado-tabla">Presupuesto Comprometido Modificado</th>
				<th class="encabezado-tabla">Presupuesto Devengado Modificado</th>
				<th class="encabezado-tabla">Presupuesto Ejercido Modificado</th>
				<th class="encabezado-tabla">Presupuesto Pagado Modificado</th>
				<th class="encabezado-tabla">Disponibilidad Financiera Modificada</th>
				<th class="encabezado-tabla">Disponible Presupuestario Modificado</th>
				<th class="encabezado-tabla">Total Proyectos</th>
			</tr>
		</thead>
		<tbody>
			@foreach($datos as $item)
				<tr {{(!$item->nombreTecnico)?'class="proyecto-no-encontrado"':''}}>
					<td>{{$item->clavePresupuestaria}}</td>
					<td>{{$item->nombreTecnico}}</td>
					<td>{{$item->estatusProyecto}}</td>
					<td>{{$item->presupuestoAprobado}}</td>
					<td>{{$item->modificacionNeta}}</td>
					<td>{{$item->presupuestoModificado}}</td>
					<td>{{$item->presupuestoLiberado}}</td>
					<td>{{$item->presupuestoPorLiberar}}</td>
					<td>{{$item->presupuestoMinistrado}}</td>
					<td>{{$item->presupuestoComprometidoModificado}}</td>
					<td>{{$item->presupuestoDevengadoModificado}}</td>
					<td>{{$item->presupuestoEjercidoModificado}}</td>
					<td>{{$item->presupuestoPagadoModificado}}</td>
					<td>{{$item->disponibilidadFinancieraModificada}}</td>
					<td>{{$item->disponiblePresupuestarioModificado}}</td>
					<td>{{$item->totalProyectos}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>