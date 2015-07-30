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
						$rows = Proyecto::getModel();
						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('unidadResponsable',$unidades);
						}
						$rows = $rows->select('unidadResponsable AS clave', 'unidades.descripcion AS unidad', 
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
						$rows = Proyecto::getModel();
						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('unidadResponsable',$unidades);
						}
						$rows = $rows->select('clasificacion.descripcion AS tipoProyecto', 
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

						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('UR',$unidades);
						}else{
							if(isset($parametros['unidad'])){
								$unidad = array($parametros['unidad']);
								$rows = $rows->where('UR',$unidad);
							}
						}

						$rows = $rows->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
											'fuenteFinan.descripcion AS fuenteFinanciamiento')
									->leftjoin('catalogoFuenteFinanciamiento AS fuenteFinan','fuenteFinan.clave','=','FF')
									->groupBy('FF')
									->get();
						$total = $rows->sum('presupuestoModificado');
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'presupuesto_ejercido_capitulo':
						$mes_actual = Util::obtenerMesActual();
						if($mes_actual == 0){ $mes_actual = date('n') -1; }

						$rows = CargaDatosEP01::where('mes','=',$mes_actual);

						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('UR',$unidades);
						}

						$rows = $rows->select(DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'),
											'capitulo.descripcion AS capitulo')
									->leftjoin('catalogoObjetosGasto AS objetoGasto','objetoGasto.clave','=','OG')
									->leftjoin('catalogoObjetosGasto AS partida','partida.id','=','objetoGasto.idPadre')
									->leftjoin('catalogoObjetosGasto AS concepto','concepto.id','=','partida.idPadre')
									->leftjoin('catalogoObjetosGasto AS capitulo','capitulo.id','=','concepto.idPadre')
									->groupBy('capitulo.clave')
									->get();
						//$queries = DB::getQueryLog();
						//var_dump(end($queries));die;
						$total = $rows->sum('presupuestoEjercido');
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'presupuesto_ejercido':
						$mes_actual = Util::obtenerMesActual();
						if($mes_actual == 0){
							$mes_actual = date('n') -1;
						}
						$presupuesto = CargaDatosEP01::getModel();
						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$presupuesto = $presupuesto->whereIn('UR',$unidades);
						}
						$presupuesto = $presupuesto->where('mes','=',$mes_actual)
														->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
															DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'))
														->first();
						//
						$data = array('data'=>$presupuesto);
					break;
				case 'metas_unidad':
						$rows = Proyecto::getModel();
						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('unidadResponsable',$unidades);
						}
						$rows = $rows->select('unidadResponsable AS clave', 'unidades.descripcion AS unidad', 
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

						$componentes = ComponenteMetaMes::getModel();
						$actividades = ActividadMetaMes::getModel();

						$proyectos_ids = Proyecto::where('idEstatusProyecto','=',5);
						$jurisdiccion = false;
						$unidades = false;

						$usuario = Sentry::getUser();
						if($usuario->claveJurisdiccion){
							$jurisdiccion = $usuario->claveJurisdiccion;
						}elseif($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
						}else{
							if(isset($parametros['unidad'])){
								$unidades = array($parametros['unidad']);
							}
							if(isset($parametros['jurisdiccion'])){
								$jurisdiccion = $parametros['jurisdiccion'];
							}
						}

						if($unidades){
							$proyectos_ids = $proyectos_ids->whereIn('unidadResponsable',$unidades);
						}

						$proyectos_ids = $proyectos_ids->lists('id');

						if($jurisdiccion){
							$componentes = $componentes->where('claveJurisdiccion','=',$jurisdiccion)
														 ->groupBy('idComponente','claveJurisdiccion');
							$actividades = $actividades->where('claveJurisdiccion','=',$jurisdiccion)
														 ->groupBy('idActividad','claveJurisdiccion');
						}else{
							$componentes = $componentes->groupBy('idComponente');
							$actividades = $actividades->groupBy('idActividad');
						}
						
						$componentes = $componentes->where('mes','<=',$mes_actual)
														->whereIn('idProyecto',$proyectos_ids)
														->select('id','idComponente AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
															DB::raw('sum(avance) AS avance'))->get();
						//
						$actividades = $actividades->where('mes','<=',$mes_actual)
														->whereIn('idProyecto',$proyectos_ids)
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
						$datos = $this->metasCumplidasPorJurisdiccion();
						$data = array('data'=>$datos['metas_jurisdiccion'],'total'=>$datos['total_metas']);
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
					break;
			}
		}elseif(isset($parametros['formatogrid'])){
			if(isset($parametros['clasificacionProyecto'])){
				$mes_actual = Util::obtenerMesActual();
				$mes_activo = $mes_actual;
				if($mes_actual == 0){ $mes_actual = date('n') - 1; }

				$jurisdiccion = false;

				$rows = Proyecto::where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto']);
				
				$usuario = Sentry::getUser();
				if($usuario->claveJurisdiccion){
					$jurisdiccion = $usuario->claveJurisdiccion;
				}elseif($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}else{
					if(isset($parametros['unidad'])){
						if($parametros['unidad']){
							$rows = $rows->where('unidadResponsable','=',$parametros['unidad']);
						}
					}
					if(isset($parametros['jurisdiccion'])){
						if($parametros['jurisdiccion']){
							$jurisdiccion = $parametros['jurisdiccion'];
						}
					}
				}

				if($jurisdiccion){
					$query_actividades = DB::table('actividadMetasMes')->select('idProyecto')
													 ->where('claveJurisdiccion','=',$jurisdiccion)
													 ->whereNull('borradoAl')
													 ->where(function($query){
													 	$query->where('meta','>','0')->orWhere('avance','>','0');
													 })->groupBy('idProyecto');
					$proyectos_ids = ComponenteMetaMes::select('idProyecto')
													 ->where('claveJurisdiccion','=',$jurisdiccion)
													 ->where(function($query){
													 	$query->where('meta','>','0')->orWhere('avance','>','0');
													 })->groupBy('idProyecto')
													 ->union($query_actividades)->lists('idProyecto');
					$rows = $rows->whereIn('id',$proyectos_ids);
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
				'componentesMetasMes'=>function($query)use($jurisdiccion){
					$query->select('id','idProyecto','mes',DB::raw('sum(meta) AS totalMeta'),DB::raw('sum(avance) AS totalAvance'))
						 ->groupBy('idProyecto','mes');
					if($jurisdiccion){ $query->where('claveJurisdiccion','=',$jurisdiccion); }
				},
				'actividadesMetasMes'=>function($query)use($jurisdiccion){
					$query->select('id','idProyecto','mes',DB::raw('sum(meta) AS totalMeta'),DB::raw('sum(avance) AS totalAvance'))
				 		 ->groupBy('idProyecto','mes');
					if($jurisdiccion){ $query->where('claveJurisdiccion','=',$jurisdiccion); }
				}));

				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
					'nombreTecnico')
					->orderBy('id', 'desc')
					->skip(($parametros['pagina']-1)*10)->take(10)
					->get();
				//return Response::json($rows,$http_status);
				//var_dump($rows->toArray());die;
				$proyectos = array();
				foreach ($rows as $proyecto) {
					$proyecto_beta = array(
						'id' => $proyecto->id,
						'clavePresupuestaria' => $proyecto->clavePresup,
						'nombreTecnico' => $proyecto->nombreTecnico,
						'meses'=>array()
					);

					if($jurisdiccion){
						$estatus_meses = $proyecto->evaluacionMeses->lists('idEstatus','mes');
						$avance_acumulado = 0;
						$meta_acumulada = 0;
					}else{
						$estatus_meses = array();
					}

					foreach ($proyecto->componentesMetasMes as $metas_mes) {
						if(floatval($metas_mes->totalMeta) > 0){
							$proyecto_beta['meses'][$metas_mes->mes] = array(
								'programado'=>true,'estatus'=>0,'avance'=>0
							);
						}
						if($jurisdiccion){
							$avance_acumulado += floatval($metas_mes->totalAvance);
							$meta_acumulada += floatval($metas_mes->totalMeta);
							if(isset($estatus_meses[$metas_mes->mes])){
								if(!isset($proyecto_beta['meses'][$metas_mes->mes])){
									$proyecto_beta['meses'][$metas_mes->mes] = array(
										'programado'=>false,'estatus'=>$estatus_meses[$metas_mes->mes],'avance'=>0
									);
								}else{
									$proyecto_beta['meses'][$metas_mes->mes]['estatus'] = $estatus_meses[$metas_mes->mes];
								}
								if($avance_acumulado > 0 || $meta_acumulada > 0){
									if($meta_acumulada > 0){ $porcentaje = ($avance_acumulado*100)/$meta_acumulada; }
									else{ $porcentaje = ($avance_acumulado*100); }

									if($porcentaje > 110 || ($porcentaje > 0 && $meta_acumulada == 0) || $porcentaje < 90){ $tipo_avance = 2; }
									else{ $tipo_avance = 1; }
									$proyecto_beta['meses'][$metas_mes->mes]['avance'] = $tipo_avance;
								}
							}
						}
					}

					if($jurisdiccion){
						$avance_acumulado = 0;
						$meta_acumulada = 0;
					}

					foreach ($proyecto->actividadesMetasMes as $metas_mes) {
						if(floatval($metas_mes->totalMeta) > 0){
							if(!isset($proyecto_beta['meses'][$metas_mes->mes])){
								$proyecto_beta['meses'][$metas_mes->mes] = array(
									'programado'=>true,'estatus'=>0,'avance'=>0
								);
							}
						}

						if($jurisdiccion){
							$avance_acumulado += floatval($metas_mes->totalAvance);
							$meta_acumulada += floatval($metas_mes->totalMeta);
							if(isset($estatus_meses[$metas_mes->mes])){
								if(!isset($proyecto_beta['meses'][$metas_mes->mes])){
									$proyecto_beta['meses'][$metas_mes->mes] = array(
										'programado'=>false,'estatus'=>$estatus_meses[$metas_mes->mes],'avance'=>0
									);
								}else{
									$proyecto_beta['meses'][$metas_mes->mes]['estatus'] = $estatus_meses[$metas_mes->mes];
								}
								if($avance_acumulado > 0 || $meta_acumulada > 0){
									if($meta_acumulada > 0){ $porcentaje = ($avance_acumulado*100)/$meta_acumulada; }
									else{ $porcentaje = ($avance_acumulado*100); }

									if($porcentaje > 110 || ($porcentaje > 0 && $meta_acumulada == 0) || $porcentaje < 90){ $tipo_avance = 2; }
									else{ $tipo_avance = 1; }
									if($tipo_avance > $proyecto_beta['meses'][$metas_mes->mes]['avance']){
										$proyecto_beta['meses'][$metas_mes->mes]['avance'] = $tipo_avance;
									}
								}
							}
						}
					}

					foreach($proyecto->evaluacionMeses as $estatus_mes){
						if($jurisdiccion){
							if(!isset($proyecto_beta['meses'][$estatus_mes->mes])){
								$proyecto_beta['meses'][$estatus_mes->mes] = array(
									'programado'=>false,
									'estatus'=>$estatus_mes->idEstatus,
									'avance'=>0
								);
							}
						}else{
							if(floatval($estatus_mes->planMejora) > 0){ $avance = 2; }
							else{ $avance = 1; }

							if(!isset($proyecto_beta['meses'][$estatus_mes->mes])){
								$proyecto_beta['meses'][$estatus_mes->mes] = array(
									'programado'=>false,
									'estatus'=>$estatus_mes->idEstatus,
									'avance'=>$avance
								);
							}else{
								$proyecto_beta['meses'][$estatus_mes->mes]['estatus'] = $estatus_mes->idEstatus;
								$proyecto_beta['meses'][$estatus_mes->mes]['avance'] = $avance;
							}
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
				
				$jurisdiccion = false;

				$componentes = Componente::getModel();
				$actividades = Actividad::getModel();

				$usuario = Sentry::getUser();
				if($usuario->claveJurisdiccion){
					$jurisdiccion = $usuario->claveJurisdiccion;
				}else{
					if(isset($parametros['jurisdiccion'])){
						if($parametros['jurisdiccion']){
							$jurisdiccion = $parametros['jurisdiccion'];
						}
					}
				}

				$componentes = $componentes->select('proyectoComponentes.id','proyectoComponentes.idProyecto',
											'proyectoComponentes.indicador',DB::raw('sum(metasMes.meta) AS metaAnual'))
										->join('componenteMetasMes AS metasMes',function($join)use($jurisdiccion){
											$join->on('metasMes.idComponente','=','proyectoComponentes.id')
												->whereNull('metasMes.borradoAl');
											if($jurisdiccion){ $join->where('metasMes.claveJurisdiccion','=',$jurisdiccion); }
										})
										->with(array('metasMes'=>function($query)use($mes_actual,$jurisdiccion){
											$query->select('id','idComponente',DB::raw('sum(meta) AS meta'),'mes',
													DB::raw('sum(avance) AS avance'))
												->where('mes','<=',$mes_actual)
												->groupBy('idComponente','mes')->orderBy('mes','desc');
											if($jurisdiccion){ $query->where('claveJurisdiccion','=',$jurisdiccion); }
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
				$actividades = $actividades->select('componenteActividades.id','componenteActividades.idProyecto',
											'componenteActividades.idComponente','componenteActividades.indicador',
											DB::raw('sum(metasMes.meta) AS metaAnual'))
										->join('actividadMetasMes AS metasMes',function($join)use($jurisdiccion){
											$join->on('metasMes.idActividad','=','componenteActividades.id')
												->whereNull('metasMes.borradoAl');
											if($jurisdiccion){ $join->where('metasMes.claveJurisdiccion','=',$jurisdiccion); }
										})
										->with(array('metasMes'=>function($query)use($mes_actual,$jurisdiccion){
											$query->select('id','idActividad',DB::raw('sum(meta) AS meta'),'mes',
													DB::raw('sum(avance) AS avance'))
												->where('mes','<=',$mes_actual)
												->groupBy('idActividad','mes')->orderBy('mes','desc');
											if($jurisdiccion){ $query->where('claveJurisdiccion','=',$jurisdiccion); }
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
				
				$jurisdiccion = false;

				$usuario = Sentry::getUser();
				if($usuario->claveJurisdiccion){
					$jurisdiccion = $usuario->claveJurisdiccion;
				}else{
					if(isset($parametros['jurisdiccion'])){
						if($parametros['jurisdiccion']){
							$jurisdiccion = $parametros['jurisdiccion'];
						}
					}
				}

				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = $recurso->with(array(
							'metasMes' => function($query)use($mes_actual){
								$query->where('mes','=',$mes_actual);
							},'metasMesAgrupado' => function($query)use($jurisdiccion){
								$query->orderBy('mes','asc');
								if($jurisdiccion){ $query->where('claveJurisdiccion','=',$jurisdiccion); }
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

			        if($datos->claveJurisdiccion != 'OC'){
			        	$idJuris = intval($datos->claveJurisdiccion);
			        }else{
			        	$idJuris = 0;
			        }

					$juris_metas[$idJuris] = array(
						'nombre' => ($datos->jurisdiccion)?$datos->jurisdiccion:'OFICINA CENTRAL',
						'clave' => $datos->claveJurisdiccion,
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
					if($meta_mes->claveJurisdiccion != 'OC'){
			        	$idJuris = intval($meta_mes->claveJurisdiccion);
			        }else{
			        	$idJuris = 0;
			        }
					$juris_metas[$idJuris]['metaMes'] = floatval($meta_mes->meta);
					$juris_metas[$idJuris]['avanceMes'] = floatval($meta_mes->avance);
					$juris_metas[$idJuris]['avanceAcumulado'] = $juris_metas[$idJuris]['avanceTotal'] - floatval($meta_mes->avance);
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

				if($jurisdiccion){
					$elemento['tomar'] = 'meses';
				}else{
					$elemento['tomar'] = 'jurisdicciones';
				}

				$data['data'] = $elemento;
				//$data["data"] = $recurso;
			}
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}
		
		return Response::json($data,$http_status);
	}

	public function presupuestoEjercidoPorUnidad(){
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){ $mes_actual = date('n') -1; }

		$rows = CargaDatosEP01::getModel();

		$usuario = Sentry::getUser();
		if($usuario->claveUnidad){
			$unidades = explode('|',$usuario->claveUnidad);
			$rows = $rows->whereIn('UR',$unidades);
		}

		//$rows = CargaDatosEP01::where('mes','=',$mes_actual)
		$rows = $rows->where('mes','=',$mes_actual)
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

		$usuario = Sentry::getUser();
		if($usuario->claveUnidad){
			$unidades = explode('|',$usuario->claveUnidad);
		}else{
			$unidades = null;
		}

		$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join)use($unidades){
							$join->on('proyectos.id','=','componenteMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->whereNull('proyectos.borradoAl');
							if($unidades){
								$join = $join->on('proyectos.unidadResponsable','in',DB::raw('('.implode(',',$unidades).')'));
							}
						})
						->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
						->groupBy('idComponente')
						->orderBy('unidadResponsable')
						->select('idComponente','unidades.descripcion AS unidad',
							'proyectos.unidadResponsable',
							DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join)use($unidades){
							$join->on('proyectos.id','=','actividadMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->whereNull('proyectos.borradoAl');
							if($unidades){
								$join = $join->on('proyectos.unidadResponsable','in',DB::raw('('.implode(',',$unidades).')'));
							}
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

	public function metasCumplidasPorJurisdiccion(){
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){ $mes_actual = date('n') -1; }

		$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join){
							$join->on('proyectos.id','=','componenteMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->whereNull('proyectos.borradoAl');
						})
						->leftjoin('vistaJurisdicciones AS jurisdiccion','jurisdiccion.clave','=','claveJurisdiccion')
						->groupBy('idComponente','claveJurisdiccion')
						->select('idComponente','jurisdiccion.nombre AS jurisdiccion',
							'claveJurisdiccion',
							DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join){
							$join->on('proyectos.id','=','actividadMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->whereNull('proyectos.borradoAl');
						})
						->leftjoin('vistaJurisdicciones AS jurisdiccion','jurisdiccion.clave','=','claveJurisdiccion')
						->groupBy('idActividad','claveJurisdiccion')
						->select('idActividad','jurisdiccion.nombre AS jurisdiccion',
							'claveJurisdiccion',
							DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$total = count($componentes) + count($actividades);
		$metas_jurisdiccion = array();
		foreach ($componentes as $componente) {
			if($componente->claveJurisdiccion != 'OC'){
				$idJuris = intval($componente->claveJurisdiccion);
			}else{
				$idJuris = 0;
			}
			if(!isset($metas_jurisdiccion[$idJuris])){
				$metas_jurisdiccion[$idJuris] = array(
					'clave'=>$componente->claveJurisdiccion,
					'jurisdiccion'=>($componente->jurisdiccion)?$componente->jurisdiccion:'OFICINA CENTRAL',
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
	        	$metas_jurisdiccion[$idJuris]['cumplidas']++; 
	        }
	       	$metas_jurisdiccion[$idJuris]['totalMetas']++;
		}

		foreach ($actividades as $actividad) {
			if($actividad->claveJurisdiccion != 'OC'){
				$idJuris = intval($actividad->claveJurisdiccion);
			}else{
				$idJuris = 0;
			}
			if(!isset($metas_jurisdiccion[$idJuris])){
				$metas_jurisdiccion[$idJuris] = array(
					'clave'=>$actividad->claveJurisdiccion,
					'jurisdiccion'=>($actividad->jurisdiccion)?$actividad->jurisdiccion:'OFICINA CENTRAL',
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
	        	$metas_jurisdiccion[$idJuris]['cumplidas']++; 
	        }
	       	$metas_jurisdiccion[$idJuris]['totalMetas']++;
		}

		return array('metas_jurisdiccion'=>$metas_jurisdiccion,'total_metas'=>$total);
	}
}