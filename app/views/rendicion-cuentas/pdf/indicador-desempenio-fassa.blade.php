<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		@page {
            margin-top: 10.3em;
            margin-left: 1.6em;
            margin-right: 0.6em;
            margin-bottom: 1.3em;
        }
        table{
        	width:100%;
        	border-collapse: collapse;
        }
        
        .misma-linea{
        	display: inline-block;
        }
		.cuerpo{
			font-size: 8pt;
			font-family: arial, sans-serif;
		}
		.titulo1{
			font-weight: bold;
			font-family: arial, sans-serif;
			font-size: 14;
		}
		.titulo2{
			font-weight: bold;
			font-family: arial, sans-serif;
			font-size: 13;
		}
		.titulo3{
			font-weight: bold;
			font-family: arial, sans-serif;
			font-size: 12;
		}
		.titulo4{
			font-weight: bold;
			font-family: arial, sans-serif;
			font-size: 11;
		}
		.texto{
			font-family: arial, sans-serif;
			font-size: 10;
		}
		.negrita{
			font-weight: bold;
		}
		.linea-firma{
			border-bottom: 1 solid #000000;
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
		.encabezado-tabla{
			font-family: arial, sans-serif;
			font-size: 8;
			font-weight: normal;
			text-align: center;
			vertical-align: middle;
			color: #FFFFFF;
			background-color: #77933C;
		}
		.tabla-datos td,
		.tabla-datos th{
			border: 1 solid #000000;
			border-collapse: collapse;
			padding:1;
		}
		.subtitulo-tabla{
			font-weight: bold;
			background-color: #DDDDDD;
		}
		.nota-titulo{
			font-family: arial, sans-serif;
			font-size:8;
			font-weight: bold;
		}
		.nota-contenido{
			font-family: arial, sans-serif;
			font-size:8;
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
		.sin-bordes{
			border: none;
			border-collapse: collapse;
		}
		.header,.footer {
		    width: 100%;
		    text-align: center;
		    position: fixed;
		}
		.header {
		    top: -15.0em;
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
		<tr>
			<td rowspan="5" class="imagen izquierda"><img src="{{ URL::to('img/LogoFederal.png') }}" width="125"></td>
			<td class="titulo1" align="center">INSTITUTO DE SALUD</td>
			<td rowspan="5" class="imagen derecha"><img src="{{ URL::to('img/LogoInstitucional.png') }}" width="125"></td>
		</tr>
		<tr><td class="titulo2" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td></tr>
		<tr><td class="titulo3" align="center">SUBDIRECCIÓN DE PLANEACIÓN EN SALUD</td></tr>
		<tr><td class="titulo3" align="center">DEPARTAMENTO DE EVALUACIÓN</td></tr>
		<tr><td class="titulo3" align="center">INDICADORES DE DESEMPEÑO DEL FASSA {{$indicador['ejercicio']}}</td></tr>
	</table>
</diV>

<br>
<table>
	<tr>
		<th width="100" class="texto-izquierda">Nivel:</th>
		<td>{{$indicador['nivel']}}</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<th width="100" class="texto-izquierda">Indicador:</th>
		<td>{{$indicador['indicador']}}</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<th width="100" class="texto-izquierda">Tipo de Fórmula:</th>
		<td>{{$indicador['tipoFormula']}}</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<th width="100" class="texto-izquierda">Fórmula:</th>
		<td>{{$indicador['formula']}}</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<th width="100" class="texto-izquierda">Fuente de Información:</th>
		<td>{{$indicador['fuenteInformacion']}}</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>
<br>
@if(isset($indicador['metas']))
	<table>
		<thead>
			<tr class="tabla-datos">
				<th class="encabezado-tabla"  colspan="3">META</th>
			</tr>
			<tr class="tabla-datos">
				<th class="encabezado-tabla" >NUMERADOR</th>
				<th class="encabezado-tabla" >DENOMINADOR</th>
				<th class="encabezado-tabla" >%</th>
			</tr>
		</thead>
		<tbody>
			@foreach($indicador['metas'] as $meta)
			<tr class="tabla-datos">
				<td align="center" vertical-align="middle"  height="30">{{$meta['numerador']}}</td>
				<td align="center" vertical-align="middle" >{{$meta['denominador']}}</td>
				<td align="center" vertical-align="middle" >{{$meta['porcentaje']}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@else
	<table>
		<thead>
			<tr class="tabla-datos">
				<th class="encabezado-tabla"  colspan="3">META</th>
				<th class="encabezado-tabla"  colspan="3">AVANCES DEL TRIMESTRE</th>
				<th class="encabezado-tabla"  rowspan="2">% DESEMPEÑO</th>
				<th class="encabezado-tabla"  rowspan="2">JUSTIFICACIÓN ACUMULADA</th>
			</tr>
			<tr class="tabla-datos">
				<th class="encabezado-tabla" >NUMERADOR</th>
				<th class="encabezado-tabla" >DENOMINADOR</th>
				<th class="encabezado-tabla" >%</th>
				<th class="encabezado-tabla" >NUMERADOR</th>
				<th class="encabezado-tabla" >DENOMINADOR</th>
				<th class="encabezado-tabla" >%</th>
			</tr>
		</thead>
		<tbody>
			<tr class="tabla-datos">
				<td align="center" vertical-align="middle"  height="30">{{$indicador['metaNumerador']}}</td>
				<td align="center" vertical-align="middle" >{{$indicador['metaDenominador']}}</td>
				<td align="center" vertical-align="middle" >{{$indicador['metaPorcentaje']}}</td>
				<td align="center" vertical-align="middle" >{{$indicador['avanceNumerador']}}</td>
				<td align="center" vertical-align="middle" >{{$indicador['avanceDenominador']}}</td>
				<td align="center" vertical-align="middle" >{{$indicador['avancePorcentaje']}}</td>
				<td align="center" vertical-align="middle" >{{$indicador['desempenio']}}</td>
				<td align="center" vertical-align="middle" >{{$indicador['justificacion']}}</td>
			</tr>
		</tbody>
	</table>
@endif
<br>
<table style="page-break-inside:avoid;">
	<tr class="negrita" height="20">
		<td width="10%"></td>
		<td align="center">RESPONSABLE DE LA INFORMACIÓN</td>
		<td width="10%"></td>
		<td align="center">LIDER DEL PROGRAMA</td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td></td>
		<td height="40" class="linea-firma"></td>
		<td>&nbsp;</td>
		<td class="linea-firma"></td>
		<td></td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td align="center">{{ $indicador['responsableInformacion'] }}</td>
		<td></td>
		<td align="center">{{ $indicador['liderPrograma'] }}</td>
		<td></td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td vertical-align="top" align="center">{{ $indicador['cargoResponsableInformacion'] }}</td>
		<td></td>
		<td vertical-align="top" align="center">{{ $indicador['cargoLiderPrograma'] }}</td>
		<td></td>
	</tr>
</table>
</body>
</html>