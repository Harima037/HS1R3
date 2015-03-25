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
		.tabla-datos td{
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
	<table>
		<tr>
			<td height="20" class="titulo1" colspan="13" align="center">INSTITUTO DE SALUD</td>
		</tr>
		<tr>
			<td height="19" class="titulo2" colspan="13" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="13" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="13" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="13" align="center">
				PLAN DE ACCIÓN DE MEJORA {{$mes['trimestre_letras']}} TRIMESTRE {{$proyecto['ejercicio']}}
			</td>
		</tr>
		<tr>
			<td height="18" colspan="13" align="right" class="negrita">Formato RC-7</td>
		</tr>

		<tr height="90" class="texto-medio texto">
			<td class="texto-centro">Nombre del proyecto: </td>
			<td class="negrita" colspan="7">{{ $proyecto['nombreTecnico'] }}</td>
			<td class="texto-centro">Clave presupuestaria: </td>
			<td class="negrita" colspan="2">{{ $proyecto['ClavePresupuestaria'] }}</td>
			<td></td>
			<td></td>
		</tr>

		<tr><td colspan="13"></td></tr>

		<tr class="tabla-datos" height="40">
			<th colspan="5"></th>
			<th colspan="2" class="encabezado-tabla">¿REQUIERE ACCIÓN DE MEJORA?</th>
			<th colspan="6"></th>
		</tr>

		<tr class="tabla-datos" height="50">
			<th width="18" class="encabezado-tabla">NIVEL</th>
			<th width="30" class="encabezado-tabla">INDICADOR</th>
			<th width="15" class="encabezado-tabla">% DE AVANCE ACUMULADO</th>
			<th width="30" class="encabezado-tabla">ANÁLISIS DE RESULTADOS </th>
			<th width="30" class="encabezado-tabla">JUSTIFICACIÓN</th>
			<th width="10" class="encabezado-tabla">SI</th>
			<th width="10" class="encabezado-tabla">NO</th>
			<th width="22" class="encabezado-tabla">ACCIÓN DE MEJORA</th>
			<th width="22" class="encabezado-tabla">GRUPO DE TRABAJO</th>
			<th width="12" class="encabezado-tabla">FECHA DE INICIO</th>
			<th width="12" class="encabezado-tabla">FECHA DE TERMINO</th>
			<th width="13" class="encabezado-tabla">FECHA DE NOTIFICACIÓN</th>
			<th width="25" class="encabezado-tabla">DOCUMENTACIÓN COMPROBATORIA</th>
		</tr>

		@foreach($componentes as $index => $componente)
		<tr height="90" class="tabla-datos">
			<td class="texto-medio">Componente {{$index+1}}</td>
			<td class="texto-medio">{{$componente['indicador']}}</td>
			<td class="texto-medio texto-centro">
			@if($avances_mes['componentes'][$componente['id']]['meta_programada'] > 0)
				{{
				round(
					(
						$avances_mes['componentes'][$componente['id']]['avance_acumulado']/
						$avances_mes['componentes'][$componente['id']]['meta_programada']
					)
					*100
				,2)
				}}
			@else
				100
			@endif
			 %</td>
			<td class="texto-medio">{{$avances_mes['componentes'][$componente['id']]['analisis_resultados']}}</td>
			<td class="texto-medio">{{$avances_mes['componentes'][$componente['id']]['justificacion_acumulada']}}</td>
			@if(isset($planes_mejora['componentes'][$componente['id']]))
				<td class="texto-medio texto-centro">X</td>
				<td></td>
				<td class="texto-medio">{{$planes_mejora['componentes'][$componente['id']]['accionMejora']}}</td>
				<td class="texto-medio">{{$planes_mejora['componentes'][$componente['id']]['grupoTrabajo']}}</td>
				<td class="texto-medio">{{$planes_mejora['componentes'][$componente['id']]['fechaInicio']}}</td>
				<td class="texto-medio">{{$planes_mejora['componentes'][$componente['id']]['fechaTermino']}}</td>
				<td class="texto-medio">{{$planes_mejora['componentes'][$componente['id']]['fechaNotificacion']}}</td>
				<td class="texto-medio">{{$planes_mejora['componentes'][$componente['id']]['documentacionComprobatoria']}}</td>
			@else
				<td></td>
				<td class="texto-medio texto-centro">X</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			@endif
		</tr>

		@foreach($componente['actividades'] as $indice => $actividad)
		<tr height="90" class="tabla-datos">
			<td class="texto-medio">Actividad {{$index+1}}.{{$indice+1}}</td>
			<td class="texto-medio">{{$actividad['indicador']}}</td>
			<td class="texto-medio texto-centro">
			@if($avances_mes['actividades'][$actividad['id']]['meta_programada'] > 0)
				{{
				round(
					(
						$avances_mes['actividades'][$actividad['id']]['avance_acumulado']/
						$avances_mes['actividades'][$actividad['id']]['meta_programada']
					)
					*100
				,2)
				}}
			@else
				100
			@endif
			 %</td>
			<td class="texto-medio">{{$avances_mes['actividades'][$actividad['id']]['analisis_resultados']}}</td>
			<td class="texto-medio">{{$avances_mes['actividades'][$actividad['id']]['justificacion_acumulada']}}</td>
			@if(isset($planes_mejora['actividades'][$actividad['id']]))
				<td class="texto-medio texto-centro">X</td>
				<td></td>
				<td class="texto-medio">{{$planes_mejora['actividades'][$actividad['id']]['accionMejora']}}</td>
				<td class="texto-medio">{{$planes_mejora['actividades'][$actividad['id']]['grupoTrabajo']}}</td>
				<td class="texto-medio">{{$planes_mejora['actividades'][$actividad['id']]['fechaInicio']}}</td>
				<td class="texto-medio">{{$planes_mejora['actividades'][$actividad['id']]['fechaTermino']}}</td>
				<td class="texto-medio">{{$planes_mejora['actividades'][$actividad['id']]['fechaNotificacion']}}</td>
				<td class="texto-medio">{{$planes_mejora['actividades'][$actividad['id']]['documentacionComprobatoria']}}</td>
			@else
				<td></td>
				<td class="texto-medio texto-centro">X</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			@endif
		</tr>
		@endforeach
		@endforeach

		<tr><td colspan="13"></td></tr>

		<tr class="negrita" height="20">
			<td></td>
			<td></td>
			<td colspan="4" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td></td>
			<td colspan="4" align="center">LIDER DEL PROYECTO</td>
			<td></td>
			<td></td>
		</tr>
		<tr height="40">
			<td></td>
			<td></td>
			<td colspan="4" class="texto-centro">___________________________________________________</td>
			<td></td>
			<td colspan="4" class="texto-centro">___________________________________________________</td>
			<td></td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td></td>
			<td colspan="4" align="center">Nombre</td>
			<td></td>
			<td colspan="4" align="center">{{ $proyecto['liderProyecto'] }}</td>
			<td></td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td></td>
			<td colspan="4" align="center">Cargo</td>
			<td></td>
			<td colspan="4" align="center">Cargo</td>
			<td></td>
			<td></td>
		</tr>

	</table>
</body>