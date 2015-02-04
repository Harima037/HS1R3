<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="2"></td>
			<td colspan="10" align="center">GOBIERNO DEL ESTADO DE CHIAPAS</td>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td colspan="10" align="center">SECRETARÍA DE SALUD</td>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td colspan="10" align="center">INSTITUTO DE SALUD</td>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td colspan="10" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td colspan="10" align="center">DEPARTAMENTO DE INTEGRACION PROGRAMATICA PRESUPUESTAL</td>
			<td colspan="3"></td>
		</tr>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="15" align="center" style="background-color:#CCCCCC;">PROYECTO DE INVERSIÓN</td>
		</tr>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">UNIDAD RESPONSABLE</td>
			<td colspan="13">{{ $data['datos_unidad_responsable']->id.' '.$data['datos_unidad_responsable']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">FINALIDAD:</td>
			<td colspan="13">{{ $data['datos_finalidad']->clave.' '.$data['datos_finalidad']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">FUNCIÓN:</td>
			<td colspan="13">{{ $data['datos_funcion']->clave.' '.$data['datos_funcion']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">SUB FUNCIÓN:</td>
			<td colspan="13">{{ $data['datos_sub_funcion']->clave.' '.$data['datos_sub_funcion']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">SUB SUB FUNCIÓN:</td>
			<td colspan="13">{{ $data['datos_sub_sub_funcion']->clave.' '.$data['datos_sub_sub_funcion']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">PROGRAMA SECTORIAL:</td>
			<td colspan="13">{{ $data['datosProgramaSectorial']->clave.' '.$data['datosProgramaSectorial']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">PROGRAMA PRESUPUESTARIO:</td>
			<td colspan="13">{{ $data['datosProgramaPresupuestario']->clave.' '.$data['datosProgramaPresupuestario']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">PROGRAMA ESPECIAL:</td>
			<td colspan="13">{{ $data['datosProgramaEspecial']->clave.' '.$data['datosProgramaEspecial']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">ACTIVIDAD INSTITUCIONAL:</td>
			<td colspan="6">{{ $data['datosActividadInstitucional']->clave.' '.$data['datosActividadInstitucional']->descripcion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">PROYECTO ESTRATEGICO:</td>
			<td colspan="4">{{ $data['datosProyectoEstrategico']->clave.' '.$data['datosProyectoEstrategico']->descripcion }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">NÚMERO DE PROYECTO ESTRATEGICO:</td>
			<td>{{ $data['numeroProyectoEstrategico'] }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">VINCULACIÓN AL PED:</td>
			<td colspan="4">{{ $data['objetivoPED']->clave.' '.$data['objetivoPED']->descripcion }}</td>
		</tr>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">NOMBRE TÉCNICO:</td>
			<td colspan="10">{{ $data['nombreTecnico'] }}</td>
		</tr>
		<tr>
			<td style="background-color:#CCCCCC;border:1px solid #000">TIPO DE PROYECTO:</td>
			<td>{{ $data['tipoProyecto']->clave.' '.$data['tipoProyecto']->descripcion }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">COBERTURA:</td>
			<td>{{ $data['cobertura']->clave.' '.$data['cobertura']->descripcion }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">TIPO DE ACCIÓN:	</td>
			<td>{{ $data['tipoAccion']->clave.' '.$data['tipoAccion']->descripcion }}</td>
		</tr>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">LIDER DEL PROYECTO:</td>
			<td colspan="9"></td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">JEFE INMEDIATO AL LIDER:</td>
			<td colspan="9"></td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">JEFE DE PLANEACIÓN:</td>
			<td colspan="9"></td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">COORDINADOR DEL GRUPO ESTRATEGICO:</td>
			<td colspan="9"></td>
		</tr>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="15" align="center" style="background-color: #CCCCCC;">BENEFICIARIOS</td>
		</tr>
		<tr>
			<td rowspan="2" valign="middle" style="background-color: #CCCCCC;">DESCRIPCIÓN DE BENEFICIARIO</td>
			<td colspan="2" rowspan="2" align="center" valign="middle" style="background-color: #CCCCCC;">TOTAL</td>
			<td rowspan="2" style="background-color: #CCCCCC;" align="center" valign="middle">GENERO</td>
			<td colspan="2" align="center" style="background-color: #CCCCCC;">ZONA</td>
			<td colspan="4" align="center" style="background-color: #CCCCCC;">POBLACIÓN</td>
			<td colspan="5" align="center" style="background-color: #CCCCCC;">MARGINACIÓN</td>
		</tr>
		<tr>
			<td></td>
			<td align="center" style="background-color:#CCCCCC;">URBANA</td>
			<td align="center" style="background-color:#CCCCCC;">RURAL</td>
			<td align="center" style="background-color:#CCCCCC;">MESTIZA</td>
			<td align="center" style="background-color:#CCCCCC;">INDIGENA</td>
			<td align="center" style="background-color:#CCCCCC;">INMIGRANTE</td>
			<td align="center" style="background-color:#CCCCCC;">OTROS</td>
			<td align="center" style="background-color:#CCCCCC;">MUY ALTA</td>
			<td align="center" style="background-color:#CCCCCC;">ALTA</td>
			<td align="center" style="background-color:#CCCCCC;">MEDIA</td>
			<td align="center" style="background-color:#CCCCCC;">BAJA</td>
			<td align="center" style="background-color:#CCCCCC;">MUY BAJA</td>
		</tr>
		<tr>
			<td rowspan="2" valign="middle">{{ $data['tipoBeneficiario']->descripcion }}</td>
			<td rowspan="2" valign="middle">{{ $data['totalBeneficiarios'] }}</td>
			<td>{{ $data['totalBeneficiariosF'] }}</td>
			<td>FEMENINO</td>
			@foreach($data['beneficiarios'] as $beneficiario)
			 @if($beneficiario->sexo=='f')
			<td>{{ $beneficiario->urbana }}</td>
			<td>{{ $beneficiario->rural }}</td>
			<td>{{ $beneficiario->mestiza }}</td>
			<td>{{ $beneficiario->indigena }}</td>
			<td>{{ $beneficiario->inmigrante }}</td>
			<td>{{ $beneficiario->otros }}</td>
			<td>{{ $beneficiario->muyAlta }}</td>
			<td>{{ $beneficiario->alta }}</td>
			<td>{{ $beneficiario->media }}</td>
			<td>{{ $beneficiario->baja }}</td>
			<td>{{ $beneficiario->muyBaja }}</td>
			 @endif
			@endforeach
		</tr>
		<tr>
			<td>{{ $data['totalBeneficiariosM'] }}</td>
			<td>MASCULINO</td>
			@foreach($data['beneficiarios'] as $beneficiario)
			 @if($beneficiario->sexo=='m')
			<td>{{ $beneficiario->urbana }}</td>
			<td>{{ $beneficiario->rural }}</td>
			<td>{{ $beneficiario->mestiza }}</td>
			<td>{{ $beneficiario->indigena }}</td>
			<td>{{ $beneficiario->inmigrante }}</td>
			<td>{{ $beneficiario->otros }}</td>
			<td>{{ $beneficiario->muyAlta }}</td>
			<td>{{ $beneficiario->alta }}</td>
			<td>{{ $beneficiario->media }}</td>
			<td>{{ $beneficiario->baja }}</td>
			<td>{{ $beneficiario->muyBaja }}</td>
			 @endif
			@endforeach
		</tr>
		
		@foreach($data['componentes'] as $componente)
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="15" align="center" style="background-color: #CCCCCC;">OBJETIVOS DEL COMPONENTE</td>
		</tr>
		@if($data['idClasificacionProyecto']== 2)
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">ENTREGABLE:</td>
			<td colspan="2">{{ $componente->entregable->descripcion }}</td>
			<td colspan="3" style="background-color: #CCCCCC;">TIPO</td>
			<td colspan="2">{{ ($componente->entregableTipo) ? $componente->entregableTipo->descripcion : 'N / A' }}</td>
			<td colspan="3" style="background-color: #CCCCCC;">ACCIÓN</td>
			<td colspan="2">{{ $componente->entregableAccion->descripcion }}</td>
		</tr>
		@else
		<tr><td colspan="15"></td></tr>
		@endif
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">DESCRIPCIÓN:</td>
			<td colspan="13">{{ $componente->objetivo }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">MEDIOS DE VERIFICACIÓN:</td>
			<td colspan="13">{{ $componente->mediosVerificacion }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">SUPUESTOS:</td>
			<td colspan="13">{{ $componente->supuestos }}</td>
		</tr>
		<tr></tr>

		<tr>
			<td colspan="9" align="center" style="background-color: #CCCCCC;">INDICADOR DEL COMPONENTE (MIR)</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">DESCRIPCIÓN:</td>
			<td colspan="8">{{ $componente->indicador }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">FORMULA:</td>
			<td colspan="3">{{ $componente->formula->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">NUMERADOR:</td>
			<td colspan="8">{{ $componente->numerador }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">DIMENSIÓN:</td>
			<td colspan="3">{{ $componente->dimension->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">DENOMINADOR:</td>
			<td colspan="8">{{ $componente->denominador }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">FRECUENCIA:</td>
			<td colspan="3">{{ $componente->frecuencia->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">INTERPRETACIÓN:</td>
			<td colspan="8">{{ $componente->interpretacion }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">TIPO:</td>
			<td colspan="3">{{ $componente->tipoIndicador->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">NUMERO TRIMESTRE 1:</td>
			<td colspan="3" style="background-color: #CCCCCC;">NUMERO TRIMESTRE 2:</td>
			<td colspan="3" style="background-color: #CCCCCC;">NUMERO TRIMESTRE 3:</td>
			<td colspan="3" style="background-color: #CCCCCC;">NUMERO TRIMESTRE 4:</td>
			<td colspan="2" style="background-color: #CCCCCC;">UNIDAD DE MEDIDA:</td>
			<td colspan="3">{{ $componente->unidadMedida->descripcion }}</td>
		</tr>
		<tr>
			<td>{{ $componente->numeroTrim1 }}</td>
			<td colspan="3">{{ $componente->numeroTrim2 }}</td>
			<td colspan="3">{{ $componente->numeroTrim3 }}</td>
			<td colspan="3">{{ $componente->numeroTrim4 }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">META INDICADOR:</td>
			<td colspan="3">{{ $componente->metaIndicador }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">NUMERADOR:</td>
			<td colspan="2">{{ $componente->valorNumerador }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">DENOMINADOR:</td>
			<td colspan="2">{{ $componente->valorDenominador }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">LÍNEA BASE:</td>
			<td colspan="2">{{ $componente->lineaBase }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">AÑO LÍNEA BASE:</td>
			<td colspan="2">{{ $componente->anioBase }}</td>
		</tr>

        @foreach($componente->actividades as $actividad)
        <tr></tr>
		<tr>
			<td colspan="15" align="center" style="background-color: #CCCCCC;">OBJETIVOS DE LA ACTIVIDAD</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">DESCRIPCIÓN:</td>
			<td colspan="14">{{ $actividad->objetivo }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">MEDIOS DE VERIFICACIÓN:</td>
			<td colspan="14">{{ $actividad->mediosVerificacion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">SUPUESTOS:</td>
			<td colspan="14">{{ $actividad->supuestos }}</td>
		</tr>
		<tr></tr>

		<tr>
			<td colspan="9" align="center" style="background-color: #CCCCCC;">INDICADOR DE LA ACTIVIDAD</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">DESCRIPCIÓN:</td>
			<td colspan="8">{{ $actividad->indicador }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">FORMULA:</td>
			<td colspan="3">{{ $actividad->formula->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">NUMERADOR:</td>
			<td colspan="8">{{ $actividad->numerador }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">DIMENSIÓN:</td>
			<td colspan="3">{{ $actividad->dimension->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">DENOMINADOR:</td>
			<td colspan="8">{{ $actividad->denominador }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">FRECUENCIA:</td>
			<td colspan="3">{{ $actividad->frecuencia->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">INTERPRETACIÓN:</td>
			<td colspan="8">{{ $actividad->interpretacion }}</td>
			<td></td>
			<td colspan="2" style="background-color: #CCCCCC;">TIPO:</td>
			<td colspan="3">{{ $actividad->tipoIndicador->descripcion }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">NUMERO TRIMESTRE 1:</td>
			<td colspan="3" style="background-color: #CCCCCC;">NUMERO TRIMESTRE 2:</td>
			<td colspan="3" style="background-color: #CCCCCC;">NUMERO TRIMESTRE 3:</td>
			<td colspan="3" style="background-color: #CCCCCC;">NUMERO TRIMESTRE 4:</td>
			<td colspan="2" style="background-color: #CCCCCC;">UNIDAD DE MEDIDA:</td>
			<td colspan="3">{{ $actividad->unidadMedida->descripcion }}</td>
		</tr>
		<tr>
			<td>{{ $actividad->numeroTrim1 }}</td>
			<td colspan="3">{{ $actividad->numeroTrim2 }}</td>
			<td colspan="3">{{ $actividad->numeroTrim3 }}</td>
			<td colspan="3">{{ $actividad->numeroTrim4 }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">META INDICADOR:</td>
			<td colspan="3">{{ $actividad->metaIndicador }}</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">NUMERADOR:</td>
			<td colspan="2">{{ $actividad->valorNumerador }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">DENOMINADOR:</td>
			<td colspan="2">{{ $actividad->valorDenominador }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">LÍNEA BASE:</td>
			<td colspan="2">{{ $actividad->lineaBase }}</td>
			<td colspan="2" style="background-color: #CCCCCC;">AÑO LÍNEA BASE:</td>
			<td colspan="2">{{ $actividad->anioBase }}</td>
		</tr>
		<tr>
			<td colspan="2" style="background-color: #CCCCCC;">INDICADOR COMPONENTE:</td>
			<td colspan="13"></td>
		</tr>
		
		@endforeach

        @endforeach
        @if($data['idClasificacionProyecto']== 2)
        <tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="15" align="center" style="background-color: #CCCCCC;">DESGLOSE DEL COMPONENTE</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">MUNICIPIO:</td>
			<td colspan="3"></td>
			<td colspan="2" style="background-color: #CCCCCC;">LOCALIDAD:</td>
			<td colspan="8"></td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;border:1px solid #000">BENEFICIARIO:</td>
			<td colspan="3"></td>
			<td colspan="2" style="background-color: #CCCCCC;">CANTIDAD:</td>
			<td colspan="2"></td>
			<td colspan="3" style="background-color: #CCCCCC;">PRESUPUESTO:</td>
			<td colspan="3"></td>
		</tr>
		<tr><td colspan="15"></td></tr>

		<tr>
			<td colspan="3" style="background-color: #CCCCCC;">CANTIDAD DENOMINADOR:</td>
		</tr>
		<tr>
			<td style="background-color: #CCCCCC;">TRIMESTRE 1:</td>
			<td colspan="2"></td>
			<td colspan="2" style="background-color: #CCCCCC;">TRIMESTRE 2:</td>
			<td colspan="2"></td>
			<td colspan="2" style="background-color: #CCCCCC;">TRIMESTRE 3:</td>
			<td colspan="2"></td>
			<td colspan="2" style="background-color: #CCCCCC;">TRIMESTRE 4:</td>
			<td colspan="2"></td>
		</tr>
		@endif
		<tr><td colspan="15"></td></tr>

		<tr>
			<td colspan="5"></td>
			<td colspan="5"></td>
			<td colspan="5"></td>
		</tr>

		<tr>
			<td colspan="5">Jefe Inmediato Superior al Lider del Proyecto.</td>
			<td colspan="5"></td>
			<td colspan="5">Lider del proyecto.</td>
		</tr>
		<tr></tr>
		<tr>
			<td colspan="5"></td>
			<td colspan="5"></td>
			<td colspan="5"></td>
		</tr>
		<tr>
			<td colspan="5">Coordinador General del Grupo Estratégico.Coordinador General del Grupo Estratégico.</td>
			<td colspan="5"></td>
			<td colspan="5">Responsable de la Unida de Planeación u Homólogo.</td>
		</tr>
	</table>
</body>
</html>