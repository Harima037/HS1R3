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

		body{
			margin-top:10.3em;
		}

		header {
			position: fixed;
			top: 0cm;
			left: 0cm;
			right: 0cm;
			height: 2cm;
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

	@if($reporte == 'caratula')

		<header>
			<table class="tabla" width="100%">
				<tr>
					<td rowspan="5" class="imagen izquierda">
						<img src="{{ public_path().'/img/LogoFederal.png' }}" width="150">
					</td>
					<td colspan="11" class="titulo">GOBIERNO DEL ESTADO DE CHIAPAS</td>
					<td colspan="3" rowspan="5" class="imagen derecha">
						<img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="150">
					</td>
				</tr>
				<tr>
					<td colspan="11" class="titulo">SECRETARÍA DE SALUD</td>
				</tr>
				<tr>
					<td colspan="11" class="titulo">INSTITUTO DE SALUD</td>
				</tr>
				<tr>
					<td colspan="11" class="titulo">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
				</tr>
				<tr>
					<td colspan="11" class="titulo">DEPARTAMENTO DE EVALUACIÓN</td>
				</tr>
			</table>
		</header>
		
		{{View::make('expediente.pdf.caratula',array('data'=>$data))}}

		<div style="page-break-after:always;"></div>

		{{View::make('expediente.pdf.anexo-programacion-metas',array('data'=>$data))}}

	@endif

	@if($reporte == 'fibap')

		{{View::make('expediente.pdf.ficha-informacion-basica',array('data'=>$data))}}

	@endif

	@if($reporte == 'cedula')

		{{View::make('expediente.pdf.cedula-validacion-acciones',array('data'=>$data))}}

	@endif

</body>
</html>