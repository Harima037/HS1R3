<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		@page {
            margin-top: 6.5em;
            margin-left: 1.6em;
            margin-right: 0.6em;
            margin-bottom: 1.3em;
        }
        table{
        	width:100%;
        	border-collapse: collapse;
        }
        .tabla-datos td,.tabla-datos th{
			border: 1 solid #000000;
			border-collapse: collapse;
			padding:1;
		}
		.cuerpo{
			font-size: 11pt;
			font-family: arial, sans-serif;
		}
        .titulo1{
			font-weight: bold;
			font-family: arial, sans-serif;
			font-size: 28pt;
		}
		.titulo2{
			font-weight: bold;
			font-family: arial, sans-serif;
			font-size: 24pt;
		}
		.texto-medio{
			vertical-align: middle;
		}
		.texto-centro{
			text-align: center;
		}
		.texto-derecha{
			text-align: right;
		}
		.texto-izquierda{
			text-align: left;
		}
		.imagen{
			vertical-align: top;
		}
		.imagen.izquierda{
			text-align: left;
		}

		.imagen.derecha{
			text-align: right;
		}
		.header,.footer {
		    width: 100%;
		    text-align: center;
		    position: fixed;
		}
		.header {
		    top: -7.5em;
		}
		.footer {
		    bottom: 0px;
		}
		.pagenum:before {
		    content: counter(page);
		}
	</style>
</head>
<body class="cuerpo">
	<div class="header">
		<table>
			<tr><td colspan="3" height="10">&nbsp;</td></tr>
			<tr>
				<td class="imagen izquierda">
					<img src="{{ URL::to('img/EscudoGobiernoChiapas.png') }}" width="180">
				</td>
				<td height="40" class="titulo2 texto-medio" align="center">Instituto de Salud</td>
				<td class="imagen derecha">
					<img src="{{ URL::to('img/Marca.png') }}" width="180">
				</td>
			</tr>
			<tr><td colspan="3" height="10">&nbsp;</td></tr>
		</table>
	</div>
	<table>
		<tr>
			<td height="500" class="titulo1 texto-medio texto-centro">Cédulas de Avances Físico-financieros al Tercer Trimestre del 2014</td>
		</tr>
	</table>
	@foreach($datos as $proyecto)
	<div style="page-break-after:always;"></div>
	<table>
		<tr><td colspan="2" height="7">&nbsp;</td></tr>
		<tr>
			<th class="texto-izquierda" nowrap="nowrap" width="1">Programa Presupuestario: </th>
			<td>{{ $proyecto->programaPresupuestarioDescipcion }}</td>
		</tr>
		<tr><td colspan="2" height="7">&nbsp;</td></tr>
		<tr>
			<th class="texto-izquierda">
				@if($proyecto->idCalsificacionProyecto == 1)
					Proyecto Institucional:
				@else
					Proyecto de Inversión:
				@endif
			</th>
			<td>
				{{ $proyecto->nombreTecnico }}
			</td>
		</tr>
		<tr>
			<th class="texto-izquierda">Clave Presupuestaria:</th>
			<td>{{ $proyecto->ClavePresupuestaria }}</td>
		</tr>
		<tr><td colspan="2" height="7">&nbsp;</td></tr>
		<tr>
			<th class="texto-izquierda">Objetivo General:</th>
			<td>{{ $proyecto->finalidadProyecto }}</td>
		</tr>
	</table>
	<br>
	<table class="tabla-datos" style="width:60%">
		<tr>
			<th>Presupuesto Autorizado</th>
			<th>Presupuesto Modfificado</th>
			<th>Presupuesto Ejercido</th>
		</tr>
		<tr>
			<td class="texto-derecha">$ {{number_format($proyecto->presupuestoAprobado,2)}}</td>
			<td class="texto-derecha">$ {{number_format($proyecto->presupuestoModificado,2)}}</td>
			<td class="texto-derecha">$ {{number_format($proyecto->presupuestoEjercidoModificado,2)}}</td>
		</tr>
	</table>
	<br>
	<table class="tabla-datos">
		<tr>
			<th>Nivel</th>
			<th>Indicador</th>
			<th>Unidad/Medida</th>
			<th>Meta</th>
			<th>Avance</th>
			<th>% Avance</th>
		</tr>
		@foreach($proyecto->componentes AS $indice => $componente)
		<tr>
			<td class="texto-centro">C {{$indices[$componente->id]['indice'] = $indice+1}}</td>
			<td>{{$componente->indicador}}
				<!-- {{$indices[$componente->id]['indiceActividad']=1}} -->
			</td>
			<td class="texto-centro">{{$componente->unidadMedida}}</td>
			<td class="texto-centro">{{number_format($componente->metaAnual,2)}}</td>
			<td class="texto-centro">{{number_format($componente->avanceAcumulado,2)}}</td>
			<td class="texto-centro">
			@if($componente->avanceAcumulado)
				{{number_format(($componente->avanceAcumulado/$componente->metaAnual)*100,2)}}
			@else
				0.00
			@endif
			</td>
		</tr>
		@endforeach
		@foreach($proyecto->actividades AS $indice => $actividad)
		<tr>
			<td class="texto-centro">A {{$indices[$actividad->idComponente]['indice']}}.{{$indices[$actividad->idComponente]['indiceActividad']++}}</td>
			<td>{{$actividad->indicador}}</td>
			<td class="texto-centro">{{$actividad->unidadMedida}}</td>
			<td class="texto-centro">{{number_format($actividad->metaAnual,2)}}</td>
			<td class="texto-centro">{{number_format($actividad->avanceAcumulado,2)}}</td>
			<td class="texto-centro">
			@if($actividad->avanceAcumulado)
				{{number_format(($actividad->avanceAcumulado/$actividad->metaAnual)*100,2)}}
			@else
				0.00
			@endif
			</td>
		</tr>
		@endforeach
	</table>
	<br>
	<!--{{$total_programado=0}}-->
	<!--{{$total_avance=0}}-->
	<table class="tabla-datos" style="width:50%; margin-left:auto; margin-right:auto;">
		<tr>
			<th colspan="4">Beneficiarios</th>
		</tr>
		<tr>
			<th>Tipo de Beneficiario</th>
			<th>Programado</th>
			<th>Atendido</th>
			<th>% Avance</th>
		</tr>
		@foreach($proyecto->beneficiariosDescripcion AS $beneficiario)
		<tr>
			<td class="texto-centro">{{$beneficiario->tipoBeneficiario}}</td>
			<td class="texto-centro">{{number_format($beneficiario->programadoTotal)}}
			<!--{{$total_programado += $beneficiario->programadoTotal}}-->
			</td>
			<td class="texto-centro">{{number_format($beneficiario->avanceTotal)}}
			<!--{{$total_avance += $beneficiario->avanceTotal}}-->
			</td>
			<td class="texto-centro">{{number_format(($beneficiario->avanceTotal/$beneficiario->programadoTotal)*100,2)}}</td>
		</tr>
		@endforeach
		<tr>
			<th>Total</th>
			<th>{{number_format($total_programado)}}</th>
			<th>{{number_format($total_avance)}}</th>
			<th>
				{{number_format(($total_avance/$total_programado)*100,2)}}
			</th>
		</tr>
	</table>
	@endforeach
</body>
</html>