<table class="tabla" width="100%">
	<tr><td colspan="8"></td></tr>
	<tr>
		<td rowspan="8" class="imagen izquierda">
			<img src="{{ public_path().'/img/LogoFederal.png' }}" width="150">
		</td>
		<td class="titulo">GOBIERNO DEL ESTADO DE CHIAPAS</td>
		<td rowspan="8" class="imagen derecha">
			<img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="150">
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
	<tr><td class="titulo">SISTEMA PRESUPUESTARIO {{$data['ejercicio']}}</td></tr>
	<tr><td height="5"></td></tr>
	<tr>
		<td class="titulo"><strong>ESTRATEGIA INSTITUCIONAL</strong></td>
	</tr>
</table>
<table class="tabla" width="100%">
	<tr>
		<td class="encabezado">DATOS GENERALES</td>
		<td colspan="5"></td>
	</tr>
	<tr>
        <td class="encabezado">UNIDAD RESPONSABLE:</td>
		<td class="dato">{{$data['estrategia']['unidadResponsable']['descripcion']}}</td>
		<td class="encabezado">PROGRAMA SECTORIAL:</td>
		<td class="dato">{{$data['estrategia']['programaSectorial']['descripcion']}}</td>
		<td class="encabezado">EJERCICIO:</td>
        <td class="dato">{{$data['estrategia']['ejercicio']}}</td>
	</tr>
	<tr>
        <td class="encabezado">ESTRATEGIA DEL OBJETIVO DEL PLAN NACIONAL:</td>
		<td colspan="5" class="dato">{{$data['estrategia']['estrategiaNacional']['descripcion']}}</td>
	</tr>
	<tr>
        <td class="encabezado">ODS:</td>
		<td colspan="5" class="dato">{{$data['estrategia']['estrategiaNacional']['descripcion']}}</td>
	</tr>
	<tr>
		<td class="encabezado">AÑO DE TERMINO:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['fechaTermino']}}</td>
		<td class="encabezado">TELÉFONO:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['liderTelefono']}}</td>
		<td class="encabezado">POLÍTICA PÚBLICA:</td>
		<td class="dato" nowrap="nowrap">{{$data['programaPresupuestarioAsignado']['politicaPublica']}}</td>
	</tr>
	<tr>
		<td colspan="4" class="encabezado texto-centro">RESULTADOS ESPERADOS POR LA IMPLEMENTACIÓN:</td>
		<td class="encabezado">OPE:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['objetivoPED']}}</td>
	</tr>
	<tr>
		<td colspan="4" rowspan="2" class="dato">{{$data['programaPresupuestarioAsignado']['resultadosEsperados']}}</td>
		<td class="encabezado">PROGRAMA SECTORIAL:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['programaSectorial']}}</td>
	</tr>
	<tr>
		<td class="encabezado">OPN:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['objetivoPND']}}</td>
	</tr>
</table>
@endforeach