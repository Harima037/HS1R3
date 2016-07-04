<?php
/* 
*	SIRE
*	Sistema de Integración Rendición de cuentas y Evaluación
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2015, Instituto de Salud.
*	@author 			Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime,Mail;
use IndicadorFASSAMeta;

class ReporteSeguimientoIndicadoresFASSAController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

			if(isset($parametros['ejercicio'])){
				$ejercicio_actual = $parametros['ejercicio'];
			}else{
				$ejercicio_actual = Util::obtenerAnioCaptura();
			}
			//$rows = IndicadorFASSA::getModel();
			$rows = IndicadorFASSAMeta::getModel();
			$rows = $rows->with(array('registroAvance'=>function($query){
				$query->whereIn('idEstatus',array(4,5));
			}));
			$rows = $rows->indicadoresEjercicio()->where('ejercicio','=',$ejercicio_actual);
				
			$usuario = Sentry::getUser();
			if($usuario->idDepartamento == 2){
				if($usuario->filtrarProgramas){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}
			}else{
				$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
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

			$rows = $rows->select('indicadorFASSAMeta.id','indicadorFASSAMeta.idEstatus','indicadorFASSAMeta.idEstatusCierre','indicadorFASSA.indicador','indicadorFASSA.claveNivel')
						->orderBy('id', 'desc')
						->skip(($parametros['pagina']-1)*10)->take(10)
						->get();

			//$queries = DB::getQueryLog();
			//$last_query = end($queries);

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
			return Response::json($data,$http_status);
		}
		$rows = IndicadorFASSA::getModel();
			
		$usuario = Sentry::getUser();
		if($usuario->idDepartamento == 2){
			if($usuario->filtrarProgramas){
				$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
			}
		}else{
			$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
		}
		
		$rows = $rows->orderBy('id', 'desc')->get();

		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}else{
			$data = array("data"=>$rows->toArray());
		}

		return Response::json($data,$http_status);
	}
}