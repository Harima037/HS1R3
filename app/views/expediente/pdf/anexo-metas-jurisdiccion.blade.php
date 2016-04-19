<table class="tabla" width="100%">
	<tr>
		<td rowspan="6" class="imagen izquierda">
			<img src="{{ public_path().'/img/EscudoGobiernoChiapas.png' }}" width="150">
		</td>
		<td class="titulo" nowrap="nowrap">GOBIERNO DEL ESTADO DE CHIAPAS</td>
		<td rowspan="6" class="imagen derecha">
			<img src="{{ public_path().'/img/Marca.png' }}" width="150">
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
		<td class="titulo">DISTRIBUCIÓN DE METAS POR JURISDICCIÓN {{$data['ejercicio']}}</td>
	</tr>
</table>
<table class="tabla" width="100%">
	<tr><td colspan="15" height="10"></td></tr>
	<tr>
		<td colspan="15"><b>NOMBRE DEL PROYECTO:</b> {{ $data['nombreTecnico'] }}</td>
	</tr>
	<tr><td colspan="15" height="10"></td></tr>
	<tr>
		<td colspan="15"><b>CLAVE PRESUPUESTAL:</b> {{ $data['ClavePresupuestaria'] }}</td>
	</tr>
	<tr><td colspan="15" height="10"></td></tr>
	<tr>
		<td class="encabezado-metas"></td>
		<td class="encabezado-metas" colspan="2">DESCRIPCIÓN DEL INDICADOR</td>
		<td class="encabezado-metas">OC</td>
		<td class="encabezado-metas">I</td>
		<td class="encabezado-metas">II</td>
		<td class="encabezado-metas">III</td>
		<td class="encabezado-metas">IV</td>
		<td class="encabezado-metas">V</td>
		<td class="encabezado-metas">VI</td>
		<td class="encabezado-metas">VII</td>
		<td class="encabezado-metas">VIII</td>
		<td class="encabezado-metas">IX</td>
		<td class="encabezado-metas">X</td>
		<td class="encabezado-metas">ESTATAL</td>
	</tr>
	@foreach($data['componentes'] as $index => $componente)
	<tr>
		<td class="dato-metas" >Componente {{$index+1}}</td>
		<td colspan="2" class="dato-metas">{{ $componente->indicador }}</td>
		@foreach ($data['componentesMetasJuris'][$componente->id] as $jurisdiccion => $meta)
			<td class="dato-metas">
				{{($meta > 0)?number_format($meta):''}}
			</td>
		@endforeach
	</tr>
		@foreach($componente->actividades as $indice => $actividad)
		<tr>
			<td class="dato-metas">Actividad {{ $indice+1 }} C {{$index+1}}</td>
			<td class="dato-metas" colspan="2">{{ $actividad->indicador }}</td>
			@foreach ($data['actividadesMetasJuris'][$actividad->id] as $jurisdiccion => $meta)
				<td class="dato-metas">
					{{($meta > 0)?number_format($meta):''}}
				</td>
			@endforeach
		</tr>
		@endforeach
	@endforeach

	<tr><td colspan="15" height="10"></td></tr>

	<tr>
		<td colspan="15"><b>FUENTE DE INFORMACIÓN:</b> {{$data['fuenteInformacion']}}</td>
	</tr>
	
	<tr><td colspan="15" height="10"></td></tr>	
</table>
<table class="table" width="100%">
	<tr><td height="15" colspan="5"></td></tr>
	<tr>
		<td></td>
		<th class="texto-centro">Responsable del Programa.</th>
		<td></td>
		<th class="texto-centro">Director.</th>
		<td></td>
	</tr>
	<tr><td height="15" colspan="5"></td></tr>
	<tr>
		<td width="10%"></td>
		<td width="25%" class="texto-centro firma">{{ $data['responsableInformacion'] }}</td>
		<td width="30%"></td>
		<td width="25%" class="texto-centro firma">{{ $data['liderProyecto'] }}</td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td></td>
		<th class="texto-centro">{{ $data['responsableInformacionCargo'] }}</th>
		<td></td>
		<th class="texto-centro">{{ $data['liderProyectoCargo'] }}</th>
		<td></td>
	</tr>
	<tr><td height="15" colspan="5"></td></tr>
</table>