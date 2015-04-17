<table class="tabla" width="100%">
	<tr>
		<td rowspan="4" class="imagen izquierda">
			<img src="{{ URL::to('img/EscudoGobiernoChiapas.png') }}">
		</td>
		<td class="titulo" nowrap="nowrap">Secretaría de Planeación, Gestión Pública y Programa de Gobierno</td>
		<td rowspan="4" class="imagen derecha">
			<img src="{{ URL::to('img/Marca.png') }}">
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
	<tr><td colspan="18" class="dato"><strong>Programa presupuestario:</strong> {{ $data['datosProgramaPresupuestario']->clave.' '.$data['datosProgramaPresupuestario']->descripcion }}</td></tr>
	<tr><td colspan="18" class="dato"><strong>Proyecto:</strong> {{ $data['nombreTecnico'] }}</td></tr>
	<?php
	$cobertura = '';
	switch ($data['cobertura']->clave) {
		case 'M':
			$cobertura = $data['municipio']->nombre;
			break;
		case 'R':
			$cobertura = $data['region']->descripcion;
			break;
		default:
			$cobertura = 'Chiapas';
			break;
	}
	?>
	<tr><td colspan="18" class="dato"><strong>Cobertura\municipio:</strong> {{$cobertura}}</td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>ALINEACIÓN AL PED</strong></td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" class="dato"><strong>Eje rector:</strong> {{$data['ejeRectorDescripcion']}}</td></tr>
	<tr><td colspan="18" class="dato"><strong>Política pública:</strong> {{$data['politicaPublicaDescripcion']}}</td></tr>
	<tr><td colspan="18" class="dato"><strong>Objetivo:</strong> {{$data['objetivoPEDDescripcion']}}</td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr><td colspan="18" align="center" class="encabezado"><strong>ALINEACIÓN A LOS OBJETIVOS DE DESARROLLO DEL MILENIO</strong></td></tr>
	<tr>
		<td colspan="5"><strong>Alineación específica:</strong></td>
		<td colspan="5"></td>
		<td colspan="8"><strong>Alineación general:</strong></td>
	</tr>
	<tr>
		<td colspan="5" class="dato">{{$data['fibap']['alineacionEspecifica']}}</td>
		<td colspan="5"></td>
		<td colspan="8" class="dato">{{$data['fibap']['alineacionGeneral']}}</td>
	</tr>
	<tr><td colspan="18" height="5"></td></tr>
</table>
<table class="tabla" width="100%">
	<tr>
		<td colspan="13" align="center" class="encabezado"><strong>DOCUMENTACIÓN DE SOPORTE</strong></td>
		<td></td>
		<td colspan="4" align="center" class="encabezado"><strong>BENEFICIARIOS</strong></td>
	</tr>
	<tr>
		<td colspan="2">Estudio de impacto ambiental</td>
		<td class="dato">
			@if(in_array(1, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td colspan="3">Convenio o acuerdo</td>
		<td class="dato">
			@if(in_array(5, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="dato-metas texto-centro" rowspan="2" valign="middle">Tipo</td>
		<td class="dato-metas texto-centro" rowspan="2" valign="middle">Cantidad total</td>
		<td class="dato-metas texto-centro" colspan="2">GENERO</td>
	</tr>
	<tr>
		<td colspan="2">Aceptación de la comunidad</td>
		<td class="dato">
			@if(in_array(2, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td colspan="3">Factibilidad de uso de suelo</td>
		<td class="dato">
			@if(in_array(6, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td colspan="3">Acta de validación sectorial</td>
		<td class="dato">
			@if(in_array(9, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td class="dato-metas">Femenino</td>
		<td class="dato-metas">Masculino</td>
	</tr>
	<tr>
		<td colspan="2">Estudio técnico y economico</td>
		<td class="dato">
			@if(in_array(3, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td colspan="3">Proyecto ejecutivo</td>
		<td class="dato">
			@if(in_array(7, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td colspan="3">Estudio de riesgi emitido por IPC</td>
		<td class="dato">
			@if(in_array(10, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td class="dato">$data['tipoBeneficiario']->descripcion</td>
		<td class="dato">{{ $data['totalBeneficiarios'] }}</td>
		<td class="dato">{{ $data['totalBeneficiariosF'] }}</td>
		<td class="dato">{{ $data['totalBeneficiariosM'] }}</td>
	</tr>
	<tr>
		<td colspan="2">Principal normatividad (ROP, manual de procedimientos, manual de operación)</td>
		<td class="dato">
			@if(in_array(4, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td colspan="3">Certificado de propiedad del terreno</td>
		<td class="dato">
			@if(in_array(8, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td></td>
		<td colspan="3">Acta de validación de COPLADER y CPR.</td>
		<td class="dato">
			@if(in_array(11, array_fetch($data['fibap']['documentos'],'id')))
				X
			@endif
		</td>
		<td colspan="5"></td>
	</tr>
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
		<td colspan="3" class="dato">{{$antecedente['autorizado']}}</td>
		<td></td>
		<td class="dato">{{$antecedente['ejercido']}}</td>
		<td></td>
		<td class="dato">{{$antecedente['porcentaje']}}</td>
		<td></td>
		<td></td>
		<td colspan="7" class="dato">{{$antecedente['fechaCorte']}}</td>
	</tr>
	@endforeach
	
	<tr><td colspan="18" height="5"></td></tr>

	<tr>
		<td colspan="9" align="center" class="encabezado">RESULTADOS OBTENIDOS</td>
		<td></td>
		<td></td>
		<td colspan="7" align="center" class="encabezado">RESULTADOS ESPERADOS</td>
	</tr>
	<tr>
		<td colspan="9" rowspan="2" class="dato">{{$data['fibap']['resultadosObtenidos']}}</td>
		<td></td>
		<td></td>
		<td colspan="7" rowspan="2" class="dato">{{$data['fibap']['resultadosEsperados']}}</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
	</tr>
	<tr><td colspan="18" align="center" class="encabezado"><strong>JUSTIFICACIÓN DEL PROYECTO</strong></td></tr>
	<tr><td colspan="18" rowspan="2" class="dato">{{$data['fibap']['justificacionProyecto']}}</td></tr>
	<tr></tr>
	<tr><td colspan="18" align="center" class="encabezado"><strong>DESCRIPCIÓN DEL PROYECTO</strong></td></tr>
	<tr><td colspan="18" class="dato">Objetivo del proyecto: {{$data['fibap']['objetivoProyecto']}}</td></tr>
	<tr><td colspan="18" align="center" class="encabezado"><strong>PRESUPUESTO REQUERIDO Y PROPUESTA DE FINANCIAMIENTO</strong></td></tr>

	<tr><td colspan="18" height="5"></td></tr>

	<tr>
		<td colspan="2">Presupuesto requerido:</td>
		<td colspan="3" class="dato">{{number_format($data['fibap']['presupuestoRequerido'])}}</td>
		<td></td>
		<td>Estatal:</td>
		<td colspan="4" class="dato">
			@if(($valor = array_search('1', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
		<td colspan="3">Beneficiarios:</td>
		<td colspan="3" class="dato">
			@if(($valor = array_search('4', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Periodo de ejecución:</td>
		<td colspan="3" class="dato"></td>
		<td>Origen:</td>
		<td>Municipal:</td>
		<td colspan="4" class="dato">
			@if(($valor = array_search('2', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
		<td colspan="3">Crédito:</td>
		<td colspan="3" class="dato">
			@if(($valor = array_search('5', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ number_format($data['fibap']['propuestas_financiamiento'][$valor]['cantidad']) }}
			@else
				0
			@endif
		</td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>Federal:</td>
		<td colspan="4" class="dato">
			@if(($valor = array_search('3', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
			@else
				0
			@endif
		</td>
		<td colspan="3">Otros:</td>
		<td colspan="3" class="dato">
			@if(($valor = array_search('6', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
				{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
			@else
				0
			@endif
		</td>
		<td></td>
	</tr>

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
		<td colspan="4" class="dato">{{number_format($distribucion['cantidad'])}}</td>
		<td></td>
		<td class="dato">{{ number_format( $distribucion['cantidad']/$data['fibap']['presupuestoRequerido'] * 100, 2 ) }}</td>
		<td></td>
	</tr>
	@endforeach
	<tr><td colspan="18" height="5"></td></tr>
</table>