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
	</style>
</head>
<body class="cuerpo">
	<div class="header">
		<table>
			<tr><td colspan="3" height="10">&nbsp;</td></tr>
			<tr>
				<td class="imagen izquierda">
					<img src="{{ public_path().'/img/LogoFederal.png' }}" width="180">
				</td>
				<td height="40" class="titulo2 texto-medio" align="center">Instituto de Salud</td>
				<td class="imagen derecha">
					<img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="180">
				</td>
			</tr>
			<tr><td colspan="3" height="10">&nbsp;</td></tr>
		</table>
	</div>
	<table>
		<tr>
			<td height="500" class="titulo1 texto-medio texto-centro">Resumen de Avances Físico-financieros al {{$trimestre}} Trimestre del {{$ejercicio}}</td>
		</tr>
	</table>
	<div style="page-break-after:always;"></div>
	@foreach($datos as $proyecto)
	<table>
		<tr height="2"><td>&nbsp;</td></tr>
	</table>
	<table style="page-break-inside:avoid;">
		<tr>
			<td colspan="3" class="texto-izquierda">
				<b>
				@if($proyecto['idClasificacionProyecto'] == 1)
					Proyecto Institucional:
				@else
					Proyecto de Inversión:
				@endif
				</b>
				{{ $proyecto['nombreTecnico'] }}
			</td>
		</tr>
		<tr>
			<td colspan="3" class="texto-izquierda"><b>Clave Presupuestaria:</b> {{ $proyecto['ClavePresupuestaria'] }}</td>
		</tr>
		<tr class="tabla-datos">
			<th>Nivel de Cumplimiento Físico</th>
			<th>Beneficiarios Atendidos</th>
			<th>Presupuesto Devengado</th>
		</tr>
		<tr class="tabla-datos">
			<td class="texto-derecha">{{$proyecto['nivelCumplimientoFisico']}} %</td>
			<td class="texto-derecha">{{$proyecto['totalBeneficiarios']}}</td>
			<td class="texto-derecha">$ {{$proyecto['presupuestoDevengadoModificado']}}</td>
		</tr>
	</table>
	@endforeach
</body>
</html>