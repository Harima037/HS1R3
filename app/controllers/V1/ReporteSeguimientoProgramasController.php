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
use Programa;

class ReporteSeguimientoProgramasController extends BaseController {
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
			$rows = Programa::getModel();
			$rows = $rows->where('programa.idEstatus','=',5);
			
			$usuario = Sentry::getUser();
			if($usuario->idDepartamento == 2){
				if($usuario->filtrarProgramas){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}
			}else{
				$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
			}
			
			$rows = $rows->with(array('registroAvance'=>function($query){
				$query->select('id','idPrograma','trimestre',DB::raw('sum(justificacion) AS justificacion'),
								DB::raw('count(idIndicador) AS registros'))->groupBy('idPrograma','trimestre');
			},'evaluacionTrimestre'=>function($query){
				$query->whereIn('idEstatus',array(4,5));
			}));

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			$total = $rows->count();
			
			
			$rows = $rows->select('programa.id','programaPresupuestario.descripcion AS programaPresupuestario','programaPresupuestario.clave')
					->join('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','programa.claveProgramaPresupuestario')
					->orderBy('id', 'desc')
					->groupBy('programa.id')
					->skip(($parametros['pagina']-1)*10)->take(10)
					->get();

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

			return Response::json($data,$http_status);
		}
		
		$rows = Proyecto::all();

		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}else{
			$data = array("data"=>$rows->toArray());
		}

		return Response::json($data,$http_status);
	}
}