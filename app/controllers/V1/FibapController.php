<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use FIBAP, Exception;

class FibapController extends BaseController {
	private $reglasProyecto = array(
		'funciongasto'				=> 'required'
	);

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

			$rows = FIBAP::getModel();

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('fibap.tipo','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.sector','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.subcomite','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.grupoTrabajo','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.alineacionEspecifica','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.alineacionGeneral','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.descripcionProyecto','like','%'.$parametros['buscar'].'%')
							 ->where('p.nombreTecnico','like','%'.$parametros['buscar'].'%')
							 ->where('Proyecto','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{				
				$total = $rows->count();
			}
			
			$rows = $rows->select('fibap.id',DB::raw('concat(p.unidadResponsable,p.finalidad,p.funcion,p.subfuncion,p.subsubfuncion,p.programaSectorial,p.programaPresupuestario,p.programaEspecial,p.actividadInstitucional,p.proyectoEstrategico,LPAD(p.numeroProyectoEstrategico,3,"0")) as Proyecto'),'p.nombreTecnico','tipo','descripcionProyecto','sentryUsers.username','fibap.modificadoAl')
								->leftjoin('sentryUsers','sentryUsers.id','=','fibap.creadoPor')
								->leftjoin('proyectos AS p','p.id','=','fibap.idProyecto')
								->orderBy('id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
			return Response::json($data,$http_status);
		}	

		$rows = FIBAP::all();

		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}else{
			$data = array("data"=>$rows->toArray());
		}

		return Response::json($data,$http_status);
	}
}