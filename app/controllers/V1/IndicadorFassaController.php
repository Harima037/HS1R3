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

class IndicadorFassaController extends \BaseController {

	private $reglas = array(
			'nivel-indicador'			=> 'sometimes|required',
			'indicador'					=> 'sometimes|required',
			'tipo-formula'				=> 'sometimes|required',
			'formula'					=> 'sometimes|required',
			'fuente-informacion'		=> 'sometimes|required',

			//'ejercicio'					=> 'sometimes|required',
			//'numerador'					=> 'sometimes|required',
			//'denominador'				=> 'sometimes|required',
			'unidad-responsable'		=> 'sometimes|required',
			'responsable-informacion'	=> 'sometimes|required'
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
			
				$rows = IndicadorFASSA::getModel();
				
				$usuario = Sentry::getUser();
				if($usuario->idDepartamento == 2){
					if($usuario->filtrarProgramas){
						$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
					}
				}else{
					$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
				}

				if(isset($parametros['ejercicio'])){
					$ejercicio = $parametros['ejercicio'];
					$rows = $rows->join('indicadorFASSAMeta',function($join)use($ejercicio){
							$join->on('indicadorFASSAMeta.idIndicadorFASSA','=','indicadorFASSA.id')
								->where('indicadorFASSAMeta.ejercicio','=',$ejercicio)
								->whereNull('indicadorFASSAMeta.borradoAl');
						});//->groupBy('indicadorFASSAMeta.idIndicadorFASSA');
						//->having(DB::raw('MAX(indicadorFASSAMeta.ejercicio)'),'=',$ejercicio);
				}
					
				
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

				$rows = $rows->select('indicadorFASSA.id','indicadorFASSA.indicador','indicadorFASSA.claveNivel','indicadorFASSA.idEstatus','sentryUsers.username','indicadorFASSA.modificadoAl')
							->orderBy('id', 'desc')
							->leftjoin('sentryUsers','indicadorFASSA.actualizadoPor','=','sentryUsers.id')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
				//
				$data = array('resultados'=>$total,'data'=>$rows);
				$respuesta['data'] = $data;

			

				if($total<=0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
			}elseif(isset($parametros['cargar-responsables'])){
				$responsables = Directorio::responsablesActivos($parametros['unidad-responsable'])->get();
				$respuesta['data'] = array('data'=>$responsables);
			}elseif(isset($parametros['typeahead'])){
				$rows = IndicadorFASSA::getModel();
	
				if(isset($parametros['buscar'])){				
					$rows = $rows->where(function($query)use($parametros){
							$query->where('indicador','like','%'.$parametros['buscar'].'%');
						});
				}
				
				//$rows = $rows->where('indicadorFASSA.idEstatus','=',1);
	
				if(isset($parametros['departamento'])){
					if(isset($parametros['usuario'])){
						$id_usuario = $parametros['usuario'];
					}else{
						$id_usuario = 0;
					}
					
					if($parametros['departamento'] == 2){
						$rows = $rows->where(function($query)use($id_usuario){
							$query->whereNull('indicadorFASSA.idUsuarioValidacionSeg')
								->orWhere('indicadorFASSA.idUsuarioValidacionSeg','=',$id_usuario);
						});
					}else{
						$rows = $rows->where(function($query)use($id_usuario){
							$query->whereNull('indicadorFASSA.idUsuarioRendCuenta')
								->orWhere('indicadorFASSA.idUsuarioRendCuenta','=',$id_usuario);
						});
					}
				}
				//var_dump($proyectos_asignados);die;
				//throw new Exception("Error:: " + print_r($proyectos_asignados,true), 1);
				
				$rows = $rows->contenidoSuggester()->get();
	
				if(count($rows)<=0){
					$respuesta['data'] = array('resultados'=>0,"data"=>array());
				}else{
					$respuesta['data'] = array('resultados'=>count($rows),'data'=>$rows);
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
			$recurso = IndicadorFASSA::with('metasDetalle')->find($id);
			if($recurso){
				$ejercicio_actual = date('Y');
				foreach ($recurso->metasDetalle as $index => $meta) {
					if($meta->ejercicio == $ejercicio_actual){
						$responsables = Directorio::responsablesActivos($meta->claveUnidadResponsable)->get();
						$recurso->metasDetalle[$index]['responsables'] = $responsables;
					}
				}
				$recurso['ejercicio_actual'] = intval(date('Y'));
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
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$respuesta = Validador::validar(Input::all(), $this->reglas);
		
		if($respuesta === true){
			try{
				$respuesta = array();
				$parametros = Input::all();

				$recurso = new IndicadorFASSA;

				$recurso->claveNivel 				= $parametros['nivel-indicador'];
				$recurso->indicador 				= $parametros['indicador'];
				$recurso->claveTipoFormula 			= $parametros['tipo-formula'];
				$recurso->formula 					= $parametros['formula'];
				$recurso->fuenteInformacion 		= $parametros['fuente-informacion'];
				$recurso->idEstatus					= 1;

				if($recurso->claveTipoFormula == 'T'){
					$recurso->tasa = $parametros['tasa'];
				}

				$recurso_meta = new IndicadorFASSAMeta;
				$recurso_meta->ejercicio				= date('Y');
				$recurso_meta->claveFrecuencia			= $parametros['frecuencia'];
				$recurso_meta->claveUnidadResponsable 	= $parametros['unidad-responsable'];
				$recurso_meta->idResponsableInformacion	= $parametros['responsable-informacion'];
				$recurso_meta->idEstatus				= 1;

				$titular = Directorio::titularesActivos(array($parametros['unidad-responsable']))->first();
				$recurso_meta->idLiderPrograma = $titular->id;
				/*
				$recurso_meta->numerador 				= $parametros['numerador'];
				$recurso_meta->denominador 				= $parametros['denominador'];
				$numerador = $parametros['numerador'];
				$denominador = $parametros['denominador'];
				if($parametros['tipo-formula'] == 'T'){
					$porcentaje = floatval(($numerador * 100000)/$denominador);
				}else{
					$porcentaje = floatval(($numerador * 100)/$denominador);
				}
				$recurso_meta->porcentaje = $porcentaje;
				*/
				
				$respuesta = DB::transaction(function() use ($recurso,$recurso_meta){
					$respuesta_transaction = array();

					if($recurso->save()){
						$recurso->metas()->save($recurso_meta);
						$respuesta_transaction['http_status'] = 200;
						$recurso['meta'] = $recurso_meta;
						$respuesta_transaction['data'] = array("data"=>$recurso);
					}else{
						$respuesta_transaction['http_status'] = 500;
						$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la informacion','code'=>'S01');
					}
					return $respuesta_transaction;
				});
				
			}catch(\Exception $e){
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
			}
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
			$valid_result = Validador::validar(Input::all(), $this->reglas);
			if($valid_result === true){
				$parametros = Input::all();

				if(isset($parametros['cambio_frecuencia']) && $parametros['cambio_frecuencia'] == 1)
				{
					$ids = array($id);
					$rows = DB::transaction(function()use($ids){
						IndicadorFASSAMeta::whereIn('idIndicadorFASSA',$ids)->delete();
						$rows = IndicadorFASSA::whereIn('id',$ids)->delete();
					});
					
					$recurso = new IndicadorFASSA;
					$recurso->idEstatus = 1;
				}else
				{
					$recurso = IndicadorFASSA::find($id);
				}

				//$checar_ejercicio = FALSE;

				if(!isset($parametros['cambio_frecuencia']) || $parametros['cambio_frecuencia'] != 1)
				{
					if(isset($parametros['id-meta'])){
						$recurso_meta = IndicadorFASSAMeta::find($parametros['id-meta']);
					}else if(isset($parametros['estatus-indicador'])){
						
						$http_status = 200;
						$data = array();
						
						if(is_null($recurso)){
							$http_status = 404;
							$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
						}else{
							$recurso->idEstatus = $parametros['estatus-indicador'];
							if($recurso->save()){
								$data["data"] = $recurso;
							}else{
								$http_status = 500;
								$data = array("data"=>"Ocurrio un error al intentar guardar el recurso.",'code'=>'U06');
							}
						}
						return Response::json($data,$http_status);

					}else{
						$recurso_meta = new IndicadorFASSAMeta;
						$recurso_meta->idEstatus = 1;
						$recurso_meta->ejercicio = date('Y');
					}
				}

				$recurso->claveNivel 				= $parametros['nivel-indicador'];
				$recurso->indicador 				= $parametros['indicador'];
				$recurso->claveTipoFormula 			= $parametros['tipo-formula'];
				$recurso->formula 					= $parametros['formula'];
				$recurso->fuenteInformacion 		= $parametros['fuente-informacion'];

				if($recurso->claveTipoFormula == 'T'){
					$recurso->tasa = $parametros['tasa'];
				}else{
					$recurso->tasa = null;
				}
				
				$tipo_formula = $recurso->claveTipoFormula;

				
				//if($recurso_meta->idEstatus == 1 || $recurso_meta->idEstatus == 3){
				if(!isset($parametros['cambio_frecuencia']) || $parametros['cambio_frecuencia'] != 1)
				{
					if($parametros['unidad-responsable'] != $recurso_meta->claveUnidadResponsable){
						$titular = Directorio::titularesActivos(array($parametros['unidad-responsable']))->first();
						$recurso_meta->idLiderPrograma = $titular->id;
					}
				}else
				{
					$recurso_meta = new IndicadorFASSAMeta;
					$recurso_meta->ejercicio				= date('Y');
					$recurso_meta->idEstatus				= 1;

					$titular = Directorio::titularesActivos(array($parametros['unidad-responsable']))->first();
					$recurso_meta->idLiderPrograma = $titular->id;
				}

				$recurso_meta->claveUnidadResponsable 	= $parametros['unidad-responsable'];
				$recurso_meta->idResponsableInformacion	= $parametros['responsable-informacion'];
				$recurso_meta->claveFrecuencia = $parametros['frecuencia'];

				
				
				$respuesta = DB::transaction(function() use ($recurso,$recurso_meta){
					$respuesta_transaction = array();

					//if($recurso->idEstatus == 1 || $recurso->idEstatus == 3){
					//}
					if(!$recurso->save()){
						$respuesta_transaction['http_status'] = 500;
						$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la información del indicador','code'=>'S01');
					}

					//if($recurso_meta->idEstatus == 1 || $recurso_meta->idEstatus == 3){
					//}
					if(!$recurso->metas()->save($recurso_meta)){
						$respuesta_transaction['http_status'] = 500;
						$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la información de las metas','code'=>'S01');
					}
					

					if(!isset($respuesta_transaction['http_status'])){
						$respuesta_transaction['http_status'] = 200;
						$recurso['meta'] = $recurso_meta;
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