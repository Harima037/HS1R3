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

		.cuerpo{
			font-size: 10pt;
			font-family: Arial, Calibri;
		}
		.encabezado{
			padding:2px;
			background-color: #CCCCCC;
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
	</style>
</head>
<body class="cuerpo">

	@if($reporte == 'caratula')

		{{View::make('expediente.excel.caratula',array('data'=>$data))}}

		<div style="page-break-after:always;"></div>

		{{View::make('expediente.excel.anexo-metas-jurisdiccion',array('data'=>$data))}}

		<div style="page-break-after:always;"></div>

		{{View::make('expediente.excel.anexo-metas-mes',array('data'=>$data))}}

	@endif

	@if($reporte == 'fibap')

		{{View::make('expediente.excel.ficha-informacion-basica',array('data'=>$data))}}

	@endif

</body>
</html>