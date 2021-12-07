<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		@page {
            margin-top: 0.3em;
            margin-left: 0.6em;
            margin-right: 0.6em;
            margin-bottom: 1.6em;
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
	<header>
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
	</header>
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
		<tr>
			<td colspan="6" class="encabezado">Objetivo Estrategico:</td>
		</tr>
		<tr>
			<td colspan="6" class="dato">{{$data['objetivoEstrategico']}}</td>
		</tr>
		<tr><td colspan="6" height="5">&nbsp;</td></tr>
	</table>
	<table class="tabla" width="100%" style="page-break-inside: avoid;">
		<tr>
			<td class="encabezado">DESCRIPCIÓN DEL INDICADOR:</td>
			<td colspan="3" class="dato">{{$data['descripcionIndicador']}}</td>
			<td colspan="2"></td>
		</tr>
		<tr>
		<td class="encabezado">NUMERADOR:</td>
			<td colspan="3" class="dato">{{$data['numerador']}}</td>
			<td class="encabezado">NUMERADOR:</td>
			<td class="dato">{{number_format($data['valorNumerador'])}}</td>
		</tr>
		<tr>
			<td class="encabezado">DENOMINADOR:</td>
			<td colspan="3" class="dato">{{$data['denominador']}}</td>
			<td class="encabezado">DENOMINADOR:</td>
			<td class="dato">{{number_format($data['valorDenominador'])}}</td>
		</tr>
		<tr>
			<td class="encabezado">FÓRMULA:</td>
			<td colspan="3" class="dato">{{$data['formula']}}</td>
			<td class="encabezado">LÍNEA BASE:</td>
			<td class="dato">{{number_format($data['lineaBase'])}}</td>
		</tr>
		<tr>
			<td class="encabezado">TIPO:</td>
			<td class="dato">{{$data['tipoIndicador']}}</td>
			<td class="encabezado">DIMENSIÓN:</td>
			<td class="dato">{{$data['dimension']}}</td>
			<td class="encabezado">AÑO DE LA LÍNEA BASE:</td>
			<td class="dato">{{$data['anioBase']}}</td>
		</tr>
		<tr>
			<td class="encabezado">UNIDAD DE MEDIDA:</td>
			<td class="dato">{{$data['unidadMedida']}}</td>
			<td class="encabezado">FRECUENCIA:</td>
			<td class="dato">{{$data['frecuencia']}}</td>
			<td class="encabezado">META INDICADOR:</td>
			<td class="dato">{{$data['metaIndicador']}}</td>
		</tr>
		<tr>
			<td class="encabezado">INTERPRETACIÓN:</td>
			<td class="dato">{{$data['interpretacion']}}</td>
			<td class="encabezado">COMPORTAMIENTO:</td>
			<td class="dato">{{$data['comportamientoAccion']}}</td>
			<td class="encabezado">TIPO DE VALOR  DE LA META:</td>
			<td class="dato">{{$data['tipoValorMeta']}}</td>
		</tr>
		<tr><td colspan="6" height="5">&nbsp;</td></tr>
	</table>
	<table class="tabla" width="100%" style="page-break-inside: avoid;">
		<tr>
			<td width="10%">&nbsp;</td>
			<td colspan="5" class="encabezado texto-centro">PROGRAMACIÓN TRIMESTRAL:</td>
			<td width="10%">&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td class="encabezado texto-centro">PRIMER TRIMESTRE:</td>
			<td class="encabezado texto-centro">SEGUNDO TRIMESTRE:</td>
			<td class="encabezado texto-centro">TERCER TRIMESTRE:</td>
			<td class="encabezado texto-centro">CUARTO TRIMESTRE:</td>
			<td class="encabezado texto-centro">TOTAL</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td class="dato">{{number_format($data['trim1'])}}</td>
			<td class="dato">{{number_format($data['trim2'])}}</td>
			<td class="dato">{{number_format($data['trim3'])}}</td>
			<td class="dato">{{number_format($data['trim4'])}}</td>
			<td class="dato">{{number_format($data['valorNumerador'])}}</td>
			<td></td>
		</tr>
		<tr><td colspan="7" height="5">&nbsp;</td></tr>
	</table>
	<table class="tabla" width="100%" style="page-break-inside: avoid;">
		<tr>
			<td width="33%"></td>
			<td class="encabezado texto-centro">AÑO</td>
			<td class="encabezado texto-centro">NUMERADOR</td>
			<td class="encabezado texto-centro">DENOMINADOR</td>
			<td class="encabezado texto-centro">META DEL INDICADOR</td>
			<td width="33%"></td>
		</tr>
		@foreach($data['metas_anios'] as $indice => $meta)
		<tr>
			<td></td>
			<td class="dato">{{number_format($meta['anio'])}}</td>
			<td class="dato">{{number_format($meta['numerador'])}}</td>
			<td class="dato">{{number_format($meta['denominador'])}}</td>
			<td class="dato">{{number_format($meta['metaIndicador'])}}</td>
			<td></td>
		</tr>
		@endforeach
		<tr><td colspan="6" height="5">&nbsp;</td></tr>
	</table>
	<table class="tabla" width="100%" style="page-break-inside: avoid;">
		<tr><td height="15" colspan="5"></td></tr>
		<tr>
			<td></td>
			<th class="texto-centro">RESPONSABLE DE LA INFORMACIÓN</th>
			<td></td>
			<th class="texto-centro">LIDEL DEL PROYECTO</th>
			<td></td>
		</tr>
		<tr><td height="15" colspan="5"></td></tr>
		<tr>
			<td></td>
			<td class="texto-centro firma">{{ $data['nombreResponsable'] }}</td>
			<td></td>
			<td width="30%" class="texto-centro firma">{{ $data['liderPrograma'] }}</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<th class="texto-centro">{{ $data['cargoResponsable'] }}</th>
			<td></td>
			<th class="texto-centro">{{ $data['liderProgramaCargo'] }}</th>
			<td></td>
		</tr>
	</table>
</body>
</html>