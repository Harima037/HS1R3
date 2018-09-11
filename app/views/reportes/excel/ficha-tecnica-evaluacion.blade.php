<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
		<tr>
        <!--       A                   B                  C                 D                  E                  F                  G                   H                   I                 J                   K                   L                 M                  N                  O                    P                 Q                  R        -->
			<td width="11"></td><td width="7"></td><td width="7"></td><td width="7"></td><td width="9"></td><td width="3"></td><td width="6"></td><td width="10"></td><td width="3"></td><td width="3"></td><td width="13"></td><td width="6"></td><td width="6"></td><td width="3"></td><td width="12"></td><td width="12"></td><td width="8"></td><td width="9"></td>
		</tr>
		<tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		<tr>
            <td colspan="18" style="text-align:center; font-weight:bold; font-size:10;">FICHA TÉNICA DE SEGUIMIENTO DE PROYECTOS 2018</td><!--td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td-->
		</tr>
		<tr>
			<td colspan="18" style="text-align:center; font-weight:bold; font-size:9;"> AL {{$mes['trimestre_letras']}} TRIMESTRE </td><!--td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td-->
		</tr>
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
        <tr>
            <td colspan="3"  style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">ORGANISMO PÚBLICO:</td><!--td></td><td></td-->
            <td colspan="15" border="1" style="text-align:left; font-weight:bold; font-size:8;">21120640 Instituto de Salud</td><!--td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td-->
		</tr>
		<tr>
            <td colspan="3"  style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">NOMBRE DEL PROYECTO:</td>
            <td colspan="15" style="text-align:left; font-weight:bold; font-size:8;">{{$proyecto['nombre']}}</td>
		</tr>
        <tr>
            <td colspan="3"  style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PROGRAMA PRESUPUESTARIO:</td>
            <td colspan="15" style="text-align:left; font-weight:bold; font-size:8;">{{$proyecto['programa']}}</td>
		</tr>
        <tr height="35">
            <td colspan="3"  valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">FINALIDAD DEL PROYECTO:</td>
            <td colspan="15" valign="middle" style="text-align:left; font-weight:bold; font-size:8;">{{$proyecto['finalidad']}}</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr height="22" style="font-size:8;">
            <td colspan="2" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PRESUPUESTO AUTORIZADO</td>
            <td colspan="2" valign="middle">{{$proyecto['presupuesto_autorizado']}}</td>
            
            <td colspan="3" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PRESUPUESTO MODIFICADO</td>
            <td colspan="2" valign="middle">{{$proyecto['presupuesto_modificado']}}</td>

            <td colspan="2" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PRESUPUESTO DEVENGADO</td>
            <td colspan="2" valign="middle">{{$proyecto['presupuesto_devengado']}}</td>
            
            <td></td><td></td><td></td><td></td><td></td>
		</tr>
		
        <tr style="font-size:8;">
            <td colspan="3" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">FUENTE DE FINANCIAMIENTO</td>
            <td colspan="15">{{$proyecto['fuente_financiamiento']}}</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>
		
        <tr>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">% DE PROMEDIO ALCANZADO EN EL PROYECTO</td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td colspan="3" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">ACUMULADO</td>

            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td colspan="3" valign="middle" style="text-align:center; color:{{$proyecto['promedio_auxiliar_color']}}; background-color:{{$proyecto['promedio_auxiliar_fondo']}}">{{$proyecto['promedio_alcanzado']}}</td>

            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td colspan="11" style="font-size:8;">COMPORTAMIENTO DE LAS PRINCIPALES VARIABLES DEL COMPONENTE Y ACTIVIDAD</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">VARIABLE</td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td colspan="5" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">AVANCE ACUMULADO AL TRIMESTRE</td>
            <td colspan="6" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">AVANCE PROGRAMADO ANUAL</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>

            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PROGRAMADO</td>
            <td colspan="3" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">ALCANZADO</td>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">%</td>
            
            <td colspan="3" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PROGRAMADO</td>
            <td colspan="2" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">ALCANZADO</td>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">%</td>
		</tr>

        <tr style="font-size:8;">
            <td colspan="7" valign="middle" style="text-align:center;">{{$proyecto['variable']['datos']['indicador']}}</td>

            <td valign="middle" style="text-align:center;">{{$proyecto['variable']['datos']['programado']}}</td>
            <td colspan="3" valign="middle" style="text-align:center;">{{$proyecto['variable']['datos']['alcanzado']}}</td>
            <td valign="middle" style="text-align:center;">{{$proyecto['variable']['datos']['porcentaje_meta']}}</td>
            
            <td colspan="3" valign="middle" style="text-align:center;">{{$proyecto['variable']['datos']['programado_denominador']}}</td>
            <td colspan="2" valign="middle" style="text-align:center;">{{$proyecto['variable']['datos']['alcanzado']}}</td>
            <td valign="middle" style="text-align:center;">=P22/M22</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td colspan="11" style="font-size:8;">ESCTRUCTURA DEL COMPONENTE Y ACTIVIDAD</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">NIVEL DE OBJETIVO</td>
            <td colspan="4" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">INDICADOR</td>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;"></td>
            <td colspan="8" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">VARIABLE</td>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PROGRAMADO</td>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">ALCANZADO</td>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">% DE EFFEC. P/A</td>
            <td valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">% META DE INDICADOR</td>
		</tr>

        @foreach($proyecto['componentes'] as $componente)

        <tr style="font-size:8;">
            <td rowspan="2" valign="middle" style="text-align:center;">Componente {{$componente['nivel']}}</td>
            <td valign="middle" style="text-align:center;">{{$componente['indicador']}}</td><td></td><td></td><td></td>
            <td style="text-align:center;">N</td>
            <td colspan="8" style="text-align:center;">{{$componente['numerador']}}</td>
            <td style="text-align:center;">{{$componente['programado_numerador']}}</td>
            <td rowspan="2" valign="middle" style="text-align:center;">{{$componente['alcanzado']}}</td>
            <td rowspan="2" valign="middle" style="text-align:center;">=P{{$componente['auxiliar_formula']}}/O{{$componente['auxiliar_formula']}}</td>
            <td rowspan="2" valign="middle" style="text-align:center; color:{{$componente['auxiliar_color']}}; background-color:{{$componente['auxiliar_fondo']}}">{{$componente['porcentaje_meta']}}</td>
		</tr>

        <tr style="font-size:8;">
            <td></td>
            <td></td><td></td><td></td><td></td>
            <td style="text-align:center;">D</td>
            <td colspan="8" style="text-align:center; background-color:#EEECE1;">{{$componente['denominador']}}</td>
            <td style="text-align:center; background-color:#EEECE1;">{{$componente['programado_denominador']}}</td>
            <td></td>
            <td></td>
            <td></td>
		</tr>

            @foreach($componente['actividades'] as $actividad)

            <tr style="font-size:8;">
                <td rowspan="2" valign="middle" style="text-align:center;">Actividad {{$actividad['nivel']}}</td>
                <td valign="middle" style="text-align:center;">{{$actividad['indicador']}}</td><td></td><td></td><td></td>
                <td style="text-align:center;">N</td>
                <td colspan="8" style="text-align:center;">{{$actividad['numerador']}}</td>
                <td style="text-align:center;">{{$actividad['programado_numerador']}}</td>
                <td rowspan="2" valign="middle" style="text-align:center;">{{$actividad['alcanzado']}}</td>
                <td rowspan="2" valign="middle" style="text-align:center;">=P{{$actividad['auxiliar_formula']}}/O{{$actividad['auxiliar_formula']}}</td>
                <td rowspan="2" valign="middle" style="text-align:center; color:{{$actividad['auxiliar_color']}}; background-color:{{$actividad['auxiliar_fondo']}}">{{$actividad['porcentaje_meta']}}</td>
            </tr>

            <tr style="font-size:8;">
                <td></td>
                <td></td><td></td><td></td><td></td>
                <td style="text-align:center;">D</td>
                <td colspan="8" style="text-align:center; background-color:#EEECE1;">{{$actividad['denominador']}}</td>
                <td style="text-align:center; background-color:#EEECE1;">{{$actividad['programado_denominador']}}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            @endforeach

        @endforeach

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr height="60">
            <td colspan="2" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">EVALUACIÓN:</td>
            <td colspan="16" valign="middle" style="font-size:8;">{{$proyecto['evaluacion']}}</td>
		</tr>

        <tr height="29">
            <td colspan="2" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">JUSTIFICACIÓN:</td>
            <td colspan="16" valign="middle" style="font-size:8;">{{($proyecto['variable']['tipo'])?$proyecto['variable']['datos']['registro_avance']['analisisResultadosTrimestral']:''}}</td>
		</tr>

        <tr height="37">
            <td colspan="2" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">PLAN DE ACCIÓN DE MEJORA:</td>
            <td colspan="16" valign="middle" style="font-size:8;">{{($proyecto['variable']['tipo'])?$proyecto['variable']['datos']['plan_mejora']['accionMejora']:''}}</td>
		</tr>

        <tr height="43">
            <td colspan="2" valign="middle" style="text-align:center; font-weight:bold; font-size:8; background-color:#DDDDDD;">RECOMENDACIÓN:</td>
            <td colspan="16" valign="middle" style="font-size:8;">{{($proyecto['variable']['tipo'])?$proyecto['variable']['recomendacion']:''}}</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">RESPONSABLE DE LA INFORMACIÓN</td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">LIDER DEL PROYECTO</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;"></td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;"></td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['responsable_informacion']['nombre']}}</td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['lider_proyecto']['nombre']}}</td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['responsable_informacion']['cargo']}}</td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['lider_proyecto']['cargo']}}</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">SUBCOORDINADOR DEL GRUPO ESTRATEGICO</td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">COORDINADOR DEL GRUPO ESTRATEGICO</td>
		</tr>

        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;"></td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;"></td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['subcoordinador_grupo_estrategico']['nombre']}}</td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['coordinador_grupo_estrategico']['nombre']}}</td>
		</tr>

        <tr>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['subcoordinador_grupo_estrategico']['cargo']}}</td>
            <td></td><td></td>
            <td colspan="8" style="text-align:center; font-size:9; font-weight:bold;">{{$proyecto['coordinador_grupo_estrategico']['cargo']}}</td>
		</tr>
	</table>
</body>
</html>