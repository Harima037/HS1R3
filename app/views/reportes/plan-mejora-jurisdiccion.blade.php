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
		.tabla-datos{
			width: 100%;
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
				<td rowspan="5" class="imagen izquierda">
					<img src="{{ public_path().'/img/LogoFederal.png' }}" width="125">
				</td>
				<td class="titulo1" align="center">INSTITUTO DE SALUD</td>
				<td rowspan="5" class="imagen derecha">
					<img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="125">
				</td>
			</tr>
			<tr><td class="titulo2" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td></tr>
			<tr><td class="titulo3" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td></tr>
			<tr><td class="titulo3" align="center">DEPARTAMENTO DE EVALUACIÓN</td></tr>
			<tr><td class="titulo3" align="center">PLAN DE ACCIÓN DE MEJORA {{$mes['trimestre_letras']}} TRIMESTRE {{$proyecto['ejercicio']}}</td></tr>
			<tr><td colspan="3" align="right" class="negrita">Formato RC-7</td></tr>
		</table>
		<table>
			<tr height="90" class="texto-medio texto">
				<td width="100" class="texto-centro">Nombre del proyecto: </td>
				<td class="negrita" colspan="7">{{ $proyecto['nombreTecnico'] }}</td>
				<td width="105" class="texto-centro">Clave presupuestaria: </td>
				<td width="90" class="negrita" colspan="2">{{ $proyecto['ClavePresupuestaria'] }}</td>
			</tr>
			<tr><td colspan="13" height="20"></td></tr>
		</table>
	</div>

	<table class="tabla-datos">
	<thead>
		<tr>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">NIVEL</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">INDICADOR</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">% DE AVANCE<br> ACUMULADO</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">JUSTIFICACIÓN</th>
			<th height="20"  colspan="2" class="encabezado-tabla" style="font-size:6;">¿REQUIERE ACCIÓN DE MEJORA?</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">ACCIÓN <br>DE MEJORA</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">GRUPO DE <br>TRABAJO</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">FECHA DE <br>INICIO</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">FECHA DE <br>TERMINO</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">FECHA DE <br>NOTIFICACIÓN</th>
			<th height="20"  rowspan="2" class="encabezado-tabla" style="font-size:6;">DOCUMENTACIÓN <br>COMPROBATORIA</th>
		</tr>
		<tr>
			<th height="13" class="encabezado-tabla" style="font-size:6;">SI</th>
			<th height="13" class="encabezado-tabla" style="font-size:6;">NO</th>
		</tr>
	</thead>
	<tbody>
		@foreach($proyecto['componentes'] as $index => $componente)
		<tr>
			<td class="texto-medio" nowrap="nowrap">Componente {{$index+1}}</td>
			<td class="texto-medio">{{{ $componente['indicador'] }}}</td>
			<td class="texto-medio texto-centro">% {{{number_format($componente['avance_acumulado'],2)}}}</td>
			<td class="texto-medio">{{{ ($componente['estatus'] > 1)?((isset($componente['plan_mejora']))?$componente['plan_mejora']->justificacionAcumulada:'De debe capturar Plan de Acción de Mejora'):'El avance se encuentra dentro de lo programado' }}}</td>
			<td class="texto-medio texto-centro">{{{($componente['estatus'] > 1)?'X':''}}}</td>
			<td class="texto-medio texto-centro">{{{($componente['estatus'] === 1)?'X':''}}}</td>
			@if(isset($componente['plan_mejora']))
				<td class="texto-medio">{{{ $componente['plan_mejora']->accionMejora }}}</td>
				<td class="texto-medio">{{{ $componente['plan_mejora']->grupoTrabajo }}}</td>
				<td class="texto-medio">{{{ $componente['plan_mejora']->fechaInicio }}}</td>
				<td class="texto-medio">{{{ $componente['plan_mejora']->fechaTermino }}}</td>
				<td class="texto-medio">{{{ $componente['plan_mejora']->fechaNotificacion }}}</td>
				<td class="texto-medio">{{{ $componente['plan_mejora']->documentacionComprobatoria }}}</td>
			@else
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			@endif
		</tr>

		@foreach($componente['actividades'] as $indice => $actividad)
		<tr>
			<td class="texto-medio">Actividad {{$index+1}}.{{$indice+1}}</td>
			<td class="texto-medio">{{{ $actividad['indicador'] }}}</td>
			<td class="texto-medio texto-centro">% {{{number_format($actividad['avance_acumulado'],2)}}}</td>
			<td class="texto-medio">{{{ ($actividad['estatus'] > 1)?((isset($actividad['plan_mejora']))?$actividad['plan_mejora']->justificacionAcumulada:'De debe capturar Plan de Acción de Mejora'):'El avance se encuentra dentro de lo programado' }}}</td>
			<td class="texto-medio texto-centro">{{{($actividad['estatus'] > 1)?'X':''}}}</td>
			<td class="texto-medio texto-centro">{{{($actividad['estatus'] === 1)?'X':''}}}</td>
			@if(isset($actividad['plan_mejora']))
				<td class="texto-medio">{{{ $actividad['plan_mejora']->accionMejora }}}</td>
				<td class="texto-medio">{{{ $actividad['plan_mejora']->grupoTrabajo }}}</td>
				<td class="texto-medio">{{{ $actividad['plan_mejora']->fechaInicio }}}</td>
				<td class="texto-medio">{{{ $actividad['plan_mejora']->fechaTermino }}}</td>
				<td class="texto-medio">{{{ $actividad['plan_mejora']->fechaNotificacion }}}</td>
				<td class="texto-medio">{{{ $actividad['plan_mejora']->documentacionComprobatoria }}}</td>
			@else
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			@endif
		</tr>
		@endforeach
		@endforeach
	</tbody>
	</table>

	<table style="page-break-inside:avoid;">
		<tr><td colspan="5" height="10">&nbsp;</td></tr>
		<tr class="negrita" height="20">
			<td width="10%"></td>
			<td align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td width="10%"></td>
			<td align="center">LIDER DEL PROYECTO</td>
			<td width="10%"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td height="20" class="linea-firma">&nbsp;</td>
			<td>&nbsp;</td>
			<td class="linea-firma">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr class="negrita" height="20">
			<td>&nbsp;</td>
			<td align="center">{{ $proyecto['responsableInformacion'] }}</td>
			<td>&nbsp;</td>
			<td align="center">{{ $proyecto['liderProyecto'] }}</td>
			<td>&nbsp;</td>
		</tr>
		<tr class="negrita" height="20">
			<td>&nbsp;</td>
			<td align="center">{{ $proyecto['cargoResponsableInformacion'] }}</td>
			<td>&nbsp;</td>
			<td align="center">{{ $proyecto['cargoLiderProyecto'] }}</td>
			<td>&nbsp;</td>
		</tr>
	</table>
</body>
</html>