<div>
<table>
	<tr>
		<td rowspan="4" class="imagen izquierda">
			<img src="{{ URL::to('img/LogoFederal.png') }}" width="150">
		</td>
		<td height="20" class="titulo3" align="center">GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS</td>
		<td rowspan="4" class="imagen derecha">
			<img src="{{ URL::to('img/LogoInstitucional.png') }}" width="150">
		</td>
	</tr>
	<tr>
		<td height="19" class="titulo4" align="center">INSTITUTO DE SALUD</td>
	</tr>
	<tr>
		<td height="18" class="titulo4" align="center">ANÁLISIS FUNCIONAL {{$mes['trimestre_letras']}} TRIMESTRE {{$proyecto['ejercicio']}}</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td height="18" align="right" colspan="4" class="negrita">Formato RC-6</td>
	</tr>
</table>
<table>
	<tr class="tabla-datos" height="20">
		<th colspan="2" class="encabezado-tabla texto-izquierda" style="font-size:8;">EJE:</th>
		<th class="encabezado-tabla texto-izquierda"  style="font-size:8;">TEMA:</th>
		<th class="encabezado-tabla texto-izquierda"  style="font-size:8;">POLITICA PÚBLICA:</th>
		<th colspan="2" class="encabezado-tabla texto-izquierda"  style="font-size:8;">PROGRAMA PRESUPUESTARIO:</th>
	</tr>
	<tr class="tabla-datos" height="50">
		<td class="texto-medio" style="padding:5px;" colspan="2">{{$proyecto['eje']}}</td>
		<td class="texto-medio" style="padding:5px;">{{$proyecto['tema']}}</td>
		<td class="texto-medio" style="padding:5px;">{{$proyecto['politicaPublica']}}</td>
		<td class="texto-medio" style="padding:5px;" colspan="2">{{$proyecto['programaPresupuestario']}}</td>
	</tr>

	<tr><td colspan="6"></td></tr>

	<tr class="tabla-datos" height="20">
		<th colspan="2" class="encabezado-tabla texto-izquierda"  style="font-size:8;">FUNCIÓN:</th>
		<th class="encabezado-tabla texto-izquierda"  style="font-size:8;">SUBFUNCIÓN:</th>
		<th class="encabezado-tabla texto-izquierda"  style="font-size:8;">FUENTE DE FINANCIAMIENTO:</th>
		<th colspan="2" class="encabezado-tabla texto-izquierda"  style="font-size:8;">SUBFUENTE DE FINANCIAMIENTO:</th>
	</tr>
	<tr class="tabla-datos" height="100">
		<td colspan="2" class="texto-medio" style="padding:5px;" rowspan="{{count($fuentes_financiamiento)}}" >{{$proyecto['funcion']}}</td>
		<td class="texto-medio" style="padding:5px;" rowspan="{{count($fuentes_financiamiento)}}" >{{$proyecto['subFuncion']}}</td>
		@foreach ($fuentes_financiamiento as $key => $fuente)
			@if($key != 0)
				{{'<tr class="tabla-datos">'}}
			@endif
			<td class="texto-medio" style="padding:5px;">
				{{$fuente->fuenteFinanciamiento->clave}}. {{$fuente->fuenteFinanciamiento->descripcion}}
			</td>
			<td class="texto-medio" style="padding:5px;" colspan="2">
			@foreach ($fuente->subFuentesFinanciamiento as $llave => $subfuente)
				{{$subfuente->clave}} {{$subfuente->descripcion}} <br>
			@endforeach
			</td>
			@if($key < count($fuentes_financiamiento))
				{{'</tr>'}}
			@endif
		@endforeach
	</tr>

	<tr><td colspan="6"></td></tr>

	<tr class="tabla-datos" height="20">
		<th colspan="2" class="encabezado-tabla texto-izquierda" style="font-size:8;">CLAVE PRESUPUESTAL:</th>
		<th colspan="4" class="encabezado-tabla texto-izquierda" style="font-size:8;">NOMBRE DEL PROYECTO:</th>
	</tr>
	<tr class="tabla-datos" height="30">
		<td colspan="2" class="texto-medio" style="padding:5px;"> {{ $proyecto['ClavePresupuestaria'] }}</td>
		<td colspan="4" class="texto-medio" style="padding:5px;"> {{ $proyecto['nombreTecnico'] }} </td>
	</tr>

	<tr><td colspan="6"></td></tr>

	<tr class="tabla-datos">
		<th colspan="6" class="encabezado-tabla" style="font-size:8;">FINALIDAD DEL PROYECTO</th>
	</tr>
	<tr class="tabla-datos" height="50">
		<td class="texto-medio" style="padding:5px;" colspan="6">{{$analisis_funcional->finalidadProyecto}}</td>
	</tr>

	<tr><td colspan="6"></td></tr>

	<tr class="tabla-datos">
		<th colspan="6" class="encabezado-tabla" style="font-size:8;">ANALISIS DE RESULTADO</th>
	</tr>
	<tr class="tabla-datos" height="150">
		<td class="texto-medio" style="padding:5px;" colspan="6">{{$analisis_funcional->analisisResultado}}</td>
	</tr>

	<tr><td colspan="6"></td></tr>

	<tr class="tabla-datos">
		<th colspan="6" class="encabezado-tabla" style="font-size:8;">BENEFICIARIOS</th>
	</tr>
	<tr class="tabla-datos" height="50">
		<td class="texto-medio" style="padding:5px;" colspan="6">{{$analisis_funcional->beneficiarios}}</td>
	</tr>

	<tr><td colspan="6"></td></tr>

	<tr class="tabla-datos">
		<th colspan="6" class="encabezado-tabla" style="font-size:8;">JUSTIFICACIÓN GLOBAL DEL PROYECTO</th>
	</tr>
	<tr class="tabla-datos" height="150">
		<td class="texto-medio" style="padding:5px;" colspan="6">{{$analisis_funcional->justificacionGlobal}}</td>
	</tr>

	<tr><td colspan="6" height="40"></td></tr>
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
</div>