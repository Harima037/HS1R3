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
			<td height="20" class="titulo1" colspan="10" align="center">INSTITUTO DE SALUD</td>
		</tr>
		<tr>
			<td height="19" class="titulo2" colspan="10" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="10" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="10" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="10" align="center">SEGUIMIENTO DE METAS {{$proyecto['ejercicio']}}</td>
		</tr>
		<tr>
			<td height="18" colspan="10" align="right" class="negrita">Formato RC-3</td>
		</tr>



		<tr height="90" class="texto-medio texto">
			<td class="texto-centro">Nombre del proyecto: </td>
			<td class="negrita" colspan="2">{{ $proyecto['nombreTecnico'] }}</td>
			<td class="texto-centro">Clave presupuestaria: </td>
			<td class="negrita" colspan="2">{{ $proyecto['ClavePresupuestaria'] }}</td>
			<td class="texto-derecha">Al mes de: </td>
			<td class="negrita">{{$mes['mes']}}</td>
			<td></td>
			<td></td>
		</tr>



		<tr height="20" class="texto">
			<td class="texto-derecha">Información: </td>
			<td class="negrita">Estatal</td>
			<td colspan="8"></td>
		</tr>


		<tr height="15"><td colspan="10"></td></tr>

		<tr class="tabla-datos" height="40">
			<td width="18" class="encabezado-tabla">NIVEL</td>
			<td width="58" class="encabezado-tabla">INDICADOR</td>
			<td width="20" class="encabezado-tabla">META PROGRAMADA</td>
			<td width="17" class="encabezado-tabla">META MODIFICADA</td>
			<td width="17" class="encabezado-tabla">AVANCES DEL MES</td>
			<td width="20" class="encabezado-tabla">AVANCE ACUMULADO</td>
			<td width="16" class="encabezado-tabla">% DE AVANCE ACUMULADO</td>
			<td width="16" class="encabezado-tabla">% DE AVANCE MODIFICADO</td>
			<td width="45" class="encabezado-tabla">ANALISIS DE RESULTADOS 	ACUMULADO</td>
			<td width="36" class="encabezado-tabla">JUSTIFICACIÓN ACUMULADA</td>
		</tr>

		@foreach($componentes as $index => $componente)
		<tr height="38" class="tabla-datos">
			<td>Componente {{$index+1}}</td>
			<td>{{$componente['indicador']}}</td>
			<td>{{$avances_mes['componentes'][$componente['id']]['meta_programada']}}</td>
			<td></td>
			<td>{{$avances_mes['componentes'][$componente['id']]['avance_mes']}}</td>
			<td>{{$avances_mes['componentes'][$componente['id']]['avance_acumulado']}}</td>
			<td>
			@if($avances_mes['componentes'][$componente['id']]['meta_programada'] > 0)
				{{
				(
					(
						$avances_mes['componentes'][$componente['id']]['avance_acumulado']/
						$avances_mes['componentes'][$componente['id']]['meta_programada']
					)
					*100
				)
				}}
			@else
				100
			@endif
			 %</td>
			<td></td>
			<td>{{$avances_mes['componentes'][$componente['id']]['analisis_resultados']}}</td>
			<td>{{$avances_mes['componentes'][$componente['id']]['justificacion_acumulada']}}</td>
		</tr>

		@foreach($componente['actividades'] as $indice => $actividad)
		<tr height="38" class="tabla-datos">
			<td>Actividad {{$index+1}}.{{$indice+1}}</td>
			<td>{{$actividad['indicador']}}</td>
			<td>{{$avances_mes['actividades'][$actividad['id']]['meta_programada']}}</td>
			<td></td>
			<td>{{$avances_mes['actividades'][$actividad['id']]['avance_mes']}}</td>
			<td>{{$avances_mes['actividades'][$actividad['id']]['avance_acumulado']}}</td>
			<td>
			@if($avances_mes['actividades'][$actividad['id']]['meta_programada'] > 0)
				{{
				(
					(
						$avances_mes['actividades'][$actividad['id']]['avance_acumulado']/
						$avances_mes['actividades'][$actividad['id']]['meta_programada']
					)
					*100
				)
				}}
			@else
				100
			@endif
			 %</td>
			<td></td>
			<td>{{$avances_mes['actividades'][$actividad['id']]['analisis_resultados']}}</td>
			<td>{{$avances_mes['actividades'][$actividad['id']]['justificacion_acumulada']}}</td>
		</tr>
		@endforeach
		@endforeach
		<tr>
			<td class="nota-titulo">Fuente de información:</td>
			<td class="nota-contenido" colspan="3"></td>
		</tr>
		<tr><td colspan="10"></td></tr>



		<tr class="negrita" height="20">
			<td></td>
			<td colspan="2" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td colspan="2"></td>
			<td colspan="4" align="center">LIDER DEL PROYECTO</td>
			<td></td>
		</tr>
		<tr height="40">
			<td></td>
			<td colspan="2" class="linea-firma"></td>
			<td colspan="2"></td>
			<td colspan="4" class="linea-firma"></td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td colspan="2" align="center">Nombre</td>
			<td colspan="2"></td>
			<td colspan="4" align="center">{{ $proyecto['liderProyecto'] }}</td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td colspan="2" align="center">Cargo</td>
			<td colspan="2"></td>
			<td colspan="4" align="center">Cargo</td>
			<td></td>
		</tr>

		<tr><td colspan="10"></td></tr>
		<tr class="tabla-datos" height="40">
			<td class="encabezado-tabla">NIVEL</td>
			<td class="encabezado-tabla">INDICADOR</td>
			<td class="encabezado-tabla">META PROGRAMADA</td>
			<td class="encabezado-tabla">META MODIFICADA</td>
			<td class="encabezado-tabla">AVANCES DEL MES</td>
			<td class="encabezado-tabla">AVANCE ACUMULADO</td>
			<td class="encabezado-tabla">% DE AVANCE ACUMULADO</td>
			<td class="encabezado-tabla">% DE AVANCE MODIFICADO</td>
		</tr>

		@foreach($componentes as $index => $componente)
		<tr height="20" class="tabla-datos">
			<td class="subtitulo-tabla">Componente {{$index+1}}</td>
			<td class="subtitulo-tabla">{{$componente['indicador']}}</td>
			<td class="subtitulo-tabla">{{$avances_mes['componentes'][$componente['id']]['meta_programada']}}</td>
			<td class="subtitulo-tabla"></td>
			<td class="subtitulo-tabla">{{$avances_mes['componentes'][$componente['id']]['avance_mes']}}</td>
			<td class="subtitulo-tabla">{{$avances_mes['componentes'][$componente['id']]['avance_acumulado']}}</td>
			<td class="subtitulo-tabla">
			@if($avances_mes['componentes'][$componente['id']]['meta_programada'] > 0)
				{{
				(
					(
						$avances_mes['componentes'][$componente['id']]['avance_acumulado']/
						$avances_mes['componentes'][$componente['id']]['meta_programada']
					)
					*100
				)
				}}
			@else
				100
			@endif
			 %</td>
			<td class="subtitulo-tabla"></td>
		</tr>

			@foreach($jurisdicciones as $clave => $jurisdiccion)
			<tr height="20" class="tabla-datos">
				<td></td>
				<td>{{$clave}} {{$jurisdiccion}}</td>
				@if(isset($jurisdicciones_mes['componentes'][$componente['id']][$clave]))
					<td>{{$jurisdicciones_mes['componentes'][$componente['id']][$clave]['meta_programada']}}</td>
					<td></td>
					<td>{{$jurisdicciones_mes['componentes'][$componente['id']][$clave]['avance_mes']}}</td>
					<td>{{$jurisdicciones_mes['componentes'][$componente['id']][$clave]['avance_acumulado']}}</td>
					<td>
					@if($jurisdicciones_mes['componentes'][$componente['id']][$clave]['meta_programada'] > 0)
						{{
						(
							(
								$jurisdicciones_mes['componentes'][$componente['id']][$clave]['avance_acumulado']/
								$jurisdicciones_mes['componentes'][$componente['id']][$clave]['meta_programada']
							)
							*100
						)
						}}
					@else
						100
					@endif
					 %</td>
				@else
					<td>0</td>
					<td></td>
					<td>0</td>
					<td>0</td>
					<td></td>
				@endif
				<td></td>
			</tr>
			@endforeach

			@foreach($componente['actividades'] as $indice => $actividad)
			<tr height="20" class="tabla-datos">
				<td class="subtitulo-tabla">Actividad {{$index+1}}.{{$indice+1}}</td>
				<td class="subtitulo-tabla">{{$actividad['indicador']}}</td>
				<td class="subtitulo-tabla">{{$avances_mes['actividades'][$actividad['id']]['meta_programada']}}</td>
				<td class="subtitulo-tabla"></td>
				<td class="subtitulo-tabla">{{$avances_mes['actividades'][$actividad['id']]['avance_mes']}}</td>
				<td class="subtitulo-tabla">{{$avances_mes['actividades'][$actividad['id']]['avance_acumulado']}}</td>
				<td class="subtitulo-tabla">
				@if($avances_mes['actividades'][$actividad['id']]['meta_programada'] > 0)
					{{
					(
						(
							$avances_mes['actividades'][$actividad['id']]['avance_acumulado']/
							$avances_mes['actividades'][$actividad['id']]['meta_programada']
						)
						*100
					)
					}}
				@else
					100
				@endif
				 %</td>
				<td class="subtitulo-tabla"></td>
			</tr>

				@foreach($jurisdicciones as $clave => $jurisdiccion)
				<tr height="20" class="tabla-datos">
					<td></td>
					<td>{{$clave}} {{$jurisdiccion}}</td>
					@if(isset($jurisdicciones_mes['actividades'][$actividad['id']][$clave]))
						<td>{{$jurisdicciones_mes['actividades'][$actividad['id']][$clave]['meta_programada']}}</td>
						<td></td>
						<td>{{$jurisdicciones_mes['actividades'][$actividad['id']][$clave]['avance_mes']}}</td>
						<td>{{$jurisdicciones_mes['actividades'][$actividad['id']][$clave]['avance_acumulado']}}</td>
						<td>
						@if($jurisdicciones_mes['actividades'][$actividad['id']][$clave]['meta_programada'] > 0)
							{{
							(
								(
									$jurisdicciones_mes['actividades'][$actividad['id']][$clave]['avance_acumulado']/
									$jurisdicciones_mes['actividades'][$actividad['id']][$clave]['meta_programada']
								)
								*100
							)
							}}
						@else
							100
						@endif
						 %</td>
					@else
						<td>0</td>
						<td></td>
						<td>0</td>
						<td>0</td>
						<td></td>
					@endif
					<td></td>
				</tr>
				@endforeach
			@endforeach
		@endforeach
	</table>
</body>
</html>