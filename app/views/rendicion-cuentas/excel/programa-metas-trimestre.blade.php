<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		.titulo1{
			font-weight: bold;
			font-family: Arial;
			font-size: 18;
		}
		.titulo2{
			font-weight: bold;
			font-family: Arial;
			font-size: 16;
		}
		.titulo3{
			font-weight: bold;
			font-family: Arial;
			font-size: 14;
		}
		.texto{
			font-family: Arial;
			font-size: 12;
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
			font-family: Arial;
			font-size: 11;
			font-weight: bold;
			text-align: center;
			vertical-align: middle;
			color: #FFFFFF;
			background-color: #0070C0;
		}
		.tabla-datos td{
			border: 1 solid #000000;
			border-collapse: collapse;
			padding:1;
		}
		.subtitulo-tabla{
			font-weight: bold;
			background-color: #DDDDDD;
		}
		.nota-titulo{
			font-family: Arial;
			font-size:8;
			font-weight: bold;
		}
		.nota-contenido{
			font-family: Arial;
			font-size:8;
		}
	</style>
</head>
<body>
	<table>
		<tr>
			<td height="20" class="titulo1" colspan="8" align="center">INSTITUTO DE SALUD</td>
		</tr>
		<tr>
			<td height="19" class="titulo2" colspan="8" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="8" align="center">SUBDIRECCIÓN DE PLANEACIÓN EN SALUD</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="8" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="8" align="center">PROGRAMA PRESUPUESTARIO {{$programa['ejercicio']}}</td>
		</tr>
		<tr>
			<td height="18" colspan="8" align="right" class="negrita">Formato RC-5</td>
		</tr>



		<tr height="90" class="texto-medio texto">
			<td class="texto-centro">Nombre del Programa Presupuestario : </td>
			<td class="negrita" colspan="2">{{ $programa['nombre'] }}</td>
			<td colspan="2"></td>
			<td class="texto-centro">Trimestre: </td>
			<td class="negrita">{{ $trimestre_lbl }}</td>
			<td></td>
		</tr>

		<tr height="15"><td colspan="8"></td></tr>

		<tr class="tabla-datos" height="50">
			<td width="15" class="encabezado-tabla">NIVEL</td>
			<td width="40" class="encabezado-tabla">INDICADOR</td>
			<td width="15" class="encabezado-tabla">META ORIGINAL</td>
			<td width="15" class="encabezado-tabla">AVANCES DEL TRIMESTRE</td>
			<td width="15" class="encabezado-tabla">AVANCE ACUMULADO</td>
			<td width="13" class="encabezado-tabla">% DE AVANCE ACUMULADO</td>
			<td width="35" class="encabezado-tabla">ANALISIS DE RESULTADOS 	ACUMULADO</td>
			<td width="35" class="encabezado-tabla">JUSTIFICACIÓN ACUMULADA</td>
		</tr>

		@foreach($indicadores as $indicador)
		<tr height="50" class="tabla-datos">
			<td class="texto-medio">{{$indicador['nivel']}}</td>
			<td class="texto-medio">{{$indicador['indicador']}}</td>
			<td class="texto-medio texto-centro">{{$indicador['meta_original']}}</td>
			<td class="texto-medio texto-centro">{{$indicador['avance_trimestre']}}</td>
			<td class="texto-medio texto-centro">{{$indicador['avance_acumulado']}}</td>
			<td class="texto-medio texto-centro">
			@if($indicador['meta_original'] > 0)
				{{
				round(($indicador['avance_acumulado']/$indicador['meta_original'])*100,2)
				}}
			@else
				100
			@endif
			 %</td>
			<td class="texto-medio">{{$indicador['analisis_resultados']}}</td>
			<td class="texto-medio">{{$indicador['justificacion_acumulada']}}</td>
		</tr>
		@endforeach
		<tr>
			<td class="nota-titulo">Fuente de información:</td>
			<td class="nota-contenido" colspan="3"></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr><td colspan="8"></td></tr>



		<tr class="negrita" height="20">
			<td></td>
			<td colspan="2" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td></td>
			<td colspan="3" align="center">LIDER DEL PROYECTO</td>
			<td></td>
		</tr>
		<tr height="40">
			<td></td>
			<td colspan="2" class="linea-firma"></td>
			<td></td>
			<td colspan="3" class="linea-firma"></td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td colspan="2" align="center">Nombre</td>
			<td></td>
			<td colspan="3" align="center">{{ $programa['liderPrograma'] }}</td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td colspan="2" align="center">Cargo</td>
			<td></td>
			<td colspan="3" align="center">{{ $programa['cargoLiderPrograma'] }}</td>
			<td></td>
		</tr>
	</table>
</body>
</html>