<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>

		<tr><td width="8.4"></td><td width="8.4"></td><td width="52.5"></td><td width="12.7"></td><td width="12.7"></td><td width="12.7"></td><td width="12.7"></td><td width="1.8"></td><td width="9.7"></td><td width="20.5"></td><td width="20.5"></td><td width="20.5"></td><td width="13.8"></td><td width="13.8"></td><td width="12.5"></td></tr>

		<tr>
			<td style="font-size:10; font-weight:bold;">GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr>
			<td style="font-size:10; font-weight:bold;">INDICADORES DE RESULTADOS AL {{$trimestre}} TRIMESTRE DEL {{$ejercicio}}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<tr>
			<td></td><td></td>
			<td><span style="font-weight:bold;">Organismo Público:</span> Instituto de Salud</td>
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
		<!-- {{ $current_row = 13; }} -->
		
		@foreach($hoja['clase'] as $idClasificacion => $clasificacion)
		<tr>
		<!-- {{ $current_row++; }} -->
			<td></td><td></td>
			<td height="20" align="center" valign="top" style="font-size:12; font-weight:bold;">
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
		<!-- {{ $current_row++; }} -->
			<td></td><td></td>
			<td align="center" valign="top" style="text-decoration:underline; font-size:11;">{{$fuente['titulo']}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		
		@foreach($fuente['proyectos'] as $proyecto)
		<tr>
		<!-- {{ $current_row++; }} -->
			<td align="center" valign="top" style="font-weight:bold;">
				{{$proyecto->proyectoEstrategico}}{{str_pad($proyecto->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT)}}
				<!-- {{ $sum_rows[] = $current_row; }} -->
			</td>
			<td></td>
			<td align="center" style="font-weight:bold;text-align:justify;">{{{ rtrim($proyecto->nombreTecnico,'.') }}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td>
			<td valign="top" {{$estilo_fuente = (count($proyecto->fuentesFinanciamiento) > 1)?'style="font-weight:bold; text-decoration:underline;"':''}} >
			{{ (count($proyecto->fuentesFinanciamiento) > 1)?'=SUM(J'.($current_row+1).':J'.($current_row+count($proyecto->fuentesFinanciamiento)).')':$proyecto->totalPresupuestoAprobado}}
			</td>
			<td valign="top" {{$estilo_fuente}}>
			{{ (count($proyecto->fuentesFinanciamiento) > 1)?'=SUM(K'.($current_row+1).':K'.($current_row+count($proyecto->fuentesFinanciamiento)).')':$proyecto->totalPresupuestoModificado}}
			</td>
			<td valign="top" {{$estilo_fuente}}>
			{{ (count($proyecto->fuentesFinanciamiento) > 1)?'=SUM(L'.($current_row+1).':L'.($current_row+count($proyecto->fuentesFinanciamiento)).')':$proyecto->totalPresupuestoDevengado}}
			</td>
			<td valign="top">
				@if($proyecto->idCobertura == 1)
				Cobertura Estatal
				@elseif($proyecto->idCobertura == 2)
				{{$proyecto->municipio}}
				@else
				Cobertura Regional
				@endif
			</td>
			<td valign="top">
				@if($proyecto->idCobertura == 1)
				Cobertura Estatal
				@elseif($proyecto->idCobertura == 2)
				{{$proyecto->municipio}}
				@else
				Cobertura Regional
				@endif
			</td>
			@if(count($proyecto->beneficiariosDescripcion) > 1)
				@if($proyecto->evaluacionMes)
					@if($proyecto->evaluacionMes->indicadorResultadoBeneficiarios)
						<td valign="top">{{$proyecto->evaluacionMes->indicadorResultadoBeneficiarios or 0}}</td>
					@else
						@foreach($proyecto->beneficiariosDescripcion AS $beneficiario)
						<td valign="top">{{$beneficiario->tipoBeneficiario}}</td><td>{{$beneficiario->avanceBeneficiario}}</td>
						@endforeach
					@endif
				@else
					@foreach($proyecto->beneficiariosDescripcion AS $beneficiario)
					<td valign="top">{{$beneficiario->tipoBeneficiario}}</td><td>{{$beneficiario->avanceBeneficiario}}</td>
					@endforeach
				@endif
			@else
				<td valign="top">{{$proyecto->beneficiariosDescripcion[0]->avanceBeneficiario or 0}}</td>
			@endif
		</tr>

		@for($i = 0 ; $i < $proyecto->totalItems ; $i++)
			<tr>
			<!-- {{ $current_row++; }} -->
				<td></td><td></td>
				
			@if(isset($proyecto->componentes[$i]))
				<td valign="top" style="text-align:justify;">{{{ rtrim($proyecto->componentes[$i]->indicador,'.') }}}</td>
				<td valign="top" align="center">{{{ $proyecto->componentes[$i]->unidadMedida }}}</td>
				<td valign="top">{{{ $proyecto->componentes[$i]->metaAnual }}}</td>
				<td valign="top">{{{ $proyecto->componentes[$i]->metaAnual }}}</td>
				<td valign="top">{{{ $proyecto->componentes[$i]->avanceAcumulado or 0.00 }}}</td>
				<td valign="top">
				@if($proyecto->componentes[$i]->planMejora)
					{{{ $proyecto->componentes[$i]->identificador }}}
					<!-- {{ $hoja['justificaciones'][$proyecto->componentes[$i]->identificador]=$proyecto->componentes[$i]->justificacionAcumulada }} -->
				@endif
				</td>
				<td valign="top">
				{{'=SUM(G'.$current_row.')/F'.$current_row.'*100'}}
				</td>
			@elseif(isset($proyecto->actividades[$i-$proyecto->desfaseActividades]))
				<td valign="top" style="text-align:justify;">
				{{{ rtrim($proyecto->actividades[$i-$proyecto->desfaseActividades]->indicador,'.') }}}
				</td>
				<td valign="top" align="center">{{{ $proyecto->actividades[$i-$proyecto->desfaseActividades]->unidadMedida }}}</td>
				<td valign="top">{{{ $proyecto->actividades[$i-$proyecto->desfaseActividades]->metaAnual }}}</td>
				<td valign="top">{{{ $proyecto->actividades[$i-$proyecto->desfaseActividades]->metaAnual }}}</td>
				<td valign="top">{{{ $proyecto->actividades[$i-$proyecto->desfaseActividades]->avanceAcumulado or 0.00 }}}</td>
				<td valign="top">
				@if($proyecto->actividades[$i-$proyecto->desfaseActividades]->planMejora)
					{{{ $proyecto->actividades[$i-$proyecto->desfaseActividades]->identificador }}}
					<!-- {{ $hoja['justificaciones'][$proyecto->actividades[$i-$proyecto->desfaseActividades]->identificador]=$proyecto->actividades[$i-$proyecto->desfaseActividades]->justificacionAcumulada }} -->
				@endif
				</td>
				<td valign="top">
					{{'=SUM(G'.$current_row.')/F'.$current_row.'*100'}}
				</td>
			@else
				<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			@endif

			@if(isset($proyecto->fuentesFinanciamiento[$i]) && count($proyecto->fuentesFinanciamiento) > 1)
				<td valign="top">{{$proyecto->fuentesFinanciamiento[$i]->presupuestoAprobado or 0.00}}</td>
				<td valign="top">{{$proyecto->fuentesFinanciamiento[$i]->presupuestoModificado or 0.00}}</td>
				<td valign="top">{{$proyecto->fuentesFinanciamiento[$i]->presupuestoDevengado or 0.00}}</td>
			@else
				<td></td><td></td><td></td>
			@endif

				<td></td><td></td><td></td>
			</tr>
		@endfor

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

		<tr>
			<td>{{implode(',',$sum_rows)}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		
	</table>
</body>
</html>