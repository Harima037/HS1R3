<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use SysConfiguracionVariable, Hash;

class AdminVariablesController extends \BaseController {
	private $reglas = array(
		'variable' => 'required'
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
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){
			$rows = SysConfiguracionVariable::getModel();

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('variable','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{
				$total = $rows->count();						
			}

			$rows = $rows->select('id','variable','valor','modificadoAl')
								->orderBy('id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No se encontraron variables",'code'=>'W00');
			}			
			
			return Response::json($data,$http_status);
		}	

		$rows = SysConfiguracionVariable::all();
		
		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No se encontraron variables",'code'=>'W00');
		}else{
			$data = array("data"=>$rows->toArray());
		}
		return Response::json($data,$http_status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		$http_status = 200;
		$data = array();

		try{
			$recurso = SysConfiguracionVariable::find($id);
			$data = array("data"=>$recurso);
		}catch(\Exception $ex){
			$http_status = 500;
			$data = array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01');
		}
		return Response::json($data,$http_status);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$respuesta = array();
		try{
			$parametros = Input::all();

			$validacion = Validador::validar($parametros,$this->reglas);

			if($validacion === TRUE){
				$recurso = new SysConfiguracionVariable;

				$recurso->variable = $parametros['variable'];
				if($parametros['valor']){
					$recurso->valor = $parametros['valor'];
				}else{
					$recurso->valor = NULL;
				}
				
				if($recurso->save()){
					$respuesta['http_status'] = 200;
					$respuesta['data'] = array("data"=>$recurso);
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar los datos del recurso','code'=>'U01');
				}
			}else{
				$respuesta = $validacion;
			}
		}catch(\Exception $ex){
			$respuesta['http_status']=500;
			$respuesta['data']=array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
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
			$parametros = Input::all();

			$validacion = Validador::validar($parametros,$this->reglas);

			if($validacion === TRUE){
				$recurso = SysConfiguracionVariable::find($id);

				$recurso->variable = $parametros['variable'];
				if($parametros['valor']){
					$recurso->valor = $parametros['valor'];
				}else{
					$recurso->valor = NULL;
				}
				
				if($recurso->save()){
					$respuesta['http_status'] = 200;
					$respuesta['data'] = array("data"=>$recurso);
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar los datos del recurso','code'=>'U01');
				}
			}else{
				$respuesta = $validacion;
			}
		}catch(\Exception $ex){
			$respuesta['http_status']=500;
			$respuesta['data']=array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		$http_status = 200;
		$data = array();

		try{
			$ids = Input::get('rows');

			$rows = SysConfiguracionVariable::whereIn('id',$ids)->delete();
			
			$data = array("data"=>"Se han eliminado los recursos.");
		}catch(\Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');
		}
		return Response::json($data,$http_status);
	}
}