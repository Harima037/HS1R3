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
		.cuerpo{
			font-size: 8pt;
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
		    top: -9.5em;
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
	<div style="page-break-after:always;"></div>
	@foreach($datos as $proyecto)
	<table>
		<tr>
			<td>Programa Presupuestario: </td>
			<td>{{ $proyecto->programaPresupuestarioDescipcion }}</td>
		</tr>
		<tr>
			<td>
				@if($proyecto->idCalsificacionProyecto == 1)
					Proyecto Institucional:
				@else
					Proyecto de Inversión:
				@endif
			</td>
			<td>
				{{ $proyecto->nombreTecnico }}
			</td>
		</tr>
		<tr>
			<td>Clave Presupuestaria:</td>
			<td>{{ $proyecto->ClavePresupuestaria }}</td>
		</tr>
		<tr>
			<td>Objetivo General:</td>
			<td></td>
		</tr>
	</table>
	<br>
	<table>
		<tr>
			<th>Presupuesto Autorizado</th>
			<th>Presupuesto Modfificado</th>
			<th>Presupuesto Ejercido</th>
		</tr>
		<tr>
			<td>{{$proyecto->presupuestoAprobado}}</td>
			<td>{{$proyecto->presupuestoModfificado}}</td>
			<td>{{$proyecto->presupuestoEjercidoModificado}}</td>
		</tr>
	</table>
	<br>
	<table>
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
			<td>C</td>
			<td>{{$componente->indicador}}</td>
			<td>{{$componente->unidadMedida}}</td>
			<td>{{$componente->metaAnual}}</td>
			<td>{{$componente->avanceAcumulado}}</td>
			<td>00.00</td>
		</tr>
		@endforeach
		@foreach($proyecto->actividades AS $indice => $actividad)
		<tr>
			<td>A</td>
			<td>{{$actividad->indicador}}</td>
			<td>{{$actividad->unidadMedida}}</td>
			<td>{{$actividad->metaAnual}}</td>
			<td>{{$actividad->avanceAcumulado}}</td>
			<td>00.00</td>
		</tr>
		@endforeach
	</table>
	<table>
		<tr>
			<th>Tipo de Beneficiario</th>
			<th>Programado</th>
			<th>Atendido</th>
			<th>% Avance</th>
		</tr>
		@foreach($proyecto->beneficiariosDescripcion AS $beneficiario)
		<tr>
			<td>{{$beneficiario->tipoBeneficiario}}</td>
			<td>{{$beneficiario->programadoTotal}}</td>
			<td>{{$beneficiario->avanceTotal}}</td>
			<td>00.00</td>
		</tr>
		@endforeach
	</table>
	<div style="page-break-after:always;"></div>
	@endforeach
</body>
</html>