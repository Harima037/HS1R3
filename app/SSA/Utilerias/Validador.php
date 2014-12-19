<?php
namespace SSA\Utilerias;

use Validator;

class Validador 
{
	public static $mensajes = array(
			'required'  		=> '{"field":":attribute","error":"Este campo es requerido."}',
			'required_without'	=> '{"field":":attribute","error":"Este campo es requerido."}',
			'required_if'		=> '{"field":":attribute","error":"Este campo es requerido."}',
			'same'      		=> '{"field":":attribute","error":"Estos campos deben ser iguales.","other":":other"}',
			'size'      		=> '{"field":":attribute","error":"Este campo no puede sobrepasar los :size caracteres."}',
			'alpha_dash'		=> '{"field":":attribute","error":"Este campo debe ser alfanumérico."}',
			'between'   		=> '{"field":":attribute","error":"Este campo debe estar entre los valores :min - :max."}',
			'in'        		=> '{"field":":attribute","error":"Este campo debe ser cualquiera de los siguientes valores: :values."}',
			'not_in'    		=> '{"field":":attribute","error":"Este campo no debe ser cualquiera de los siguientes valores: :values."}',
			'date'    			=> '{"field":":attribute","error":"Este campo debe ser una fecha válida."}',
			'numeric'   		=> '{"field":":attribute","error":"Este campo debe ser un valor numérico."}',
			'integer'   		=> '{"field":":attribute","error":"Este campo debe ser un valor entero numérico."}',
			'email'    			=> '{"field":":attribute","error":"El correo no tiene un formato válido."}',
			'active_url'		=> '{"field":":attribute","error":"El enlace no es válido."}',
			'image' 			=> '{"field":":attribute","error":"El archivo debe ser una imagen."}',
			'min'				=> '{"field":":attribute","error":"Este campo debe tener un valor mayor o igual a :min"}',
			'digits_between'	=> '{"field":":attribute","error":"Este campo debe ser ser de :min a :max caracteres de largo."}'
		);
	
	public static function guardar($_inputs,$_reglas, $_obj){	
		$respuesta = array();

		$v = Validator::make($_inputs, $_reglas, self::$mensajes);

		if ($v->fails()) {
			$respuesta['http_status'] = 409;
			$respuesta['data'] = array("data"=>$v->messages()->all(),'code'=>'U00');
		// Termina Validacion
		}else{
			try{
				$_obj->save();					
				$respuesta['http_status'] = 200;
				$respuesta['data'] = array("data"=>$_obj->toArray());	
			}catch(Exception $ex){
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array('data'=>$ex->getMessage(),'code'=>'S03');
			}			
		}
		return $respuesta;
	}
	public static function validar($_inputs,$_reglas){
		$v = Validator::make($_inputs, $_reglas, self::$mensajes);
		if ($v->fails()) {
			$respuesta['http_status'] = 409;
			$respuesta['data'] = array("data"=>$v->messages()->all(),'code'=>'U00');
		// Termina Validacion
		}else{
			$respuesta = true;
		}
		return $respuesta;
	}
	public static function parseHTML($_inputs){		
		foreach($_inputs as $key => $value){
			$_inputs[$key] = htmlentities($value);
		}
		return $_inputs;
	}
}