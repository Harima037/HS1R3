<table class="tabla" width="100%">
	<tr>
		<td rowspan="6" class="imagen izquierda">
			<img src="{{ URL::to('img/EscudoGobiernoChiapas.png') }}" width="150">
		</td>
		<td class="titulo" nowrap="nowrap">GOBIERNO DEL ESTADO DE CHIAPAS</td>
		<td rowspan="6" class="imagen derecha">
			<img src="{{ URL::to('img/Marca.png') }}" width="150">
		</td>
	</tr>
	<tr>
		<td class="titulo">SECRETARÍA DE SALUD</td>
	</tr>
	<tr>
		<td class="titulo">INSTITUTO DE SALUD</td>
	</tr>
	<tr>
		<td class="titulo">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
	</tr>
	<tr>
		<td class="titulo">DEPARTAMENTO DE INTEGRACION PROGRAMATICA PRESUPUESTAL</td>
	</tr>
	<tr>
		<td class="titulo">DISTRIBUCIÓN DE METAS POR MES {{$data['ejercicio']}}</td>
	</tr>
</table>
<table class="tabla" width="100%">
	<tr><td colspan="16" height="10"></td></tr>
	<tr>
		<td colspan="16"><b>NOMBRE DEL PROYECTO:</b> {{ $data['nombreTecnico'] }}</td>
	</tr>
	<tr><td colspan="16" height="10"></td></tr>
	<tr>
		<td colspan="16"><b>CLAVE PRESUPUESTAL:</b> {{ $data['ClavePresupuestaria'] }}</td>
	</tr>
	<tr><td colspan="16" height="10"></td></tr>
	<tr>
		<td class="encabezado-metas"></td>
		<td class="encabezado-metas" colspan="2">DESCRIPCIÓN DEL INDICADOR</td>
		<td class="encabezado-metas" width="1">ENERO</td>
		<td class="encabezado-metas" width="1">FEBRERO</td>
		<td class="encabezado-metas" width="1">MARZO</td>
		<td class="encabezado-metas" width="1">ABRIL</td>
		<td class="encabezado-metas" width="1">MAYO</td>
		<td class="encabezado-metas" width="1">JUNIO</td>
		<td class="encabezado-metas" width="1">JULIO</td>
		<td class="encabezado-metas" width="1">AGOSTO</td>
		<td class="encabezado-metas" width="1">SEPTIEMBRE</td>
		<td class="encabezado-metas" width="1">OCTUBRE</td>
		<td class="encabezado-metas" width="1">NOVIEMBRE</td>
		<td class="encabezado-metas" width="1">DICIEMBRE</td>
		<td class="encabezado-metas">TOTAL</td>
	</tr>
	@foreach($data['componentes'] as $index => $componente)
	<tr>
		<td class="dato-metas" >Componente {{$index+1}}</td>
		<td colspan="2" class="dato-metas">{{ $componente->indicador }}</td>
		@foreach ($data['componentesMetasMes'][$componente->id] as $mes => $meta)
			<td class="dato-metas">
				{{($meta > 0)?number_format($meta):''}}
			</td>
		@endforeach
	</tr>
		@foreach($componente->actividades as $indice => $actividad)
		<tr>
			<td class="dato-metas">Actividad {{ $indice+1 }} C {{$index+1}}</td>
			<td class="dato-metas" colspan="2">{{ $actividad->indicador }}</td>
			@foreach ($data['actividadesMetasMes'][$actividad->id] as $mes => $meta)
				<td class="dato-metas">
					{{($meta > 0)?number_format($meta):''}}
				</td>
			@endforeach
		</tr>
		@endforeach
	@endforeach

	<tr><td colspan="16" height="10"></td></tr>

	<tr>
		<td colspan="16"><b>FUENTE DE INFORMACIÓN:</b></td>
	</tr>
	
	<tr><td colspan="16" height="10"></td></tr>	
</table>
<table class="table" width="100%">
	<tr><td height="15" colspan="5"></td></tr>
	<tr>
		<td width="10%"></td>
		<td width="25%" class="texto-centro firma">{{ $data['liderProyecto'] }}</td>
		<td width="30%"></td>
		<td width="25%" class="texto-centro firma">{{ $data['jefeInmediato'] }}</td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td></td>
		<th class="texto-centro">Responsable del Programa.</th>
		<td></td>
		<th class="texto-centro">Director.</th>
		<td></td>
	</tr>
	<tr><td height="15" colspan="5"></td></tr>
</table>