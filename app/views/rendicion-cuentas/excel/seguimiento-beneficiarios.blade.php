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
		.sin-bordes{
			border: 1px solid #FFFFFF;
			border-collapse: collapse;
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
			font-weight: normal;
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
			<td height="18" colspan="15" align="right" class="negrita">Formato RC-5</td>
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

		@foreach($beneficiarios as $beneficiario)
		<tr height="40"></tr>

		<tr class="tabla-datos" height="25">
			<td class="sin-bordes"></td>
			<td class="sin-bordes"></td>
			<td class="sin-bordes"></td>
			<td class="sin-bordes"></td>
			<td class="sin-bordes"></td>
			<td class="encabezado-tabla" colspan="4">POBLACIÓN</td>
			<td class="encabezado-tabla" colspan="5">MARGINACIÓN</td>
			<td rowspan="2" width="15" class="encabezado-tabla">POBLACIÓN ACUMULADA</td>
			<!--td class="sin-bordes"></td-->
		</tr>
		<tr class="tabla-datos" height="30">
			<td width="25" class="encabezado-tabla">TIPO</td>
			<td width="12" class="encabezado-tabla">GÉNERO</td>
			<td width="15" class="encabezado-tabla">TOTAL</td>
			<td width="15" class="encabezado-tabla">ZONA URBANA</td>
			<td width="15" class="encabezado-tabla">ZONA RURAL</td>
			<td width="15" class="encabezado-tabla">MESTIZA</td>
			<td width="15" class="encabezado-tabla">INDÍGENA</td>
			<td width="15" class="encabezado-tabla">INMIGRANTE</td>
			<td width="15" class="encabezado-tabla">OTROS</td>
			<td width="15" class="encabezado-tabla">MUY ALTA</td>
			<td width="15" class="encabezado-tabla">ALTA</td>
			<td width="15" class="encabezado-tabla">MEDIA</td>
			<td width="15" class="encabezado-tabla">BAJA</td>
			<td width="15" class="encabezado-tabla">MUY BAJA</td>
		</tr>

		<tr class="tabla-datos" height="40">
			<td class="texto-centro texto-medio">{{$beneficiario['tipoBeneficiario']}}</td>
			<td class="texto-centro texto-medio negrita">Femenino</td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['total']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['urbana']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['rural']}} </td>
			<!--td width="0"></td-->
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['mestiza']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['indigena']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['inmigrante']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['otros']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['muyAlta']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['alta']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['media']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['baja']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['muyBaja']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['f']['acumulado']}} </td>
		</tr>

		<tr class="tabla-datos" height="40">
			<td class="texto-centro texto-medio"></td>
			<td class="texto-centro texto-medio negrita">Masculino</td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['total']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['urbana']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['rural']}} </td>
			<!--td width="0"></td-->
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['mestiza']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['indigena']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['inmigrante']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['otros']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['muyAlta']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['alta']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['media']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['baja']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['muyBaja']}} </td>
			<td class="texto-centro texto-medio"> {{$beneficiarios_avances[$beneficiario['id']]['m']['acumulado']}} </td>
		</tr>

		<tr class="tabla-datos" height="40">
			<td class="texto-centro texto-medio"></td>
			<td class="texto-centro texto-medio negrita">Total</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['total'] + $beneficiarios_avances[$beneficiario['id']]['m']['total'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['urbana'] + $beneficiarios_avances[$beneficiario['id']]['m']['urbana'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['rural'] + $beneficiarios_avances[$beneficiario['id']]['m']['rural'] }}
			</td>
			<!--td width="0"></td-->
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['mestiza'] + $beneficiarios_avances[$beneficiario['id']]['m']['mestiza'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['indigena'] + $beneficiarios_avances[$beneficiario['id']]['m']['indigena'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['inmigrante'] + $beneficiarios_avances[$beneficiario['id']]['m']['inmigrante'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['otros'] + $beneficiarios_avances[$beneficiario['id']]['m']['otros'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['muyAlta'] + $beneficiarios_avances[$beneficiario['id']]['m']['muyAlta'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['alta'] + $beneficiarios_avances[$beneficiario['id']]['m']['alta'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['media'] + $beneficiarios_avances[$beneficiario['id']]['m']['media'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['baja'] + $beneficiarios_avances[$beneficiario['id']]['m']['baja'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['muyBaja'] + $beneficiarios_avances[$beneficiario['id']]['m']['muyBaja'] }}
			</td>
			<td class="texto-centro texto-medio">
				{{ $beneficiarios_avances[$beneficiario['id']]['f']['acumulado'] + $beneficiarios_avances[$beneficiario['id']]['m']['acumulado'] }}
			</td>
		</tr>

		<tr height="40"></tr>

		<tr class="negrita" height="20">
			<td></td>
			<td></td>
			<td></td>
			<td colspan="4" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td></td>
			<td colspan="5" align="center">LIDER DEL PROYECTO</td>
			<td></td>
			<td></td>
		</tr>
		<tr height="40">
			<td></td>
			<td></td>
			<td></td>
			<td colspan="4" class="linea-firma"></td>
			<td></td>
			<td colspan="5" class="linea-firma"></td>
			<td></td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td></td>
			<td></td>
			<td colspan="4" align="center">{{ $proyecto['responsableInformacion'] }}</td>
			<td></td>
			<td colspan="5" align="center">{{ $proyecto['liderProyecto'] }}</td>
			<td></td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td></td>
			<td></td>
			<td colspan="4" align="center">{{ $proyecto['cargoResponsableInformacion'] }}</td>
			<td></td>
			<td colspan="5" align="center">{{ $proyecto['cargoLiderProyecto'] }}</td>
			<td></td>
			<td></td>
		</tr>
		@endforeach

	</table>
</body>