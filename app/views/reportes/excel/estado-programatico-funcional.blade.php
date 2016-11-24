<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
		<tr>
			<td style="font-weight:bold; font-size:10;">GOBIERNO DEL ESTADO DE CHIAPAS</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td style="font-weight:bold; font-size:10;">Instituto de Salud</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td style="font-weight:bold; font-size:10;">Estado Programático-Funcional</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td style="font-weight:bold; font-size:10;">Del 1 de Enero al {{$fecha_trimestre}}</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td style="font-weight:bold; font-size:8;"> ( PESOS ) </td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

		<tr>
			<td width="30.4"></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td width="20.4"></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td valign="middle" style="font-size:9;">CONCEPTO</td>
			<td valign="middle" style="font-size:10;">PRESUPUESTO</td>
			<td></td>
			<td></td>
			<td></td>
			<td valign="middle" style="font-size:10;">APLICACIÓN Y DISPONIBILIDAD PRESUPUESTARIA</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td valign="middle"  style="font-size:9;">APROBADO</td>
			<td valign="middle"  style="font-size:9;">MODIFICACIÓN<br>NETA</td>
			<td valign="middle"  style="font-size:9;">TOTAL<br>MODIFICADO</td>
			<td></td>
			<td valign="middle"  style="font-size:9;">DEVENGADO</td>
			<td valign="middle"  style="font-size:9;">COMPROMISO POR APLICAR</td>
			<td valign="middle"  style="font-size:9;">DISPONIBLE<br>PRESUPUESTARIO</td>
			<td valign="middle"  style="font-size:9">TOTAL APLICACIÓN Y<br>DISPONIBILIDAD PPTAL.</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td style="font-size:10; font-weight:bold;" valign="top" height="25">Salud</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<!-- {{$current_row = 11;}} -->
		@foreach($datos as $dato)
		<!-- {{$current_row++;}} -->
		<tr>
			<td style="font-size:10;" valign="top" height="25" >{{ $dato->concepto }}</td>
			<td style="font-size:10;" valign="top" align="right">{{ $dato->presupuestoAprobado }}</td>
			<td style="font-size:10;" valign="top" align="right">{{ $dato->modificacionNeta }}</td>
			<td style="font-size:10;" valign="top" align="right">{{ '=SUM(B'.$current_row.':C'.$current_row.')' }}</td>
			<td></td>
			<td style="font-size:10;" valign="top" align="right">{{ $dato->presupuestoDevengadoModificado }}</td>
			<td style="font-size:10;" valign="top" align="right">{{ $dato->presupuestoComprometidoModificado - $dato->presupuestoDevengadoModificado }}</td>
			<td style="font-size:10;" valign="top" align="right">{{ $dato->presupuestoModificado - $dato->presupuestoComprometidoModificado }}</td>
			<td style="font-size:10;" valign="top" align="right">{{ '=SUM(F'.$current_row.':H'.$current_row.')' }}</td>
		</tr>
		@endforeach

		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<tr>
			<td style="font-weight:bold; font-size:10;" valign="top" height="25" >TOTALES</td> 
			<td style="font-weight:bold; font-size:10;" valign="top">{{ '=SUM(B12:B'.$current_row.')' }}</td>
			<td style="font-weight:bold; font-size:10;" valign="top">{{ '=SUM(C12:C'.$current_row.')' }}</td>
			<td></td>
			<td></td>
			<td style="font-weight:bold; font-size:10;" valign="top">{{ '=SUM(F12:F'.$current_row.')' }}</td>
			<td style="font-weight:bold; font-size:10;" valign="top">{{ '=SUM(G12:G'.$current_row.')' }}</td>
			<td style="font-weight:bold; font-size:10;" valign="top">{{ '=SUM(H12:H'.$current_row.')' }}</td>
			<td></td>
		</tr>

		<tr>
			<td style="font-weight:bold; font-size:10;" valign="top" height="25" >SUMAS IGUALES</td> 
			<td></td>
			<td></td> 
			<td style="font-weight:bold; font-size:10;" valign="top">{{ '=SUM(D12:D'.$current_row.')' }}</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="font-weight:bold; font-size:10;" valign="top">{{ '=SUM(I12:I'.$current_row.')' }}</td>
		</tr>

		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td height="15"></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<tr>
			<td></td>
			<td style="font-size:10;">{{{$firmas['secretario']->nombre}}}</td>
			<td></td>
			<td></td>
			<td></td>
			<td style="font-size:10;">{{{$firmas['dir_planeacion']->nombre}}}</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td style="font-size:10;">{{{$firmas['secretario']->cargo}}}</td>
			<td></td>
			<td></td>
			<td></td>
			<td style="font-size:10;">{{{$firmas['dir_planeacion']->cargo}}}</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
</body>
</html>