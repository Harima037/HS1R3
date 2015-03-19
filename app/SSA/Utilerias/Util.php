<?php
namespace SSA\Utilerias;

class Util 
{
	public static function obtenerMesActual(){
		$usuario = \Sentry::getUser();
		if($usuario->mesCaptura){
			return $usuario->mesCaptura;
		}else{
			return date("n");
		}
		$mes = date('n');
		$dia = date('j');
		if($dia <= 10){
			$mes = $mes - 1;
		}else{
			$mes = 0;
		}
		return $mes;
	}
	public static function obtenerMesTrimestre(){
		$mes_actual = self::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n');
		}
		$trimestre = ceil($mes_actual/3);
        $ajuste = ($trimestre - 1) * 3;
        $mes_del_trimestre = $mes_actual - $ajuste;
        return $mes_del_trimestre;
	}
	public static function obtenerTrimestre(){
		$mes_actual = self::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n');
		}
		$trimestre = ceil($mes_actual/3);
		return $trimestre;
	}
	public static function transformarFecha($fecha)
	{
		$fecha= explode("-",$fecha);
	
		switch($fecha[1])
		{
			case '1': $fecha[1]= "enero";break;
			case '2': $fecha[1]= "febrero";break;
			case '3': $fecha[1]= "marzo";break;
			case '4': $fecha[1]= "abril";break;
			case '5': $fecha[1]= "mayo";break;
			case '6': $fecha[1]= "junio";break;						
			case '7': $fecha[1]= "julio";break;	
			case '8': $fecha[1]= "agosto";break;
			case '9': $fecha[1]= "septiembre";break;
			case '10': $fecha[1]= "octubre";break;
			case '11': $fecha[1]= "noviembre";break;
			case '12': $fecha[1]= "diciembre";break;
		}
		return $fecha[2]." de ".$fecha[1]." del ".$fecha[0];
	}
	public static function transformarCantidadLetras($cantidad)
	{
		$cantidad = number_format($cantidad, 2, '.', '');
		$numeros = explode('.',$cantidad);
		
		
		
		$enteros = number_format($numeros[0]);
		
		$enteros_array = explode(',',$enteros);
		
		$total = count($enteros_array);
		$numero_letras = "";
		

		$miles = array();
		switch($total)
		{
			case 2 : $miles[0][0] = " MIL"; $miles[0][1] = " MIL"; break;
			case 3 :
					$miles[0][0] = " MILLON"; $miles[0][1] = " MILLONES"; 
					$miles[1][0] = " MIL"; $miles[1][1] = " MIL";
					break;
			case 4 : 
					$miles[0][0] = " MIL"; $miles[0][1] = " MIL";
					$miles[1][0] = " MILLON"; $miles[1][1] = " MILLONES";
					$miles[2][0] = " MIL"; $miles[2][1] = " MIL";
					break;
			case 5 : 
					$miles[0][0] = " BILLON"; $miles[0][1] = " BILLONES";
					$miles[1][0] = " MIL"; $miles[1][1] = " MIL";
					$miles[2][0] = " MILLON"; $miles[2][1] = " MILLONES";
					$miles[3][0] = " MIL"; $miles[3][1] = " MIL";
					break;
			case 6 : 
					$miles[0][0] = " MIL"; $miles[0][1] = " MIL";
					$miles[1][0] = " BILLON"; $miles[1][1] = " BILLONES";
					$miles[2][0] = " MIL"; $miles[2][1] = " MIL";
					$miles[3][0] = " MILLON"; $miles[3][1] = " MILLONES";
					$miles[4][0] = " MIL"; $miles[4][1] = " MIL";
					break;
			
		}
		
		for($i=0;$i<$total;$i++)
		{
			
			if($i==$total-1){
				$centesimos=0;
				$anexo = "";
			}				
			else{
				$centesimos=1;				
				
				if($enteros_array[$i]>1)
					$anexo = $miles[$i][1];
				else
					$anexo = $miles[$i][0];
			}
				
			$numero_letras.= self::regresa_letras($enteros_array[$i],$centesimos).$anexo;
		}
		return $numero_letras." PESOS ".$numeros[1]."/100";
		
		
	}
	public static function transformarCantidadLetrasEnteros($cantidad)
	{
		$cantidad = number_format($cantidad, 2, '.', '');
		$numeros = explode('.',$cantidad);
		
		
		
		$enteros = number_format($numeros[0]);
		
		$enteros_array = explode(',',$enteros);
		
		$total = count($enteros_array);
		$numero_letras = "";
		
		$miles = array();
		switch($total)
		{
			case 2 : $miles[0][0] = " MIL"; $miles[0][1] = " MIL"; break;
			case 3 :
					$miles[0][0] = " MILLON"; $miles[0][1] = " MILLONES"; 
					$miles[1][0] = " MIL"; $miles[1][1] = " MIL";
					break;
			case 4 : 
					$miles[0][0] = " MIL"; $miles[0][1] = " MIL";
					$miles[1][0] = " MILLON"; $miles[1][1] = " MILLONES";
					$miles[2][0] = " MIL"; $miles[2][1] = " MIL";
					break;
			case 5 : 
					$miles[0][0] = " BILLON"; $miles[0][1] = " BILLONES";
					$miles[1][0] = " MIL"; $miles[1][1] = " MIL";
					$miles[2][0] = " MILLON"; $miles[2][1] = " MILLONES";
					$miles[3][0] = " MIL"; $miles[3][1] = " MIL";
					break;
			case 6 : 
					$miles[0][0] = " MIL"; $miles[0][1] = " MIL";
					$miles[1][0] = " BILLON"; $miles[1][1] = " BILLONES";
					$miles[2][0] = " MIL"; $miles[2][1] = " MIL";
					$miles[3][0] = " MILLON"; $miles[3][1] = " MILLONES";
					$miles[4][0] = " MIL"; $miles[4][1] = " MIL";
					break;
			
		}
		
		for($i=0;$i<$total;$i++)
		{
			if($i==$total-1)
				$centesimos=0;
			else
				$centesimos=1;
			if($enteros_array[$i]>1)
				$anexo = $miles[$i][1];
			else
				$anexo = $miles[$i][0];
			$numero_letras.= self::regresa_letras($enteros_array[$i],$centesimos).$anexo;
		}
		return $numero_letras;
		
		
	}
	private static function regresa_letras($numeros, $centesimo)
	{
		
		$cadena_enteros = "";
		$total = strlen($numeros);
		
		if($total==2)
			$numeros = "0".$numeros;
		if($total==1)
			$numeros = "00".$numeros;
		
		$enteros = str_split($numeros);
		
		$total = count($enteros);
		
		for($i=0;$i<$total;$i++)
		{
		
			switch($i)
			{
				case 0: 
						$decimo = $enteros[$i+1] . $enteros[$i+2];
						
						
						switch((int)$enteros[$i])
						{
							case 1: 
									if($decimo=='00')
										$cadena_enteros.=" CIEN";
									else
										$cadena_enteros.=" CIENTO";
								break;
							case 2: $cadena_enteros.=" DOCIENTOS"; break;
							case 3: $cadena_enteros.=" TRESCIENTOS"; break;
							case 4: $cadena_enteros.=" CUATROCIENTOS"; break;
							case 5: $cadena_enteros.=" QUINIENTOS"; break;
							case 6: $cadena_enteros.=" SEISCIENTOS"; break;
							case 7: $cadena_enteros.=" SETECIENTOS"; break;
							case 8: $cadena_enteros.=" OCHOCIENTOS"; break;
							case 9: $cadena_enteros.=" NOVECIENTOS"; break;
						}
						
					
					break;
				case 1: 
						$natural = $enteros[$i+1];
					
						switch((int)$enteros[$i])
						{
							case 1:									
									switch((int)$natural)
									{
										case 0: $cadena_enteros.=" DIEZ"; break;
										case 1: $cadena_enteros.=" ONCE"; break;
										case 2: $cadena_enteros.=" DOCE"; break;
										case 3: $cadena_enteros.=" TRECE"; break;
										case 4: $cadena_enteros.=" CATORCE"; break;
										case 5: $cadena_enteros.=" QUINCE"; break;
										case 6: $cadena_enteros.=" DIECISEIS"; break;
										case 7: $cadena_enteros.=" DIECISIETE"; break;
										case 8: $cadena_enteros.=" DIECIOCHO"; break;
										case 9: $cadena_enteros.=" DIECINUEVE"; break;
									}
									//En este caso finalizamos el cilclo de una vez
									$i++;
										
									
								break;
							case 2:
									switch((int)$natural)
									{
										case 0: $cadena_enteros.=" VEINTE"; break;
										case 1:
												if($centesimo>0)
													$cadena_enteros.=" VEINTIUN"; 
												else
													$cadena_enteros.=" VEINTIUNO"; 
													break;
										case 2: $cadena_enteros.=" VEINTIDOS"; break;
										case 3: $cadena_enteros.=" VEINTITRES"; break;
										case 4: $cadena_enteros.=" VEINTICUATRO"; break;
										case 5: $cadena_enteros.=" VEINTICINCO"; break;
										case 6: $cadena_enteros.=" VEITISEIS"; break;
										case 7: $cadena_enteros.=" VEINTISIETE"; break;
										case 8: $cadena_enteros.=" VEINTIOCHO"; break;
										case 9: $cadena_enteros.=" VEINTINUEVE"; break;
									}
									//En este caso finalizamos el cilclo de una vez
									$i++;
										
									
								break;
							case 3: 
									$cadena_enteros.=" TREINTA"; 
									if($natural!=0)
										$cadena_enteros.= " Y";
									else
									{
										//Finalizamos
										$i++;
									}
									break;
							case 4: $cadena_enteros.=" CUARENTA"; 
									if($natural!=0)
										$cadena_enteros.= " Y";
									else
									{
										//Finalizamos
										$i++;
									}
									break;
							case 5: $cadena_enteros.=" CINCUENTA"; 
									if($natural!=0)
										$cadena_enteros.= " Y";
									else
									{
										//Finalizamos
										$i++;
									}
									break;
							case 6: $cadena_enteros.=" SESENTA"; 
									if($natural!=0)
										$cadena_enteros.= " Y";
									else
									{
										//Finalizamos
										$i++;
									}
										
									break;
							case 7: $cadena_enteros.=" SETENTA"; 
									if($natural!=0)
										$cadena_enteros.= " Y";
									else
									{
										//Finalizamos
										$i++;
									}
									break;
							case 8: $cadena_enteros.=" OCHENTA"; 
									if($natural!=0)
										$cadena_enteros.= " Y";
									else
									{
										//Finalizamos
										$i++;
									}
									break;
							case 9: $cadena_enteros.=" NOVENTA"; 
									if($natural!=0)
										$cadena_enteros.= " Y";
									else
									{
										//Finalizamos
										$i++;
									}
									break;
						}
						
					
					break;
				case 2:
						switch((int)$enteros[$i])
						{
							case 0: 
									if($total==1)
										$cadena_enteros.=" CERO"; 
									break;
							case 1:
									if($centesimo>0)
										$cadena_enteros.=" UN"; 
									else
										$cadena_enteros.=" UNO"; 
									break;
							case 2: $cadena_enteros.=" DOS"; break;
							case 3: $cadena_enteros.=" TRES"; break;
							case 4: $cadena_enteros.=" CUATRO"; break;
							case 5: $cadena_enteros.=" CINCO"; break;
							case 6: $cadena_enteros.=" SEIS"; break;
							case 7: $cadena_enteros.=" SIETE"; break;
							case 8: $cadena_enteros.=" OCHO"; break;
							case 9: $cadena_enteros.=" NUEVE"; break;
						}
						
					break;
			}
		}
		return $cadena_enteros;
	}
}