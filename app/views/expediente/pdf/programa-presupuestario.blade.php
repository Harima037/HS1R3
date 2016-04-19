<table class="tabla" width="100%">
	<tr><td colspan="8"></td></tr>
	<tr>
		<td rowspan="8" class="imagen izquierda">
			<img src="{{ public_path().'/img/EscudoGobiernoChiapas.png' }}" width="150">
		</td>
		<td class="titulo">GOBIERNO DEL ESTADO DE CHIAPAS</td>
		<td rowspan="8" class="imagen derecha">
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
	<tr><td class="titulo">SISTEMA PRESUPUESTARIO {{$data['ejercicio']}}</td></tr>
	<tr><td height="5"></td></tr>
	<tr>
		<td class="titulo"><strong>PROGRAMA PRESUPUESTARIO</strong></td>
	</tr>
</table>
<table class="tabla" width="100%">
	<tr>
		<td class="encabezado">DATOS GENERALES</td>
		<td colspan="5"></td>
	</tr>
	<tr>
		<td class="encabezado">PROGRAMA PRESUPUESTARIO:</td>
		<td colspan="5" class="dato">{{$data['programaPresupuestarioAsignado']['programaPresupuestarioDescripcion']}}</td>
	</tr>
	<tr>
		<td class="encabezado">ODM:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['ODM']}}</td>
		<td class="encabezado">UNIDAD RESPONSABLE:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['unidadResponsable']}}</td>
		<td colspan="2" class="encabezado" align="center">ALINEACION AL PED:</td>
	</tr>
	<tr>
		<td class="encabezado">MODALIDAD:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['modalidad']}}</td>
		<td class="encabezado">NOMBRE DEL RESPONSABLE:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['liderPrograma']}}</td>
		<td class="encabezado">EJE:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['eje']}}</td>
	</tr>
	<tr>
		<td class="encabezado">AÑO INICIO:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['fechaInicio']}}</td>
		<td class="encabezado">CORREO ELECTRÓNICO:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['liderCorreo']}}</td>
		<td class="encabezado">TEMA:</td>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['tema']}}</td>
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

<table class="tabla" width="100%">
	<tr><td colspan="4" height="5"></td></tr>
	<tr>
		<td colspan="4" class="encabezado texto-centro"><strong>DIAGNÓSTICO DEL PROGRAMA PRESUPUESTARIO</strong></td>
	</tr>
	<tr><td colspan="4" height="5"></td></tr>
	<tr>
		<td colspan="3" class="encabezado texto-centro">DEFINICIÓN DE LA PROBLEMÁTICA</td>
		<td width="40%"></td>
	</tr>
	<tr><td colspan="4" height="5"></td></tr>
	<tr>
		<td class="encabezado">ÁRBOL DEL PROBLEMA:</td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="4" class="dato">{{$data['programaPresupuestarioAsignado']['arbolProblema']}}</td>
	</tr>
	<tr>
		<td colspan="2" class="encabezado">CAUSAS:</td>
		<td></td>
		<td class="encabezado">EFECTOS:</td>
	</tr>
	@foreach ($data['programaPresupuestarioAsignado']['arbolProblemas'] as $problema)
		<tr>
			<td colspan="2" class="dato">{{$problema->causa}}</td>
			<td></td>
			<td class="dato">{{$problema->efecto}}</td>
		</tr>
	@endforeach
	<tr><td colspan="4" height="5"></td></tr>
	<tr>
		<td class="encabezado">ÁRBOL DE OBJETIVOS:</td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="4" class="dato">{{$data['programaPresupuestarioAsignado']['arbolObjetivo']}}</td>
	</tr>
	<tr>
		<td colspan="2" class="encabezado">MEDIOS:</td>
		<td></td>
		<td class="encabezado">FINES:</td>
	</tr>
	@foreach ($data['programaPresupuestarioAsignado']['arbolObjetivos'] as $objetivo)
		<tr>
			<td colspan="2" class="dato">{{$objetivo->medio}}</td>
			<td></td>
			<td class="dato">{{$objetivo->fin}}</td>
		</tr>
	@endforeach
	<tr><td colspan="4" height="5"></td></tr>
</table>
<table class="tabla" width="100%">
	<tr><td colspan="4"></td></tr>
	<tr>
		<td width="75%" class="encabezado">GRUPO DE ATENCIÓN A LA POBLACIÓN O AREA DE ENFOQUE POTENCIAL</td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['areaEnfoquePotencial']}}</td>
		<td width="100">&nbsp;</td>
		<td width="100" class="encabezado">CUANTIFICACIÓN</td>
		<td class="dato">{{number_format($data['programaPresupuestarioAsignado']['cuantificacionEnfoquePotencial'])}}</td>
	</tr>
	<tr>
		<td width="75%" class="encabezado">GRUPO DE ATENCIÓN A LA POBLACIÓN O AREA DE ENFOQUE OBJETIVO:</td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td class="dato">{{$data['programaPresupuestarioAsignado']['areaEnfoqueObjetivo']}}</td>
		<td width="100">&nbsp;</td>
		<td width="100" class="encabezado">CUANTIFICACIÓN</td>
		<td class="dato">{{number_format($data['programaPresupuestarioAsignado']['cuantificacionEnfoqueObjetivo'])}}</td>
	</tr>
</table>
<table class="tabla" width="100%"><tr><td></td></tr></table>
<table class="tabla" width="100%" style="page-break-inside: avoid;">
	<tr><td colspan="2" height="5"></td></tr>
	<tr>
		<td width="150" class="encabezado">JUSTIFICACIÓN DEL PROGRAMA:</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2" class="dato">{{$data['programaPresupuestarioAsignado']['justificacionPrograma']}}</td>
	</tr>
	<tr><td colspan="2" height="5"></td></tr>
</table>
@foreach ($data['programaPresupuestarioAsignado']['indicadoresDescripcion'] as $indicador)
<table class="tabla" width="100%">
	<tr><td colspan="6" height="5">&nbsp;</td></tr>
	<tr>
		<td colspan="6" class="encabezado texto-centro">
			OBJETIVOS E INDICADORES DEL {{($indicador->claveTipoIndicador == 'F')?'FIN':'PROPOSITO'}} PP.{{$data['programaPresupuestarioAsignado']['claveProgramaPresupuestario']}}
		</td>
	</tr>	
	<tr>
		<td class="encabezado">OBJETIVO:</td>
		<td colspan="5" class="dato">{{$indicador->descripcionObjetivo}}</td>
	</tr>
	<tr>
		<td class="encabezado">ÁMBITO:</td>
		<td colspan="5" class="dato">{{($indicador->claveAmbito == 'E')?'Estatal':'Federal'}}</td>
	</tr>
	<tr>
		<td class="encabezado">MEDIOS DE VERIFICACIÓN:</td>
		<td colspan="5" class="dato">{{$indicador->mediosVerificacion}}</td>
	</tr>
	<tr>
		<td class="encabezado">SUPUESTOS:</td>
		<td colspan="5" class="dato">{{$indicador->supuestos}}</td>
	</tr>
	<tr><td colspan="6" height="5">&nbsp;</td></tr>
	<tr>
		<td colspan="6" class="encabezado texto-centro">
		INDICADORES DEL {{($indicador->claveTipoIndicador == 'F')?'FIN':'PROPOSITO'}} PP.{{$data['programaPresupuestarioAsignado']['claveProgramaPresupuestario']}}
		</td>
	</tr>
	<tr>
		<td class="encabezado">DESCRIPCIÓN:</td>
		<td colspan="3" class="dato">{{$indicador->descripcionIndicador}}</td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="encabezado">NUMERADOR:</td>
		<td colspan="3" class="dato">{{$indicador->numerador}}</td>
		<td class="encabezado">NUMERADOR:</td>
		<td class="dato">{{number_format($indicador->valorNumerador)}}</td>
	</tr>
	<tr>
		<td class="encabezado">DENOMINADOR:</td>
		<td colspan="3" class="dato">{{$indicador->denominador}}</td>
		<td class="encabezado">DENOMINADOR:</td>
		<td class="dato">{{number_format($indicador->valorDenominador)}}</td>
	</tr>
	<tr>
		<td class="encabezado">FÓRMULA:</td>
		<td colspan="3" class="dato">{{$indicador->formula}}</td>
		<td class="encabezado">LÍNEA BASE:</td>
		<td class="dato">{{number_format($indicador->lineaBase)}}</td>
	</tr>
	<tr>
		<td class="encabezado">TIPO:</td>
		<td class="dato">{{$indicador->tipoIndicador}}</td>
		<td class="encabezado">DIMENSIÓN:</td>
		<td class="dato">{{$indicador->dimensionIndicador}}</td>
		<td class="encabezado">AÑO DE LA LÍNEA BASE:</td>
		<td class="dato">{{$indicador->anioBase}}</td>
	</tr>
	<tr>
		<td class="encabezado">UNIDAD DE MEDIDA:</td>
		<td class="dato">{{$indicador->unidadMedida}}</td>
		<td class="encabezado">FRECUENCIA:</td>
		<td class="dato">{{$indicador->frecuencia}}</td>
		<td class="encabezado">META INDICADOR:</td>
		<td class="dato">{{$indicador->metaIndicador}}</td>
	</tr>
	<tr>
		<td class="encabezado">INTERPRETACIÓN:</td>
		<td colspan="3" class="dato">{{$indicador->interpretacion}}</td>
		<td class="encabezado">TIPO META INDICADOR:</td>
		<td class="dato"></td>
	</tr>
	<tr><td colspan="6" height="5">&nbsp;</td></tr>
	<tr>
		<td colspan="5" class="encabezado texto-centro">PROGRAMACIÓN TRIMESTRAL:</td>
		<td></td>
	</tr>
	<tr>
		<td class="encabezado texto-centro">PRIMER TRIMESTRE:</td>
		<td class="encabezado texto-centro">SEGUNDO TRIMESTRE:</td>
		<td class="encabezado texto-centro">TERCER TRIMESTRE:</td>
		<td class="encabezado texto-centro">CUARTO TRIMESTRE:</td>
		<td class="encabezado texto-centro">TOTAL</td>
		<td></td>
	</tr>
	<tr>
		<td class="dato">{{number_format($indicador->trim1)}}</td>
		<td class="dato">{{number_format($indicador->trim2)}}</td>
		<td class="dato">{{number_format($indicador->trim3)}}</td>
		<td class="dato">{{number_format($indicador->trim4)}}</td>
		<td class="dato">{{number_format($indicador->valorNumerador)}}</td>
		<td></td>
	</tr>
	<tr><td colspan="6" height="5">&nbsp;</td></tr>
</table>
@endforeach