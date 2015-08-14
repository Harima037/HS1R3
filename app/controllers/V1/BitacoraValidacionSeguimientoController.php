<?php
/* 
*	POA
*	Programa Operativo Anual
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2015, Instituto de Salud.
*	@author 			Donaldo Ríos, Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Validador,SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception;
use BitacoraValidacionSeguimiento;

class BitacoraValidacionSeguimientoController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){

			if(isset($parametros['mes'])){
				$mes = $parametros['mes'];
			}else{
				$mes = Util::obtenerMesActual();
				if($mes == 0){ $mes = date('n')-1; }
			}

			if(isset($parametros['ejercicio'])){
				$ejercicio = $parametros['ejercicio'];
			}else{
				$ejercicio = date('Y');
			}

			$rows = BitacoraValidacionSeguimiento::getModel()
									->where('bitacoraValidacionSeguimiento.mes','=',$mes)
									->where('bitacoraValidacionSeguimiento.ejercicio','=',$ejercicio);

			$rows = $rows->leftjoin('proyectos',function($join){
				$join->on('proyectos.id','=','bitacoraValidacionSeguimiento.idProyecto')
					->whereNull('proyectos.borradoAl');
			})->leftjoin('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','bitacoraValidacionSeguimiento.idEstatus')
			->leftjoin('sentryUsers','sentryUsers.id','=','bitacoraValidacionSeguimiento.idUsuario');

			$usuario = Sentry::getUser();
			if(($usuario->filtrarProyectos && $usuario->idDepartamento == 2) || $usuario->idDepartamento != 2){
				$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
			}
			if($usuario->claveUnidad){
				$unidades = explode('|',$usuario->claveUnidad);
				$rows = $rows->whereIn('unidadResponsable',$unidades);
			}
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where(function($query)use($parametros){
					$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
						->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
				});
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}
			
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','bitacoraValidacionSeguimiento.idEstatus',
				'catalogoEstatusProyectos.descripcion AS estatus',
				'sentryUsers.username as usuario','bitacoraValidacionSeguimiento.fechaHora')
					->orderBy('bitacoraValidacionSeguimiento.fechaHora', 'desc')
					->skip(($parametros['pagina']-1)*10)->take(10)
					->get();
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
			return Response::json($data,$http_status);
		}else{
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}
		return Response::json($data,$http_status);
	}
}