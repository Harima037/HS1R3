<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
		<tr>
			<td colspan="10" align="center">INSTITUTO DE SALUD</td>
		</tr>
		<tr>
			<td colspan="10" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
		</tr>
		<tr>
			<td colspan="10" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td>
		</tr>
		<tr>
			<td colspan="10" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
		</tr>
		<tr>
			<td colspan="10" align="center">SEGUIMIENTO DE METAS {{$proyecto['ejercicio']}}</td>
		</tr>
		<tr>
			<td colspan="10" align="right">Formato Rc-3</td>
		</tr>



		<tr>
			<td colspan="3">Nombre del proyecto: {{ $proyecto['nombreTecnico'] }}</td>
			<td colspan="3">Clave presupuestaria: {{ $proyecto['ClavePresupuestaria'] }}</td>
			<td colspan="3">Al mes de: {{$mes['mes']}}</td>
			<td></td>
		</tr>
		<tr>
			<td>Información</td>
			<td><b>Estatal</b></td>
			<td colspan="8"></td>
		</tr>
		<tr><td colspan="10"></td></tr>
		<tr>
			<td>NIVEL</td>
			<td>INDICADOR</td>
			<td>META PROGRAMADA</td>
			<td>META MODIFICADA</td>
			<td>AVANCES DEL MES</td>
			<td>AVANCE ACUMULADO</td>
			<td>% DE AVANCE ACUMULADO</td>
			<td>% DE AVANCE MODIFICADO</td>
			<td>ANALISIS DE RESULTADOS 	ACUMULADO</td>
			<td>JUSTIFICACIÓN ACUMULADA</td>
		</tr>
		<tr><td colspan="10"></td></tr>
		<tr>
			<td><b>Fuente de información:</b></td>
			<td colspan="3"></td>
		</tr>
		<tr><td colspan="10"></td></tr>



		<tr>
			<td></td>
			<td colspan="3" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
			<td colspan="2"></td>
			<td colspan="3" align="center">LIDER DEL PROYECTO</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="3" align="center">______________________________________</td>
			<td colspan="2"></td>
			<td colspan="3" align="center">______________________________________</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="3" align="center">Nombre</td>
			<td colspan="2"></td>
			<td colspan="3" align="center">Nombre</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="3" align="center">Cargo</td>
			<td colspan="2"></td>
			<td colspan="3" align="center">Cargo</td>
			<td></td>
		</tr>

		<tr><td colspan="10"></td></tr>
		<tr>
			<td>NIVEL</td>
			<td>INDICADOR</td>
			<td>META PROGRAMADA</td>
			<td>META MODIFICADA</td>
			<td>AVANCES DEL MES</td>
			<td>AVANCE ACUMULADO</td>
			<td>% DE AVANCE ACUMULADO</td>
			<td>% DE AVANCE MODIFICADO</td>
			<td></td>
			<td></td>
		</tr>

		<tr></tr>
	</table>
</body>
</html>