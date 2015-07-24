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

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception;
use Proyecto,Directorio;

class AdminProyectosController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){

			$rows = Proyecto::getModel();

			$usuario = Sentry::getUser();

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
				'nombreTecnico','proyectos.idEstatusProyecto','catalogoEstatusProyectos.descripcion AS estatusProyecto',
				'sentryUsers.username','proyectos.modificadoAl')
								->join('sentryUsers','sentryUsers.id','=','proyectos.actualizadoPor')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->orderBy('modificadoAl', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
			return Response::json($data,$http_status);
		}else{
			$rows = Proyecto::all();

			if(count($rows) == 0){
				$http_status = 404;
				$data = array("data"=>"No hay datos",'code'=>'W00');
			}else{
				$data = array("data"=>$rows->toArray());
			}
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

		$recurso = Proyecto::find($id);

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();
			$data["data"] = $recurso;
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
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		$recurso = Proyecto::find($id);

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso->idEstatusProyecto = $parametros['estatus-proyecto'];
			if($recurso->save()){
				$data["data"] = $recurso;
			}else{
				$http_status = 500;
				$data = array("data"=>"Ocurrio un error al intentar guardar el recurso.",'code'=>'U06');
			}
		}
		return Response::json($data,$http_status);
	}
}
