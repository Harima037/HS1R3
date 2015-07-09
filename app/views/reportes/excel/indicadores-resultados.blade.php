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
			<td valign="top" height="30">PRESTACIÓN DE SERVICIOS DE SALUD A LA COMUNIDAD</td>
			<td></td><td></td><td></td><td></td><td></td><td></td>
			<td valign="top">{{$total_presup_aprobado}}</td><td valign="top">{{$total_presup_modificado}}</td><td valign="top">{{$total_presup_devengado}}</td>
			<td></td><td></td><td></td>
		</tr>

		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr>
			<td>
				<!-- Numero de Proyecto Estrategio -->
			</td>
			<td>
				<!-- ODM vacio? -->
			</td>

			<td align="center"><b>PROYECTOS INSTITUCIONALES:</b></td>
			
			<td>
				<!-- Undidad Medida -->
			</td>
			<td>
				<!-- Programado Anual -->
			</td>
			<td>
				<!-- Programado/Modificado Anual -->
			</td>
			<td>
				<!-- Avance acumulado al periodo -->
			</td>

			<td>
				<!-- a/ Señalar para justificaciones -->
			</td>
			<td>
				<!-- Porcentaje avance -->
			</td>

			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>
				<!-- Numero de Proyecto Estrategio -->
			</td>
			<td>
				<!-- ODM vacio? -->
			</td>

			<td align="center">Fuentes de financiamiento</td>
			
			<td>
				<!-- Undidad Medida -->
			</td>
			<td>
				<!-- Programado Anual -->
			</td>
			<td>
				<!-- Programado/Modificado Anual -->
			</td>
			<td>
				<!-- Avance acumulado al periodo -->
			</td>

			<td>
				<!-- a/ Señalar para justificaciones -->
			</td>
			<td>
				<!-- Porcentaje avance -->
			</td>

			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		@foreach($proyectos as $proyecto)
		<tr>
			<td>
				{{$proyecto->proyectoEstrategico}}{{str_pad($proyecto->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT)}}
			</td>
			<td>
				<!-- ODM vacio? -->
			</td>

			<td align="center"><b>{{{ $proyecto->nombreTecnico }}}</b></td>
			
			<td>
				<!-- Undidad Medida -->
			</td>
			<td>
				<!-- Programado Anual -->
			</td>
			<td>
				<!-- Programado/Modificado Anual -->
			</td>
			<td>
				<!-- Avance acumulado al periodo -->
			</td>

			<td>
				<!-- a/ Señalar para justificaciones -->
			</td>
			<td>
				<!-- Porcentaje avance -->
			</td>

			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		@foreach($proyecto->componentes as $componente)
		<tr>
			<td></td>
			<td></td>
			<td>{{{ $componente->indicador }}}</td>
			<td>{{{ $componente->unidadMedida }}}</td>
			<td>{{{ $componente->metaAnual }}}</td>
			<td>{{{ $componente->metaAnual }}}</td>
			<td>{{{ $componente->avanceMes }}}</td>
			<td>{{{ $componente->planMejora }}}</td>
			<td>100.00</td>

			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		@endforeach
		@foreach($proyecto->actividades as $actividad)
		<tr>
			<td></td>
			<td></td>
			<td>{{{ $actividad->indicador }}}</td>
			<td>{{{ $actividad->unidadMedida }}}</td>
			<td>{{{ $actividad->metaAnual }}}</td>
			<td>{{{ $actividad->metaAnual }}}</td>
			<td>{{{ $actividad->avanceMes }}}</td>
			<td>{{{ $actividad->planMejora }}}</td>
			<td>100.00</td>

			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		@endforeach
		@endforeach
		
	</table>
</body>
</html>