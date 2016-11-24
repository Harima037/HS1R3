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
*	@author 			Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View, PDF;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, ProyectosVariacionGastoRazones;

class VariacionesGastoController extends BaseController {

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
				$rows = Proyecto::variacionesGasto($mes,$ejercicio);

				$usuario = Sentry::getUser();
				
				if($usuario->filtrarProyectos){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}

				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}
				
				$total = $rows->get();
				$total = count($total);
				
				//var_dump($total);die;
				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;

				$rows = $rows->skip(($parametros['pagina']-1)*10)->take(10)->get();

				$data = array('resultados'=>$total,'data'=>$rows);
				$http_status = 200;

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}

				return Response::json($data,$http_status);
			}else{

			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}
	//Funcion para editar
	public function show($id){
		//
		$http_status = 200;
		$data = array();
		$parametros = Input::all();
		
		$recurso = ProyectosVariacionGastoRazones::ultimaRazon($id)->get();
		
		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();			
			$data["data"] = $recurso;
		}
		return Response::json($data,$http_status);
	}

	public function update($id)
	{
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		$parametros = Input::all();
		
		//var_dump($parametros);die;
		
		if(isset($parametros["mes-razones"]))
		{
			$Busqueda = ProyectosVariacionGastoRazones::hallaRazonPorMes($id,$parametros["mes-razones"])->get();
			$idabuscar = 0;
			
			foreach($Busqueda as $row)
				$idabuscar = $row->id;
			
			$recurso = ProyectosVariacionGastoRazones::find($idabuscar);
			
			if(is_null($recurso)){//Insertar
				$recurso = new ProyectosVariacionGastoRazones;
				$recurso->idProyecto = $id;
				$recurso->mes = $parametros["mes-razones"];
			}
			//Si no es nulo, solamente actualizar las razones
			$recurso->razonesAprobado = $parametros["razones"];
			$recurso->razonesDevengado = $parametros["razones2"];
			$recurso->save();
			$respuesta['data']['data'] = $recurso;		
		}
		else
		{
			$respuesta['http_status'] = 500;
		}
		
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}
?>