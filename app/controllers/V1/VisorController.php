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
				case 'metas_cumplidas_unidad':
						$datos = $this->metasCumplidasPorUnidad();
						$data = array('data'=>$datos['metas_unidad'],'total'=>$datos['total_metas']);
					break;
				case 'metas_cumplidas_jurisdiccion':
					break;
				case 'presupuesto_ejercido_unidad':
						$datos = $this->presupuestoEjercidoPorUnidad();
						$data = array('data'=>$datos['presupuesto_unidad'],'total'=>$datos['total_modificado']);
					break;
				case 'presupuesto_vs_metas_unidad':
						$datos_metas = $this->metasCumplidasPorUnidad();
						$datos_presupuesto = $this->presupuestoEjercidoPorUnidad();

						$metas_vs_presupuesto = array();
						foreach ($datos_metas['metas_unidad'] as $clave => $metas) {
							$metas_vs_presupuesto[$clave] = array(
								'unidadResponsable'=>$metas['unidadResponsable'],
								'metasTotal'=>$metas['totalMetas'],
								'metasCumplidas'=>$metas['cumplidas'],
								'presupuestoModificado'=>0,
								'presupuestoEjercido'=>0
							);
						}

						foreach ($datos_presupuesto['presupuesto_unidad'] as $presupuesto) {
							if(!isset($metas_vs_presupuesto[$presupuesto->clave])){
								$metas_vs_presupuesto[$presupuesto->clave] = array(
									'unidadResponsable'=>$presupuesto->unidadResponsable,
									'metasTotal'=>0,
									'metasCumplidas'=>0,
									'presupuestoModificado'=>floatval($presupuesto->presupuestoModificado),
									'presupuestoEjercido'=>floatval($presupuesto->presupuestoEjercido)
								);
							}else{
								$metas_vs_presupuesto[$presupuesto->clave]['presupuestoModificado']=floatval($presupuesto->presupuestoModificado);
								$metas_vs_presupuesto[$presupuesto->clave]['presupuestoEjercido']=floatval($presupuesto->presupuestoEjercido);
							}
						}
						$data = array('data'=>$metas_vs_presupuesto);
					break;
				default:
					# code...
					break;
			}
		}elseif(isset($parametros['formatogrid'])){
			if(isset($parametros['clasificacionProyecto'])){
				$mes_actual = Util::obtenerMesActual();
				$mes_activo = $mes_actual;
				if($mes_actual == 0){ $mes_actual = date('n') - 1; }

				$rows = Proyecto::where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto']);
				
				$usuario = Sentry::getUser();
				//if($usuario->filtrarProyectos){ $rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id); }
				//Obtenemos las unidades asignadas al usuario -- mas tarde agregar condicion para jurisdicciones
				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}

				$rows = $rows->with(array(
				'evaluacionMeses'=>function($query) use ($mes_actual){
					$query->where('evaluacionProyectoMes.mes','<=',$mes_actual)
						->leftjoin('registroAvancesMetas',function($join){
								$join->on('registroAvancesMetas.idProyecto','=','evaluacionProyectoMes.idProyecto')
									->on('registroAvancesMetas.mes','=','evaluacionProyectoMes.mes');
						})
						->select('evaluacionProyectoMes.*',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'))
						->groupBy('registroAvancesMetas.idProyecto','registroAvancesMetas.mes');
				},
				'componentesMetasMes'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(meta) AS totalMeta'))->groupBy('idProyecto','mes');
				},
				'actividadesMetasMes'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(meta) AS totalMeta'))->groupBy('idProyecto','mes');
				}));

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
					'nombreTecnico')
					->orderBy('id', 'desc')
					->skip(($parametros['pagina']-1)*10)->take(10)
					->get();

				$proyectos = array();
				foreach ($rows as $proyecto) {
					$proyecto_beta = array(
						'id' => $proyecto->id,
						'clavePresupuestaria' => $proyecto->clavePresup,
						'nombreTecnico' => $proyecto->nombreTecnico,
						'meses'=>array()
					);

					foreach ($proyecto->componentesMetasMes as $metas_mes) {
						if(floatval($metas_mes->totalMeta) > 0){
							$proyecto_beta['meses'][$metas_mes->mes] = array(
								'programado'=>true,'estatus'=>0,'avance'=>0
							);
						}
					}

					foreach ($proyecto->actividadesMetasMes as $metas_mes) {
						if(floatval($metas_mes->totalMeta) > 0){
							if(!isset($proyecto_beta['meses'][$metas_mes->mes])){
								$proyecto_beta['meses'][$metas_mes->mes] = array(
									'programado'=>true,'estatus'=>0,'avance'=>0
								);
							}
						}
					}

					foreach($proyecto->evaluacionMeses as $estatus_mes){
						if(!isset($proyecto_beta['meses'][$estatus_mes->mes])){
							$proyecto_beta['meses'][$estatus_mes->mes] = array(
								'programado'=>false,
								'estatus'=>$estatus_mes->idEstatus,
								'avance'=>floatval($estatus_mes->planMejora)
							);
						}else{
							$proyecto_beta['meses'][$estatus_mes->mes]['estatus'] = $estatus_mes->idEstatus;
							$proyecto_beta['meses'][$estatus_mes->mes]['avance'] = floatval($estatus_mes->planMejora);
						}
					}
					$proyectos[] = $proyecto_beta;
				}
				$data = array('data'=>$proyectos,'resultados'=>$total,'mesActual'=>$mes_actual,'mesActivo'=>$mes_activo);
			}elseif(isset($parametros['avancesIndicadores'])){
				$mes_actual = Util::obtenerMesActual();
				if($mes_actual == 0){
					$mes_actual = date('n') -1;
				}
				
				$componentes = Componente::select('proyectoComponentes.id','proyectoComponentes.idProyecto',
											'proyectoComponentes.indicador',DB::raw('sum(metasMes.meta) AS metaAnual'))
										->join('componenteMetasMes AS metasMes',function($join){
											$join->on('metasMes.idComponente','=','proyectoComponentes.id')
												//->where('metasMes.claveJurisdiccion','=',$clave_jurisdiccion)
												->whereNull('metasMes.borradoAl');
										})
										->with(array('metasMes'=>function($query)use($mes_actual){
											$query->select('id','idComponente',DB::raw('sum(meta) AS meta'),'mes',
													DB::raw('sum(avance) AS avance'))
												->where('mes','<=',$mes_actual)
												->groupBy('idComponente','mes')->orderBy('mes','desc');
										},'registroAvance'=>function($query){
											$query->join('evaluacionProyectoMes AS proyectoMes',function($join){
												$join->on('proyectoMes.idProyecto','=','registroAvancesMetas.idProyecto')
													->on('proyectoMes.mes','=','registroAvancesMetas.mes')
													->on('proyectoMes.idEstatus','in',DB::raw('(4,5)'))
													->whereNull('proyectoMes.borradoAl');
											})->select('registroAvancesMetas.*');
										}))
										->groupBy('proyectoComponentes.id')
										->where('proyectoComponentes.idProyecto','=',$parametros['idProyecto'])
										->get();
				$actividades = Actividad::select('componenteActividades.id','componenteActividades.idProyecto',
											'componenteActividades.idComponente','componenteActividades.indicador',
											DB::raw('sum(metasMes.meta) AS metaAnual'))
										->join('actividadMetasMes AS metasMes',function($join){
											$join->on('metasMes.idActividad','=','componenteActividades.id')
												//->where('metasMes.claveJurisdiccion','=',$clave_jurisdiccion)
												->whereNull('metasMes.borradoAl');
										})
										->with(array('metasMes'=>function($query)use($mes_actual){
											$query->select('id','idActividad',DB::raw('sum(meta) AS meta'),'mes',
													DB::raw('sum(avance) AS avance'))
												->where('mes','<=',$mes_actual)
												->groupBy('idActividad','mes')->orderBy('mes','desc');
										},'registroAvance'=>function($query){
											$query->join('evaluacionProyectoMes AS proyectoMes',function($join){
												$join->on('proyectoMes.idProyecto','=','registroAvancesMetas.idProyecto')
													->on('proyectoMes.mes','=','registroAvancesMetas.mes')
													->on('proyectoMes.idEstatus','in',DB::raw('(4,5)'))
													->whereNull('proyectoMes.borradoAl');
											})->select('registroAvancesMetas.*');
										}))
										->groupBy('componenteActividades.id')
										->where('componenteActividades.idProyecto','=',$parametros['idProyecto'])
										->get();
				$elementos = array();
				$componentes_nivel = array();
				foreach ($componentes as $indice => $componente) {
					$elemento = array(
						'id' => $componente->id,
						'tipo' => 1,
						'nivel' => 'C ' . ($indice+1),
						'indicador' => $componente->indicador,
						'metaAnual' => floatval($componente->metaAnual),
						'metaMes' => $componente->metasMes->sum('meta'),
						'avanceAcumulado' => 0,'avanceMes' => 0
					);

					$avances_validos = $componente->registroAvance->lists('mes','mes');

					foreach ($componente->metasMes as $meta_mes) {
						if(isset($avances_validos[$meta_mes->mes])){
							$elemento['avanceAcumulado'] += floatval($meta_mes->avance);
							if($meta_mes->mes == $mes_actual){
								$elemento['avanceMes'] = floatval($meta_mes->avance);
							}
						}
					}
					$componentes_nivel[$componente->id] = array('nivel'=>($indice+1),'actividades'=>1);
					$elementos[] = $elemento;
				}
				
				foreach ($actividades as $indice => $actividad) {
					$elemento = array(
						'id' => $actividad->id,
						'tipo' => 2,
						'nivel' => 'A ',
						'indicador' => $actividad->indicador,
						'metaAnual' => floatval($actividad->metaAnual),
						'metaMes' => $actividad->metasMes->sum('meta'),
						'avanceAcumulado' => 0,'avanceMes' => 0
					);

					if(isset($componentes_nivel[$actividad->idComponente])){
						$elemento['nivel'] .= $componentes_nivel[$actividad->idComponente]['nivel'] . '.' . $componentes_nivel[$actividad->idComponente]['actividades']++;
					}else{
						$componentes_nivel[0] = 1;
						$elemento['nivel'] .= $componentes_nivel[0]++;
					}

					$avances_validos = $actividad->registroAvance->lists('mes','mes');

					foreach ($actividad->metasMes as $meta_mes) {
						if(isset($avances_validos[$meta_mes->mes])){
							$elemento['avanceAcumulado'] += floatval($meta_mes->avance);
							if($meta_mes->mes == $mes_actual){
								$elemento['avanceMes'] = floatval($meta_mes->avance);
							}
						}
					}
					$elementos[] = $elemento;
				}
				$total = count($elementos);
				$data = array('data'=>$elementos,'resultados'=>$total);
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
	public function show($id){
		$http_status = 200;
		$data = array();
		$parametros = Input::all();

		if(isset($parametros['mostrar'])){
			if($parametros['mostrar'] == 'detalles-avance-indicador'){
				$mes_actual = Util::obtenerMesActual();
				if($mes_actual == 0){
					$mes_actual = date('n')-1;
				}
				if($parametros['nivel'] == 'componente'){
					$recurso = Componente::getModel();
					$tabla = 'componenteMetasMes.';
					$campo = 'idComponente';
				}else{
					$recurso = Actividad::getModel();
					$tabla = 'actividadMetasMes.';
					$campo = 'idActividad';
				}
				
				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = $recurso->with(array(
							'metasMes' => function($query)use($mes_actual){
								$query->where('mes','=',$mes_actual);
							},'metasMesAgrupado' => function($query){
								$query->orderBy('mes','asc');
							},'metasMesJurisdiccion'=>function($query) use ($mes_actual,$tabla,$campo){
								$query->select($tabla.'id','idProyecto',$campo,'claveJurisdiccion',
									DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'),
									'vistaJurisdicciones.nombre AS jurisdiccion')
										->where('mes','<=',$mes_actual)
										->leftjoin('vistaJurisdicciones','vistaJurisdicciones.clave','=','claveJurisdiccion');
							},'registroAvance'=>function($query) use ($mes_actual){
								$query->join('evaluacionProyectoMes AS proyectoMes',function($join){
									$join->on('proyectoMes.idProyecto','=','registroAvancesMetas.idProyecto')
										->on('proyectoMes.mes','=','registroAvancesMetas.mes')
										->on('proyectoMes.idEstatus','in',DB::raw('(4,5)'))
										->whereNull('proyectoMes.borradoAl');
									})->where('registroAvancesMetas.mes','<=',$mes_actual)
									->orderBy('registroAvancesMetas.mes','desc')
									->select('registroAvancesMetas.*');
							}
							,'planMejora'=>function($query) use ($mes_actual){
								$query->where('mes','=',$mes_actual);
							},'unidadMedida'))->find($id);

				$elemento = array(
					'id' => $recurso->id,
					'nivel' => $parametros['nivel'],
					'indicador' => $recurso->indicador,
					'unidadMedida' => $recurso->unidadMedida->descripcion,
					'metaTotal' => $recurso->metasMesAgrupado->sum('meta'),
					'analisisResultados' => null,'justificacion' => null,
					'planMejora' => null
				);

				$meses_capturados = $recurso->registroAvance->lists('mes','mes');

				if(isset($meses_capturados[$mes_actual])){
					$elemento['analisisResultados'] = $recurso->registroAvance[0]->analisisResultados;
					$elemento['justificacion'] = $recurso->registroAvance[0]->justificacionAcumulada;
				}

				if(count($recurso->planMejora)){
					$elemento['planMejora'] = $recurso->planMejora[0]->toArray();
				}

				$juris_metas = array();
				foreach ($recurso->metasMesJurisdiccion as $datos) {
					$meta = floatval($datos->meta);
			        $avance = floatval($datos->avance);

			        if($meta > 0){ $porcentaje = ($avance*100) / $meta; }
			        else{ $porcentaje = ($avance*100); }

			        $estatus = 1;
			        if(!($meta == 0 && $avance == 0)){
						if($porcentaje > 110){ $estatus = 3; }
						elseif($porcentaje < 90){ $estatus = 2; }
						elseif($porcentaje > 0 && $meta == 0){ $estatus = 3; }
			        }

					$juris_metas[$datos->claveJurisdiccion] = array(
						'nombre' => $datos->jurisdiccion,
						'metaMes' => 0,
						'metaAcumulada' => $meta,
						'avanceAcumulado' => $avance,
						'avanceMes' => 0,
						'avanceTotal' => $avance,
						'porcentaje' => $porcentaje,
						'estatus' => $estatus
					);
				}
				foreach ($recurso->metasMes as $meta_mes) {
					$juris_metas[$meta_mes->claveJurisdiccion]['metaMes'] = floatval($meta_mes->meta);
					$juris_metas[$meta_mes->claveJurisdiccion]['avanceMes'] = floatval($meta_mes->avance);
					$juris_metas[$meta_mes->claveJurisdiccion]['avanceAcumulado'] = $juris_metas[$meta_mes->claveJurisdiccion]['avanceTotal'] - floatval($meta_mes->avance);
				}
				$elemento['jurisdicciones'] = $juris_metas;

				$mes_metas = array();
				$meta_acumulada = 0;
				$avance_acumulado = 0;
				foreach ($recurso->metasMesAgrupado as $meta_mes) {
					$meta_acumulada += floatval($meta_mes->meta);
					$mes_metas[$meta_mes->mes] = array(
						'meta' => floatval($meta_mes->meta),
						'metaAcumulada' => $meta_acumulada,
						'estatus' => 0, 'activo'=>false, 'porcentaje'=>0
					);

					if(isset($meses_capturados[$meta_mes->mes])){
						$avance_acumulado += floatval($meta_mes->avance);
						$mes_metas[$meta_mes->mes]['avance'] = floatval($meta_mes->avance);
						$mes_metas[$meta_mes->mes]['avanceAcumulado'] = $avance_acumulado;
						$mes_metas[$meta_mes->mes]['activo'] = true;
					}

					if($meta_acumulada > 0){ $porcentaje = ($avance_acumulado*100) / $meta_acumulada; }
			        else{ $porcentaje = ($avance_acumulado*100); }

			        $estatus = 1;
			        if(!($meta_acumulada == 0 && $avance_acumulado == 0)){
						if($porcentaje > 110){ $estatus = 3; }
						elseif($porcentaje < 90){ $estatus = 2; }
						elseif($porcentaje > 0 && $meta_acumulada == 0){ $estatus = 3; }
			        }

			        $mes_metas[$meta_mes->mes]['estatus'] = $estatus;
			        $mes_metas[$meta_mes->mes]['porcentaje'] = $porcentaje;
				}
				$elemento['meses'] = $mes_metas;

				if($parametros['nivel'] == 'componente'){
					$recurso->load('desgloseMunicipios');
					$elemento['desgloseMunicipios'] = $recurso->desgloseMunicipios;
				}
				$data['data'] = $elemento;
				//$data["data"] = $recurso;
			}
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}
		/*else{
			$data["data"] = $recurso;
		}*/
		return Response::json($data,$http_status);
	}

	public function presupuestoEjercidoPorUnidad(){
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){ $mes_actual = date('n') -1; }

		$rows = CargaDatosEP01::where('mes','=',$mes_actual)
					->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
						DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'),
						'unidad.descripcion AS unidadResponsable','UR AS clave')
					->leftjoin('catalogoUnidadesResponsables AS unidad','unidad.clave','=','UR')
					->groupBy('UR')
					->get();
		$total_modif = $rows->sum('presupuestoModificado');
		$total_ejer = $rows->sum('presupuestoEjercido');
		return array('presupuesto_unidad'=>$rows,'total_modificado'=>$total_modif,'total_ejercido'=>$total_ejer);
	}

	public function metasCumplidasPorUnidad(){
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n') -1;
		}

		$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
										->join('proyectos',function($join){
											$join->on('proyectos.id','=','componenteMetasMes.idProyecto')
												->where('proyectos.idEstatusProyecto','=',5)
												->whereNull('proyectos.borradoAl');
										})
										->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
										->groupBy('idComponente')
										->orderBy('unidadResponsable')
										->select('idComponente','unidades.descripcion AS unidad',
											'proyectos.unidadResponsable',
											DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
										->join('proyectos',function($join){
											$join->on('proyectos.id','=','actividadMetasMes.idProyecto')
												->where('proyectos.idEstatusProyecto','=',5)
												->whereNull('proyectos.borradoAl');
										})
										->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
										->groupBy('idActividad')
										->orderBy('unidadResponsable')
										->select('idActividad','unidades.descripcion AS unidad',
											'proyectos.unidadResponsable',
											DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$total = count($componentes) + count($actividades);
		$metas_unidad = array();
		foreach ($componentes as $componente) {
			if(!isset($metas_unidad[$componente->unidadResponsable])){
				$metas_unidad[$componente->unidadResponsable] = array(
					'unidadResponsable'=>$componente->unidad,
					'totalMetas'=>0,
					'cumplidas'=>0
				);
			}
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

	        if($estatus == 1){ 
	        	$metas_unidad[$componente->unidadResponsable]['cumplidas']++; 
	        }
	       	$metas_unidad[$componente->unidadResponsable]['totalMetas']++;
		}

		foreach ($actividades as $actividad) {
			if(!isset($metas_unidad[$actividad->unidadResponsable])){
				$metas_unidad[$actividad->unidadResponsable] = array(
					'unidadResponsable'=>$actividad->unidad,
					'totalMetas'=>0,
					'cumplidas'=>0
				);
			}
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

	        if($estatus == 1){ 
	        	$metas_unidad[$actividad->unidadResponsable]['cumplidas']++; 
	        }
	       	$metas_unidad[$actividad->unidadResponsable]['totalMetas']++;
		}

		return array('metas_unidad'=>$metas_unidad,'total_metas'=>$total);
	}

}