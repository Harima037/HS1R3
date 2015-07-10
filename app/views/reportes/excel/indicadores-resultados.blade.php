<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>

		<tr><td width="8.4"></td><td width="8.4"></td><td width="52.5"></td><td width="12.7"></td><td width="12.7"></td><td width="12.7"></td><td width="12.7"></td><td width="1.8"></td><td width="9.7"></td><td width="17.5"></td><td width="17.5"></td><td width="17.5"></td><td width="12.5"></td><td width="12.5"></td><td width="12.5"></td></tr>

		<tr>
			<td><b>GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr>
			<td><b>INDICADORES DE RESULTADOS AL {{$trimestre}} TRIMESTRE DEL {{$ejercicio}}</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<tr>
			<td></td><td></td>
			<td><b>Organismo Público:</b> Instituto de Salud</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<tr>
			<td valign="middle" height="23">NUM. CONSEC. / CLAVE PROYECTO</td>
			<td valign="middle">OBJETIVO DEL MILENIO</td>
			<td valign="middle">SUBFUNCIÓN / TIPO DE PROYECTO / FUENTE DE FINANCIAMIENTO</td>
			<td valign="middle">UNIDAD DE MEDIDA</td>
			<td valign="middle">METAS</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td valign="middle">PRESUPUESTO<br>APROBADO<br>( PESOS )</td>
			<td valign="middle">PRESUPUESTO<br>MODIFICADO<br> ( PESOS )</td>
			<td valign="middle">PRESUPUESTO<br>DEVENGADO<br>( PESOS )</td>
			<td valign="middle">BENEFICIARIOS</td>
			<td></td>
			<td></td>
		</tr>

		<tr>
			<td height="38"></td>
			<td></td>
			<td valign="middle">PROYECTOS /  METAS (CONCEPTOS)</td>
			<td></td>
			<td valign="middle">PROGRAM.ANUAL</td>
			<td valign="middle">MODIF. ANUAL</td>
			<td valign="middle">ALCANZ. AL PERIODO</td>
			<td valign="middle">% CUMPLIM./MODIF.</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td valign="middle">MUNICIPIO</td>
			<td valign="middle">LOCALIDAD</td>
			<td valign="middle">PERSONA</td>
		</tr>
		
		<tr class="encabezado-tabla">
			<td valign="middle" height="23">SALUD</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<td></td><td></td>
			<td valign="top" height="30">{{$hoja['titulo']}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td>
			<td valign="top">{{$hoja['total_presup_aprobado']}}</td>
			<td valign="top">{{$hoja['total_presup_modificado']}}</td>
			<td valign="top">{{$hoja['total_presup_devengado']}}</td>
			<td></td><td></td><td></td>
		</tr>

		<tr>
		<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		@foreach($hoja['clase'] as $idClasificacion => $clasificacion)

		<tr>
			<td></td><td></td>
			<td height="20" align="center" valign="top" style="font-size:10; font-weight:bold;">
			@if($idClasificacion == '1')
				PROYECTOS INSTITUCIONALES:
			@else
				PROYECTOS DE INVERSIÓN:
			@endif
			</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		@foreach($clasificacion['fuentes'] as $fuente)

		<tr>
			<td></td><td></td>
			<td align="center" valign="top" style="text-decoration:underline; font-size:9;">{{$fuente['titulo']}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		@foreach($fuente['proyectos'] as $proyecto)
		<tr>
			<td align="center" valign="top" style="font-weight:bold;">
				{{$proyecto->proyectoEstrategico}}{{str_pad($proyecto->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT)}}
			</td>
			<td></td>
			<td align="center" style="font-weight:bold;">{{{ $proyecto->nombreTecnico }}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		@foreach($proyecto->componentes as $componente)
		<tr>
			<td></td><td></td>
			<td valign="top">{{{ $componente->indicador }}}</td>
			<td valign="top">{{{ $componente->unidadMedida }}}</td>
			<td valign="top">{{{ $componente->metaAnual }}}</td>
			<td valign="top">{{{ $componente->metaAnual }}}</td>
			<td valign="top">{{{ $componente->avanceMes }}}</td>
			<td valign="top">
			@if($componente->planMejora)
				{{{ $componente->identificador }}}
				{{$hoja['justificaciones'][$componente->identificador]=$componente->justificacionAcumulada}}
			@endif
			</td>
			<td valign="top">100.00</td>
			<td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		@endforeach

		@foreach($proyecto->actividades as $actividad)
		<tr>
			<td></td><td></td>
			<td valign="top">{{{ $actividad->indicador }}}</td>
			<td valign="top">{{{ $actividad->unidadMedida }}}</td>
			<td valign="top">{{{ $actividad->metaAnual }}}</td>
			<td valign="top">{{{ $actividad->metaAnual }}}</td>
			<td valign="top">{{{ $actividad->avanceMes }}}</td>
			<td valign="top">
			@if($actividad->planMejora)
				{{{ $actividad->identificador }}}
				<!-- {{  $hoja['justificaciones'][$actividad->identificador]=$actividad->justificacionAcumulada }} -->
			@endif
			</td>
			<td valign="top">100.00</td>
			<td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		@endforeach

		@endforeach

		@endforeach

		@endforeach

		@foreach($hoja['justificaciones'] as $identificador => $justificacion)
		<tr>
			<td></td><td></td>
			<td>{{$identificador}} {{{$justificacion}}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		@endforeach
	</table>
</body>
</html>