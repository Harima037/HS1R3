<table class="tabla" width="100%">
	<tr>
		<td rowspan="4" class="imagen izquierda">
			<img src="{{ URL::to('img/EscudoGobiernoChiapas.png') }}" width="150">
		</td>
		<td class="titulo" nowrap="nowrap">Secretaría de Planeación, Gestión Pública y Programa de Gobierno</td>
		<td rowspan="4" class="imagen derecha">
			<img src="{{ URL::to('img/Marca.png') }}" width="150">
		</td>
	</tr>
	<tr>
		<td class="titulo">FICHA DE INFORMACIÓN BÁSICA DEL PROYECTO {{$data['ejercicio']}}</td>
	</tr>
	<tr>
		<td class="titulo"></td>
	</tr>
	<tr>
		<td class="texto-centro">Tipo:</td>
	</tr>
</table>
<table class="tabla" width="100%">
	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" class="dato"><strong>Organismo público:</strong> {{$data['fibap']['organismoPublico']}}</td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr>
		<td colspan="5" class="dato"><strong>Sector:</strong> {{$data['fibap']['sector']}}</td>
		<td colspan="6" class="dato"><strong>Subcomité:</strong> {{$data['fibap']['subcomite']}}</td>
		<td colspan="7" class="dato"><strong>Grupo de trabajo:</strong> {{$data['fibap']['grupoTrabajo']}}</td>
	</tr>
	<tr>
		<td colspan="18" class="dato"><strong>Programa presupuestario:</strong> {{ $data['programaPresupuestarioDescripcion'] }}</td>
	</tr>
	<tr><td colspan="18" class="dato"><strong>Proyecto:</strong> {{ $data['nombreTecnico'] }}</td></tr>
	<?php
	$cobertura = '';
	switch ($data['claveCobertura']) {
		case 'M':
			$cobertura = $data['municipioDescripcion'];
			break;
		case 'R':
			$cobertura = $data['regionDescripcion'];
			break;
		default:
			$cobertura = 'Chiapas';
			break;
	}
	?>
	<tr>
		<td colspan="18" class="dato"><strong>Cobertura\municipio:</strong> {{$data['coberturaDescripcion']}} \ {{$cobertura}}</td>
	</tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>ALINEACIÓN AL PED</strong></td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" class="dato"><strong>Eje rector:</strong> {{$data['ejeRectorDescripcion']}}</td></tr>
	<tr><td colspan="18" class="dato"><strong>Política pública:</strong> {{$data['politicaPublicaDescripcion']}}</td></tr>
	<tr><td colspan="18" class="dato"><strong>Objetivo:</strong> {{$data['objetivoPEDDescripcion']}}</td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>ALINEACIÓN A LOS OBJETIVOS DE DESARROLLO DEL MILENIO</strong></td></tr>
	<tr>
		<td colspan="8"><strong>Alineación específica:</strong></td>
		<td colspan="2"></td>
		<td colspan="8"><strong>Alineación general:</strong></td>
	</tr>
	<tr>
		<td colspan="8" class="dato">{{$data['fibap']['alineacionEspecifica']}}</td>
		<td colspan="2"></td>
		<td colspan="8" class="dato">{{$data['fibap']['alineacionGeneral']}}</td>
	</tr>
	<tr><td colspan="18" height="5"></td></tr>
</table>
<div>
	<div style="width:53%; display:inline-block;">
		<table class="tabla" width="100%">
			<tr>
				<th colspan="8" class="encabezado texto-centro">DOCUMENTACIÓN DE SOPORTE</th>
			</tr>
			@for ($i = 0; $i < count($data['fibap']['documentos_soporte'])  ; $i++)
				@if($i == 0)
				<tr>
				@endif
					<td>{{$data['fibap']['documentos_soporte'][$i]['descripcion']}}</td>
					<td class="dato-metas">{{($data['fibap']['documentos_soporte'][$i]['seleccionado'])?'X':''}}</td>
					@if((($i + 1) % 3) != 0)
					<td></td>
					@endif
				@if((($i+1) % 3) === 0)
				</tr>
				@endif
				{{( $i < count($data['fibap']['documentos_soporte']) && (($i+1) % 3) === 0 )?'<tr>':''}}
			@endfor
			<!-- Para complementar las tr que queden sin cerrar -->
			@if(count($data['fibap']['documentos_soporte']) % 3 !== 0)
				@for($i = (count($data['fibap']['documentos_soporte']) % 3) + 1 ; $i <= 3 ; $i++)
					<td></td>
					<td></td>
					@if($i < 3)
					<td></td>
					@endif
				@endfor
				{{'</tr>'}}
			@endif
		</table>
	</div>
	<div style="width:45%; position:absolute; right:0;">
		<table class="tabla" width="100%">
			<tr>
				<th colspan="4" class="encabezado texto-centro">BENEFICIARIOS</th>
			</tr>
			<tr>
				<td class="dato-metas texto-centro" rowspan="2" valign="middle">Tipo</td>
				<td class="dato-metas texto-centro" rowspan="2" valign="middle">Cantidad total</td>
				<td class="dato-metas texto-centro" colspan="2">GENERO</td>
			</tr>
			<tr>
				<td class="dato-metas">Femenino</td>
				<td class="dato-metas">Masculino</td>
			</tr>
			@foreach ($data['beneficiarios'] as $key => $beneficiario)
			<tr>
				<td class="dato" valign="middle">{{ $beneficiario['tipo'] }}</td>
				<td class="dato" valign="middle">{{ number_format($beneficiario['total']) }}</td>
				<td class="dato">{{ (isset($beneficiario['desglose']['f']['total']))? number_format($beneficiario['desglose']['f']['total']) : 0 }}</td>
				<td class="dato">{{ (isset($beneficiario['desglose']['m']['total']))? number_format($beneficiario['desglose']['m']['total']) : 0 }}</td>
			</tr>	
			@endforeach
		</table>
	</div>
</div>
<table class="tabla" width="100%">
	<tr><td colspan="18" height="5"></td></tr>
	<tr>
		<td colspan="9" align="center" class="encabezado">ANTECEDENTES FINANCIEROS</td>
		<td></td>
		<td></td>
		<td colspan="7" align="center" class="encabezado">FECHA DE CORTE DE LA INFORMACIÓN</td>
	</tr>
	<tr><td colspan="18" height="5"></td></tr>
	<tr>
		<td>Año</td>
		<td></td>
		<td colspan="3">Autorizados</td>
		<td></td>
		<td>Ejercido</td>
		<td></td>
		<td>%</td>
	</tr>
	@foreach($data['fibap']['antecedentes_financieros'] as $antecedente)
	<tr>
		<td class="dato">{{$antecedente['anio']}}</td>
		<td></td>
		<td colspan="3" class="dato">$ {{number_format($antecedente['autorizado'])}}</td>
		<td></td>
		<td class="dato">$ {{number_format($antecedente['ejercido'])}}</td>
		<td></td>
		<td class="dato">{{$antecedente['porcentaje']}} %</td>
		<td></td>
		<td></td>
		<td colspan="7" class="dato">{{$antecedente['fechaCorte']}}</td>
	</tr>
	@endforeach
</table>
<table class="tabla" width="100%">
	<tr><td colspan="18" height="5"></td></tr>
	<tr width="50%">
		<td colspan="9" align="center" class="encabezado">RESULTADOS OBTENIDOS</td>
		<td></td>
		<td></td>
		<td colspan="7" align="center" class="encabezado">RESULTADOS ESPERADOS</td>
	</tr>
	<tr width="50%">
		<td colspan="9" class="dato">{{$data['fibap']['resultadosObtenidos']}}</td>
		<td></td>
		<td></td>
		<td colspan="7" class="dato">{{$data['fibap']['resultadosEsperados']}}</td>
	</tr>
</table>
<table class="tabla" width="100%">
	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>JUSTIFICACIÓN DEL PROYECTO</strong></td></tr>
	<tr><td colspan="18" class="dato">{{$data['fibap']['justificacionProyecto']}}</td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>DESCRIPCIÓN DEL PROYECTO</strong></td></tr>
	<tr><td colspan="18" class="dato">{{$data['fibap']['descripcionProyecto']}}</td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>OBJETIVO DEL PROYECTO</strong></td></tr>
	<tr><td colspan="18" class="dato">{{$data['fibap']['objetivoProyecto']}}</td></tr>

	<tr>
		<td colspan="18" align="center" class="encabezado"><strong>PRESUPUESTO REQUERIDO Y PROPUESTA DE FINANCIAMIENTO</strong></td>
	</tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr>
		<td nowrap="nowrap">Presupuesto requerido:</td>
		<td colspan="3" class="dato">$ {{number_format($data['fibap']['presupuestoRequerido'])}}</td>
		<td></td>
		<td></td>
		<td  width="60">Estatal:</td>
		<td colspan="4" class="dato" width="60">$ 
			@if(($valor = array_search('1', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
		<td></td>
		<td colspan="3" width="60">Beneficiarios:</td>
		<td colspan="3" class="dato"  width="60">$ 
			@if(($valor = array_search('4', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap">Periodo de ejecución:</td>
		<td colspan="3" class="dato">{{$data['fibap']['periodoEjecucionInicio']}} al {{$data['fibap']['periodoEjecucionFinal']}}</td>
		<td></td>
		<td>Origen:</td>
		<td>Municipal:</td>
		<td colspan="4" class="dato">$ 
			@if(($valor = array_search('2', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
		<td></td>
		<td colspan="3">Crédito:</td>
		<td colspan="3" class="dato">$ 
			@if(($valor = array_search('5', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>Federal:</td>
		<td colspan="4" class="dato">$ 
			@if(($valor = array_search('3', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
		<td></td>
		<td colspan="3">Otros:</td>
		<td colspan="3" class="dato">$ 
			@if(($valor = array_search('6', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
	</tr>
</table>
<table class="tabla" width="100%">
	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>DISTRIBUCIÓN DEL PRESUPUESTO ESTATAL</strong></td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr>
		<td colspan="5">Capítulo, concepto y partida</td>
		<td></td>
		<td colspan="5">Descripción</td>
		<td colspan="4" align="center">Cantidad</td>
		<td></td>
		<td align="center">%</td>
		<td></td>
	</tr>
	@foreach($data['fibap']['distribucion_presupuesto_agrupado'] as $distribucion)
	<tr>
		<td colspan="5" class="dato">{{$distribucion['objeto_gasto']['clave']}}</td>
		<td></td>
		<td colspan="5" class="dato">{{$distribucion['objeto_gasto']['descripcion']}}</td>
		<td colspan="4" class="dato">$ {{number_format($distribucion['cantidad'])}}</td>
		<td></td>
		<td class="dato">{{ number_format( $distribucion['cantidad']/$data['fibap']['presupuestoRequerido'] * 100, 2 ) }} %</td>
		<td></td>
	</tr>
	@endforeach
	<tr><td colspan="18" height="5"></td></tr>
</table>