<?php
/* 
*	POA
*	Programa Operativo Anual
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2016, Instituto de Salud.
*	@author 			Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View, PDF, Exception;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, Jurisdiccion, UnidadResponsable,CargaDatosEP01, EvaluacionProyectoObservacion;

class EvaluacionProyectosController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();

			if(!isset($parametros['mes'])){
				$mes = Util::obtenerMesActual();
				if($mes == 0){ $mes = date('n')-1; }
			}else{
				$mes = intval($parametros['mes']);
			}

			if(!isset($parametros['ejercicio'])){
				$ejercicio = date('Y');
			}else{
				$ejercicio = intval($parametros['ejercicio']);
			}

			if(isset($parametros['formatogrid'])){
				$rows = Proyecto::reporteEvaluacionProyectos($ejercicio,$mes)
						->where('idEstatusProyecto',5)
						->with([
							'analisisFuncional' => function($analisisFuncional) use ($mes){
								$analisisFuncional->where('mes',$mes);
							},
							'evaluacionMes' => function($evaluacionMes) use ($ejercicio,$mes){
								$evaluacionMes->where('mes','=',$mes)->where('anio','=',$ejercicio)->where('idEstatus','>',3);
							}
						]);

				$usuario = Sentry::getUser();

				if($usuario->filtrarProyectos){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);

					if($usuario->claveUnidad){
						$unidades = explode('|',$usuario->claveUnidad);
						$rows = $rows->whereIn('unidadResponsable',$unidades);
					}
				}

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}
				
				$total = $rows->get();
				$total = count($total);

				$rows = $rows->skip(($parametros['pagina']-1)*10)->take(10)->get();

				$data = array('resultados'=>$total,'data'=>$rows);
				$http_status = 200;

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}

				return Response::json($data,$http_status);
			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	public function show($id){
		//
		$http_status = 200;
		$data = array();
		$parametros = Input::all();

		if(!isset($parametros['mes'])){
			$mes = Util::obtenerMesActual();
			if($mes == 0){ 
				$mes = date('n')-1; 
				if($mes == 0){
					$mes = 12;
				}
			}
		}else{
			$mes = intval($parametros['mes']);
		}

		if(!isset($parametros['ejercicio'])){
			$ejercicio = date('Y');
		}else{
			$ejercicio = intval($parametros['ejercicio']);
		}
		
		$recurso = Proyecto::with([
			'analisisFuncional' => function($analisisFuncional) use ($mes){
				$analisisFuncional->where('mes',$mes);
			},
			'componentes.metasMes' => function($componentesMetasMes) use ($mes){
				$componentesMetasMes->where('mes','<=',$mes);
			},
			'componentes.actividades.metasMes' => function($actividadesMetasMes) use ($mes){
				$actividadesMetasMes->where('mes','<=',$mes);
			},
			'evaluacionProyectoObservacion' => function($evaluacionProyecto) use ($mes){
				$evaluacionProyecto->where('mes','=',$mes);
			}
		])
		->leftjoin('cargaDatosEP01 AS ep01',function($join) use ($mes,$ejercicio){
			$join->on('ep01.UR','=','proyectos.unidadResponsable')
				->on('ep01.FI','=','proyectos.finalidad')
				->on('ep01.FU','=','proyectos.funcion')
				->on('ep01.SF','=','proyectos.subFuncion')
				->on('ep01.SSF','=','proyectos.subSubFuncion')
				->on('ep01.PS','=','proyectos.programaSectorial')
				->on('ep01.PP','=','proyectos.programaPresupuestario')
				->on('ep01.PE','=','proyectos.programaEspecial')
				->on('ep01.AI','=','proyectos.actividadInstitucional')
				->on('ep01.PT','=',DB::raw('concat(proyectos.proyectoEstrategico,LPAD(proyectos.numeroProyectoEstrategico,3,"0"))'))
				->where('ep01.mes','=',$mes)
				->where('ep01.ejercicio','=',$ejercicio);
		})
		->select('proyectos.*',DB::raw('SUM(ep01.presupuestoModificado) AS presupuestoModificado'),DB::raw('SUM(ep01.presupuestoEjercidoModificado) AS presupuestoEjercidoModificado'))
		->find($id);
		
		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();			
			$data["data"] = $recurso;
		}
		return Response::json($data,$http_status);
	}

	public function store(){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		try{
			$parametros = Input::all();
			
			//var_dump($parametros);die;

			if(isset($parametros['id-proyecto'])){
				$proyecto = Proyecto::find($parametros['id-proyecto']);
				if($proyecto){
					if(isset($parametros['observaciones'])){
						$recurso = new EvaluacionProyectoObservacion;
						$recurso->observaciones = $parametros['observaciones'];
						$recurso->mes = $parametros['mes'];

						if($proyecto->evaluacionProyectoObservacion()->save($recurso)){
							$respuesta['data'] = array('data'=>$recurso);
						}else{
							$respuesta['http_status'] = 500;
							$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar los datos');
						}
					}
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'Proyecto no encontrado');
				}
			}else{
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>'Error no se encontraron los datos necesarios para crear el elemento');
			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al guardar la información.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
		
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	public function update($id){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		try{
			$parametros = Input::all();
			
			//var_dump($parametros);die;
			$recurso = EvaluacionProyectoObservacion::find($id);

			if($recurso){
				$recurso->observaciones = $parametros['observaciones'];

				if($recurso->save()){
					$respuesta['data'] = array('data'=>$recurso);
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar los datos');
				}
			}else{
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>'Recurso no encontrado');
			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al guardar la información.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
		
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}