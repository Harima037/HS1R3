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
        table{
        	width:100%;
        	border-collapse: collapse;
        }
        
        .misma-linea{
        	display: inline-block;
        }
		.cuerpo{
			font-size: 8pt;
			font-family: Arial, Calibri;
		}
		.titulo1{
			font-weight: bold;
			font-family: Arial;
			font-size: 14;
		}
		.titulo2{
			font-weight: bold;
			font-family: Arial;
			font-size: 13;
		}
		.titulo3{
			font-weight: bold;
			font-family: Arial;
			font-size: 12;
		}
		.titulo4{
			font-weight: bold;
			font-family: Arial;
			font-size: 11;
		}
		.texto{
			font-family: Arial;
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
			font-family: Arial;
			font-size: 10;
			font-weight: bold;
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
		.nota-titulo{
			font-family: Arial;
			font-size:8;
			font-weight: bold;
		}
		.nota-contenido{
			font-family: Arial;
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
		.portrait {
		    -webkit-transform: rotate(90deg); /* Safari and Chrome */
		    -moz-transform: rotate(90deg);   /* Firefox */
		    -ms-transform: rotate(90deg);   /* IE 9 */
		    -o-transform: rotate(90deg);   /* Opera */
		    transform: rotate(90deg);
		} 
	</style>
</head>
<body class="cuerpo">
	@if($tipo_reporte == 'analisis')
		{{View::make('rendicion-cuentas.pdf.analisis-funcional',$datos)}}
	@else
		{{View::make('rendicion-cuentas.pdf.seguimiento-metas-mes',$datos)}}
		@if($reporte == 'trimestre')
			<div style="page-break-after:always;"></div>
			{{View::make('rendicion-cuentas.pdf.seguimiento-beneficiarios',$datos)}}
			<div style="page-break-after:always;"></div>
			{{View::make('rendicion-cuentas.pdf.plan-mejora',$datos)}}
		@endif
	@endif
</body>
</html>