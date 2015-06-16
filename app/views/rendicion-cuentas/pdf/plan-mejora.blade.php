<table>
	<tr>
		<td rowspan="5" class="imagen izquierda">
			<img src="{{ URL::to('img/LogoFederal.png') }}" width="125">
		</td>
		<td height="20" class="titulo1" align="center">INSTITUTO DE SALUD</td>
		<td rowspan="5" class="imagen derecha">
			<img src="{{ URL::to('img/LogoInstitucional.png') }}" width="125">
		</td>
	</tr>
	<tr>
		<td height="19" class="titulo2" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
	</tr>
	<tr>
		<td height="18" class="titulo3" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td>
	</tr>
	<tr>
		<td height="18" class="titulo3" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
	</tr>
	<tr>
		<td height="18" class="titulo3" align="center">
			PLAN DE ACCIÓN DE MEJORA {{$mes['trimestre_letras']}} TRIMESTRE {{$proyecto['ejercicio']}}
		</td>
	</tr>
	<tr>
		<td height="18" colspan="3" align="right" class="negrita">Formato RC-7</td>
	</tr>
</table>
<table>
	<tr height="90" class="texto-medio texto">
		<td class="texto-centro">Nombre del proyecto: </td>
		<td class="negrita" colspan="7">{{ $proyecto['nombreTecnico'] }}</td>
		<td class="texto-centro">Clave presupuestaria: </td>
		<td class="negrita" colspan="2">{{ $proyecto['ClavePresupuestaria'] }}</td>
	</tr>

	<tr><td colspan="13" height="20"></td></tr>
</table>

<table class="tabla-datos">
<thead>
	<tr>
		<th width="60" rowspan="2" class="encabezado-tabla" style="font-size:6;">NIVEL</th>
		<th width="79" rowspan="2" class="encabezado-tabla" style="font-size:6;">INDICADOR</th>
		<th width="50" rowspan="2" class="encabezado-tabla" style="font-size:6;">% DE AVANCE ACUMULADO</th>
		<th width="79" rowspan="2" class="encabezado-tabla" style="font-size:6;">ANÁLISIS DE RESULTADOS </th>
		<th width="79" rowspan="2" class="encabezado-tabla" style="font-size:6;">JUSTIFICACIÓN</th>
		<th width="40" colspan="2" class="encabezado-tabla" style="font-size:6;">¿REQUIERE ACCIÓN DE MEJORA?</th>
		<th rowspan="2" class="encabezado-tabla" style="font-size:6;">ACCIÓN <br>DE MEJORA</th>
		<th rowspan="2" class="encabezado-tabla" style="font-size:6;">GRUPO DE <br>TRABAJO</th>
		<th rowspan="2" class="encabezado-tabla" style="font-size:6;">FECHA DE <br>INICIO</th>
		<th rowspan="2" class="encabezado-tabla" style="font-size:6;">FECHA DE <br>TERMINO</th>
		<th rowspan="2" class="encabezado-tabla" style="font-size:6;">FECHA DE <br>NOTIFICACIÓN</th>
		<th rowspan="2" class="encabezado-tabla" style="font-size:6;">DOCUMENTACIÓN <br>COMPROBATORIA</th>
	</tr>
	<tr>
		<th class="encabezado-tabla" style="font-size:6;">SI</th>
		<th class="encabezado-tabla" style="font-size:6;">NO</th>
	</tr>
</thead>
<tbody>
	@foreach($componentes as $index => $componente)
	<tr>
		<td class="texto-medio" nowrap="nowrap">Componente {{$index+1}}</td>
		<td class="texto-medio">{{{ $componente['indicador'] }}}</td>
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
		<td class="texto-medio">{{{ $avances_mes['componentes'][$componente['id']]['analisis_resultados'] }}}</td>
		<td class="texto-medio">{{{ $avances_mes['componentes'][$componente['id']]['justificacion_acumulada'] }}}</td>
		@if(isset($planes_mejora['componentes'][$componente['id']]))
			<td class="texto-medio texto-centro">X</td>
			<td>&nbsp;</td>
			<td class="texto-medio">{{{ $planes_mejora['componentes'][$componente['id']]['accionMejora'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['componentes'][$componente['id']]['grupoTrabajo'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['componentes'][$componente['id']]['fechaInicio'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['componentes'][$componente['id']]['fechaTermino'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['componentes'][$componente['id']]['fechaNotificacion'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['componentes'][$componente['id']]['documentacionComprobatoria'] }}}</td>
		@else
			<td>&nbsp;</td>
			<td class="texto-medio texto-centro">X</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		@endif
	</tr>

	@foreach($componente['actividades'] as $indice => $actividad)
	<tr>
		<td class="texto-medio">Actividad {{$index+1}}.{{$indice+1}}</td>
		<td class="texto-medio">{{{ $actividad['indicador'] }}}</td>
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
		<td class="texto-medio">{{{ $avances_mes['actividades'][$actividad['id']]['analisis_resultados'] }}}</td>
		<td class="texto-medio">{{{ $avances_mes['actividades'][$actividad['id']]['justificacion_acumulada'] }}}</td>
		@if(isset($planes_mejora['actividades'][$actividad['id']]))
			<td class="texto-medio texto-centro">X</td>
			<td>&nbsp;</td>
			<td class="texto-medio">{{{ $planes_mejora['actividades'][$actividad['id']]['accionMejora'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['actividades'][$actividad['id']]['grupoTrabajo'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['actividades'][$actividad['id']]['fechaInicio'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['actividades'][$actividad['id']]['fechaTermino'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['actividades'][$actividad['id']]['fechaNotificacion'] }}}</td>
			<td class="texto-medio">{{{ $planes_mejora['actividades'][$actividad['id']]['documentacionComprobatoria'] }}}</td>
		@else
			<td>&nbsp;</td>
			<td class="texto-medio texto-centro">X</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		@endif
	</tr>
	@endforeach
	@endforeach
</tbody>
</table>

<table style="page-break-inside:avoid;">
	<tr><td colspan="5" height="10">&nbsp;</td></tr>
	<tr class="negrita" height="20">
		<td width="10%"></td>
		<td align="center">RESPONSABLE DE LA INFORMACIÓN</td>
		<td width="10%"></td>
		<td align="center">LIDER DEL PROYECTO</td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td height="20" class="linea-firma">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="linea-firma">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="negrita" height="20">
		<td>&nbsp;</td>
		<td align="center">{{ $proyecto['responsableInformacion'] }}</td>
		<td>&nbsp;</td>
		<td align="center">{{ $proyecto['liderProyecto'] }}</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="negrita" height="20">
		<td>&nbsp;</td>
		<td align="center">{{ $proyecto['cargoResponsableInformacion'] }}</td>
		<td>&nbsp;</td>
		<td align="center">{{ $proyecto['cargoLiderProyecto'] }}</td>
		<td>&nbsp;</td>
	</tr>
</table>