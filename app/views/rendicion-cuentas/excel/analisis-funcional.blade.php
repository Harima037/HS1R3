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
		.tabla-datos td,th{
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
			<td height="20" class="titulo1" colspan="4" align="center">GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS</td>
		</tr>
		<tr>
			<td height="19" class="titulo2" colspan="4" align="center">INSTITUTO DE SALUD</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="4" align="center">ANÁLISIS FUNCIONAL {{$mes['trimestre_letras']}} TRIMESTRE {{$proyecto['ejercicio']}}</td>
		</tr>
		<tr>
			<td height="18" colspan="4" align="right" class="negrita">Formato RC-6</td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="tabla-datos" height="20">
			<th width="300" class="encabezado-tabla texto-izquierda">EJE:</th>
			<th width="300" class="encabezado-tabla texto-izquierda">TEMA:</th>
			<th width="300" class="encabezado-tabla texto-izquierda">POLITICA PÚBLICA:</th>
			<th width="320" class="encabezado-tabla texto-izquierda">PROGRAMA PRESUPUESTARIO:</th>
		</tr>
		<tr class="tabla-datos" height="30">
			<td>{{$proyecto['eje']}}</td>
			<td>{{$proyecto['tema']}}</td>
			<td>{{$proyecto['politicaPublica']}}</td>
			<td>{{$proyecto['programaPresupuestario']}}</td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="tabla-datos" height="20">
			<th class="encabezado-tabla texto-izquierda">FUNCIÓN:</th>
			<th class="encabezado-tabla texto-izquierda">SUBFUNCIÓN:</th>
			<th class="encabezado-tabla texto-izquierda">FUENTE DE FINANCIAMIENTO:</th>
			<th class="encabezado-tabla texto-izquierda">SUBFUENTE DE FINANCIAMIENTO:</th>
		</tr>
		<tr class="tabla-datos" height="100">
			<td class="texto-medio">{{$proyecto['funcion']}}</td>
			<td class="texto-medio">{{$proyecto['subFuncion']}}</td>
			<td></td>
			<td></td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="tabla-datos" height="20">
			<th class="encabezado-tabla texto-izquierda">CLAVE PRESUPUESTAL:</th>
			<th colspan="3" class="encabezado-tabla texto-izquierda">NOMBRE DEL PROYECTO:</th>
		</tr>
		<tr class="tabla-datos" height="30">
			<td class="texto-medio">{{ $proyecto['ClavePresupuestaria'] }}</td>
			<td class="texto-medio" colspan="3"> {{ $proyecto['nombreTecnico'] }} </td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="tabla-datos">
			<th colspan="4" class="encabezado-tabla">FINALIDAD DEL PROYECTO</th>
		</tr>
		<tr class="tabla-datos" height="50">
			<td class="texto-medio" colspan="4">{{$analisis_funcional->finalidadProyecto}}</td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="tabla-datos">
			<th colspan="4" class="encabezado-tabla">ANALISIS DE RESULTADO</th>
		</tr>
		<tr class="tabla-datos" height="150">
			<td class="texto-medio" colspan="4">{{$analisis_funcional->analisisResultado}}</td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="tabla-datos">
			<th colspan="4" class="encabezado-tabla">BENEFICIARIOS</th>
		</tr>
		<tr class="tabla-datos" height="50">
			<td class="texto-medio" colspan="4">{{$analisis_funcional->beneficiarios}}</td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="tabla-datos">
			<th colspan="4" class="encabezado-tabla">JUSTIFICACIÓN GLOBAL DEL PROYECTO</th>
		</tr>
		<tr class="tabla-datos" height="150">
			<td class="texto-medio" colspan="4">{{$analisis_funcional->justificacionGlobal}}</td>
		</tr>

		<tr><td colspan="4"></td></tr>

		<tr class="negrita" height="20">
			<td colspan="2" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td colspan="2" align="center">LIDER DEL PROYECTO</td>
		</tr>
		<tr height="40">
			<td colspan="2" class="texto-centro">___________________________________________________</td>
			<td colspan="2" class="texto-centro">___________________________________________________</td>
		</tr>
		<tr class="negrita" height="20">
			<td colspan="2" align="center">Nombre</td>
			<td colspan="2" align="center">{{ $proyecto['liderProyecto'] }}</td>
		</tr>
		<tr class="negrita" height="20">
			<td colspan="2" align="center">Cargo</td>
			<td colspan="2" align="center">Cargo</td>
		</tr>

	</table>
</body>
