<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>

		<tr><td width="38.07"></td><td width="21.06"></td>
		<td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td><td width="17.95"></td>
		<td width="22"></td><td width="22"></td><td width="22"></td></tr>

		<tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		

		<tr>			
			<td>2112 064 0 INSTITUTO DE SALUD</td><td></td><td></td>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
               	
        <tr><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td><td></tr>
        
        <tr class="encabezado-tabla"><td valign="middle" height="15">GASTO REGIONALIZADO EN CLASIFICACIÓN FUNCIONAL</td></tr>
		<tr class="encabezado-tabla"><td valign="middle" height="15">AL {{$trimestre}} TRIMESTRE DEL {{$ejercicio}}</td></tr>

		<tr class="encabezado-tabla">
			<td valign="middle" height="23">FUNCIÓN / PROGRAMAS PRESUPUESTARIOS</td>
			<td valign="middle">PRESUPUESTO DEVENGADO</td>
            <td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>			
			<td valign="middle">TOTAL DEVENGADO POR PROGRAMA</td>
			<td valign="middle">PRESUPUESTO APROBADO</td>
			<td valign="middle">PRESUPUESTO MODIFICADO</td>
		</tr>
		<tr class="encabezado-tabla">
			<td height="38"></td>			
			<td valign="middle">COBERTURA ESTATAL</td>			
			<td valign="middle">REGIÓN I</td>
			<td valign="middle">REGIÓN II</td>
			<td valign="middle">REGIÓN III</td>
			<td valign="middle">REGIÓN IV</td>
			<td valign="middle">REGIÓN V</td>
			<td valign="middle">REGIÓN VI</td>
			<td valign="middle">REGIÓN VII</td>
			<td valign="middle">REGIÓN VIII</td>
			<td valign="middle">REGIÓN IX</td>
			<td valign="middle">REGIÓN X</td>
			<td valign="middle">REGIÓN XI</td>
			<td valign="middle">REGIÓN XII</td>
			<td valign="middle">REGIÓN XIII</td>
			<td valign="middle">REGIÓN XIV</td>
			<td valign="middle">REGIÓN XV</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
        <tr>
			<td height="5"></td>			
			<td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td>			
			<td></td><td></td><td></td><td></td>			
			<td></td><td></td><td></td><td></td>
			<td></td><td></td><td></td>
		</tr>
        <tr>
			<td height="15">SALUD</td>			
			<td></td>			
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
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
		<!-- {{$current_row = 15;}} -->
        @foreach($fila as $filaEP01)
        <!-- {{$current_row++;}} -->
        <tr>
			<td>{{{ $filaEP01['programapre'] }}}</td>
            <td>{{{ $filaEP01['importeEstatal'] }}}</td>
            <td>{{{ $filaEP01['regiones'][1] }}}</td>
            <td>{{{ $filaEP01['regiones'][2] }}}</td>
            <td>{{{ $filaEP01['regiones'][3] }}}</td>
            <td>{{{ $filaEP01['regiones'][4] }}}</td>
            <td>{{{ $filaEP01['regiones'][5] }}}</td>
            <td>{{{ $filaEP01['regiones'][6] }}}</td>
            <td>{{{ $filaEP01['regiones'][7] }}}</td>
            <td>{{{ $filaEP01['regiones'][8] }}}</td>
            <td>{{{ $filaEP01['regiones'][9] }}}</td>
            <td>{{{ $filaEP01['regiones'][10] }}}</td>
            <td>{{{ $filaEP01['regiones'][11] }}}</td>
            <td>{{{ $filaEP01['regiones'][12] }}}</td>
            <td>{{{ $filaEP01['regiones'][13] }}}</td>
            <td>{{{ $filaEP01['regiones'][14] }}}</td>
            <td>{{{ $filaEP01['regiones'][15] }}}</td>
            <td>{{{ '=SUM(B'.$current_row.':Q'.$current_row.')' }}}</td>
            <td>{{{ $filaEP01['presupuestoAprobado'] }}}</td>
            <td>{{{ $filaEP01['presupuestoModificado'] }}}</td>
		</tr>
        @endforeach
        <tr>
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
        <tr>
			<td align="center">SUMA TOTAL</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr height="5">
			<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
	</table>
</body>
</html>