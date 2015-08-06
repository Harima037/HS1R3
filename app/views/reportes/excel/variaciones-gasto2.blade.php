<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
    
		<tr>
        	<td width="15"></td>
            <td width="15"></td>
    	    <td width="18"></td>
	        <td width="15"></td>
        	<td width="15"></td>
    	    <td width="15"></td>
        	<td width="40"></td>
        </tr>
        
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr>
    	    <td></td><td></td>
            <td>FUNCIÓN: SALUD</td><td></td><td></td><td></td><td></td>
        </tr>  
        <tr>
    	    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>      		
        <tr>
    	    <td></td><td></td>
            <td>PRESUPUESTO (Millones de Pesos)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>      	
        <tr>
    	    <td></td><td></td>
            <td>MODIFICADO</td>
            <td>DEVENGADO</td>
            <td>VARIACIÓN</td>
            <td></td>
            <td></td>
        </tr>	
        <tr>
    	    <td></td><td></td>
            <td></td>
            <td></td>
            <td>MONTO</td>
            <td>%</td>
            <td></td>
        </tr>  
        <tr>
    	    <td></td><td></td>
            <td>{{{ $totalModificado }}}</td><td>{{{ $totalDevengado }}}</td>
            <td></td><td></td>
            <td></td>
        </tr> 
        
        <tr>
    	    <td></td><td></td>
            <td></td><td></td>
            <td></td><td></td>
            <td></td>
        </tr>
        <tr>
    	    <td></td><td></td>
            <td></td><td></td>
            <td></td><td></td>
            <td></td>
        </tr>
        
        <tr>
    		<td>PRINCIPALES PROYECTOS Y RAZONES DE LA VARIACIÓN</td>
            <td></td><td></td>
            <td></td><td></td>
            <td></td><td></td>
        </tr>
        
        <tr>
    		<td>PROYECTO</td><td></td><td></td>            
            <td>MODIFICADO</td>
            <td>DEVENGADO</td>
            <td>VARIACIÓN</td>            
            <td>RAZONES</td>
        </tr>   
        
        
        <tr>
    		<td>Instituto de Salud</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td><td></td>
        </tr>
         @foreach($fila as $arrayReporte)
         	@if($arrayReporte['mostrarDevengado']==1)
		        <tr>
        			<td valign="top">{{{ $arrayReporte['nombre'] }}}</td><td></td><td></td>                       
		            <td>{{{ $arrayReporte['modificado'] }}}</td>
        		    <td>{{{ $arrayReporte['devengado'] }}}</td>
		            <td></td>
        		    <td>{{{ $arrayReporte['razonesDevengado'] }}}</td>
				</tr>
			@endif
        @endforeach
              
        
        <tr>
    		<td>TOTAL</td><td></td><td></td>            
            <td></td><td></td><td></td><td></td>
        </tr>
        <tr>
    	    <td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td>
        </tr>
        
        
        
	</table>
</body>
</html>