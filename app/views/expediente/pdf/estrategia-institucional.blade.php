<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		@page {
            margin-top: 0.3em;
            margin-left: 0.6em;
            margin-right: 0.6em;
            margin-bottom: 0.3em;
        }
		
        .misma-linea{
        	display: inline-block;
        }
		.cuerpo{
			font-size: 8pt;
			font-family: Arial, Calibri;
		}
		.encabezado{
			padding:2px;
			background-color: #EEEEEE;
			font-weight: bold;
			border: 1px solid #000000;
			border-collapse: collapse;
		}
		.encabezado-metas{
			padding:5px;
			background-color:#FFFF00;
			border:1px solid #000000;
			font-weight: bold;
			border-collapse: collapse;
		}
		.dato{
			padding:2px;
			border:1px solid #000000;
		}
		.dato-metas{
			padding:5px;
			border:1px solid #000000;
		}
		.tabla{
			border-collapse: collapse;
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
		.texto-medio{
			vertical-align: middle;
		}
		.firma{
			border-bottom:1px solid #000000;
		}
		.titulo{
			font-size: 12pt;
			font-weight: bold;
			text-align: center;
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

		.encabezado-tabla{
			font-family: Arial;
			font-weight: bold;
			text-align: center;
			vertical-align: middle;
			color: #FFFFFF;
			background-color: #0070C0;
			border:1px solid #000000;
			border-collapse: collapse;
		}
	</style>
</head>
<body class="cuerpo">
	<table class="tabla" width="100%">
		<tr><td colspan="8"></td></tr>
		<tr>
			<td rowspan="8" class="imagen izquierda">
				<img src="{{ public_path().'/img/LogoFederal.png' }}" width="150">
			</td>
			<td class="titulo">GOBIERNO DEL ESTADO DE CHIAPAS</td>
			<td rowspan="8" class="imagen derecha">
				<img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="150">
			</td>
		</tr>
		<tr><td class="titulo">SECRETARÍA DE SALUD</td></tr>
		<tr><td class="titulo">INSTITUTO DE SALUD</td></tr>
		<tr><td class="titulo">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td></tr>
		<tr><td class="titulo"><strong>ESTRATEGIA INSTITUCIONAL</strong></td></tr>
	</table>
	<table class="tabla" width="100%" style="page-break-inside: avoid;">
		<tr>
			<td class="encabezado">DATOS GENERALES</td>
			<td colspan="5"></td>
		</tr>
		<tr>
			<td class="encabezado">UNIDAD RESPONSABLE:</td>
			<td class="dato">{{$data['unidadResponsable']}}</td>
			<td class="encabezado">PROGRAMA SECTORIAL:</td>
			<td class="dato">{{$data['programaSectorial']}}</td>
			<td class="encabezado">EJERCICIO:</td>
			<td class="dato">{{$data['ejercicio']}}</td>
		</tr>
		<tr>
			<td colspan="3" class="encabezado">ESTRATEGIA DEL OBJETIVO DEL PLAN NACIONAL:</td>
			<td colspan="3" class="encabezado">VINCULACIÓN AL PED:</td>
		</tr>
		<tr>
			<td colspan="3" class="dato">{{$data['estrategiaNacional']}}</td>
			<td colspan="3" class="dato">{{$data['objetivoPED']}}</td>
		</tr>
		<tr>
			<td colspan="6" class="encabezado">ODS:</td>
		</tr>
		<tr>
			<td colspan="6" class="dato">{{$data['ods']}}</td>
		</tr>
	</table>
</body>
</html>