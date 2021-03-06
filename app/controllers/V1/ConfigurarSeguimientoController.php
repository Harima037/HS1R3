<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use Hash,SysConfiguracionVariable;

class ConfigurarSeguimientoController extends \BaseController {
	private $reglas = array(
		'mes-captura' => 'integer|max:12',
		'mes-usuario' => 'integer|max:12',
		'dias-captura' => 'integer|max:30'
	);

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();

		$validacion = Validador::validar(Input::all(), $this->reglas);

		if($validacion === TRUE){
			$variables = SysConfiguracionVariable::obtenerVariables(array('mes-captura','dias-captura','anio-captura','poblacion-total'));

			foreach ($variables as $variable) {
				$valor = trim($parametros[$variable->variable]);
				if($valor){
					$variable->valor = $valor;
				}else{
					$variable->valor = null;
				}
			}
			$usuario = Sentry::getUser();
			if($parametros['mes-usuario']){
				$usuario->mesCaptura = intval($parametros['mes-usuario']);
			}else{
				$usuario->mesCaptura = null;
			}

			DB::transaction(function()use($variables,$usuario){
				foreach ($variables as $variable) {
					$variable->save();
				}
				$usuario->save();
			});

			$respuesta['data'] = array('data'=>$variables);

			/*if(!SysConfiguracionVariable::getModel()->saveMany($variables)){
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array('data'=>'Ocurrio un error al intentar guardar los datos.');
			}*/
		}else{
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}

		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}