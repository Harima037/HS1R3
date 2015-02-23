<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
		<tr><td colspan="18" align="center"><strong>Secretaría de Planeación, Gestión Pública y Programa de Gobierno</strong></td></tr>
		<tr><td colspan="18" align="center"><strong>FICHA DE INFORMACIÓN BÁSICA DEL PROYECTO 2014</strong></td></tr>
		<tr><td colspan="18"></td></tr>
		<tr><td colspan="18" align="center">Tipo:</td></tr>
		<tr><td colspan="18"></td></tr>
		<tr><td colspan="18"><strong>Organismo público: {{$data['fibap']['organismoPublico']}}</strong></td></tr>
		<tr><td colspan="18"></td></tr>
		<tr>
			<td colspan="5"><strong>Sector:</strong> {{$data['fibap']['sector']}}</td>
			<td colspan="6"><strong>Subcomité:</strong> {{$data['fibap']['subcomite']}}</td>
			<td colspan="7"><strong>Grupo de trabajo:</strong> {{$data['fibap']['grupoTrabajo']}}</td>
		</tr>
		<tr><td colspan="18"><strong>Programa presupuestario:</strong> {{ $data['datosProgramaPresupuestario']->clave.' '.$data['datosProgramaPresupuestario']->descripcion }}</td></tr>
		<tr><td colspan="18"><strong>Proyecto:</strong> {{ $data['nombreTecnico'] }}</td></tr>
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
		<tr><td colspan="18"><strong>Cobertura\municipio:</strong> {{$cobertura}}</td></tr>
		<tr><td colspan="18"></td></tr>
		<tr><td colspan="18" align="center" style="background-color:#CCCCCC;"><strong>ALINEACIÓN AL PED</strong></td></tr>
		<tr><td colspan="18"></td></tr>
		<tr><td colspan="18"><strong>Eje rector:</strong></td></tr>
		<tr><td colspan="18"><strong>Política pública:</strong></td></tr>
		<tr><td colspan="18"><strong>Objetivo:</strong> {{$data['objetivo_ped']->descripcion}}</td></tr>
		<tr><td colspan="18"></td></tr>
		<tr><td colspan="18" align="center" style="background-color:#CCCCCC;"><strong>ALINEACIÓN A LOS OBJETIVOS DE DESARROLLO DEL MILENIO</strong></td></tr>
		<tr>
			<td colspan="5"><strong>Alineación específica:</strong></td>
			<td colspan="5"></td>
			<td colspan="8"><strong>Alineación general:</strong></td>
		</tr>
		<tr>
			<td colspan="5">{{$data['fibap']['alineacionEspecifica']}}</td>
			<td colspan="5"></td>
			<td colspan="8">{{$data['fibap']['alineacionGeneral']}}</td>
		</tr>
		<tr><td colspan="18"></td></tr>
		<tr>
			<td colspan="13" align="center" style="background-color:#CCCCCC;"><strong>DOCUMENTACIÓN DE SOPORTE</strong></td>
			<td></td>
			<td colspan="4" align="center" style="background-color:#CCCCCC;"><strong>BENEFICIARIOS</strong></td>
		</tr>
		<tr>
			<td colspan="2">Estudio de impacto ambiental</td>
			<td>
				@if(in_array(1, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td colspan="3">Convenio o acuerdo</td>
			<td>
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
			<td rowspan="2" align="center" valign="middle">Tipo</td>
			<td rowspan="2" align="center" valign="middle">Cantidad total</td>
			<td colspan="2" align="center">GENERO</td>
		</tr>
		<tr>
			<td colspan="2">Aceptación de la comunidad</td>
			<td>
				@if(in_array(2, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td colspan="3">Factibilidad de uso de suelo</td>
			<td>
				@if(in_array(6, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td colspan="3">Acta de validación sectorial</td>
			<td>
				@if(in_array(9, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td>Femenino</td>
			<td>Masculino</td>
		</tr>
		<tr>
			<td colspan="2">Estudio técnico y economico</td>
			<td>
				@if(in_array(3, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td colspan="3">Proyecto ejecutivo</td>
			<td>
				@if(in_array(7, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td colspan="3">Estudio de riesgi emitido por IPC</td>
			<td>
				@if(in_array(10, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td>{{ $data['tipoBeneficiario']->descripcion }}</td>
			<td>{{ $data['totalBeneficiarios'] }}</td>
			<td>{{ $data['totalBeneficiariosF'] }}</td>
			<td>{{ $data['totalBeneficiariosM'] }}</td>
		</tr>
		<tr>
			<td colspan="2">Principal normatividad (ROP, manual de procedimientos, manual de operación)</td>
			<td>
				@if(in_array(4, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td colspan="3">Certificado de propiedad del terreno</td>
			<td>
				@if(in_array(8, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
			<td></td>
			<td colspan="3">Acta de validación de COPLADER y CPR.</td>
			<td>
				@if(in_array(11, array_fetch($data['fibap']['documentos'],'id')))
					X
				@endif
			</td>
		</tr>
		<tr><td colspan="18"></td></tr>
		<tr>
			<td colspan="9" align="center" style="background-color:#CCCCCC;">ANTECEDENTES FINANCIEROS</td>
			<td></td>
			<td></td>
			<td colspan="7" align="center" style="background-color:#CCCCCC;">FECHA DE CORTE DE LA INFORMACIÓN</td>
		</tr>
		<tr><td colspan="18"></td></tr>
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
			<td>{{$antecedente['anio']}}</td>
			<td></td>
			<td colspan="3">{{$antecedente['autorizado']}}</td>
			<td></td>
			<td>{{$antecedente['ejercido']}}</td>
			<td></td>
			<td>{{$antecedente['porcentaje']}}</td>
			<td></td>
			<td></td>
			<td colspan="7">{{$antecedente['fechaCorte']}}</td>
		</tr>
		@endforeach
		
		<tr><td colspan="18"></td></tr>
		<tr>
			<td colspan="9" align="center" style="background-color:#CCCCCC;">RESULTADOS OBTENIDOS</td>
			<td></td>
			<td></td>
			<td colspan="7" align="center" style="background-color:#CCCCCC;">RESULTADOS ESPERADOS</td>
		</tr>
		<tr>
			<td colspan="9" rowspan="2">{{$data['fibap']['resultadosObtenidos']}}</td>
			<td></td>
			<td></td>
			<td colspan="7" rowspan="2">{{$data['fibap']['resultadosEsperados']}}</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
		</tr>
		<tr><td colspan="18" align="center" style="background-color:#CCCCCC;"><strong>JUSTIFICACIÓN DEL PROYECTO</strong></td></tr>
		<tr><td colspan="18" rowspan="2">{{$data['fibap']['justificacionProyecto']}}</td></tr>
		<tr></tr>
		<tr><td colspan="18" align="center" style="background-color:#CCCCCC;"><strong>DESCRIPCIÓN DEL PROYECTO</strong></td></tr>
		<tr><td colspan="18">Objetivo del proyecto: {{$data['fibap']['objetivoProyecto']}}</td></tr>
		<tr><td colspan="18" align="center" style="background-color:#CCCCCC;"><strong>PRESUPUESTO REQUERIDO Y PROPUESTA DE FINANCIAMIENTO</strong></td></tr>
		<tr><td colspan="18"></td></tr>
		<tr>
			<td colspan="2">Presupuesto requerido:</td>
			<td colspan="3">{{$data['fibap']['presupuestoRequerido']}}</td>
			<td></td>
			<td>Estatal:</td>
			<td colspan="4">
				@if(($valor = array_search('1', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
					{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
				@else
					0
				@endif
			</td>
			<td colspan="3">Beneficiarios:</td>
			<td colspan="3">
				@if(($valor = array_search('4', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
					{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
				@else
					0
				@endif
			</td>
		</tr>
		<tr>
			<td colspan="2">Periodo de ejecución:</td>
			<td colspan="3"></td>
			<td>Origen:</td>
			<td>Municipal:</td>
			<td colspan="4">
				@if(($valor = array_search('2', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
					{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
				@else
					0
				@endif
			</td>
			<td colspan="3">Crédito:</td>
			<td colspan="3">
				@if(($valor = array_search('5', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
					{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
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
			<td colspan="4">
				@if(($valor = array_search('3', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
					{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
				@else
					0
				@endif
			</td>
			<td colspan="3">Otros:</td>
			<td colspan="3">
				@if(($valor = array_search('6', array_fetch($data['fibap']['propuestas_financiamiento'],'idOrigenFinanciamiento')))!== false)
					{{ $data['fibap']['propuestas_financiamiento'][$valor]['cantidad'] }}
				@else
					0
				@endif
			</td>
		</tr>
		<tr><td colspan="18"></td></tr>
		<tr><td colspan="18" align="center" style="background-color:#CCCCCC;"><strong>DISTRIBUCIÓN DEL PRESUPUESTO ESTATAL</strong></td></tr>
		<tr><td colspan="18"></td></tr>
		<tr>
			<td colspan="5">Capítulo, concepto y partida</td>
			<td></td>
			<td colspan="5">Descripción</td>
			<td colspan="4" align="center">Cantidad</td>
			<td></td>
			<td align="center">%</td>
		</tr>
		@foreach($data['fibap']['distribucion_presupuesto_agrupado'] as $distribucion)
		<tr>
			<td colspan="5">{{$distribucion['objeto_gasto']['clave']}}</td>
			<td></td>
			<td colspan="5">{{$distribucion['objeto_gasto']['descripcion']}}</td>
			<td colspan="4">{{$distribucion['cantidad']}}</td>
			<td></td>
			<td>{{ number_format( $distribucion['cantidad']/$data['fibap']['presupuestoRequerido'] * 100, 2 ) }}</td>
		</tr>
		@endforeach
		<tr><td colspan="18"></td></tr>
	</table>
</body>
</html>