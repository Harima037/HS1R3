<!--table class="tabla" width="100%">
	<tr>
		<td rowspan="5" class="imagen izquierda" width="150">
			<img src="{{ public_path().'/img/LogoFederal.png' }}" width="150">
		</td>
		<td height="20" class="titulo" align="center">INSTITUTO DE SALUD</td>
		<td rowspan="5" class="imagen derecha" width="150">
			<img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="150">
		</td>
	</tr>
	<tr>
		<td height="19" class="titulo" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
	</tr>
	<tr>
		<td height="18" class="titulo" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td>
	</tr>
	<tr>
		<td height="18" class="titulo" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
	</tr>
	<tr>
		<td height="18" class="titulo" align="center">PROGRAMACIÓN DE INDICADORES {{$data['ejercicio']}}</td>
	</tr>
</table-->
<!--table class="tabla" width="100%">
	<thead>
	<tr>
		<td>Nombre del proyecto:</td>
		<td height="10"><b>{{ $data['nombreTecnico'] }}</b></td>
		<td>Clave presupuestaria:</td>
		<td><b>{{ $data['ClavePresupuestaria'] }}</b></td>
		<td><b>Formato RC-1</b></td>
	</tr>
</thead>
</table-->
<table class="tabla" width="100%">
	<thead>
		<tr>
			<td height="18" colspan="16" class="titulo" align="center">PROGRAMACIÓN DE INDICADORES {{$data['ejercicio']}}</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:right;">Nombre del proyecto:</td>
			<td colspan="5" height="10"><b>{{ $data['nombreTecnico'] }}</b></td>
			<td style="text-align:right;" colspan="3">Clave presupuestaria:</td>
			<td colspan="3"><b>{{ $data['ClavePresupuestaria'] }}</b></td>
			<td colspan="2"></td>
			<td><b>Formato RC-1</b></td>
		</tr>
		<tr>
			<th colspan="16" height="5"></th>
		</tr>
		<tr>
			<th class="encabezado-tabla" rowspan="2">NIVEL</th>
			<th class="encabezado-tabla" rowspan="2">INDICADOR</th>
			<th class="encabezado-tabla" rowspan="2">UNIDAD DE MEDIDA</th>
			<th class="encabezado-tabla" rowspan="2">TOTAL PROGRAMADO</th>
			<th class="encabezado-tabla" colspan="12">PROGRAMADO</th>
		</tr>
		<tr>
			<th class="encabezado-tabla">ENERO</th>
			<th class="encabezado-tabla">FEBRERO</th>
			<th class="encabezado-tabla">MARZO</th>
			<th class="encabezado-tabla">ABRIL</th>
			<th class="encabezado-tabla">MAYO</th>
			<th class="encabezado-tabla">JUNIO</th>
			<th class="encabezado-tabla">JULIO</th>
			<th class="encabezado-tabla">AGOSTO</th>
			<th class="encabezado-tabla">SEPTIEMBRE</th>
			<th class="encabezado-tabla">OCTUBRE</th>
			<th class="encabezado-tabla">NOVIEMBRE</th>
			<th class="encabezado-tabla">DICIEMBRE</th>
		</tr>
	</thead>
<tbody>
	@foreach( $data['componentesCompletoDescripcion'] as $index => $componente)
	<tr style="background-color:#DDDDDD;">
		<td class="dato-metas" >Componente {{$index+1}}</td>
		<td class="dato-metas">{{ $componente->indicador }}</td>
		<td class="dato-metas">{{ $componente->unidadMedida }}</td>
		<td class="dato-metas">{{ $data['componentesMetasCompleto'][$componente->id]['totales']['total'] }}</td>
		@for ($i = 1; $i <= 12 ; $i++)
			@if(isset($data['componentesMetasCompleto'][$componente->id]['totales'][$i])) 
				<td class="dato-metas">{{ $data['componentesMetasCompleto'][$componente->id]['totales'][$i] }}</td>
			@else
				<td class="dato-metas">0</td>
			@endif
		@endfor
	</tr>
		@foreach($data['componentesMetasCompleto'][$componente->id]['jurisdicciones'] as $clave => $jurisdiccion)
			<tr>
				<td class="dato-metas"></td>
				<td class="dato-metas">
					@if($clave == 'OC')
						OFICINA CENTRAL
					@else
						{{ $clave }} {{ $data['jurisdicciones'][$clave] }}
					@endif
				</td>
				<td class="dato-metas"></td>
				<td class="dato-metas">{{ $jurisdiccion['total'] }}</td>
				@for ($i = 1; $i <= 12 ; $i++)
					@if(isset($jurisdiccion['meses'][$i])) 
						<td class="dato-metas">{{ $jurisdiccion['meses'][$i] }}</td>
					@else
						<td class="dato-metas"></td>
					@endif
				@endfor
			</tr>
		@endforeach

		@foreach($componente->actividadesDescripcion as $indice => $actividad)
		<tr style="background-color:#DDDDDD;">
			<td class="dato-metas">Actividad {{$index+1}}.{{ $indice+1 }}</td>
			<td class="dato-metas">{{ $actividad->indicador }}</td>
			<td class="dato-metas">{{ $actividad->unidadMedida }}</td>
			<td class="dato-metas">{{ $data['actividadesMetasCompleto'][$actividad->id]['totales']['total'] }}</td>
			@for ($i = 1; $i <= 12 ; $i++)
				@if(isset($data['actividadesMetasCompleto'][$actividad->id]['totales'][$i])) 
					<td class="dato-metas">{{ $data['actividadesMetasCompleto'][$actividad->id]['totales'][$i] }}</td>
				@else
					<td class="dato-metas">0</td>
				@endif
			@endfor
		</tr>

			@foreach($data['actividadesMetasCompleto'][$actividad->id]['jurisdicciones'] as $clave => $jurisdiccion)
				<tr>
					<td class="dato-metas"></td>
					<td class="dato-metas">
						@if($clave == 'OC')
							OFICINA CENTRAL
						@else
							{{ $clave }} {{ $data['jurisdicciones'][$clave] }}
						@endif
					</td>
					<td class="dato-metas"></td>
					<td class="dato-metas">{{ $jurisdiccion['total'] }}</td>
					@for ($i = 1; $i <= 12 ; $i++)
						@if(isset($jurisdiccion['meses'][$i])) 
							<td class="dato-metas">{{ $jurisdiccion['meses'][$i] }}</td>
						@else
							<td class="dato-metas"></td>
						@endif
					@endfor
				</tr>
			@endforeach

		@endforeach

	@endforeach
	<tr>
		<td colspan="16"><b>FUENTE DE INFORMACIÓN:</b> {{$data['fuenteInformacion']}}</td>
	</tr>
</tbody>
</table>
<br>
<table class="table" width="100%"  style="page-break-inside: avoid;">
	<tr><td height="5" colspan="5"></td></tr>
	<tr>
		<td></td>
		<th class="texto-centro">Responsable del Programa.</th>
		<td></td>
		<th class="texto-centro">Director.</th>
		<td></td>
	</tr>
	<tr><td height="8" colspan="5"></td></tr>
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
</table>