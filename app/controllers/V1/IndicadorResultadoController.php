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
use BaseController, Input, Response, DB, Sentry, View, PDF, Exception;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, EvaluacionProyectoMes;

class IndicadorResultadoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();
			$datos = array();

			if(isset($parametros['formatogrid'])){

				if(!isset($parametros['mes'])){
					$mes = Util::obtenerMesActual();
					if($mes == 0){ 
						if(date('n') == 1){
							$mes = 12; 
						}else{
							$mes = date('n')-1; 
						}
					}
				}else{
					$mes = intval($parametros['mes']);
				}

				if(!isset($parametros['ejercicio'])){
					$ejercicio = Util::obtenerAnioCaptura();
				}else{
					$ejercicio = intval($parametros['ejercicio']);
				}

				$rows = Proyecto::indicadoresResultados($mes,$ejercicio);

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}
				
				$total = count($rows->get());

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
			}
			/*
			$rows = Proyecto::indicadoresResultados();
			$rows = $rows->get();
			$total = count($rows);
			*/
			$data = array('resultados'=>0,'data'=>[]);
			$http_status = 200;

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

			return Response::json($data,$http_status);

		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
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
		$parametros = Input::all();
		
		try{
			if(isset($parametros['mes'])){
				$mes = intval($parametros['mes']);
			}else{
				$mes = Util::obtenerMesActual();
				if($mes == 0){ 
					if(date('n') == 1){
						$mes = 12;
					}else{
						$mes = date('n')-1; 
					}
				}
			}

			$recurso = Proyecto::select(
							'proyectos.id', 'proyectos.nombreTecnico', 'proyectos.idClasificacionProyecto',
							'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
							'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
							'proyectos.programaPresupuestario','proyectos.origenAsignacion','estatusMes.idEstatus',
							'proyectos.actividadInstitucional','proyectos.proyectoEstrategico','estatusMes.id AS idAvanceMes',
							'proyectos.numeroProyectoEstrategico',DB::raw('concat_ws(".- ",subFuncionGasto.clave,subFuncionGasto.descripcion) AS subFuncionDescripcion'),'estatusMes.indicadorResultadoBeneficiarios',
							'estatusMes.indicadorResultadoBeneficiariosF','estatusMes.indicadorResultadoBeneficiariosM'
						)
						->leftjoin('catalogoFuncionesGasto AS subFuncionGasto','subFuncionGasto.clave','=',DB::raw('concat_ws(".",proyectos.finalidad,	proyectos.funcion,proyectos.subFuncion,proyectos.subSubFuncion)'))
						->with(array('beneficiariosDescripcion'=>function($beneficiario)use($mes){
							$beneficiario->leftjoin('registroAvancesBeneficiarios as avanceBenef',function($join)use($mes){
								$join->on('avanceBenef.idProyectoBeneficiario','=','proyectoBeneficiarios.id')
									->on('avanceBenef.idTipoBeneficiario','=','proyectoBeneficiarios.idTipoBeneficiario')
									->where('avanceBenef.mes','=',$mes)
									->whereNull('avanceBenef.borradoAl');
							})
							->select('proyectoBeneficiarios.id','proyectoBeneficiarios.idProyecto','proyectoBeneficiarios.idTipoBeneficiario','avanceBenef.sexo',
								DB::raw('avanceBenef.total AS avanceBeneficiario'),
								'tipoBeneficiario.descripcion AS tipoBeneficiario')
							->groupBy('proyectoBeneficiarios.idProyecto','proyectoBeneficiarios.id','proyectoBeneficiarios.sexo','proyectoBeneficiarios.idTipoBeneficiario');
						}))
						->leftjoin('evaluacionProyectoMes AS estatusMes',function($query)use($mes){
							$query->on('estatusMes.idProyecto','=','proyectos.id')
								->where('estatusMes.mes','=',$mes)
								->where('estatusMes.idEstatus','>=',4);
						})
						->where('proyectos.id','=',$id)->first();
			$data = array("data"=>$recurso);
		}catch(\Exception $ex){
			$http_status = 500;
			$data = array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01','line'=>$ex->getLine(),'message'=>$ex->getMessage());
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
			$parametros = Input::all();

			$recurso = EvaluacionProyectoMes::find($id);

			if($recurso){
				if($parametros['beneficiariosF'] !== ''){
					$recurso->indicadorResultadoBeneficiariosF = $parametros['beneficiariosF'];
				}else{
					$recurso->indicadorResultadoBeneficiariosF = NULL;
				}

				if($parametros['beneficiariosM'] !== ''){
					$recurso->indicadorResultadoBeneficiariosM = $parametros['beneficiariosM'];
				}else{
					$recurso->indicadorResultadoBeneficiariosM = NULL;
				}

				if($parametros['beneficiarios'] !== ''){
					$recurso->indicadorResultadoBeneficiarios = $parametros['beneficiarios'];
				}else{
					$recurso->indicadorResultadoBeneficiarios = NULL;
				}

				if($recurso->save()){
					$respuesta['http_status'] = 200;
					$respuesta['data'] = array("data"=>$recurso);
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar los datos','code'=>'U01');
				}
			}else{
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>'Recurso no encontrado','code'=>'U01');
			}
			
			
		}catch(\Exception $ex){
			$respuesta['http_status']=500;
			$respuesta['data']=array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}
?>