@foreach($data['fibap']['accionesCompletasDescripcion'] as $indice => $accion)
<table class="tabla" width="100%">
	<tr>
		<td rowspan="4" class="imagen izquierda">
			<img src="{{ public_path().'/img/EscudoGobiernoChiapas.png' }}" width="150">
		</td>
		<td class="titulo" nowrap="nowrap">SECRETARÍA DE PLANEACIÓN, GESTIÓN PÚBLICA Y PROGRAMA DE GOBIERNO</td>
		<td rowspan="4" class="imagen derecha">
			<img src="{{ public_path().'/img/Marca.png' }}" width="150">
		</td>
	</tr>
	<tr>
		<td class="titulo">PROGRAMA DE INVERSIÓN {{$data['ejercicio']}}</td>
	</tr>
	<tr>
		<td class="titulo">CEDULA DE VALIDACIÓN DE ACCIONES</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>

<table class="tabla" width="100%">
	<tr><td colspan="8" height="5"></td></tr>
	<tr>
		<td class="dato">ORG. PÚBLICO:</td>
		<td colspan="3" class="dato">{{$data['fibap']['organismoPublico']}}</td>
		<td colspan="2"></td>
		<td class="dato">No. SOLICITUD:</td>
		<td class="dato">{{'0'}}</td>
	</tr>
	<tr>
		<td class="dato">PROG. PPTARIO:</td>
		<td colspan="3" class="dato">{{$data['programaPresupuestarioDescripcion']}}</td>
		<td colspan="2"></td>
		<td class="dato">FECHA SOLICITUD:</td>
		<td class="dato">{{date('d/m/Y')}}</td>
	</tr>
	<tr>
		<td class="dato">PROYECTO:</td>
		<td colspan="3" class="dato">{{$data['nombreTecnico']}}</td>
		<td colspan="4"></td>
	</tr>
	<tr>
		<td class="dato">ACCIÓN:</td>
		<td colspan="3" class="dato">{{$accion->entregableAccion}}</td>
		<td colspan="4"></td>
	</tr>
	<tr>
		<td class="dato">OBJETIVO:</td>
		<td colspan="7" class="dato">{{$data['fibap']['objetivoProyecto']}}</td>
	</tr>
	<tr>
		<td class="dato">CLAVE PROGR:</td>
		<td colspan="2" class="dato">{{'-'}}</td>
		<td colspan="2" class="dato">CLAVE PPTAL:</td>
		<td colspan="3" class="dato">{{$data['clavePresupuestaria']}}</td>
	</tr>
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
		<td class="dato">COBERT/MPIO:</td>
		<td colspan="2" class="dato">{{$data['coberturaDescripcion']}} \ {{$cobertura}}</td>
		<td colspan="2" class="dato">LOCALIDAD:</td>
		<td class="dato">
			@if(count($data['fibap']['distribucion_localidad'][$accion->id]) > 1)
				Varios
			@else
				{{$data['fibap']['distribucion_localidad'][$accion->id][0]['localidad']}}
			@endif
		</td>
		<td class="dato">TIPO:</td>
		<td class="dato">{{$data['tipoProyectoDescripcion']}}</td>
	</tr>
	<tr>
		<td class="dato">MODALIDAD:</td>
		<td class="dato">{{$accion->modalidadAccion}}</td>
		<td class="dato">PERIODO DE EJECUCIÓN:</td>
		<td colspan="3" class="dato">{{$data['fibap']['periodoEjecucionInicio']}} al {{$data['fibap']['periodoEjecucionFinal']}}</td>
		<td colspan="2"></td>
	</tr>
	<tr><td colspan="8" height="10"></td></tr>
</table>

<div>
	<div style="width:53%; display:inline-block;">
		<table class="tabla" width="100%">
			@foreach($data['fibap']['origenes_financiamiento'][$accion->id] as $origen)
			@if($origen['indice'] % 2 == 1)
			<tr>
			@endif
				<td class="dato" width="100">{{$origen['descripcion']}}:</td>
				<td class="dato">$ {{number_format($origen['monto'])}}</td>
			@if($origen['indice'] % 2 == 0)
			</tr>
			@endif
			@endforeach
			<tr>
				<td colspan="2"></td>
				<td class="dato">COSTO TOTAL:</td>
				<td class="dato">$ {{number_format($data['fibap']['origenes_total'][$accion->id])}}</td>
			</tr>
		</table>
	</div>
	<div style="width:45%; position:absolute; right:0;">
		<table class="tabla" width="100%">
			<tr>
				<td class="dato" colspan="2">FUENTE DE FINANC.</td>
				<td colspan="2" class="dato">MONTO</td>
				<td class="dato">{{'$00.00'}}</td>
			</tr>
			<tr>
				<td class="dato" colspan="2">&nbsp;</td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="2" class="dato">TOTAL:</td>
				<td colspan="2" class="dato">{{'$00.00'}}</td>
			</tr>
		</table>
	</div>
</div>

<table class="tabla" width="100%">
	<tr><td colspan="15" height="15"></td></tr>
	<tr><td colspan="15" class="texto-centro encabezado">DISTRIBUCIÓN DEL PRESUPUESTO SOLICITADO Y CALENDARIZADO DE MINISTRACIONES</td></tr>
	<tr>
		<td class="texto-centro encabezado-metas">PARTIDA</td>
		<td class="texto-centro encabezado-metas">DESCRIPCIÓN</td>
		<td class="texto-centro encabezado-metas">TOTAL</td>
		<td class="texto-centro encabezado-metas">ENE</td>
		<td class="texto-centro encabezado-metas">FEB</td>
		<td class="texto-centro encabezado-metas">MAR</td>
		<td class="texto-centro encabezado-metas">ABR</td>
		<td class="texto-centro encabezado-metas">MAY</td>
		<td class="texto-centro encabezado-metas">JUN</td>
		<td class="texto-centro encabezado-metas">JUL</td>
		<td class="texto-centro encabezado-metas">AGO</td>
		<td class="texto-centro encabezado-metas">SEP</td>
		<td class="texto-centro encabezado-metas">OCT</td>
		<td class="texto-centro encabezado-metas">NOV</td>
		<td class="texto-centro encabezado-metas">DIC</td>
	</tr>
	@foreach($data['fibap']['distribucion_partidas'][$accion->id] as $distribucion)
	<tr>
		<td class="dato-metas">{{$distribucion['partida']}}</td>
		<td class="dato-metas">{{$distribucion['descripcion']}}</td>
		<td class="dato-metas">$ {{number_format($distribucion['total'])}}</td>
		@for($i = 1 ; $i <= 12 ; $i++)
		<td class="dato-metas">$ {{(isset($distribucion['desglose'][$i]))?number_format($distribucion['desglose'][$i]):'0'}}</td>
		@endfor
	</tr>
	@endforeach
	<tr>
		<td class="dato-metas">TOTALES:</td><td class="dato-metas"></td>
		<td class="dato-metas">$ {{number_format($data['fibap']['distribucion_partidas_totales'][$accion->id]['total'])}}</td>
		@for($i = 1 ; $i <= 12 ; $i++)
		<td class="dato-metas">$ {{number_format($data['fibap']['distribucion_partidas_totales'][$accion->id]['desglose'][$i])}}</td>
		@endfor
	</tr>
</table>

<table class="tabla" width="100%">
	<tr>
		<td colspan="9" class="encabezado texto-centro">METAS</td>
		<td colspan="3" class="encabezado texto-centro">BENEFICIARIOS</td>
	</tr>
	<tr>
		<td class="encabezado-metas texto-centro">Grupo</td>
		<td class="encabezado-metas texto-centro">Tipo</td>
		<td class="encabezado-metas texto-centro">Descripción</td>
		<td class="encabezado-metas texto-centro">Unidad</td>
		<td class="encabezado-metas texto-centro">Cantidad</td>
		<td class="encabezado-metas texto-centro">T1</td>
		<td class="encabezado-metas texto-centro">T2</td>
		<td class="encabezado-metas texto-centro">T3</td>
		<td class="encabezado-metas texto-centro">T4</td>
		<td class="encabezado-metas texto-centro">Unidad</td>
		<td class="encabezado-metas texto-centro">Cantidad</td>
		<td class="encabezado-metas texto-centro">% MUJ.</td>
	</tr>
	@foreach($data['fibap']['distribucion_beneficiarios'][$accion->id] as $index => $beneficiario)
	<tr>
		@if($index == 0)
			<td class="dato-metas">{{$accion->entregable}}</td>
			<td class="dato-metas">{{($accion->entregableTipo)?$accion->entregableTipo:'N/A'}}</td>
			<td class="dato-metas">{{$accion->indicador}}</td>
			<td class="dato-metas">{{$accion->unidadMedida}}</td>
			<td class="dato-metas">{{number_format($accion->valorNumerador)}}</td>
			<td class="dato-metas">{{number_format($accion->numeroTrim1)}}</td>
			<td class="dato-metas">{{number_format($accion->numeroTrim2)}}</td>
			<td class="dato-metas">{{number_format($accion->numeroTrim3)}}</td>
			<td class="dato-metas">{{number_format($accion->numeroTrim4)}}</td>
		@else
			<td class="dato-metas"></td><td class="dato-metas"></td><td class="dato-metas"></td>
			<td class="dato-metas"></td><td class="dato-metas"></td><td class="dato-metas"></td>
			<td class="dato-metas"></td><td class="dato-metas"></td><td class="dato-metas"></td>
		@endif
		<td class="dato-metas">{{$beneficiario['descripcion']}}</td>
		<td class="dato-metas">{{number_format($beneficiario['total'])}}</td>
		<td class="dato-metas">{{round(($beneficiario['totalF']/$beneficiario['total'])*100,2)}} %</td>
	</tr>
	@endforeach
</table>

<table class="tabla" width="100%">
	<tr><td colspan="12" height="15"></td></tr>
	<tr>
		<th colspan="12">DETALLE EN CASO QUE LA ACCIÓN SEA DE COBERTURA</th>
	</tr>
	<tr>
		<th colspan="12">METAS</th>
	</tr>
	<tr>
		<td class="encabezado-metas texto-centro">MUNICIPIO</td>
		<td class="encabezado-metas texto-centro">LOCALIDAD</td>
		<td class="encabezado-metas texto-centro">MONTO ($)</td>
		<td class="encabezado-metas texto-centro">UNIDAD</td>
		<td class="encabezado-metas texto-centro">CANTIDAD</td>
		<td class="encabezado-metas texto-centro">T1</td>
		<td class="encabezado-metas texto-centro">T2</td>
		<td class="encabezado-metas texto-centro">T3</td>
		<td class="encabezado-metas texto-centro">T4</td>
		<td class="encabezado-metas texto-centro">UNIDAD</td>
		<td class="encabezado-metas texto-centro">CANTIDAD</td>
		<td class="encabezado-metas texto-centro">% MUJ.</td>
	</tr>
	<!--foreach($data['fibap']['distribucion_localidad'][$accion->id] as $distribucion)-->
	@for($i = 0; $i < $data['fibap']['total_lineas_desglose'][$accion->id] ; $i++)
	<tr>
		@if(isset($data['fibap']['distribucion_localidad'][$accion->id][$i]))
			<td class="dato-metas">{{$data['fibap']['distribucion_localidad'][$accion->id][$i]['municipio']}}</td>
			<td class="dato-metas">{{$data['fibap']['distribucion_localidad'][$accion->id][$i]['localidad']}}</td>
			<td class="dato-metas">$ {{number_format($data['fibap']['distribucion_localidad'][$accion->id][$i]['monto'])}}</td>
			<td class="dato-metas">{{$data['fibap']['distribucion_localidad'][$accion->id][$i]['unidad']}}</td>
			<td class="dato-metas">{{number_format($data['fibap']['distribucion_localidad'][$accion->id][$i]['cantidad'])}}</td>
			<td class="dato-metas">{{number_format($data['fibap']['distribucion_localidad'][$accion->id][$i]['metas'][1])}}</td>
			<td class="dato-metas">{{number_format($data['fibap']['distribucion_localidad'][$accion->id][$i]['metas'][2])}}</td>
			<td class="dato-metas">{{number_format($data['fibap']['distribucion_localidad'][$accion->id][$i]['metas'][3])}}</td>
			<td class="dato-metas">{{number_format($data['fibap']['distribucion_localidad'][$accion->id][$i]['metas'][4])}}</td>
		@else
			<td class="dato-metas"></td><td class="dato-metas"></td><td class="dato-metas"></td>
			<td class="dato-metas"></td><td class="dato-metas"></td><td class="dato-metas"></td>
			<td class="dato-metas"></td><td class="dato-metas"></td><td class="dato-metas"></td>
		@endif

		@if(isset($data['fibap']['distribucion_beneficiarios'][$accion->id][$i]))
			<td class="dato-metas">{{$data['fibap']['distribucion_beneficiarios'][$accion->id][$i]['descripcion']}}</td>
			<td class="dato-metas">{{number_format($data['fibap']['distribucion_beneficiarios'][$accion->id][$i]['total'])}}</td>
			<td class="dato-metas">{{round(($data['fibap']['distribucion_beneficiarios'][$accion->id][$i]['totalF']/$data['fibap']['distribucion_beneficiarios'][$accion->id][$i]['total'])*100,2)}} %</td>
		@else
			<td class="dato-metas"></td><td class="dato-metas"></td><td class="dato-metas"></td>
		@endif
	</tr>
	@endfor
	<!--endforeach-->
</table>

<table class="tabla" width="100%">
	<tr><td colspan="7" height="15"></td></tr>
	<tr>
		<th colspan="7">ORGANISMO PÚBLICO SOLICITANTE</th>
	</tr>
	<tr><td colspan="7" height="15"></td></tr>
	<tr>
		<td></td>
		<td class="texto-centro">FORMULA</td>
		<td></td>
		<td class="texto-centro">REVISA</td>
		<td></td>
		<td class="texto-centro">SOLICITA</td>
		<td></td>
	</tr>
	<tr><td colspan="7" height="15"></td></tr>
	<tr>
		<td></td>
		<td class="firma texto-centro">{{$data['liderProyecto']}}</td>
		<td></td>
		<td class="firma texto-centro">{{$data['jefePlaneacion']}}</td>
		<td></td>
		<td class="firma texto-centro">{{$data['jefeInmediato']}}</td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td class="texto-centro">LIDER DE LA EJECUCIÓN DEL PROYECTO</td>
		<td></td>
		<td class="texto-centro">JEFE DE LA UNIDAD DE PLANEACIÓN Y/O PROGRAMACIÓN</td>
		<td></td>
		<td class="texto-centro">TITULAR DEL ORGANISMO PÚBLICO</td>
		<td></td>
	</tr>
</table>

@if(($indice + 1) < count($data['fibap']['accionesCompletasDescripcion']))
	<div style="page-break-after:always;"></div>
@endif

@endforeach