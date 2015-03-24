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
			<td height="20" class="titulo1" colspan="15" align="center">INSTITUTO DE SALUD</td>
		</tr>
		<tr>
			<td height="19" class="titulo2" colspan="15" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="15" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="15" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
		</tr>
		<tr>
			<td height="18" class="titulo3" colspan="15" align="center">SEGUIMIENTO DE BENEFICIARIOS {{$proyecto['ejercicio']}}</td>
		</tr>
		<tr>
			<td height="18" colspan="15" align="right" class="negrita">Formato Rc-5</td>
		</tr>

		<tr height="90" class="texto-medio texto">
			<td class="texto-centro">Nombre del proyecto: </td>
			<td class="negrita" colspan="4">{{ $proyecto['nombreTecnico'] }}</td>
			<td></td>
			<td class="texto-centro">Clave presupuestaria: </td>
			<td class="negrita" colspan="2">{{ $proyecto['ClavePresupuestaria'] }}</td>
			<td></td>
			<td></td>
			<td class="texto-derecha">Trimestre: </td>
			<td class="negrita">{{$mes['trimestre']}}</td>
			<td></td>
			<td></td>
		</tr>

		<tr height="30"></tr>

		<tr class="tabla-datos" height="40">
			<td class="encabezado-tabla" rowspan="2">TIPO</td>
			<td class="encabezado-tabla" rowspan="2">GÉNERO</td>
			<td class="encabezado-tabla" rowspan="2">TOTAL</td>
			<td class="encabezado-tabla" rowspan="2">ZONA URBANA</td>
			<td class="encabezado-tabla" rowspan="2">ZONA RURAL</td>
			<td class="encabezado-tabla" colspan="4">POBLACIÓN</td>
			<td class="encabezado-tabla" colspan="5">MARGINACIÓN</td>
			<td class="encabezado-tabla" rowspan="2">POBLACIÓN ACUMULADA</td>
		</tr>

		<tr class="tabla-datos">
			<td class="encabezado-tabla">MESTIZA</td>
			<td class="encabezado-tabla">INDÍGENA</td>
			<td class="encabezado-tabla">INMIGRANTE</td>
			<td class="encabezado-tabla">OTROS</td>
			<td class="encabezado-tabla">MUY ALTA</td>
			<td class="encabezado-tabla">ALTA</td>
			<td class="encabezado-tabla">MEDIA</td>
			<td class="encabezado-tabla">BAJA</td>
			<td class="encabezado-tabla">MUY BAJA</td>
		</tr>

	</table>
</body>