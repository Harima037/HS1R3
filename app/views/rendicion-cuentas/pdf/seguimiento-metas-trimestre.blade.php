 <div class="header">
	<table>
		<tr>
			<td rowspan="5" class="imagen izquierda"><img src="{{ public_path().'/img/LogoFederal.png' }}" width="125"></td>
			<td class="titulo1" align="center">INSTITUTO DE SALUD</td>
			<td rowspan="5" class="imagen derecha"><img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="125"></td>
		</tr>
		<tr><td class="titulo2" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td></tr>
		<tr><td class="titulo3" align="center">SUBDIRECCIÓN DE PLANEACIÓN EN SALUD</td></tr>
		<tr><td class="titulo3" align="center">DEPARTAMENTO DE EVALUACIÓN</td></tr>
		<tr><td class="titulo3" align="center">SEGUIMIENTO DE METAS DEL {{$mes['trimestre_letras']}} TRIMESTRE DEL {{$proyecto['ejercicio']}}</td></tr>
		<tr><td colspan="3" align="right" class="negrita">Formato RC-8</td></tr>
	</table>
	<table width="100%">
		<tr height="90" class="texto-medio texto">
			<td width="100" class="texto-derecha">Nombre del proyecto: </td>
			<td class="negrita" >{{ $proyecto['nombreTecnico'] }}</td>
			<td width="105" class="texto-centro">Clave presupuestaria: </td>
			<td width="90" class="negrita">{{ $proyecto['ClavePresupuestaria'] }}</td>
			<td width="80" class="texto-derecha" nowrap="nowrap">Al mes de: </td>
			<td width="50" class="negrita">{{$mes['mes']}}</td>
		</tr>
		<tr height="10"><td colspan="6">&nbsp;</td></tr>
	</table>
</diV>

<table width="100%">
	<tr height="20" class="texto">
		<td width="100" class="texto-derecha">Información: </td>
		<td class="negrita">Estatal</td>
		<td colspan="6"></td>
	</tr>
	<tr height="15"><td colspan="8">&nbsp;</td></tr>
</table>

<table width="100%">
	<thead>
	<tr class="tabla-datos" height="50">
		<td class="encabezado-tabla">NIVEL</td>
		<td class="encabezado-tabla">INDICADOR</td>
		<td class="encabezado-tabla">META<br>TRIMESTRAL</td>
		<!--<td class="encabezado-tabla">AVANCES DEL MES</td>-->
		<td class="encabezado-tabla">AVANCE ACUMULADO <font size="6pt">(1)</font></td>
		<td class="encabezado-tabla">% DE AVANCE ACUMULADO<font size="6pt">(2)</font></td>
		<td class="encabezado-tabla">ANALISIS DE RESULTADOS 	ACUMULADO<font size="6pt">(3)</font></td>
		<td class="encabezado-tabla">JUSTIFICACIÓN<font size="6pt">(4)</font></td>
	</tr>
	</thead>
	<tbody>
	@foreach($componentes as $index => $componente)
	<tr height="50" class="tabla-datos">
		<td class="texto-medio" nowrap="nowrap">Componente {{$index+1}}</td>
		<td class="texto-medio">{{{ $componente['indicador'] }}}</td>
		@if(isset($avances_trimestre['componentes'][$componente['id']]))
		<td class="texto-medio texto-centro">{{number_format($avances_trimestre['componentes'][$componente['id']]['meta_programada'],2)}}</td>
		<!--<td class="texto-medio texto-centro">{{number_format($avances_trimestre['componentes'][$componente['id']]['avance_mes'],2)}}</td>-->
		<td class="texto-medio texto-centro">{{number_format($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'],2)}}</td>
		<td class="texto-medio texto-centro">
		@if($avances_trimestre['componentes'][$componente['id']]['meta_programada'] > 0)
			{{
			number_format(
				(
					$avances_trimestre['componentes'][$componente['id']]['avance_acumulado']/
					$avances_trimestre['componentes'][$componente['id']]['meta_programada']
				)
				*100
			,2)
			}}
		@else
			@if($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'] > 0 )
				@if($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'] > 999)
	                999.00
	            @elseif($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'] > 100)
	                {{number_format($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'],2)}}
	            @elseif($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'] > 10)
	                {{number_format($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'] * 10,2)}}
	            @else
	                {{number_format($avances_trimestre['componentes'][$componente['id']]['avance_acumulado'] * 100,2)}}
	            @endif
	        @else
				0
			@endif
		@endif
		 %</td>
		<td class="texto-medio">{{{ $avances_trimestre['componentes'][$componente['id']]['analisis_resultados'] }}}</td>
		<td class="texto-medio">{{{ $avances_trimestre['componentes'][$componente['id']]['justificacion_acumulada'] }}}</td>
		@else
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0%</td>
		<td class="texto-medio"></td>
		<td class="texto-medio"></td>
		@endif
	</tr>

	@foreach($componente['actividades'] as $indice => $actividad)
	<tr height="50" class="tabla-datos">
		<td class="texto-medio">Actividad {{$index+1}}.{{$indice+1}}</td>
		<td class="texto-medio">{{{ $actividad['indicador'] }}}</td>
		@if(isset($avances_trimestre['actividades'][$actividad['id']]))
		<td class="texto-medio texto-centro">{{number_format($avances_trimestre['actividades'][$actividad['id']]['meta_programada'],2)}}</td>
		<!--<td class="texto-medio texto-centro">{{number_format($avances_trimestre['actividades'][$actividad['id']]['avance_mes'],2)}}</td>-->
		<td class="texto-medio texto-centro">{{number_format($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'],2)}}</td>
		<td class="texto-medio texto-centro">
		@if($avances_trimestre['actividades'][$actividad['id']]['meta_programada'] > 0)
			{{
			number_format(
				(
					$avances_trimestre['actividades'][$actividad['id']]['avance_acumulado']/
					$avances_trimestre['actividades'][$actividad['id']]['meta_programada']
				)
				*100
			,2)
			}}
		@else
			@if($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'] > 0 )
				@if($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'] > 999)
	                999.00
	            @elseif($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'] > 100)
	                {{number_format($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'],2)}}
	            @elseif($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'] > 10)
	                {{number_format($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'] * 10,2)}}
	            @else
	                {{number_format($avances_trimestre['actividades'][$actividad['id']]['avance_acumulado'] * 100,2)}}
	            @endif
	        @else
				0
			@endif
		@endif
		 %</td>
		<td class="texto-medio">{{{ $avances_trimestre['actividades'][$actividad['id']]['analisis_resultados'] }}}</td>
		<td class="texto-medio">{{{ $avances_trimestre['actividades'][$actividad['id']]['justificacion_acumulada'] }}}</td>
		@else
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0%</td>
		<td class="texto-medio"></td>
		<td class="texto-medio"></td>
		@endif
	</tr>
	@endforeach
	@endforeach
	</tbody>
</table>
<table>
	<tr>
		<td class="nota-contenido"><b>Fuente de información:</b> {{$proyecto['fuenteInformacion']}}</td>
	</tr>
	<tr height="10"><td>&nbsp;</td></tr>
</table>
<table style="page-break-inside:avoid;">
	<tr class="negrita" height="20">
		<td width="10%"></td>
		<td align="center">RESPONSABLE DE LA INFORMACIÓN</td>
		<td width="10%"></td>
		<td align="center">LIDER DEL PROYECTO</td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td></td>
		<td height="40" class="linea-firma"></td>
		<td>&nbsp;</td>
		<td class="linea-firma"></td>
		<td></td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td align="center">{{ $proyecto['responsableInformacion'] }}</td>
		<td></td>
		<td align="center">{{ $proyecto['liderProyecto'] }}</td>
		<td></td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td align="center">{{ $proyecto['cargoResponsableInformacion'] }}</td>
		<td></td>
		<td align="center">{{ $proyecto['cargoLiderProyecto'] }}</td>
		<td></td>
	</tr>
</table>
<br><br>	
<table style="page-break-inside:avoid;">
	<tr height="20">
		<td align="left"><font size="7pt">(1).- Se obtiene de la suma del avances del mes reportado mas el avance acumulado del mes anterior.</font></td>
	</tr>
	<tr height="20">
		<td align="left"><font size="7pt">(2).- Es el resultado de la división del avance acumulado entre la meta programada al mes por cien.</font></td>
	</tr>
	<tr height="20">
		<td align="left"><font size="7pt">(3).-  Describe las acciones acumuladas realizadas al mes reportado.</font></td>
	</tr>
	<tr height="20">
		<td align="left"><font size="7pt">(4).- Justificación de bajo o alto avance, en relación al % de avance programado (90%-110%).</font></td>
	</tr>
</table>