<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
		<tr>
			<td style="font-weight:bold;">GOBIERNO DEL ESTADO DE CHIAPAS</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td style="font-weight:bold;">Secretaría de Salud</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td style="font-weight:bold;">Estado Programático-Funcional</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td style="font-weight:bold;">Del 1 de Enero al {{$fecha_trimestre}}</td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td> ( PESOS ) </td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<tr>
			<td valign="middle">CONCEPTO</td>  <td valign="middle">PRESUPUESTO</td><td></td><td></td>  <td></td>
			<td valign="middle">APLICACIÓN Y DISPONIBILIDAD PRESUPUESTARIA</td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<td></td>  <td valign="middle">APROBADO</td><td valign="middle">MODIFICACIÓN<br>NETA</td><td valign="middle">TOTAL<br>MODIFICADO</td>  <td></td>
			<td valign="middle">DEVENGADO</td><td valign="middle">COMPROMETIDO</td><td valign="middle">POR<br>LIBERAR</td><td valign="middle">DISPONIBLE<br>PRESUPUESTARIO</td><td valign="middle">TOTAL APLICACIÓN Y<br>DISPONIBILIDAD PPTAL.</td>
		</tr>

		<tr>
			<td></td><td></td><td></td><td></td><td></td>
			<td></td><td></td><td></td><td></td><td></td>
		</tr>

		<tr>
			<td>Salud</td><td></td><td></td><td></td><td></td>
			<td></td><td></td><td></td><td></td><td></td>
		</tr>
		<!-- {{$current_row = 11;}} -->
		@foreach($datos as $dato)
		<!-- {{$current_row++;}} -->
		<tr>
			<td>{{ $dato->concepto }}</td>
			<td align="right">{{ $dato->presupuestoAprobado }}</td>
			<td align="right">{{ '=D'.$current_row.'-B'.$current_row }}</td>
			<td align="right">{{ $dato->presupuestoModificado }}</td>
			<td></td>
			<td align="right">{{ $dato->presupuestoDevengadoModificado }}</td>
			<td align="right">{{ $dato->presupuestoComprometidoModificado }}</td>
			<td align="right">{{ $dato->presupuestoPorLiberar }}</td>
			<td align="right">{{ $dato->disponiblePresupuestarioModificado }}</td>
			<td align="right">{{ '=(G'.$current_row.'-F'.$current_row.')+F'.$current_row.'+H'.$current_row.'+I'.$current_row }}</td>
		</tr>
		@endforeach
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<tr>
			<td style="font-weight:bold;">TOTALES</td> <td style="font-weight:bold;">{{ '=SUM(B12:B'.$current_row.')' }}</td><td style="font-weight:bold;">{{ '=SUM(C12:C'.$current_row.')' }}</td> <td></td><td></td>
			<td style="font-weight:bold;">{{ '=SUM(F12:F'.$current_row.')' }}</td><td style="font-weight:bold;">{{ '=SUM(G12:G'.$current_row.')' }}</td><td style="font-weight:bold;">{{ '=SUM(H12:H'.$current_row.')' }}</td><td style="font-weight:bold;">{{ '=SUM(I12:I'.$current_row.')' }}</td><td></td>
		</tr>

		<tr>
			<td style="font-weight:bold;">SUMAS IGUALES</td> <td></td><td></td> <td style="font-weight:bold;">{{ '=SUM(D12:D'.$current_row.')' }}</td><td></td>
			<td></td><td></td><td></td><td></td><td style="font-weight:bold;">{{ '=SUM(J12:J'.$current_row.')' }}</td>
		</tr>

		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>

		<!--tr><td width="36"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="12"></td><td width="20"></td><td width="20"></td><td width="20"></td></tr-->
	</table>
</body>
</html>