<?php
/* 
*	SIRE
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2014, Instituto de Salud.
*	@author 			Mario Cabrera
*	@package 			sire
*	@version 			1.0 
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Validador;
use Illuminate\Database\QueryException, \Exception;
use BaseController, Input, Response, DB, Sentry, IndicadorFASSA, IndicadorFASSAMeta,Directorio;

class RendicionFassaController extends \BaseController {

	private $reglas = array(
			'numerador'					=> 'sometimes|required|min:0',
			'denominador'				=> 'sometimes|required|min:0',
			'avance-numerador'			=> 'sometimes|required|min:0',
			'avance-denominador'		=> 'sometimes|required|min:0'
		);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$respuesta = array('http_status'=>200,'data'=>'');

		$parametros = Input::all();
		
		try{
			if(isset($parametros['formatogrid'])){
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				$ejercicio_actual = date('Y');
				//$rows = IndicadorFASSA::getModel();
				$rows = IndicadorFASSAMeta::getModel();
				$rows = $rows->indicadoresEjercicio()->where('ejercicio','=',$ejercicio_actual);

				if(isset($parametros['buscar'])){
					if($parametros['buscar']){
						$rows = $rows->where(function($query) use ($parametros){
									$query->where('indicador','like','%'.$parametros['buscar'].'%')
										->orWhere('fuenteInformacion','like','%'.$parametros['buscar'].'%');
								});
					}
					$total = $rows->count();
				}else{
					$total = $rows->count();
				}

				$rows = $rows->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
				//
				$data = array('resultados'=>$total,'data'=>$rows);
				$respuesta['data'] = $data;

				if($total<=0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
			}else{
				$rows = IndicadorFASSA::all();
				if(count($rows) == 0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No hay datos",'code'=>'W00');
				}else{
					$respuesta['data'] = array("data"=>$rows);
				}
			}
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S01');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
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
			$recurso = IndicadorFASSAMeta::indicadorMetaDetalle()->find($id);
			if($recurso){
				$data['data'] = $recurso;
			}else{
				$http_status = 404;
				$data = array("data"=>"No se ha podido encontrar el recurso solicitado.",'code'=>'S01');
			}
		}catch(\Exception $e){
			$http_status = 500;
			$data = array("data"=>'Error al obtener los datos','code'=>'S03','ex'=>$e->getMessage());
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
		try{
			$valid_result = Validador::validar(Input::all(), $this->reglas);
			if($valid_result === true){
				$parametros = Input::all();

				$recurso = IndicadorFASSAMeta::with('indicador')->find($id);

				/*if(($recurso->idEstatus == 2 || $recurso->idEstatus == 4) && ($recurso_meta->idEstatus == 2 || $recurso_meta->idEstatus == 4)){
					throw new Exception("Ninguno de los elementos esta disponible para edición", 1);
				}*/

				$tipo_formula = $recurso->indicador->claveTipoFormula;
				
				if(isset($parametros['numerador'])){
					if($recurso->idEstatus == 1 || $recurso->idEstatus == 3){
						$recurso->numerador 				= $parametros['numerador'];
						$recurso->denominador 				= $parametros['denominador'];

						$numerador = $parametros['numerador'];
						$denominador = $parametros['denominador'];
						if($tipo_formula == 'T'){
							$porcentaje = floatval(($numerador * 100000)/$denominador);
						}else{
							$porcentaje = floatval(($numerador * 100)/$denominador);
						}
						$recurso->porcentaje = $porcentaje;
					}
				}

				$respuesta = DB::transaction(function() use ($recurso){
					$respuesta_transaction = array();

					if($recurso->idEstatus == 1 || $recurso->idEstatus == 3){
						if(!$recurso->save()){
							$respuesta_transaction['http_status'] = 500;
							$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la información de la meta','code'=>'S01');
						}
					}

					if(!isset($respuesta_transaction['http_status'])){
						$respuesta_transaction['http_status'] = 200;
						$respuesta_transaction['data'] = array("data"=>$recurso);
					}

					return $respuesta_transaction;
				});
			}else{
				$respuesta['http_status'] = $valid_result['http_status'];
				$respuesta['data'] = $valid_result['data'];
			}
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
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
			
			$rows = DB::transaction(function()use($ids){
				IndicadorFASSAMeta::whereIn('idIndicadorFASSA',$ids)->delete();
				$rows = IndicadorFASSA::whereIn('id',$ids)->delete();
			});
			
			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
			}else{
				$http_status = 500;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}
		}catch(\Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');	
		}

		return Response::json($data,$http_status);
	}

}