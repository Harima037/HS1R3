<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		@page {
            margin-top: 0.5em;
            margin-left: 1.6em;
            margin-right: 0.6em;
            margin-bottom: 1.3em;
        }
		.cuerpo{
			font-size: 10pt;
			font-family: arial, sans-serif;
		}
		table{
			width: 100%;
		}
		table.tabla-datos td,table.tabla-datos th{
			border:1px solid #AAAAAA;
			border-collapse: collapse;
			padding: 5px;
		}
		table.tabla-datos tfoot th{
			text-align: left;
		}
		.grafica{ width:50%; float:left; }
		.grafica img{ width:100%;}
		.color-metas{ background-color:#DFF0D8; }
		.color-avances{ background-color:#D9EDF7; }
		.color-avance{color:#4D824E;}
		.color-alto-bajo{color:#AD4D4B;}
	</style>
</head>
<body class="cuerpo">
	<table>
		<tr align="left">
			<th>Indicador</th>
			<th>Unidad de Medida</th>
			<th>Meta Total Programada</th>
		</tr>
		<tr align="left" valign="top">
			<td>{{$data->indicador}}</td>
			<td>{{$data->unidadMedida}}</td>
			<td>{{number_format($data->metaTotal,2)}}</td>
		</tr>
	</table>
	<hr>
	<table class="tabla-datos" cellspacing="0">
		<thead>
			<tr>
				<th rowspan="2">
				@if($data->tomar == 'jurisdicciones')
					Jurisdicción
				@else
					Mes
				@endif
				</th>
    			<th colspan="2" class="color-metas text-center">Meta Programada</th>
    			<th colspan="3" class="color-avances text-center">Avance</th>
    			<th rowspan="2" width="75" class="text-center">Porcentaje Acumulado</th>
			</tr>
			<tr>
				<th width="50px" class="color-metas">Mes</th>
				<th width="50px" class="color-metas">Acumulada</th>
				<th width="50px" class="color-avances">Acumulado</th>
				<th width="50px" class="color-avances">Mes</th>
				<th width="50px" class="color-avances">Total</th>
			</tr>
		</thead>
		@if($data->tomar == 'jurisdicciones')
		<tbody>
			<!-- {{$suma['metaMes'] = 0;}} -->
			<!-- {{$suma['metaAcumulada'] = 0;}} -->
			<!-- {{$suma['avanceAcumulado'] = 0;}} -->
			<!-- {{$suma['avanceMes'] = 0;}} -->
			<!-- {{$suma['avanceTotal'] = 0;}} -->
			@foreach ($data->jurisdicciones as $jurisdiccion)
			<tr>
				<td class="accion-municipio">
					<span class=""></span> {{$jurisdiccion->clave}} - {{$jurisdiccion->nombre}}
				</td>
				<td class="valores">			<span>{{number_format($jurisdiccion->metaMes,2)}}</span>	</td>
				<td class="valores color-metas"><span>{{number_format($jurisdiccion->metaAcumulada,2)}}</span>	</td>
				<td class="valores">			<span>{{number_format($jurisdiccion->avanceAcumulado,2)}}</span>	</td>
				<td class="valores">			<span>{{number_format($jurisdiccion->avanceMes,2)}}</span>	</td>
				<td class="valores color-avances"><span>{{number_format($jurisdiccion->avanceTotal,2)}}</span>	</td>
				<td class="valores">			<span class="{{($jurisdiccion->estatus == 1)?'color-avance':'color-alto-bajo'}}">{{number_format($jurisdiccion->porcentaje,2)}} %</span>	</td>
				<!-- {{$suma['metaMes'] += $jurisdiccion->metaMes;}} -->
				<!-- {{$suma['metaAcumulada'] += $jurisdiccion->metaAcumulada;}} -->
				<!-- {{$suma['avanceAcumulado'] += $jurisdiccion->avanceAcumulado;}} -->
				<!-- {{$suma['avanceMes'] += $jurisdiccion->avanceMes;}} -->
				<!-- {{$suma['avanceTotal'] += $jurisdiccion->avanceTotal;}} -->
			</tr>
			@endforeach
		</tbody>
		@if($suma['metaAcumulada']>0)
		<!-- {{ $suma['porcentaje'] = ($suma['avanceTotal']/$suma['metaAcumulada'])*100; }} -->
		@else
		<!-- {{ $suma['porcentaje'] = ($suma['avanceTotal']/1)*100; }} -->
		@endif
		<tfoot>
			<tr>
				<th>Totales</th>
				<th>{{number_format($suma['metaMes'],2)}}</th>
				<th class="color-metas">{{number_format($suma['metaAcumulada'],2)}}</th>
				<th>{{number_format($suma['avanceAcumulado'],2)}}</th>
				<th>{{number_format($suma['avanceMes'],2)}}</th>
				<th class="color-avances">{{number_format($suma['avanceTotal'],2)}}</th>
				<th class="{{($suma['porcentaje'] > 110 || $suma['porcentaje'] < 90 )?'color-alto-bajo':'color-avance'}}">{{number_format($suma['porcentaje'],2)}} %</th>
			</tr>
		</tfoot>
		@else
		<tbody>
		@foreach($meses as $clave => $mes)
			@if(isset($data->meses[$clave]))
			<tr {{($mes_clave == $clave)?'style="font-weight:bold;"':''}}>
				<td {{$estilo_linea = ($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} >{{$mes}}</td>
				<td {{$estilo_linea}} width="50px" class="">{{number_format($data->meses[$clave]->meta,2)}}</td>
				<td {{$estilo_linea}} width="50px" class="color-metas">{{number_format($data->meses[$clave]->metaAcumulada,2)}}</td>
				<td {{$estilo_linea}} width="50px" class="">{{($clave <= $mes_clave)?(number_format(((isset($data->meses[$clave]->avanceAcumulado))?$data->meses[$clave]->avanceAcumulado:0)-((isset($data->meses[$clave]->avance))?$data->meses[$clave]->avance:0),2)):''}}</td>
				<td {{$estilo_linea}} width="50px" class="">{{($clave <= $mes_clave)?((isset($data->meses[$clave]->avance))?number_format($data->meses[$clave]->avance,2):0.00):''}}</td>
				<td {{$estilo_linea}} width="50px" class="bg-info">{{($clave <= $mes_clave)?((isset($data->meses[$clave]->avanceAcumulado))?number_format($data->meses[$clave]->avanceAcumulado,2):0.00):''}}</td>
				<td {{$estilo_linea}} class="{{($data->meses[$clave]->estatus == 1)?'color-avance':'color-alto-bajo'}}">{{($clave <= $mes_clave)?number_format($data->meses[$clave]->porcentaje,2) . ' %':''}}</td>
			</tr>
			@else
			<tr {{($mes_clave == $clave)?'style="font-weight:bold;"':''}}>
				<td {{$estilo_linea = ($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} >{{$mes}}</td>
				<td {{$estilo_linea}} width="50px" class="">0.00</td>
				<td {{$estilo_linea}} width="50px" class="color-metas">0.00</td>
				<td {{$estilo_linea}} width="50px" class="">{{($clave <= $mes_clave)?0.00:''}}</td>
				<td {{$estilo_linea}} width="50px" class="">{{($clave <= $mes_clave)?0.00:''}}</td>
				<td {{$estilo_linea}} width="50px" class="bg-info">{{($clave <= $mes_clave)?0.00:''}}</td>
				<td {{$estilo_linea}} class="">{{($clave <= $mes_clave)?'0.00 %':''}}</td>
			</tr>
			@endif
		@endforeach
		</tbody>
		@endif		
	</table>

	@if($data->tomar == 'jurisdicciones')
	<table>
		<tr align="left">
			<th>Análisis de Resultados Acumulados</th>
			<th>Justificación Acumulada</th>
		</tr>
		<tr align="left" valign="top">
			<td>{{$data->analisisResultados}}</td>
			<td>{{$data->justificacion}}</td>
		</tr>
	</table>
	@else
		@if($data->planMejora)
		<table>
			<tr align="left">
				<th>Observaciones:</th>
			</tr>
			<tr align="left" valign="top">
				<td>
					<big>
						De acuerdo a los resultados acumulados del indicador es necesario implementar un Plan de Acción de Mejora
					</big>
				</td>
			</tr>
		</table>
		@endif
	@endif
	<br>
	@if($data->planMejora)
	<table>
		<tr align="left">
			<th colspan="3">Acción de Mejora</th>
		</tr>
		<tr align="left" valign="top">
			<td colspan="3">{{$data->planMejora->accionMejora}}</td>
		</tr>
		<tr align="left">
			<th colspan="3">Grupo de Trabajo</th>
		</tr>
		<tr align="left" valign="top">
			<td colspan="3">{{$data->planMejora->grupoTrabajo}}</td>
		</tr>
		<tr align="left">
			<th>Fecha de Inicio</th>
			<th>Fecha de Término</th>
			<th>Fecha de Notificación</th>
		</tr>
		<tr align="left" valign="top">
			<td>{{$data->planMejora->fechaInicio}}</td>
			<td>{{$data->planMejora->fechaTermino}}</td>
			<td>{{$data->planMejora->fechaNotificacion}}</td>
		</tr>
		<tr align="left">
			<th colspan="3">Documentación Comprobatoria</th>
		</tr>
		<tr align="left" valign="top">
			<td colspan="3">{{$data->planMejora->documentacionComprobatoria}}</td>
		</tr>
	</table>
	@endif
	<br>
	<div class="grafica"><img {{$srcGraficaMensual}} /></div>
	<div class="grafica"><img {{$srcGraficaJurisdiccional}} /></div>
</body>
</html>