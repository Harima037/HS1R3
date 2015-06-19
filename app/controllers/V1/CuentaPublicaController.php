<?php

namespace V1;

use SSA\Utilerias\Validador, SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Exception;
use Hash, File, EvaluacionAnalisisFuncional;

class CuentaPublicaController extends \BaseController {
	private $reglas = array(
		'cuenta-publica' => 'required'
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

		try{
			$parametros = Input::all();
			if(isset($parametros['formatogrid'])){

				$rows = EvaluacionAnalisisFuncional::cuentaPublica(Util::obtenerMesActual(),date('Y'));

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where(function($query){
							$query->where('clavePresupuestaria','like','%'.$parametros['buscar'].'%')
								->orWhere('nombreTecnico','like','%'.$parametros['buscar'].'%');
					});
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}

				$rows = $rows->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
				//
				$data = array('resultados'=>$total,'data'=>$rows);

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
				
				return Response::json($data,$http_status);
			}	

			$rows = EvaluacionAnalisisFuncional::all();

			if(count($rows) == 0){
				$http_status = 404;
				$data = array("data"=>"No hay datos",'code'=>'W00');
			}else{
				$data = array("data"=>$rows->toArray());
			}
		}catch(\Exception $e){
			$http_status = 404;
			$data = array("data"=>"",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
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

		$recurso = EvaluacionAnalisisFuncional::find($id);

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data = array("data"=>$recurso->toArray());
		}

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
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');	

		try{
			$parametros = Input::all();

			$resultado = Validador::validar($parametros, $this->reglas);
			if($resultado===true){
				$recurso = EvaluacionAnalisisFuncional::find($id);
				
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->cuentaPublica = $parametros['cuenta-publica'];
					
					if(!$recurso->save()){
						throw new Exception("Ocurrio un error al intentar guardar los datos del recurso.", 1);
					}
				}
			}else{
				$respuesta = $resultado;
			}
		}catch(Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>"Ocurrio un error en el servidor al momento de realizar la operación.",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}