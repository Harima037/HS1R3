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

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime,Mail;
use Proyecto,ComponenteMetaMes,ActividadMetaMes,Componente,Actividad,CargaDatosEP01;

class VisorController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if(isset($parametros['grafica'])){
			switch ($parametros['grafica']) {
				case 'proyectos_direccion':
						$rows = Proyecto::select('unidadResponsable AS clave', 'unidades.descripcion AS unidad', 
												DB::raw('count(distinct proyectos.id) AS noProyectos'))
										->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
										->where('idEstatusProyecto','=',5)
										->groupBy('unidadResponsable')
										->get();
						$total = $rows->sum('noProyectos');
						//$queries = DB::getQueryLog();
						//var_dump(count($queries));die;
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'proyectos_tipo':
						$rows = Proyecto::select('clasificacion.descripcion AS tipoProyecto', 
												DB::raw('count(distinct proyectos.id) AS noProyectos'))
										->leftjoin('catalogoClasificacionProyectos AS clasificacion','clasificacion.id','=','proyectos.idClasificacionProyecto')
										->where('idEstatusProyecto','=',5)
										->groupBy('idClasificacionProyecto')
										->get();
						$total = $rows->sum('noProyectos');
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'presupuesto_fuente':
						$mes_actual = Util::obtenerMesActual();
						if($mes_actual == 0){ $mes_actual = date('n') -1; }

						$rows = CargaDatosEP01::where('mes','=',$mes_actual);

						/*$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('UR',$unidades);
						}*/

						$rows = $rows->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
											'fuenteFinan.descripcion AS fuenteFinanciamiento')
									->leftjoin('catalogoFuenteFinanciamiento AS fuenteFinan','fuenteFinan.clave','=','FF')
									->groupBy('FF')
									->get();
						$total = $rows->sum('presupuestoModificado');
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'presupuesto_ejercido':
						$mes_actual = Util::obtenerMesActual();
						if($mes_actual == 0){
							$mes_actual = date('n') -1;
						}

						$presupuesto = CargaDatosEP01::where('mes','=',$mes_actual)
														->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
															DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'))
														->first();
						//
						$data = array('data'=>$presupuesto);
					break;
				case 'metas_unidad':
						$rows = Proyecto::select('unidadResponsable AS clave', 'unidades.descripcion AS unidad', 
										DB::raw('count(distinct componentes.id)+count(distinct actividades.id) AS noMetas'))
										->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
										->leftjoin('proyectoComponentes AS componentes',function($join){
											$join->on('componentes.idProyecto','=','proyectos.id')
												->whereNull('componentes.borradoAl');
										})
										->leftjoin('componenteActividades AS actividades',function($join){
											$join->on('actividades.idProyecto','=','proyectos.id')
												->whereNull('actividades.borradoAl');
										})
										->where('idEstatusProyecto','=',5)
										->groupBy('unidadResponsable')
										->get();
						$total = $rows->sum('noMetas');
						//$queries = DB::getQueryLog();
						//var_dump(count($queries));die;
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'metas_cumplidas':
						$mes_actual = Util::obtenerMesActual();
						if($mes_actual == 0){
							$mes_actual = date('n') -1;
						}

						$proyectos_ids = Proyecto::where('idEstatusProyecto','=',5)->lists('id');

						$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
														->whereIn('idProyecto',$proyectos_ids)
														->groupBy('idComponente')
														->select('id','idComponente AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
															DB::raw('sum(avance) AS avance'))->get();
						//
						$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
														->whereIn('idProyecto',$proyectos_ids)
														->groupBy('idActividad')
														->select('id','idActividad AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
															DB::raw('sum(avance) AS avance'))->get();
						//
						$total = count($componentes) + count($actividades);

						$metas = array('cumplidas'=>0,'altoAvance'=>0,'bajoAvance'=>0);
						foreach ($componentes as $componente) {
							$meta = floatval($componente->meta);
					        $avance = floatval($componente->avance);

					        if($meta > 0){ $porcentaje = ($avance*100) / $meta; }
					        else{ $porcentaje = ($avance*100); }

					        $estatus = 1;
					        if(!($meta == 0 && $avance == 0)){
								if($porcentaje > 110){ $estatus = 3; }
								elseif($porcentaje < 90){ $estatus = 2; }
								elseif($porcentaje > 0 && $meta == 0){ $estatus = 3; }
					        }

					        if($estatus == 1){ $metas['cumplidas']++; }
					        elseif($estatus == 2){ $metas['bajoAvance']++; }
					        else{ $metas['altoAvance']++; }
						}

						foreach ($actividades as $actividad) {
							$meta = floatval($actividad->meta);
					        $avance = floatval($actividad->avance);

					        if($meta > 0){ $porcentaje = ($avance*100) / $meta; }
					        else{ $porcentaje = ($avance*100); }

					        $estatus = 1;
					        if(!($meta == 0 && $avance == 0)){
								if($porcentaje > 110){ $estatus = 3; }
								elseif($porcentaje < 90){ $estatus = 2; }
								elseif($porcentaje > 0 && $meta == 0){ $estatus = 3; }
					        }

					        if($estatus == 1){ $metas['cumplidas']++; }
					        elseif($estatus == 2){ $metas['bajoAvance']++; }
					        else{ $metas['altoAvance']++; }
						}

						$data = array('data'=>$metas,'total'=>$total);
					break;
				default:
					# code...
					break;
			}
		}else{
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}

		/*
		if(isset($parametros['graficapresupuestometa'])){
			$mes_actual = Util::obtenerMesActual();
			if($mes_actual == 0){
				$mes_actual = date('n') -1;
			}

			$presupuesto = CargaDatosEP01::where('mes','=',$mes_actual)
										->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
											DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'));

			$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
											->groupBy('idComponente')
											->select('id','idComponente AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
												DB::raw('sum(avance) AS avance'));
			
			$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
											->groupBy('idActividad')
											->select('id','idActividad AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
												DB::raw('sum(avance) AS avance'));

			$usuario = Sentry::getUser();
			if($usuario->claveUnidad){
				$unidades = explode('|',$usuario->claveUnidad);
				$presupuesto = $presupuesto->whereIn('UR',$unidades);

				$proyectos_ids = Proyecto::whereIn('unidadResponsable',$unidades)->groupBy('id')->lists('id');
				$componentes = $componentes->whereIn('idProyecto',$proyectos_ids);
				$actividades = $actividades->whereIn('idProyecto',$proyectos_ids);
			}

			
			
			$componentes = $componentes->get();
			$actividades = $actividades->get();
			$presupuesto = $presupuesto->first();
			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;
			
			$data = array('data'=>array('presupuesto'=>$presupuesto,'componentes'=>$componentes,'actividades'=>$actividades));
			return Response::json($data,$http_status);
		}elseif(isset($parametros['graficapresupuesto'])){
			$mes_actual = Util::obtenerMesActual();
			if($mes_actual == 0){
				$mes_actual = date('n') -1;
			}

			$rows = CargaDatosEP01::where('mes','=',$mes_actual);

			$usuario = Sentry::getUser();
			if($usuario->claveUnidad){
				$unidades = explode('|',$usuario->claveUnidad);
				$rows = $rows->whereIn('UR',$unidades);
			}

			$total = $rows->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'))->get();
			$rows = $rows->select('FF','mes','UR',DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
								'fuenteFinan.descripcion AS fuenteFinanciamiento')
						->leftjoin('catalogoFuenteFinanciamiento AS fuenteFinan','fuenteFinan.clave','=','FF')
						->groupBy('FF')->get();
			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;

			$data = array('total'=>$total[0]->presupuestoModificado,'data'=>$rows);
			if($total[0]->presupuestoModificado <= 0){
				$http_status = 404;
				$data = array("data"=>"No hay datos",'code'=>'W00');
			}
			return Response::json($data,$http_status);
		}elseif(isset($parametros['graficageneral'])){
			$mes_actual = Util::obtenerMesActual();
			if($mes_actual == 0){
				$mes_actual = date('n') -1;
			}

			$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
											->groupBy('idComponente')
											->select('id','idComponente AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
												DB::raw('sum(avance) AS avance'));
			
			$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
											->groupBy('idActividad')
											->select('id','idActividad AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
												DB::raw('sum(avance) AS avance'));

			$usuario = Sentry::getUser();
			if($usuario->claveUnidad){
				$unidades = explode('|',$usuario->claveUnidad);
				$proyectos_ids = Proyecto::whereIn('unidadResponsable',$unidades)->groupBy('id')->lists('id');
				$componentes = $componentes->whereIn('idProyecto',$proyectos_ids);
				$actividades = $actividades->whereIn('idProyecto',$proyectos_ids);
			}
			
			$componentes = $componentes->get();
			$actividades = $actividades->get();
			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;

			$data = array();
			$data['componentes'] = $componentes;
			$data['actividades'] = $actividades;

			return Response::json($data,$http_status);
		}elseif(isset($parametros['formatogrid'])){
			if(isset($parametros['grid'])){
				if($parametros['grid'] == 'rendicion-acciones'){
					$mes_actual = Util::obtenerMesActual();
					if($mes_actual == 0){
						$mes_actual = date('n') -1;
					}
					$rows = Proyecto::with(array(
									'componentes'=>function($query)use($mes_actual){
										$query->select('proyectoComponentes.id','proyectoComponentes.indicador',
													'proyectoComponentes.valorNumerador','proyectoComponentes.idProyecto',
													DB::raw('sum(componenteMetasMes.meta) AS metasAlMes'))
											->leftjoin('componenteMetasMes',function($join)use($mes_actual){
												$join->on('componenteMetasMes.idComponente','=','proyectoComponentes.id')
													->where('componenteMetasMes.mes','<=',$mes_actual)
													->whereNull('componenteMetasMes.borradoAl');
											})
											->groupBy('proyectoComponentes.id');
									}
									,'componentes.registroAvance'
									,'componentes.actividades'=>function($query)use($mes_actual){
										$query->select('componenteActividades.id','componenteActividades.idComponente',
													'componenteActividades.indicador','componenteActividades.valorNumerador',
													'componenteActividades.idProyecto',DB::raw('sum(actividadMetasMes.meta) AS metasAlMes'))
											->leftjoin('actividadMetasMes',function($join)use($mes_actual){
												$join->on('actividadMetasMes.idActividad','=','componenteActividades.id')
													->where('actividadMetasMes.mes','<=',$mes_actual)
													->whereNull('actividadMetasMes.borradoAl');
											})
											->groupBy('componenteActividades.id');
									}
									,'componentes.actividades.registroAvance'))
									->find($parametros['idProyecto']);
					
					$total = count($rows);
				}
			}else{
				$mes_actual = Util::obtenerMesActual();

				$rows = Proyecto::getModel();
				$rows = $rows->where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto']);

				if($mes_actual == 0){
					$mes_actual = date('n') - 1;
				}
				
				$rows = $rows->leftjoin('evaluacionProyectoMes', function($join) use($mes_actual){
									$join->on('proyectos.id', '=', 'evaluacionProyectoMes.idProyecto')
									->where('evaluacionProyectoMes.mes', '=', $mes_actual)
									->where('evaluacionProyectoMes.anio', '=', date('Y'));
								});
				

				$usuario = Sentry::getUser();
				
				if($usuario->filtrarProyectos){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}

				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}

				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'),DB::raw('count(idNivel) as registros'))->groupBy('idProyecto','mes');
				},'evaluacionMeses'=>function($query) use ($mes_actual){
					$query->where('evaluacionProyectoMes.mes','<=',$mes_actual);
					$query->leftjoin('registroAvancesMetas',function($join){
								$join->on('registroAvancesMetas.idProyecto','=','evaluacionProyectoMes.idProyecto')
									->on('registroAvancesMetas.mes','=','evaluacionProyectoMes.mes');
							})
							->select('evaluacionProyectoMes.*',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'))
							->groupBy('registroAvancesMetas.idProyecto','registroAvancesMetas.mes');
				},'componentesMetasMes'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(meta) AS totalMeta'))->groupBy('idProyecto','mes');
				},'actividadesMetasMes'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(meta) AS totalMeta'))->groupBy('idProyecto','mes');
				}));

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					//$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
					$rows = $rows->where(function($query)use($parametros){
						$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
							->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
					});
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}
				
				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto','proyectos.idEstatusProyecto',
					'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl',
					'proyectos.fuenteInformacion','proyectos.idResponsable')
					->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
					->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')									
					->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
					->orderBy('id', 'desc')
					->skip(($parametros['pagina']-1)*10)->take(10)
					->get();
				//var_dump($total);die;
				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;
				//var_dump($rows->toArray());die;
			}
			
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

			return Response::json($data,$http_status);
		}
		*/
		
		return Response::json($data,$http_status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){}
}