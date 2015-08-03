<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
    
		<tr>
        	<td width="40"></td>
    	    <td width="18"></td>
	        <td width="18"></td>
        	<td width="10"></td>
    	    <td width="10"></td>
        	<td width="40"></td>
        </tr>
        
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr>
    	    <td></td>
            <td>FUNCIÓN: SALUD</td><td></td><td></td><td></td><td></td>
        </tr>  
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>      		
        <tr>
    	    <td></td>
            <td>PRESUPUESTO (Millones de Pesos)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>      	
        <tr>
    	    <td></td>
            <td>MODIFICADO</td>
            <td>APROBADO</td>
            <td>VARIACIÓN</td>
            <td></td>
            <td></td>
        </tr>	
        <tr>
    	    <td></td>
            <td></td>
            <td></td>
            <td>MONTO</td>
            <td>%</td>
            <td></td>
        </tr>  
        <tr>
    	    <td></td>
            <td>{{{ $totalModificado }}}</td>
            <td>{{{ $totalAprobado }}}</td>
            <td>{{{ $totalVariacion }}}</td>
            <td>{{{ $porcentajeVariacion }}}</td>
            <td></td>
        </tr> 
        
        <tr>
    		<td>PRINCIPALES PROYECTOS Y RAZONES DE LA VARIACIÓN</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
        <tr>
    		<td>PROYECTO</td>            
            <td>MODIFICADO</td>
            <td>APROBADO</td>
            <td>VARIACIÓN</td>
            <td></td>
            <td>RAZONES</td>
        </tr>   
        
        
        <tr>
    		<td>Instituto de Salud</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
         @foreach($fila as $arrayReporte)
        <tr>
        	<td valign="top">{{{ $arrayReporte['nombre'] }}}</td>            
            <td>{{{ $arrayReporte['modificado'] }}}</td>
            <td>{{{ $arrayReporte['aprobado'] }}}</td>
            <td>{{{ $arrayReporte['variacion'] }}}</td>
            <td></td>
            <td>{{{ $arrayReporte['razonesAprobado'] }}}</td>
		</tr>
        @endforeach
              
        
        <tr>
    		<td>TOTAL</td>            
            <td>{{{ $totalModificado }}}</td>
            <td>{{{ $totalAprobado }}}</td>
            <td>{{{ $totalVariacion }}}</td>
            <td></td>
            <td></td>
        </tr> 	
        
        
        <tr>
    	    <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        
        
        
	</table>
</body>
</html>