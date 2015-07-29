<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css"></style>
</head>
<body class="cuerpo">
	<table>
		<tr>
			<td colspan="7" height="40" align="center">Instituto de Salud</td>
		</tr>
		<tr>
			<td colspan="7" height="500">Cédulas de Avances Físico-financieros al {{$trimestre}} Trimestre del {{$ejercicio}}</td>
		</tr>
		@foreach($datos as $proyecto)
		<tr>
			<td colspan="7" height="40" align="center">Instituto de Salud</td>
		</tr>

		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<th nowrap="nowrap" width="1" align="left" colspan="2">Programa Presupuestario: </th>
			<td colspan="5">{{ $proyecto['programaPresupuestarioDescipcion'] }}</td>
		</tr>

		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<th colspan="2">
				@if($proyecto['idClasificacionProyecto'] == 1)
					Proyecto Institucional:
				@else
					Proyecto de Inversión:
				@endif
			</th>
			<td colspan="5">
				{{ $proyecto['nombreTecnico'] }}
			</td>
		</tr>
		<tr>
			<th colspan="2">Clave Presupuestaria:</th>
			<td>{{ $proyecto['ClavePresupuestaria'] }}</td>
			<td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<th colspan="2">Objetivo General:</th>
			<td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td colspan="7">{{ $proyecto['finalidadProyecto'] }}</td>
		</tr>

		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<th colspan="2">Presupuesto Autorizado</th>
			<th>Presupuesto Modfificado</th>
			<th>Presupuesto Ejercido</th>
			<td></td><td></td><td></td>
		</tr>
		<tr>
			<td colspan="2">$ {{$proyecto['presupuestoAprobado']}}</td>
			<td>$ {{$proyecto['presupuestoModificado']}}</td>
			<td>$ {{$proyecto['presupuestoEjercidoModificado']}}</td>
			<td></td><td></td><td></td>
		</tr>

		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		
		<tr>
			<th>Nivel</th>
			<th colspan="2">Indicador</th>
			<th>Unidad/Medida</th>
			<th>Meta</th>
			<th>Avance</th>
			<th>% Avance</th>
		</tr>
		@foreach($proyecto['componentes'] AS $indice => $componente)
		<tr>
			<td align="center">C {{$indice+1}}</td>
			<td colspan="2">{{$componente['indicador']}}</td>
			<td align="center">{{$componente['unidadMedida']}}</td>
			<td align="center">{{$componente['metaAnual']}}</td>
			<td align="center">{{$componente['avanceAcumulado']}}</td>
			<td align="center">{{($componente['avanceAcumulado']/$componente['metaAnual'])*100}}</td>
		</tr>
		@endforeach
		@foreach($proyecto['actividades'] AS $indice => $actividad)
		<tr>
			<td align="center">A {{$indice+1}}</td>
			<td colspan="2">{{$actividad['indicador']}}</td>
			<td align="center">{{$actividad['unidadMedida']}}</td>
			<td align="center">{{$actividad['metaAnual']}}</td>
			<td align="center">{{$actividad['avanceAcumulado']}}</td>
			<td align="center">{{($actividad['avanceAcumulado']/$actividad['metaAnual'])*100}}</td>
		</tr>
		@endforeach

		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		
		<tr>
			<td></td><td></td>
			<th colspan="4">Beneficiarios</th>
			<td></td>
		</tr>

		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<td></td><td></td>
			<th>Tipo de Beneficiario</th>
			<th>Programado</th>
			<th>Atendido</th>
			<th>% Avance</th>
			<td></td>
		</tr>

		@foreach($proyecto['beneficiarios_descripcion'] AS $beneficiario)
		<tr>
			<td></td><td></td>
			<td class="texto-centro">{{$beneficiario['tipoBeneficiario']}}</td>
			<td class="texto-centro">{{$beneficiario['programadoTotal']}}</td>
			<td class="texto-centro">{{$beneficiario['avanceTotal']}}</td>
			<td class="texto-centro">{{($beneficiario['avanceTotal']/$beneficiario['programadoTotal'])*100}}</td>
			<td></td>
		</tr>
		@endforeach
		<tr>
			<td></td><td></td>
			<th>Total</th>
			<th>$total_programado</th>
			<th>$total_avance</th>
			<th>($total_avance/$total_programado)*100</th>
			<td></td>
		</tr>
		
		<tr>
			<td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		@endforeach
	</table>
</body>
</html>