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
			background-color: #0070C0;
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
		.subsubtitulo-tabla{
			font-weight: bold;
			background-color: #EFEFEF;
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
				<td rowspan="5" class="imagen izquierda"><img src="{{ public_path().'/img/LogoFederal.png' }}" width="125"></td>
				<td class="titulo1" align="center">INSTITUTO DE SALUD</td>
				<td rowspan="5" class="imagen derecha"><img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="125"></td>
			</tr>
			<tr><td class="titulo2" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td></tr>
			<tr><td class="titulo3" align="center">SUBDIRECCIÓN DE PLANEACIÓN EN SALUD</td></tr>
			<tr><td class="titulo3" align="center">DEPARTAMENTO DE EVALUACIÓN</td></tr>
			<tr><td class="titulo3" align="center">ESTRATEGIA INSTITUCIONAL {{$estrategia['ejercicio']}}</td></tr>
			<tr><td height="18" colspan="3" align="right" class="negrita">Formato RC-5</td></tr>
		</table>
        <table width="100%">
		<tr height="90" class="texto-medio texto">
			<td width="250px">Nombre del la Estrategia Institucional : </td>
			<td class="negrita" colspan="2">{{ $estrategia['nombre'] }}</td>
			<td width="90px">Trimestre: </td>
			<td class="negrita">{{ $trimestre_lbl }}</td>
		</tr>
	</table>
    <br>
    <table width="100%">
		<tr class="tabla-datos" height="50">
			<td class="encabezado-tabla">INDICADOR</td>
			<td class="encabezado-tabla">META ANUAL</td>
			<td class="encabezado-tabla">META TRIMESTRAL</td>
			<td class="encabezado-tabla">AVANCES DEL TRIMESTRE</td>
			<td class="encabezado-tabla">AVANCE ACUMULADO</td>
			<td class="encabezado-tabla">% DE AVANCE TRIMESTRAL</td>
			<td class="encabezado-tabla">% DE AVANCE ACUMULADO</td>
			<td class="encabezado-tabla">ANALISIS DE RESULTADOS</td>
			<td class="encabezado-tabla">JUSTIFICACIÓN</td>
		</tr>
		@foreach($indicadores as $indicador)
		<tr height="50" class="tabla-datos">
			
			<td class="texto-medio">{{$indicador['indicador']}}</td>
			<td class="texto-medio texto-centro">{{number_format($indicador['meta_original'],2)}}</td>
			<td class="texto-medio texto-centro">{{number_format($indicador['meta_trimestral'],2)}}</td>
			<td class="texto-medio texto-centro">{{number_format($indicador['avance_trimestre'],2)}}</td>
			<td class="texto-medio texto-centro">{{number_format($indicador['avance_acumulado'],2)}}</td>
			<td class="texto-medio texto-centro">
			@if($indicador['avance_trimestre'] > 0)
				{{
				round(($indicador['avance_trimestre']/$indicador['meta_trimestral'])*100,2)
				}}
			@else
				0
			@endif
			 %</td>
			<td class="texto-medio">@if($indicador['avance_acumulado'] > 0)
				{{
				round(($indicador['avance_acumulado']/$indicador['meta_original'])*100,2)
				}}
			@else
				0
			@endif
				%</td> 
			<td class="texto-medio">{{$indicador['analisis_resultados']}}</td>
			<td class="texto-medio">{{$indicador['justificacion_acumulada']}}</td>
		</tr>
		@endforeach
	</table>
    <table width="100%">
		<tr>
			<td class="nota-titulo" width="130px">Fuente de información:</td>
			<td class="nota-contenido" colspan="3">{{$estrategia['fuenteInformacion']}}</td>
		</tr>
	</table>
	<br><br><br>
	<table width="100%">
		<tr class="negrita" height="20">
			<td width="5%"></td>
			<td width="40%" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td></td>
			<td width="40%" align="center">LIDER DEL PROYECTO</td>
			<td width="5%"></td>
		</tr>
		<tr height="40">
			<td></td>
			<td class="linea-firma"></td>
			<td></td>
			<td class="linea-firma"></td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td align="center">{{ $estrategia['responsableInformacion'] }}</td>
			<td></td>
			<td align="center">{{ $estrategia['liderPrograma'] }}</td>
			<td></td>
		</tr>
		<tr class="negrita" height="20">
			<td></td>
			<td align="center">{{ $estrategia['cargoResponsableInformacion'] }}</td>
			<td></td>
			<td align="center">{{ $estrategia['cargoLiderPrograma'] }}</td>
			<td></td>
		</tr>
	</table>
	</div>

</body>
</html>