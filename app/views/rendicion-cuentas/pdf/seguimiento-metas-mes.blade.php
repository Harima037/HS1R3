<table>
	<tr>
		<td rowspan="5" class="imagen izquierda">
			<img src="{{ URL::to('img/LogoFederal.png') }}" width="150">
		</td>
		<td height="20" class="titulo1" align="center">INSTITUTO DE SALUD</td>
		<td rowspan="5" class="imagen derecha">
			<img src="{{ URL::to('img/LogoInstitucional.png') }}" width="150">
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
		<td height="18" class="titulo3" align="center">SEGUIMIENTO DE METAS {{$proyecto['ejercicio']}}</td>
	</tr>
	<tr>
		<td height="18" colspan="3" align="right" class="negrita">Formato RC-3</td>
	</tr>
</table>

<table>

	<tr height="90" class="texto-medio texto">
		<td class="texto-derecha">Nombre del proyecto: </td>
		<td class="negrita">{{ $proyecto['nombreTecnico'] }}</td>
		<td class="texto-centro">Clave presupuestaria: </td>
		<td class="negrita">{{ $proyecto['ClavePresupuestaria'] }}</td>
		<td class="texto-derecha">Al mes de: </td>
		<td class="negrita">{{$mes['mes']}}</td>
		<td></td>
		<td></td>
	</tr>

	<tr height="15"><td colspan="8">&nbsp;</td></tr>


	<tr height="20" class="texto">
		<td class="texto-derecha">Información: </td>
		<td class="negrita">Estatal</td>
		<td colspan="6"></td>
	</tr>

	<tr height="15"><td colspan="8">&nbsp;</td></tr>

</table>

<table>
	<tr class="tabla-datos" height="50">
		<td width="15" class="encabezado-tabla">NIVEL</td>
		<td width="40" class="encabezado-tabla">INDICADOR</td>
		<td width="15" class="encabezado-tabla">META PROGRAMADA</td>
		<td width="15" class="encabezado-tabla">META MODIFICADA</td>
		<td width="15" class="encabezado-tabla">AVANCES DEL MES</td>
		<td width="15" class="encabezado-tabla">AVANCE ACUMULADO</td>
		<td width="13" class="encabezado-tabla">% DE AVANCE ACUMULADO</td>
		<td width="13" class="encabezado-tabla">% DE AVANCE MODIFICADO</td>
		<td width="35" class="encabezado-tabla">ANALISIS DE RESULTADOS 	ACUMULADO</td>
		<td width="35" class="encabezado-tabla">JUSTIFICACIÓN ACUMULADA</td>
	</tr>

	@foreach($componentes as $index => $componente)
	<tr height="50" class="tabla-datos">
		<td class="texto-medio">Componente {{$index+1}}</td>
		<td class="texto-medio">{{$componente['indicador']}}</td>
		@if(isset($avances_mes['componentes'][$componente['id']]))
		<td class="texto-medio texto-centro">{{number_format($avances_mes['componentes'][$componente['id']]['meta_programada'],2)}}</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio texto-centro">{{number_format($avances_mes['componentes'][$componente['id']]['avance_mes'],2)}}</td>
		<td class="texto-medio texto-centro">{{number_format($avances_mes['componentes'][$componente['id']]['avance_acumulado'],2)}}</td>
		<td class="texto-medio texto-centro">
		@if($avances_mes['componentes'][$componente['id']]['meta_programada'] > 0)
			{{
			floatval(
			number_format(
				(
					$avances_mes['componentes'][$componente['id']]['avance_acumulado']/
					$avances_mes['componentes'][$componente['id']]['meta_programada']
				)
				*100
			,2)
			)
			}}
		@else
			100
		@endif
		 %</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio">{{$avances_mes['componentes'][$componente['id']]['analisis_resultados']}}</td>
		<td class="texto-medio">{{$avances_mes['componentes'][$componente['id']]['justificacion_acumulada']}}</td>
		@else
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0%</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio"></td>
		<td class="texto-medio"></td>
		@endif
	</tr>

	@foreach($componente['actividades'] as $indice => $actividad)
	<tr height="50" class="tabla-datos">
		<td class="texto-medio">Actividad {{$index+1}}.{{$indice+1}}</td>
		<td class="texto-medio">{{$actividad['indicador']}}</td>
		@if(isset($avances_mes['actividades'][$actividad['id']]))
		<td class="texto-medio texto-centro">{{number_format($avances_mes['actividades'][$actividad['id']]['meta_programada'],2)}}</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio texto-centro">{{number_format($avances_mes['actividades'][$actividad['id']]['avance_mes'],2)}}</td>
		<td class="texto-medio texto-centro">{{number_format($avances_mes['actividades'][$actividad['id']]['avance_acumulado'],2)}}</td>
		<td class="texto-medio texto-centro">
		@if($avances_mes['actividades'][$actividad['id']]['meta_programada'] > 0)
			{{
			floatval(
			number_format(
				(
					$avances_mes['actividades'][$actividad['id']]['avance_acumulado']/
					$avances_mes['actividades'][$actividad['id']]['meta_programada']
				)
				*100
			,2)
			)
			}}
		@else
			100
		@endif
		 %</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio">{{$avances_mes['actividades'][$actividad['id']]['analisis_resultados']}}</td>
		<td class="texto-medio">{{$avances_mes['actividades'][$actividad['id']]['justificacion_acumulada']}}</td>
		@else
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0</td>
		<td class="texto-medio texto-centro">0%</td>
		<td class="texto-medio texto-centro"></td>
		<td class="texto-medio"></td>
		<td class="texto-medio"></td>
		@endif
	</tr>
	@endforeach
	@endforeach
</table>
<table>
	<tr>
		<td class="nota-contenido"><b>Fuente de información:</b> {{$proyecto['fuenteInformacion']}}</td>
	</tr>
	<tr height="10"><td>&nbsp;</td></tr>
</table>
<table>
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

<div style="page-break-after:always;"></div>

<table>
	
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
		@if(isset($avances_mes['componentes'][$componente['id']]))
		<td class="subtitulo-tabla">{{number_format($avances_mes['componentes'][$componente['id']]['meta_programada'],2)}}</td>
		<td class="subtitulo-tabla"></td>
		<td class="subtitulo-tabla">{{number_format($avances_mes['componentes'][$componente['id']]['avance_mes'],2)}}</td>
		<td class="subtitulo-tabla">{{number_format($avances_mes['componentes'][$componente['id']]['avance_acumulado'],2)}}</td>
		<td class="subtitulo-tabla">
		@if($avances_mes['componentes'][$componente['id']]['meta_programada'] > 0)
			{{
			floatval(
			number_format(
				(
					$avances_mes['componentes'][$componente['id']]['avance_acumulado']/
					$avances_mes['componentes'][$componente['id']]['meta_programada']
				)
				*100
			,2)
			)
			}}
		@else
			100
		@endif
		%</td>
		@else
		<td class="subtitulo-tabla">0</td>
		<td class="subtitulo-tabla"></td>
		<td class="subtitulo-tabla">0</td>
		<td class="subtitulo-tabla">0</td>
		<td class="subtitulo-tabla">0%</td>
		@endif
		<td class="subtitulo-tabla"></td>
	</tr>

		@foreach($jurisdicciones as $clave => $jurisdiccion)
		<tr height="20" class="tabla-datos">
			<td></td>
			<td>{{$clave}} {{$jurisdiccion}}</td>
			@if(isset($jurisdicciones_mes['componentes'][$componente['id']][$clave]))
				<td>{{number_format($jurisdicciones_mes['componentes'][$componente['id']][$clave]['meta_programada'],2)}}</td>
				<td></td>
				<td>{{number_format($jurisdicciones_mes['componentes'][$componente['id']][$clave]['avance_mes'],2)}}</td>
				<td>{{number_format($jurisdicciones_mes['componentes'][$componente['id']][$clave]['avance_acumulado'],2)}}</td>
				<td>
				@if($jurisdicciones_mes['componentes'][$componente['id']][$clave]['meta_programada'] > 0)
					{{
					floatval(
					number_format(
						(
							$jurisdicciones_mes['componentes'][$componente['id']][$clave]['avance_acumulado']/
							$jurisdicciones_mes['componentes'][$componente['id']][$clave]['meta_programada']
						)
						*100
					,2)
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
				<td>0%</td>
			@endif
			<td></td>
		</tr>
			@if(isset($localidades_mes['componentes'][$componente['id']][$clave]))
				@foreach ($localidades_mes['componentes'][$componente['id']][$clave] as $desglose)
					<tr height="20" class="tabla-datos">
						<td></td>
						<td>{{$desglose['municipio']}} - {{$desglose['localidad']}}</td>
						<td>{{number_format($desglose['meta_programada'],2)}}</td>
						<td></td>
						<td>{{number_format($desglose['avance_mes'],2)}}</td>
						<td>{{number_format($desglose['avance_acumulado'],2)}}</td>
						<td>
							@if($desglose['meta_programada'] > 0)
							{{
							floatval(
							number_format(
								(
									$desglose['avance_acumulado']/
									$desglose['meta_programada']
								)
								*100
							,2)
							)
							}}
						@else
							100
						@endif
						 %</td>
						<td></td>
					</tr>
				@endforeach
			@endif
		@endforeach

		@foreach($componente['actividades'] as $indice => $actividad)
		<tr height="20" class="tabla-datos">
			<td class="subtitulo-tabla">Actividad {{$index+1}}.{{$indice+1}}</td>
			<td class="subtitulo-tabla">{{$actividad['indicador']}}</td>
			@if(isset($avances_mes['actividades'][$actividad['id']]))
			<td class="subtitulo-tabla">{{number_format($avances_mes['actividades'][$actividad['id']]['meta_programada'],2)}}</td>
			<td class="subtitulo-tabla"></td>
			<td class="subtitulo-tabla">{{number_format($avances_mes['actividades'][$actividad['id']]['avance_mes'],2)}}</td>
			<td class="subtitulo-tabla">{{number_format($avances_mes['actividades'][$actividad['id']]['avance_acumulado'],2)}}</td>
			<td class="subtitulo-tabla">
			@if($avances_mes['actividades'][$actividad['id']]['meta_programada'] > 0)
				{{
				floatval(
				number_format(
					(
						$avances_mes['actividades'][$actividad['id']]['avance_acumulado']/
						$avances_mes['actividades'][$actividad['id']]['meta_programada']
					)
					*100
				,2)
				)
				}}
			@else
				100
			@endif
			 %</td>
			@else
			<td class="subtitulo-tabla">0</td>
			<td class="subtitulo-tabla"></td>
			<td class="subtitulo-tabla">0</td>
			<td class="subtitulo-tabla">0</td>
			<td class="subtitulo-tabla">0%</td>
			@endif
			<td class="subtitulo-tabla"></td>
		</tr>

			@foreach($jurisdicciones as $clave => $jurisdiccion)
			<tr height="20" class="tabla-datos">
				<td></td>
				<td>{{$clave}} {{$jurisdiccion}}</td>
				@if(isset($jurisdicciones_mes['actividades'][$actividad['id']][$clave]))
					<td>{{number_format($jurisdicciones_mes['actividades'][$actividad['id']][$clave]['meta_programada'],2)}}</td>
					<td></td>
					<td>{{number_format($jurisdicciones_mes['actividades'][$actividad['id']][$clave]['avance_mes'],2)}}</td>
					<td>{{number_format($jurisdicciones_mes['actividades'][$actividad['id']][$clave]['avance_acumulado'],2)}}</td>
					<td>
					@if($jurisdicciones_mes['actividades'][$actividad['id']][$clave]['meta_programada'] > 0)
						{{
						floatval(
						number_format(
							(
								(
									$jurisdicciones_mes['actividades'][$actividad['id']][$clave]['avance_acumulado']/
									$jurisdicciones_mes['actividades'][$actividad['id']][$clave]['meta_programada']
								)
								*100
							)
						,2)
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
					<td>0%</td>
				@endif
				<td></td>
			</tr>
			@endforeach
		@endforeach
	@endforeach
</table>