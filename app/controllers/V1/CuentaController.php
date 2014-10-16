<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use Hash;

class CuentaController extends \BaseController {
	private $reglas = array(
		'password'=>'required',
		'password_confirm' =>'required|same:password'
	);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$http_status = 200;

		$resource = Sentry::getUser();

		$data = array('data'=>$resource->toArray());

		return Response::json($data,$http_status);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		$respuesta = array();

		try{
			$recurso = Sentry::getUser();
			$valid_result = Validador::validar(Input::all(), $this->reglas);
			if($valid_result === true){
				$recurso->password = Input::get('password');

				if($recurso->save()){
					$respuesta['http_status'] = 200;
					$respuesta['data'] = array("data"=>$recurso->toArray());
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'No se pudieron guardar los cambios.','code'=>'S03');
				}
			}else{
					$respuesta['http_status'] = $valid_result['http_status'];
					$respuesta['data'] = $valid_result['data'];
			}
		}catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
    		$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
		}

		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}