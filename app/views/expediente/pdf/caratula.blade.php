<table class="tabla" width="100%">
	<tr><td height="5" colspan="6"></td></tr>
	<tr>
		<td colspan="6" align="center" class="encabezado">
			PROYECTO {{ ($data['idClasificacionProyecto'] == 2) ? 'DE INVERSIÓN':'INSTITUCIONAL' }}
		</td>
	</tr>
	<tr><td height="5" colspan="6"></td></tr>
	<tr>
		<td class="encabezado">UNIDAD RESPONSABLE</td>
		<td colspan="5" class="dato">
			{{ $data['unidadResponsableDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">FINALIDAD:</td>
		<td colspan="5" class="dato">
			{{ $data['finalidadDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">FUNCIÓN:</td>
		<td colspan="5" class="dato">
			{{ $data['funcionDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">SUB FUNCIÓN:</td>
		<td colspan="5" class="dato">
			{{ $data['subFuncionDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">SUB SUB FUNCIÓN:</td>
		<td colspan="5" class="dato">
			{{ $data['subSubFuncionDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">PROGRAMA SECTORIAL:</td>
		<td colspan="5" class="dato">
			{{ $data['programaSectorialDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">PROGRAMA PRESUPUESTARIO:</td>
		<td colspan="5" class="dato">
			{{ $data['programaPresupuestarioDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">ORIGEN DE ASIGNACIÓN:</td>
		<td colspan="5" class="dato">
			{{ $data['origenAsignacionDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">ACTIVIDAD INSTITUCIONAL:</td>
		<td colspan="5" class="dato">
			{{ $data['actividadInstitucionalDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">PROYECTO ESTRATEGICO:</td>
		<td class="dato">{{ $data['proyectoEstrategicoDescripcion'] }}</td>
		<td class="encabezado" width="1" style="white-space:nowrap;">NÚMERO DE PROYECTO ESTRATEGICO:</td>
		<td class="dato">{{ $data['numeroProyectoEstrategico'] }}</td>
		<td colspan="2"></td>
	</tr>
	<tr><td height="5" colspan="6"></td></tr>
</table>

<table class="tabla" width="100%">
	<tr>
		<td class="encabezado">ESTRATEGIA DEL OBJETIVO DEL PLAN NACIONAL:</td>
		<td colspan="5" class="dato">
			{{ $data['estrategiaNacionalDescripcion'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">OBJETIVO ESTRATEGICO:</td>
		<td colspan="5" class="dato">
			{{ $data['objetivoEstrategico'] }}
		</td>
	</tr>
	<tr>
		<td class="encabezado">ALINEACIÓN:</td>
		<td class="dato" width="1" style="white-space:nowrap;">{{ $data['claveAlineacion'] }}</td>
		<td class="encabezado" width="1" style="white-space:nowrap;">ESTRATEGIA DEL PLAN ESTATAL:</td>
		<td class="dato">{{ $data['estrategiaEstatalDescripcion'] }}</td>
		<td class="encabezado" width="1" style="white-space:nowrap;">OBJETIVO DEL PLAN ESTATAL:</td>
		<td class="dato">{{ $data['objetivoPEDDescripcion'] }}</td>
	</tr>
	<tr><td height="5" colspan="6"></td></tr>
</table>
<table class="tabla" width="100%">
	<tr>
		<td width="180" class="encabezado">NOMBRE TÉCNICO:</td>
		<td colspan="9" class="dato">{{ $data['nombreTecnico'] }}</td>
	</tr>
	<tr>
		<td class="encabezado">FINALIDAD DEL PROYECTO:</td>
		<td colspan="9" class="dato">{{ $data['finalidadProyecto'] }}</td>
	</tr>
	<tr>
		<td class="encabezado">TIPO DE PROYECTO:</td>
		<td class="dato" width="70">{{ $data['tipoProyectoDescripcion'] }}</td>
		<td class="encabezado">COBERTURA:</td>
		<td class="dato">{{ $data['coberturaDescripcion'] }}</td>
		<td class="encabezado">TIPO DE ACCIÓN:	</td>
		<td class="dato">{{ $data['tipoAccionDescripcion'] }}</td>
		<td class="encabezado">FECHA INICIO:</td>
		<td class="dato">{{ $data['fechaInicio'] }}</td>
		<td class="encabezado">FECHA TERMINO:	</td>
		<td class="dato">{{ $data['fechaTermino'] }}</td>
	</tr>

	<tr><td height="5" colspan="10"></td></tr>

	<tr>
		<td colspan="2" class="encabezado">LIDER DEL PROYECTO:</td>
		<td colspan="8" class="dato">{{ $data['liderProyecto'] }}</td>
	</tr>
	<tr>
		<td colspan="2" class="encabezado">JEFE INMEDIATO AL LIDER:</td>
		<td colspan="8" class="dato">{{ $data['jefeInmediato'] }}</td>
	</tr>
	<tr>
		<td colspan="2" class="encabezado">JEFE DE PLANEACIÓN:</td>
		<td colspan="8" class="dato">{{ $data['jefePlaneacion'] }}</td>
	</tr>
	<tr>
		<td colspan="2" class="encabezado">COORDINADOR DEL GRUPO ESTRATEGICO:</td>
		<td colspan="8" class="dato">{{ $data['coordinadorGrupoEstrategico'] }}</td>
	</tr>

	<tr><td height="5" colspan="10"></td></tr>
</table>
<table class="tabla" width="100%">
	<tr>
		<td colspan="15" align="center" class="encabezado">BENEFICIARIOS</td>
	</tr>
	<tr>
		<td rowspan="2" valign="middle" class="encabezado">DESCRIPCIÓN DE BENEFICIARIO</td>
		<td rowspan="2" colspan="2" align="center" valign="middle" class="encabezado">TOTAL</td>
		<td rowspan="2" class="encabezado" align="center" valign="middle">GENERO</td>
		<td colspan="2" align="center" class="encabezado">ZONA</td>
		<td colspan="4" align="center" class="encabezado">POBLACIÓN</td>
		<td colspan="5" align="center" class="encabezado">MARGINACIÓN</td>
	</tr>
	<tr>
		<td align="center" class="encabezado">URBANA</td>
		<td align="center" class="encabezado">RURAL</td>
		<td align="center" class="encabezado">MESTIZA</td>
		<td align="center" class="encabezado">INDIGENA</td>
		<td align="center" class="encabezado">INMIGRANTE</td>
		<td align="center" class="encabezado">OTROS</td>
		<td align="center" width="60" class="encabezado">MUY ALTA</td>
		<td align="center" class="encabezado">ALTA</td>
		<td align="center" width="60" class="encabezado">MEDIA</td>
		<td align="center" width="60" class="encabezado">BAJA</td>
		<td align="center" width="60" class="encabezado">MUY BAJA</td>
	</tr>

	@foreach ($data['beneficiarios'] as $key => $beneficiario)
	<tr>
		<td class="dato" rowspan="2" valign="middle">{{ $beneficiario['tipo'] }}</td>
		<td class="dato" rowspan="2" valign="middle">{{ number_format($beneficiario['total']) }}</td>
		@foreach ($beneficiario['desglose'] as $key => $desglose)
			@if($key == 'm')
				</tr><tr>
			@endif
				<td class="dato">{{ (isset($desglose['total']))? number_format($desglose['total']) : 0 }}</td>
			@if($key == 'f')
				<td class="dato">FEMENINO</td>
			@else
				<td class="dato">MASCULINO</td>
			@endif
			<td class="dato">{{ (isset($desglose['urbana']))? number_format($desglose['urbana']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['rural']))? number_format($desglose['rural']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['mestiza']))? number_format($desglose['mestiza']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['indigena']))? number_format($desglose['indigena']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['inmigrante']))? number_format($desglose['inmigrante']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['otros']))? number_format($desglose['otros']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['muyAlta']))? number_format($desglose['muyAlta']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['alta']))? number_format($desglose['alta']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['media']))? number_format($desglose['media']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['baja']))? number_format($desglose['baja']) : 0 }}</td>
			<td class="dato">{{ (isset($desglose['muyBaja']))? number_format($desglose['muyBaja']) : 0 }}</td>
		@endforeach
	</tr>	
	@endforeach
	<tr><td height="15" colspan="15"></td></tr>
</table>

<div style="page-break-after:always;"></div>

@foreach($data['componentesCompletoDescripcion'] as $componente)
<table class="tabla" width="100%">
	<tr><td height="7" colspan="15"></td></tr>
	<tr>
		<td colspan="15" class="encabezado texto-centro">OBJETIVOS DEL COMPONENTE</td>
	</tr>

	@if($data['idClasificacionProyecto'] == 2)
		<tr>
			<td colspan="2" class="encabezado">ENTREGABLE:</td>
			<td colspan="2" class="dato">{{ $componente->entregable }}</td>
			<td colspan="3" class="encabezado">TIPO</td>
			<td colspan="2" class="dato">{{ ($componente->entregableTipo) ? $componente->entregableTipo : 'N / A' }}</td>
			<td colspan="3" class="encabezado">ACCIÓN</td>
			<td colspan="3" class="dato">{{ $componente->entregableAccion }}</td>
		</tr>
	@else
		<tr><td height="5" colspan="15"></td></tr>
	@endif

	<tr>
		<td colspan="2" class="encabezado">DESCRIPCIÓN:</td>
		<td colspan="13" class="dato">{{ $componente->objetivo }}</td>
	</tr>
	<tr>
		<td colspan="2" class="encabezado">MEDIOS DE VERIFICACIÓN:</td>
		<td colspan="13" class="dato">{{ $componente->mediosVerificacion }}</td>
	</tr>
	<tr>
		<td colspan="2" class="encabezado">SUPUESTOS:</td>
		<td colspan="13" class="dato">{{ $componente->supuestos }}</td>
	</tr>

	<tr><td height="5" colspan="15"></td></tr>

	<tr>
		<td colspan="9" class="encabezado texto-centro">INDICADOR DEL COMPONENTE (MIR)</td>
		<td colspan="6"></td>
	</tr>
	<tr>
		<td class="encabezado">DESCRIPCIÓN:</td>
		<td colspan="8" class="dato">{{ $componente->indicador }}</td>
		<td></td>
		<td colspan="2" class="encabezado">FORMULA:</td>
		<td colspan="3" class="dato">{{ $componente->formula }}</td>
	</tr>
	<tr>
		<td class="encabezado">NUMERADOR:</td>
		<td colspan="8" class="dato">{{ $componente->numerador }}</td>
		<td></td>
		<td colspan="2" class="encabezado">DIMENSIÓN:</td>
		<td colspan="3" class="dato">{{ $componente->dimension }}</td>
	</tr>
	<tr>
		<td class="encabezado">DENOMINADOR:</td>
		<td colspan="8" class="dato">{{ $componente->denominador }}</td>
		<td></td>
		<td colspan="2" class="encabezado">FRECUENCIA:</td>
		<td colspan="3" class="dato">{{ $componente->frecuencia }}</td>
	</tr>
	<tr>
		<td class="encabezado">INTERPRETACIÓN:</td>
		<td colspan="8" class="dato">{{ $componente->interpretacion }}</td>
		<td></td>
		<td colspan="2" class="encabezado">TIPO:</td>
		<td colspan="3" class="dato">{{ $componente->tipoIndicador }}</td>
	</tr>
	<tr>
		<td class="encabezado">NUMERO TRIMESTRE 1:</td>
		<td colspan="3" class="encabezado">NUMERO TRIMESTRE 2:</td>
		<td colspan="3" class="encabezado">NUMERO TRIMESTRE 3:</td>
		<td colspan="3" class="encabezado">NUMERO TRIMESTRE 4:</td>
		<td colspan="2" class="encabezado">UNIDAD DE MEDIDA:</td>
		<td colspan="3" class="dato">{{ $componente->unidadMedida }}</td>
	</tr>
	<tr>
		<td class="dato">{{ number_format($componente->numeroTrim1) }}</td>
		<td colspan="3" class="dato">{{ number_format($componente->numeroTrim2) }}</td>
		<td colspan="3" class="dato">{{ number_format($componente->numeroTrim3) }}</td>
		<td colspan="3" class="dato">{{ number_format($componente->numeroTrim4) }}</td>
		<td colspan="2" class="encabezado">META INDICADOR:</td>
		<td colspan="3" class="dato">{{ number_format($componente->metaIndicador) }}</td>
	</tr>
	<tr>
		<td class="encabezado">NUMERADOR:</td>
		<td colspan="2" class="dato">{{ number_format($componente->valorNumerador) }}</td>
		<td colspan="2" class="encabezado">DENOMINADOR:</td>
		<td colspan="2" class="dato">{{ number_format($componente->valorDenominador) }}</td>
		<td colspan="2" class="encabezado">LÍNEA BASE:</td>
		<td colspan="2" class="dato">{{ number_format($componente->lineaBase) }}</td>
		<td colspan="2" class="encabezado">AÑO LÍNEA BASE:</td>
		<td colspan="2" class="dato">{{ $componente->anioBase }}</td>
	</tr>
</table>

@if(count($componente->desglose_completo))
	@foreach($componente->desglose_completo as $desglose)
	<table class="tabla" width="100%">
        <tr><td colspan="15" height="7"></td></tr>
		<tr>
			<td colspan="15" class="encabezado texto-centro">DESGLOSE DEL COMPONENTE</td>
		</tr>
		<tr>
			<td class="encabezado">MUNICIPIO:</td>
			<td colspan="3" class="dato">{{$desglose->municipio}}</td>
			<td colspan="2" class="encabezado">LOCALIDAD:</td>
			<td colspan="9" class="dato">{{$desglose->localidad}}</td>
		</tr>
		<tr>
			<td class="encabezado">BENEFICIARIO:</td>
			<td colspan="3" class="dato">{{-- $data['tipoBeneficiario']->descripcion --}}</td>
			<td colspan="2" class="encabezado">CANTIDAD:</td>
			<td colspan="3" class="dato">{{number_format($desglose->beneficiariosF + $desglose->beneficiariosM)}}</td>
			<td colspan="3" class="encabezado">PRESUPUESTO:</td>
			<td colspan="3" class="dato">{{number_format($desglose->presupuesto)}}</td>
		</tr>

		<tr><td colspan="15" height="5"></td></tr>

		<tr>
			<td colspan="3" class="encabezado">CANTIDAD DENOMINADOR:</td>
			<td colspan="12"></td>
		</tr>
		<tr>
			<td class="encabezado">TRIMESTRE 1:</td>
			<td colspan="2" class="dato">
				<?php
					$trim = 0;
				?>
				@foreach($desglose->metas_mes as $meta)
					@if($meta->mes < 4)
						<?php 
						$trim += $meta->meta;
						?>
					@endif
				@endforeach
				{{number_format($trim)}}
			</td>
			<td colspan="2" class="encabezado">TRIMESTRE 2:</td>
			<td colspan="2" class="dato">
				<?php
					$trim = 0;
				?>
				@foreach($desglose->metas_mes as $meta)
					@if($meta->mes > 3 && $meta->mes < 7)
						<?php 
						$trim += $meta->meta;
						?>
					@endif
				@endforeach
				{{number_format($trim)}}
			</td>
			<td colspan="2" class="encabezado">TRIMESTRE 3:</td>
			<td colspan="2" class="dato">
				<?php
					$trim = 0;
				?>
				@foreach($desglose->metas_mes as $meta)
					@if($meta->mes > 6 && $meta->mes < 10)
						<?php 
						$trim += $meta->meta;
						?>
					@endif
				@endforeach
				{{number_format($trim)}}
			</td>
			<td colspan="2" class="encabezado">TRIMESTRE 4:</td>
			<td colspan="2" class="dato">
				<?php
					$trim = 0;
				?>
				@foreach($desglose->metas_mes as $meta)
					@if($meta->mes > 9 && $meta->mes < 13)
						<?php 
						$trim += $meta->meta;
						?>
					@endif
				@endforeach
				{{number_format($trim)}}
			</td>
		</tr>
	</table>
	@endforeach <!--Desglose-->
@endif <!--Tiene Desglose?-->

@foreach ($componente->actividadesDescripcion as $actividad)
<table class="tabla" width="100%">
	<tr><td height="7" colspan="15"></td></tr>
	<tr>
		<td colspan="15" align="center" class="encabezado">OBJETIVOS DE LA ACTIVIDAD</td>
	</tr>
	<tr>
		<td class="encabezado">DESCRIPCIÓN:</td>
		<td colspan="14" class="dato">{{ $actividad->objetivo }}</td>
	</tr>
	<tr>
		<td class="encabezado">MEDIOS DE VERIFICACIÓN:</td>
		<td colspan="14" class="dato">{{ $actividad->mediosVerificacion }}</td>
	</tr>
	<tr>
		<td class="encabezado">SUPUESTOS:</td>
		<td colspan="14" class="dato">{{ $actividad->supuestos }}</td>
	</tr>
	<tr><td height="5" colspan="15"></td></tr>
	<tr>
		<td colspan="9" align="center" class="encabezado">INDICADOR DE LA ACTIVIDAD</td>
		<td colspan="6"></td>
	</tr>
	<tr>
		<td class="encabezado">DESCRIPCIÓN:</td>
		<td colspan="8" class="dato">{{ $actividad->indicador }}</td>
		<td></td>
		<td colspan="2" class="encabezado">FORMULA:</td>
		<td colspan="3" class="dato">{{ $actividad->formula }}</td>
	</tr>
	<tr>
		<td class="encabezado">NUMERADOR:</td>
		<td colspan="8" class="dato">{{ $actividad->numerador }}</td>
		<td></td>
		<td colspan="2" class="encabezado">DIMENSIÓN:</td>
		<td colspan="3" class="dato">{{ $actividad->dimension }}</td>
	</tr>
	<tr>
		<td class="encabezado">DENOMINADOR:</td>
		<td colspan="8" class="dato">{{ $actividad->denominador }}</td>
		<td></td>
		<td colspan="2" class="encabezado">FRECUENCIA:</td>
		<td colspan="3" class="dato">{{ $actividad->frecuencia }}</td>
	</tr>
	<tr>
		<td class="encabezado">INTERPRETACIÓN:</td>
		<td colspan="8" class="dato">{{ $actividad->interpretacion }}</td>
		<td></td>
		<td colspan="2" class="encabezado">TIPO:</td>
		<td colspan="3" class="dato">{{ $actividad->tipoIndicador }}</td>
	</tr>
	<tr>
		<td class="encabezado">NUMERO TRIMESTRE 1:</td>
		<td colspan="3" class="encabezado">NUMERO TRIMESTRE 2:</td>
		<td colspan="3" class="encabezado">NUMERO TRIMESTRE 3:</td>
		<td colspan="3" class="encabezado">NUMERO TRIMESTRE 4:</td>
		<td colspan="2" class="encabezado">UNIDAD DE MEDIDA:</td>
		<td colspan="3" class="dato">{{ $actividad->unidadMedida }}</td>
	</tr>
	<tr>
		<td class="dato">{{ number_format($actividad->numeroTrim1) }}</td>
		<td colspan="3" class="dato">{{ number_format($actividad->numeroTrim2) }}</td>
		<td colspan="3" class="dato">{{ number_format($actividad->numeroTrim3) }}</td>
		<td colspan="3" class="dato">{{ number_format($actividad->numeroTrim4) }}</td>
		<td colspan="2" class="encabezado">META INDICADOR:</td>
		<td colspan="3" class="dato">{{ number_format($actividad->metaIndicador) }}</td>
	</tr>
	<tr>
		<td class="encabezado">NUMERADOR:</td>
		<td colspan="2" class="dato">{{ number_format($actividad->valorNumerador) }}</td>
		<td colspan="2" class="encabezado">DENOMINADOR:</td>
		<td colspan="2" class="dato">{{ number_format($actividad->valorDenominador) }}</td>
		<td colspan="2" class="encabezado">LÍNEA BASE:</td>
		<td colspan="2" class="dato">{{ number_format($actividad->lineaBase) }}</td>
		<td colspan="2" class="encabezado">AÑO LÍNEA BASE:</td>
		<td colspan="2" class="dato">{{ $actividad->anioBase }}</td>
	</tr>
	<tr>
		<td class="encabezado">INDICADOR DEL COMPONENTE</td>
		<td colspan="14" class="dato">{{ $componente->indicador }}</td>
	</tr>
</table>
@endforeach <!--Actividades-->
@endforeach	<!--Componentes-->
<table class="tabla" width="100%"><tr><td></td></tr></table>
<table class="tabla" width="100%" style="page-break-inside: avoid;">
	<tr><td height="15" colspan="5"></td></tr>
	<tr>
		<td></td>
		<td></td><!--th class="texto-centro">Jefe Inmediato Superior al Lider del Proyecto.</th-->
		<th class="texto-centro">Lider del proyecto.</th>
		<td></td>
		<td></td>
	</tr>
	<tr><td height="15" colspan="5"></td></tr>
	<tr>
		<td width="10%"></td>
		<td width="25%"></td><!--td width="25%" class="texto-centro firma">{{ $data['jefeInmediato'] }}</td-->
		<td width="30%" class="texto-centro firma">{{ $data['liderProyecto'] }}</td>
		<td width="25%"></td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td></td>
		<td></td><!--th class="texto-centro">{{ $data['jefeInmediatoCargo'] }}</th-->
		<th class="texto-centro">{{ $data['liderProyectoCargo'] }}</th>
		<td></td>
		<td></td>
	</tr>
	<tr><td height="15" colspan="5"></td></tr>
	<tr>
		<td></td>
		<th class="texto-centro">Coordinador General del Grupo Estratégico.</th>
		<td></td>
		<th class="texto-centro">Responsable de la Unida de Planeación u Homólogo.</th>
		<td></td>
	</tr>
	<tr><td height="15" colspan="5"></td></tr>
	<tr>
		<td></td>
		<td class="texto-centro firma">{{ $data['coordinadorGrupoEstrategico'] }}</td>
		<td></td>
		<td class="texto-centro firma">{{ $data['jefePlaneacion'] }}</td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<th class="texto-centro">{{ $data['coordinadorGrupoEstrategicoCargo'] }}</th>
		<td></td>
		<th class="texto-centro">{{ $data['jefePlaneacionCargo'] }}</th>
		<td></td>
	</tr>
</table>