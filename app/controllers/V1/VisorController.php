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
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime,Mail, PDF;
use Proyecto,ComponenteMetaMes,ActividadMetaMes,Componente,Actividad,CargaDatosEP01,PlanMejoraJurisdiccion;

class VisorController extends BaseController {
	private $reglasPlanMejora = array(
		'justificacion-acumulada-jurisdiccion'		=> 'required',
		'accion-mejora-jurisdiccion'				=> 'required',
		'grupo-trabajo-jurisdiccion'				=> 'required',
		'documentacion-comprobatoria-jurisdiccion'	=> 'required',
		'fecha-inicio-jurisdiccion'					=> 'required|date',
		'fecha-termino-jurisdiccion'				=> 'required|date',
		'fecha-notificacion-jurisdiccion'			=> 'required|date',
		'responsable-informacion'					=> 'required',
		'cargo-responsable-informacion'				=> 'required'
	);

	public function obtenerDatosCaptura(){
		$mes_actual = Util::obtenerMesActual();
		$anio_captura = Util::obtenerAnioCaptura();
		if($mes_actual == 0){ 
			$mes_actual = date('n')-1; 
		}else{ 
			$mes_actual = $mes_actual-1;
			if($mes_actual == 0){
				$anio_captura -= 1;
			}
		}
		if($mes_actual == 0){ 
			$mes_actual = 12; 
		}
		return ["mes"=>$mes_actual,"anio"=>$anio_captura];
	}
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
						$datos_captura = $this->obtenerDatosCaptura();
						$anio_captura = $datos_captura['anio'];

						if($usuario->claveJurisdiccion){
							//$proyectos_ids = [];
							$componentes = ComponenteMetaMes::groupBy('idProyecto')
													->where('claveJurisdiccion','=',$usuario->claveJurisdiccion)
													->where(function($condition){
														$condition->where('meta','>',0)
																->orWhere('avance','>',0);
													})
													->select('idProyecto')->get()->lists('idProyecto');
							$actividades = ActividadMetaMes::groupBy('idProyecto')
													->where('claveJurisdiccion','=',$usuario->claveJurisdiccion)
													->where(function($condition){
														$condition->where('meta','>',0)
																->orWhere('avance','>',0);
													})
													->select('idProyecto')->get()->lists('idProyecto');
							//$proyectos_ids = $componentes + $actividades;
							//$rows = $rows->whereIn('proyectos.id',$proyectos_ids);
							$rows = $rows->where(function($condicion)use($componentes,$actividades){
								$condicion->whereIn('proyectos.id',$componentes)
										->orWhereIn('proyectos.id',$actividades);
							});
						}
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('unidadResponsable',$unidades);
						}
						$rows = $rows->select('unidadResponsable AS clave', 'unidades.descripcion AS unidad', 
												DB::raw('count(distinct proyectos.id) AS noProyectos'))
										->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
										->where('idEstatusProyecto','=',5)
										->where('ejercicio','=',$anio_captura)
										->groupBy('unidadResponsable')
										->get();
						$total = $rows->sum('noProyectos');
						//$queries = DB::getQueryLog();
						//var_dump($queries);die;
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'proyectos_tipo':
						$rows = Proyecto::getModel();
						$usuario = Sentry::getUser();
						$datos_captura = $this->obtenerDatosCaptura();
						$anio_captura = $datos_captura['anio'];

						if($usuario->claveJurisdiccion){
							$componentes = ComponenteMetaMes::groupBy('idProyecto')
													->where('claveJurisdiccion','=',$usuario->claveJurisdiccion)
													->where(function($condition){
														$condition->where('meta','>',0)
																->orWhere('avance','>',0);
													})
													->select('idProyecto')->get()->lists('idProyecto');
							$actividades = ActividadMetaMes::groupBy('idProyecto')
													->where('claveJurisdiccion','=',$usuario->claveJurisdiccion)
													->where(function($condition){
														$condition->where('meta','>',0)
																->orWhere('avance','>',0);
													})
													->select('idProyecto')->get()->lists('idProyecto');
							$rows = $rows->where(function($condicion)use($componentes,$actividades){
								$condicion->whereIn('proyectos.id',$componentes)
										->orWhereIn('proyectos.id',$actividades);
							});
						}
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('unidadResponsable',$unidades);
						}
						$rows = $rows->select('clasificacion.descripcion AS tipoProyecto', 
												DB::raw('count(distinct proyectos.id) AS noProyectos'))
										->leftjoin('catalogoClasificacionProyectos AS clasificacion','clasificacion.id','=','proyectos.idClasificacionProyecto')
										->where('idEstatusProyecto','=',5)
										->where('ejercicio','=',$anio_captura)
										->groupBy('idClasificacionProyecto')
										->get();
						$total = $rows->sum('noProyectos');
						$data = array('data'=>$rows,'total'=>$total);
					break;
				case 'presupuesto_fuente':
						$datos_captura = $this->obtenerDatosCaptura();
						$anio_captura = $datos_captura['anio'];
						$mes_actual = $datos_captura['mes'];
						
						$rows = CargaDatosEP01::where('mes','=',$mes_actual)->where('ejercicio','=',$anio_captura);

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
						$data = array('data'=>$rows,'total'=>$total,'datos_captura'=>$datos_captura);
					break;
				case 'presupuesto_ejercido_capitulo':
						$datos_captura = $this->obtenerDatosCaptura();
						$anio_captura = $datos_captura['anio'];
						$mes_actual = $datos_captura['mes'];

						$rows = CargaDatosEP01::where('mes','=',$mes_actual)->where('ejercicio','=',$anio_captura);

						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('UR',$unidades);
						}

						$rows = $rows->select(DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'),
											'capitulo.descripcion AS capitulo','capitulo.clave')
									->leftjoin('catalogoObjetosGasto AS objetoGasto','objetoGasto.clave','=','OG')
									->leftjoin('catalogoObjetosGasto AS partida','partida.id','=','objetoGasto.idPadre')
									->leftjoin('catalogoObjetosGasto AS concepto','concepto.id','=','partida.idPadre')
									->leftjoin('catalogoObjetosGasto AS capitulo','capitulo.id','=','concepto.idPadre')
									->groupBy('capitulo.clave')
									->get();
						//$queries = DB::getQueryLog();
						//var_dump(end($queries));die;
						$total = $rows->sum('presupuestoEjercido');
						$data = array('data'=>$rows,'total'=>$total,'datos_captura'=>$datos_captura);
					break;
				case 'presupuesto_ejercido':
						$datos_captura = $this->obtenerDatosCaptura();
						$anio_captura = $datos_captura['anio'];
						$mes_actual = $datos_captura['mes'];

						$presupuesto = CargaDatosEP01::getModel();
						$usuario = Sentry::getUser();
						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$presupuesto = $presupuesto->whereIn('UR',$unidades);
						}else{
							if(isset($parametros['unidad'])){
								$unidad = array($parametros['unidad']);
								$presupuesto = $presupuesto->where('UR',$unidad);
							}
						}

						$presupuesto = $presupuesto->where('mes','=',$mes_actual)
													->where('ejercicio','=',$anio_captura)
													->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
														DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'))
													->first();
						//
						$data = array('data'=>$presupuesto,'datos_captura'=>$datos_captura);
					break;
				case 'metas_unidad':
						$rows = Proyecto::getModel();
						$usuario = Sentry::getUser();
						$datos_captura = $this->obtenerDatosCaptura();
						$anio_captura = $datos_captura['anio'];
						$componentes_ids = false;
						$actividades_ids = false;

						if($usuario->claveUnidad){
							$unidades = explode('|',$usuario->claveUnidad);
							$rows = $rows->whereIn('unidadResponsable',$unidades);
						}

						if($usuario->claveJurisdiccion){
							$clave_jurisdiccion = $usuario->claveJurisdiccion;
							
							$componentes = ComponenteMetaMes::groupBy('idProyecto','idComponente')
													->where('claveJurisdiccion','=',$usuario->claveJurisdiccion)
													->where(function($condition){
														$condition->where('meta','>',0)
																->orWhere('avance','>',0);
													})
													->select('idProyecto','idComponente')->get();
							$componentes_ids = $componentes->lists('idProyecto','idComponente');
							$componentes = $componentes->lists('idProyecto');
							
							$actividades = ActividadMetaMes::groupBy('idProyecto','idActividad')
													->where('claveJurisdiccion','=',$usuario->claveJurisdiccion)
													->where(function($condition){
														$condition->where('meta','>',0)
																->orWhere('avance','>',0);
													})
													->select('idProyecto','idActividad')->get();
							$actividades_ids = $actividades->lists('idProyecto','idActividad');
							$actividades = $actividades->lists('idProyecto');

							$totales = count($componentes) + count($actividades);

							$rows = $rows->where(function($condicion)use($componentes,$actividades){
								$condicion->whereIn('proyectos.id',$componentes)
										->orWhereIn('proyectos.id',$actividades);
							});

							$rows = $rows->select( DB::raw('concat_ws("|",unidadResponsable,unidades.descripcion) AS unidad'),'proyectos.id')
								->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
								->where('idEstatusProyecto','=',5)
								->where('ejercicio','=',$anio_captura)
								->get();

							$unidades_proyectos = $rows->lists('unidad','id');

							$rows = [];
							$total = 0;

							foreach ($componentes_ids as $key => $value) {
								if(isset($unidades_proyectos[$value])){
									$unidad = explode('|', $unidades_proyectos[$value]);
									if(!isset($rows[intval($unidad[0])])){
										$rows[intval($unidad[0])] = [
											'clave'=>$unidad[0],
											'noMetas'=>0,
											'unidad'=>$unidad[1]
										];
									}
									$rows[intval($unidad[0])]['noMetas'] += 1;
									$total += 1;
								}
							}

							foreach ($actividades_ids as $key => $value) {
								if(isset($unidades_proyectos[$value])){
									$unidad = explode('|', $unidades_proyectos[$value]);
									if(!isset($rows[intval($unidad[0])])){
										$rows[intval($unidad[0])] = [
											'clave'=>$unidad[0],
											'noMetas'=>0,
											'unidad'=>$unidad[1]
										];
									}
									$rows[intval($unidad[0])]['noMetas'] += 1;
									$total += 1;
								}
							}

							//$total = count($componentes) + count($actividades);
						}else{
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
								->where('ejercicio','=',$anio_captura)
								->groupBy('unidadResponsable')
								->get();

							$total = $rows->sum('noMetas');
						}
						
						//$queries = DB::getQueryLog();
						//var_dump(count($queries));die;
						$data = array('data'=>$rows,'total'=>$total,'datos_captura'=>$datos_captura);
					break;
				case 'metas_cumplidas':
						$datos_captura = $this->obtenerDatosCaptura();
						$anio_captura = $datos_captura['anio'];
						$mes_actual = $datos_captura['mes'];

						$componentes = ComponenteMetaMes::getModel();
						$actividades = ActividadMetaMes::getModel();

						$proyectos_ids = Proyecto::where('idEstatusProyecto','=',5)->where('ejercicio','=',$anio_captura);
						$jurisdiccion = false;
						$unidades = false;

						$usuario = Sentry::getUser();
						if($usuario->claveJurisdiccion){
							$jurisdiccion = $usuario->claveJurisdiccion;
							if(isset($parametros['unidad'])){
								$unidades = array($parametros['unidad']);
							}
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
														 ->groupBy('idComponente');
							$actividades = $actividades->where('claveJurisdiccion','=',$jurisdiccion)
														 ->groupBy('idActividad');
						}else{
							$componentes = $componentes->groupBy('idComponente');
							$actividades = $actividades->groupBy('idActividad');
						}
						
						$componentes = $componentes//->where('mes','<=',$mes_actual)
														->whereIn('idProyecto',$proyectos_ids)
														->where(function($condition){
																$condition->where('meta','>',0)
																		->orWhere('avance','>',0);
															})
														->select('id','idComponente AS idElemento','idProyecto',
															DB::raw('sum(if(mes <= '.$mes_actual.' ,meta,0)) AS meta'),
															DB::raw('sum(if(mes <= '.$mes_actual.' ,avance,0)) AS avance'),
															DB::raw('min(mes) as mes'))->get();
						//
						$actividades = $actividades//->where('mes','<=',$mes_actual)
														->whereIn('idProyecto',$proyectos_ids)
														->where(function($condition){
																$condition->where('meta','>',0)
																		->orWhere('avance','>',0);
															})
														->select('id','idActividad AS idElemento','idProyecto',
															DB::raw('sum(if(mes <= '.$mes_actual.' ,meta,0)) AS meta'),
															DB::raw('sum(if(mes <= '.$mes_actual.' ,avance,0)) AS avance'),
															DB::raw('min(mes) as mes'))->get();
						//
						$total = count($componentes) + count($actividades);
						
						$metas = array('cumplidas'=>0,'altoAvance'=>0,'bajoAvance'=>0,'posteriores'=>0);
						foreach ($componentes as $componente) {
							$meta = floatval($componente->meta);
					        $avance = floatval($componente->avance);

					        if($componente->mes <= $mes_actual){
					        	if($meta > 0){ $porcentaje = ($avance*100) / $meta; }
						        else{ $porcentaje = ($avance*100); }

						        $estatus = 1;
						        if(! ($meta == 0 && $avance == 0)){
									if($porcentaje > 110){ $estatus = 3; }
									elseif($porcentaje < 90){ $estatus = 2; }
									elseif($porcentaje > 0 && $meta == 0){ $estatus = 3; }
						        }

						        if($estatus == 1){ $metas['cumplidas']++; }
						        elseif($estatus == 2){ $metas['bajoAvance']++; }
						        else{ $metas['altoAvance']++; }
					        }else{
					        	$metas['posteriores']++;
					        }
					        
						}

						foreach ($actividades as $actividad) {
							$meta = floatval($actividad->meta);
					        $avance = floatval($actividad->avance);

					        if($actividad->mes <= $mes_actual){
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
						    }else{
						    	$metas['posteriores']++;
						    }
						}

						$data = array('data'=>$metas,'total'=>$total,'datos_captura'=>$datos_captura);
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
								'abreviacion'=>$metas['unidadAbreviacion'],
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
									'abreviacion'=>$presupuesto->unidadAbreviacion,
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
		}elseif(isset($parametros['tabla'])) {
			if($parametros['tabla'] == 'resumen_presupuesto'){
				$datos_captura = $this->obtenerDatosCaptura();
				$anio_captura = $datos_captura['anio'];
				$mes_actual = $datos_captura['mes'];

				$rows = CargaDatosEP01::where('mes','=',$mes_actual)->where('ejercicio','=',$anio_captura);

				$usuario = Sentry::getUser();
				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('UR',$unidades);
				}

				$rows = $rows->select('unidades.descripcion AS unidadResponsable',
								DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
								DB::raw('sum(presupuestoLiberado) AS presupuestoLiberado'),
								DB::raw('sum(presupuestoMinistrado) AS presupuestoMinistrado'),
								DB::raw('sum(presupuestoComprometidoModificado) AS presupuestoComprometidoModificado'),
								DB::raw('sum(presupuestoDevengadoModificado) AS presupuestoDevengadoModificado'),
								DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercidoModificado'),
								DB::raw('sum(presupuestoPagadoModificado) AS presupuestoPagadoModificado'),
								DB::raw('sum(disponiblePresupuestarioModificado) AS disponiblePresupuestarioModificado')
							)
							->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','UR')
							->groupBy('UR')
							->get();
				$total['presupuestoModificado'] = $rows->sum('presupuestoModificado');
				$total['presupuestoLiberado'] = $rows->sum('presupuestoLiberado');
				$total['presupuestoMinistrado'] = $rows->sum('presupuestoMinistrado');
				$total['presupuestoComprometidoModificado'] = $rows->sum('presupuestoComprometidoModificado');
				$total['presupuestoDevengadoModificado'] = $rows->sum('presupuestoDevengadoModificado');
				$total['presupuestoEjercidoModificado'] = $rows->sum('presupuestoEjercidoModificado');
				$total['presupuestoPagadoModificado'] = $rows->sum('presupuestoPagadoModificado');
				$total['disponiblePresupuestarioModificado'] = $rows->sum('disponiblePresupuestarioModificado');

				$data = array('data'=>$rows,'total'=>$total,'datos_captura'=>$datos_captura);
			}elseif($parametros['tabla'] == 'indicadores_resultados_jurisdiccion'){
				$datos_captura = $this->obtenerDatosCaptura();
				$anio_captura = $datos_captura['anio'];
				$mes_actual = $datos_captura['mes'];
				
				$usuario = Sentry::getUser();
				if($usuario->claveJurisdiccion){
					$jurisdiccion = $usuario->claveJurisdiccion;
				}elseif(isset($parametros['jurisdiccion'])){
					$jurisdiccion = $parametros['jurisdiccion'];
				}else{
					$jurisdiccion = false;
				}

				$query_params = [];

				$query = "SELECT PC.idProyecto, P.idTipoProyecto as tipoProyecto, 1 AS nivel, concat(unidadResponsable, finalidad, funcion, subfuncion, subsubfuncion, programaSectorial, programaPresupuestario, origenAsignacion, actividadInstitucional, proyectoEstrategico, LPAD(numeroProyectoEstrategico,3,'0')) as clave, P.nombreTecnico, PC.indicador, sum(CMM.meta) as meta, sum(if(EPM.idEstatus>3,CMM.avance,0)) as avance ";
				$query .= "FROM proyectoComponentes PC ";
				$query .= "JOIN componenteMetasMes CMM ON CMM.idComponente = PC.id and CMM.borradoAl is null ";

				if($jurisdiccion){
					$query .= "and CMM.claveJurisdiccion = ? ";
					$query_params[] = $jurisdiccion;
				}

				$query .= "LEFT JOIN evaluacionProyectoMes EPM ON EPM.idProyecto = CMM.idProyecto AND EPM.mes = CMM.mes AND EPM.mes < ? ";
				$query_params[] = $mes_actual;
				$query .= "JOIN proyectos P ON P.id = PC.idProyecto ";
				$query .= "WHERE PC.borradoAl IS NULL ";
				$query .= "GROUP BY CMM.idComponente, CMM.claveJurisdiccion ";
				//$query .= "ORDER BY P.idTipoProyecto";

				$query .= 'UNION ';

				$query .= "SELECT CA.idProyecto, P2.idTipoProyecto as tipoProyecto, 2 AS nivel, concat(unidadResponsable, finalidad, funcion, subfuncion, subsubfuncion, programaSectorial, programaPresupuestario, origenAsignacion, actividadInstitucional, proyectoEstrategico, LPAD(numeroProyectoEstrategico,3,'0')) as clave, P2.nombreTecnico, CA.indicador, sum(AMM.meta) as meta, sum(if(EPM2.idEstatus>3,AMM.avance,0)) as avance ";
				$query .= "FROM componenteActividades CA ";
				$query .= "JOIN actividadMetasMes AMM ON AMM.idActividad = CA.id and AMM.borradoAl is null ";

				if($jurisdiccion){
					$query .= "and AMM.claveJurisdiccion = ? ";
					$query_params[] = $jurisdiccion;
				}

				$query .= "LEFT JOIN evaluacionProyectoMes EPM2 ON EPM2.idProyecto = AMM.idProyecto AND EPM2.mes = AMM.mes AND EPM2.mes < ? ";
				$query_params[] = $mes_actual;
				$query .= "JOIN proyectos P2 ON P2.id = CA.idProyecto ";
				$query .= "WHERE CA.borradoAl IS NULL ";
				$query .= "GROUP BY AMM.idActividad, AMM.claveJurisdiccion ";
				$query .= "ORDER BY tipoProyecto, clave, nivel";

				$rows = DB::select($query, $query_params);
				$indicadores = [1=>[],2=>[]]; //indicadores de proyectos institucionales y de inversión
				foreach ($rows as $row) {
					if(!isset($indicadores[$row->tipoProyecto][$row->idProyecto])){
						$indicadores[$row->tipoProyecto][$row->idProyecto] = [
							'clave' => $row->clave,
							'nombre' => $row->nombreTecnico,
							'componentes' => [],
							'actividades' => []
						];
					}
					if($row->nivel == 1){
						$indicadores[$row->tipoProyecto][$row->idProyecto]['componentes'][] = [
							'indicador' => $row->indicador,
							'meta' => $row->meta,
							'avance' => $row->avance,
							'porcentaje' => 0.00
						];
					}else{
						$indicadores[$row->tipoProyecto][$row->idProyecto]['actividades'][] = [
							'indicador' => $row->indicador,
							'meta' => $row->meta,
							'avance' => $row->avance,
							'porcentaje' => 0.00
						];
					}
				}

				$data = array('data'=>$indicadores,'datos_captura'=>$datos_captura);
			}
		}elseif(isset($parametros['formatogrid'])){
			if(isset($parametros['clasificacionProyecto'])){
				$datos_captura = $this->obtenerDatosCaptura();
				$anio_captura = $datos_captura['anio'];
				$mes_actual = $datos_captura['mes'];

				$mes_activo = Util::obtenerMesActual();
				if($mes_activo == 1){
					$mes_activo = 0;
				}

				$jurisdiccion = false;

				$rows = Proyecto::where('idEstatusProyecto','=',5)
								->where('ejercicio','=',$anio_captura)
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
							->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
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

				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
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
				$datos_captura = $this->obtenerDatosCaptura();
				$anio_captura = $datos_captura['anio'];
				$mes_actual = $datos_captura['mes'];
				
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
											'proyectoComponentes.indicador',DB::raw('sum(metasMes.meta) AS metaAnual'),
											DB::raw('count(observaciones.id) AS observaciones'))
										->join('componenteMetasMes AS metasMes',function($join)use($jurisdiccion){
											$join->on('metasMes.idComponente','=','proyectoComponentes.id')
												->whereNull('metasMes.borradoAl');
											if($jurisdiccion){ $join->where('metasMes.claveJurisdiccion','=',$jurisdiccion); }
										})
										->leftjoin('observacionRendicionCuenta AS observaciones',function($join){
											$join->on('proyectoComponentes.id','=','observaciones.idElemento')
												->where('observaciones.nivel','=',1)
												->whereNull('observaciones.borradoAl');
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
											DB::raw('sum(metasMes.meta) AS metaAnual'),
											DB::raw('count(observaciones.id) AS observaciones'))
										->join('actividadMetasMes AS metasMes',function($join)use($jurisdiccion){
											$join->on('metasMes.idActividad','=','componenteActividades.id')
												->whereNull('metasMes.borradoAl');
											if($jurisdiccion){ $join->where('metasMes.claveJurisdiccion','=',$jurisdiccion); }
										})
										->leftjoin('observacionRendicionCuenta AS observaciones',function($join){
											$join->on('componenteActividades.id','=','observaciones.idElemento')
												->where('observaciones.nivel','=',2)
												->whereNull('observaciones.borradoAl');
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
						'avanceAcumulado' => 0,'avanceMes' => 0,
						'observaciones' => $componente->observaciones
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
						'avanceAcumulado' => 0,'avanceMes' => 0,
						'observaciones' => $actividad->observaciones
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
				$data = array('data'=>$elementos,'resultados'=>$total,'datos_captura'=>$datos_captura);
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
				$datos_captura = $this->obtenerDatosCaptura();
				$anio_captura = $datos_captura['anio'];
				$mes_actual = $datos_captura['mes'];

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
							},'planMejora'=>function($query) use ($mes_actual){
								$query->where('mes','=',$mes_actual);
							},'observaciones'=>function($query){
								$query->orderBy('modificadoAl','desc');
							},'unidadMedida'))->find($id);

				$elemento = array(
					'id' => $recurso->id,
					'nivel' => $parametros['nivel'],
					'indicador' => $recurso->indicador,
					'unidadMedida' => $recurso->unidadMedida->descripcion,
					'metaTotal' => $recurso->metasMesAgrupado->sum('meta'),
					'analisisResultados' => null,'justificacion' => null,
					'planMejora' => null, 'observaciones'=>$recurso->observaciones
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
					$juris_metas[$idJuris]['avanceAcumulado'] = $juris_metas[$idJuris]['avanceTotal'] - floatval($meta_mes->avance);
					if(isset($meses_capturados[$meta_mes->mes])){
						$juris_metas[$idJuris]['avanceMes'] = floatval($meta_mes->avance);
					}else{
						$juris_metas[$idJuris]['avanceTotal'] = $juris_metas[$idJuris]['avanceAcumulado'];
						if($juris_metas[$idJuris]['metaAcumulada'] > 0){ $porcentaje = ($juris_metas[$idJuris]['avanceTotal']*100) / $juris_metas[$idJuris]['metaAcumulada']; }
			        	else{ $porcentaje = ($juris_metas[$idJuris]['avanceTotal']*100); }
			        	$juris_metas[$idJuris]['porcentaje'] = $porcentaje;
			        	$estatus = 1;
				        if(!($juris_metas[$idJuris]['metaAcumulada'] == 0 && $juris_metas[$idJuris]['avanceTotal'] == 0)){
							if($porcentaje > 110){ $estatus = 3; }
							elseif($porcentaje < 90){ $estatus = 2; }
							elseif($porcentaje > 0 && $meta == 0){ $estatus = 3; }
				        }
				        $juris_metas[$idJuris]['estatus'] = $estatus;
					}
				}
				$elemento['jurisdicciones'] = $juris_metas;

				$mes_metas = array();
				$meta_acumulada = 0;
				$avance_acumulado = 0;
				foreach ($recurso->metasMesAgrupado as $meta_mes) {
					$meta_acumulada += floatval($meta_mes->meta);
					$mes_metas[$meta_mes->mes] = array(
						'meta' => floatval($meta_mes->meta),
						'metaAcumulada' => $meta_acumulada,'avance'=>0,'avanceAcumulado'=>0,
						'estatus' => 0, 'activo'=>false, 'porcentaje'=>0
					);

					if(isset($meses_capturados[$meta_mes->mes])){
						$avance_acumulado += floatval($meta_mes->avance);
						$mes_metas[$meta_mes->mes]['avance'] = floatval($meta_mes->avance);
						$mes_metas[$meta_mes->mes]['avanceAcumulado'] = $avance_acumulado;
						$mes_metas[$meta_mes->mes]['activo'] = true;
					}else{
						if($meta_mes->mes > 4){
							$mes_metas[$meta_mes->mes]['avanceAcumulado'] = $avance_acumulado;
							$mes_metas[$meta_mes->mes]['activo'] = true;
						}
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

					$planMejoraJurisdiccion = PlanMejoraJurisdiccion::where('mes','=',$mes_actual)
																	->where('idNivel','=',$id)
																	->where('nivel','=',($parametros['nivel']=='componente')?1:2)
																	->where('claveJurisdiccion','=',$jurisdiccion)
																	->first();
					if($planMejoraJurisdiccion){
						$elemento['planMejoraJurisdiccion'] = $planMejoraJurisdiccion;
					}

				}else{
					$elemento['tomar'] = 'jurisdicciones';
				}

				$data['data'] = $elemento;
				//$data["data"] = $recurso;
			}
		}elseif(isset($parametros['reporte-plan-mejora'])){
			$datos_captura = $this->obtenerDatosCaptura();
			$anio_captura = $datos_captura['anio'];
			$mes_actual = $datos_captura['mes'];

			$meses = array(
				1 => array('mes'=>'Enero',			'abrev'=>'ENE',	'trimestre'=>1, 'trimestre_letras'=>'PRIMER'),
				2 => array('mes'=>'Febrero',		'abrev'=>'FEB',	'trimestre'=>1, 'trimestre_letras'=>'PRIMER'),
				3 => array('mes'=>'Marzo',			'abrev'=>'MAR',	'trimestre'=>1, 'trimestre_letras'=>'PRIMER'),
				4 => array('mes'=>'Abril',			'abrev'=>'ABR',	'trimestre'=>2, 'trimestre_letras'=>'SEGUNDO'),
				5 => array('mes'=>'Mayo',			'abrev'=>'MAY',	'trimestre'=>2, 'trimestre_letras'=>'SEGUNDO'),
				6 => array('mes'=>'Junio',			'abrev'=>'JUN',	'trimestre'=>2, 'trimestre_letras'=>'SEGUNDO'),
				7 => array('mes'=>'Julio',			'abrev'=>'JUL',	'trimestre'=>3, 'trimestre_letras'=>'TERCER'),
				8 => array('mes'=>'Agosto',			'abrev'=>'AGO',	'trimestre'=>3, 'trimestre_letras'=>'TERCER'),
				9 => array('mes'=>'Septiembre',		'abrev'=>'SEP',	'trimestre'=>3, 'trimestre_letras'=>'TERCER'),
				10 => array('mes'=>'Octubre',		'abrev'=>'OCT',	'trimestre'=>4, 'trimestre_letras'=>'CUARTO'),
				11 => array('mes'=>'Noviembre',		'abrev'=>'NOV',	'trimestre'=>4, 'trimestre_letras'=>'CUARTO'),
				12 => array('mes'=>'Diciembre',		'abrev'=>'DIC',	'trimestre'=>4, 'trimestre_letras'=>'CUARTO')
			);

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

			if(!$jurisdiccion){
				return Response::json(array("data"=>"Se debe elegir una jursidicción.",'code'=>'U06'),500);
			}

			$recurso = Proyecto::where('idEstatusProyecto','=',5)->with(array('liderProyecto',
			'evaluacionMeses'=>function($query)use($mes_actual){
				$estatus = [4,5];
				$query->where('mes','<=',$mes_actual)
						->whereIn('idEstatus',$estatus)
						->whereNull('borradoAl')
						->orderBy('mes','ASC');
			},'componentes.metasMes'=>function($query) use ($mes_actual,$jurisdiccion){
				$query->where('mes','<=',$mes_actual)
						->where('claveJurisdiccion','=',$jurisdiccion)
						->orderBy('mes','ASC');
			},'componentes.planesMejoraJurisdiccion'=>function($query)use($mes_actual,$jurisdiccion){
				$query->where('mes','=',$mes_actual)
						->where('claveJurisdiccion','=',$jurisdiccion)
						->orderBy('mes','ASC');
			},'componentes.actividades.metasMes'=>function($query) use ($mes_actual,$jurisdiccion){
				$query->where('mes','<=',$mes_actual)
						->where('claveJurisdiccion','=',$jurisdiccion)
						->orderBy('mes','ASC');
			},'componentes.actividades.planesMejoraJurisdiccion'=>function($query)use($mes_actual,$jurisdiccion){
				$query->where('mes','=',$mes_actual)
						->where('claveJurisdiccion','=',$jurisdiccion)
						->orderBy('mes','ASC');
			}))->find($id);
			//$recurso = $recurso->toArray();

			$meses_capturados = $recurso->evaluacionMeses->lists('idEstatus','mes');
			//return Response::json($meses_capturados,200);

			$acciones = array();
			$datos['proyecto'] = array(
				'ejercicio' => $recurso->ejercicio,
				'nombreTecnico' => $recurso->nombreTecnico,
				'ClavePresupuestaria' => $recurso->ClavePresupuestaria,
				'liderProyecto' => '',
				'cargoLiderProyecto' => '',
				'responsableInformacion' => '',
				'cargoResponsableInformacion' => ''
			);

			if($recurso->liderProyecto){
				$datos['proyecto']['liderProyecto'] = $recurso->liderProyecto->nombre;
				$datos['proyecto']['cargoLiderProyecto'] = $recurso->liderProyecto->cargo;
			}

			foreach ($recurso->componentes as $componente) {
				$meta_acumulada = 0;
				$avance_acumulado = 0;
				foreach ($componente->metasMes as $metaMes) {
					$meta_acumulada += floatval($metaMes->meta);
					if(isset($meses_capturados[$metaMes->mes])){
			        	$avance_acumulado += floatval($metaMes->avance);
					}
				}
				if($meta_acumulada > 0){ $porcentaje = ($avance_acumulado*100) / $meta_acumulada; }
		        else{ $porcentaje = ($avance_acumulado*100); }

		        $estatus = 1;
		        if(!($meta_acumulada == 0 && $avance_acumulado == 0)){
					if($porcentaje > 110){ $estatus = 3; }
					elseif($porcentaje < 90){ $estatus = 2; }
					elseif($porcentaje > 0 && $meta_acumulada == 0){ $estatus = 3; }
		        }

		        $datos_componente = array(
		        	'indicador' => $componente->indicador,
		        	'nivel' => 'componente',
		        	'estatus'=>$estatus,
		        	'avance_acumulado' => $porcentaje,
		        	'actividades' => array()
		        );

		        
	        	if(count($componente->planesMejoraJurisdiccion)){
	        		$datos_componente['plan_mejora'] = $componente->planesMejoraJurisdiccion[0];
	        		$datos['proyecto']['responsableInformacion'] = $componente->planesMejoraJurisdiccion[0]->responsableInformacion;
		        	$datos['proyecto']['cargoResponsableInformacion'] = $componente->planesMejoraJurisdiccion[0]->cargoResponsableInformacion;
	        	}
		        
		        foreach ($componente->actividades as $actividad) {
					$meta_acumulada = 0;
					$avance_acumulado = 0;
					foreach ($actividad->metasMes as $metaMes) {
						$meta_acumulada += floatval($metaMes->meta);
						if(isset($meses_capturados[$metaMes->mes])){
				        	$avance_acumulado += floatval($metaMes->avance);
						}
					}
					if($meta_acumulada > 0){ $porcentaje = ($avance_acumulado*100) / $meta_acumulada; }
			        else{ $porcentaje = ($avance_acumulado*100); }

			        $estatus = 1;
			        if(!($meta_acumulada == 0 && $avance_acumulado == 0)){
						if($porcentaje > 110){ $estatus = 3; }
						elseif($porcentaje < 90){ $estatus = 2; }
						elseif($porcentaje > 0 && $meta_acumulada == 0){ $estatus = 3; }
			        }

			        $datos_actividad = array(
			        	'indicador' => $actividad->indicador,
			        	'nivel' => 'actividad',
			        	'estatus'=>$estatus,
			        	'avance_acumulado' => $porcentaje
			        );
			        
		        	if(count($actividad->planesMejoraJurisdiccion)){
		        		$datos_actividad['plan_mejora'] = $actividad->planesMejoraJurisdiccion[0];
		        		$datos['proyecto']['responsableInformacion'] = $actividad->planesMejoraJurisdiccion[0]->responsableInformacion;
		        		$datos['proyecto']['cargoResponsableInformacion'] = $actividad->planesMejoraJurisdiccion[0]->cargoResponsableInformacion;
		        	}
			        
			        $datos_componente['actividades'][] = $datos_actividad;
				}
				$acciones[] = $datos_componente;
			}
			//return Response::json($acciones,200);
			$datos['proyecto']['componentes'] = $acciones;
			$datos['mes'] = $meses[intval($mes_actual)];

			$pdf = PDF::setPaper('LETTER')->setOrientation('landscape')->setWarnings(false)->loadView('reportes.plan-mejora-jurisdiccion',$datos);
		
			$pdf->output();
			$dom_pdf = $pdf->getDomPDF();
			$canvas = $dom_pdf ->get_canvas();
			$w = $canvas->get_width();
	  		$h = $canvas->get_height();
			$canvas->page_text(($w-75), ($h-16), "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

			return $pdf->stream('plan_mejora_jurisdiccional.pdf');
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}
		
		return Response::json($data,$http_status);
	}

	public function presupuestoEjercidoPorUnidad(){
		$datos_captura = $this->obtenerDatosCaptura();
		$anio_captura = $datos_captura['anio'];
		$mes_actual = $datos_captura['mes'];

		$rows = CargaDatosEP01::getModel();

		$usuario = Sentry::getUser();
		if($usuario->claveUnidad){
			$unidades = explode('|',$usuario->claveUnidad);
			$rows = $rows->whereIn('UR',$unidades);
		}

		$rows = $rows->where('mes','=',$mes_actual)
					->where('ejercicio','=',$anio_captura)
					->select(DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
						DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercido'),
						'unidad.descripcion AS unidadResponsable','UR AS clave','unidad.abreviatura AS unidadAbreviacion')
					->leftjoin('catalogoUnidadesResponsables AS unidad','unidad.clave','=','UR')
					->groupBy('UR')
					->get();
		$total_modif = $rows->sum('presupuestoModificado');
		$total_ejer = $rows->sum('presupuestoEjercido');
		return array('presupuesto_unidad'=>$rows,'total_modificado'=>$total_modif,'total_ejercido'=>$total_ejer);
	}

	public function metasCumplidasPorUnidad(){
		$datos_captura = $this->obtenerDatosCaptura();
		$anio_captura = $datos_captura['anio'];
		$mes_actual = $datos_captura['mes'];
		
		$usuario = Sentry::getUser();
		if($usuario->claveUnidad){
			$unidades = explode('|',$usuario->claveUnidad);
		}else{
			$unidades = null;
		}

		$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join)use($unidades,$anio_captura){
							$join->on('proyectos.id','=','componenteMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->where('proyectos.ejercicio','=',$anio_captura)
								->whereNull('proyectos.borradoAl');
							if($unidades){
								$join = $join->on('proyectos.unidadResponsable','in',DB::raw('('.implode(',',$unidades).')'));
							}
						})
						->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
						->groupBy('idComponente')
						->orderBy('unidadResponsable')
						->select('idComponente','unidades.descripcion AS unidad','unidades.abreviatura AS unidadAbreviacion',
							'proyectos.unidadResponsable',
							DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join)use($unidades,$anio_captura){
							$join->on('proyectos.id','=','actividadMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->where('proyectos.ejercicio','=',$anio_captura)
								->whereNull('proyectos.borradoAl');
							if($unidades){
								$join = $join->on('proyectos.unidadResponsable','in',DB::raw('('.implode(',',$unidades).')'));
							}
						})
						->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','proyectos.unidadResponsable')
						->groupBy('idActividad')
						->orderBy('unidadResponsable')
						->select('idActividad','unidades.descripcion AS unidad','unidades.abreviatura AS unidadAbreviacion',
							'proyectos.unidadResponsable',
							DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$total = count($componentes) + count($actividades);
		$metas_unidad = array();
		foreach ($componentes as $componente) {
			if(!isset($metas_unidad[$componente->unidadResponsable])){
				$metas_unidad[$componente->unidadResponsable] = array(
					'unidadResponsable'=>$componente->unidad,
					'unidadAbreviacion'=>$componente->unidadAbreviacion,
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
					'unidadAbreviacion'=>$actividad->unidadAbreviacion,
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
		$datos_captura = $this->obtenerDatosCaptura();
		$anio_captura = $datos_captura['anio'];
		$mes_actual = $datos_captura['mes'];

		$componentes = ComponenteMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join)use($anio_captura){
							$join->on('proyectos.id','=','componenteMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->where('proyectos.ejercicio','=',$anio_captura)
								->whereNull('proyectos.borradoAl');
						})
						->leftjoin('vistaJurisdicciones AS jurisdiccion','jurisdiccion.clave','=','claveJurisdiccion')
						->groupBy('idComponente','claveJurisdiccion')
						->select('idComponente','jurisdiccion.nombre AS jurisdiccion',
							'claveJurisdiccion',
							DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))->get();
		//
		$actividades = ActividadMetaMes::where('mes','<=',$mes_actual)
						->join('proyectos',function($join)use($anio_captura){
							$join->on('proyectos.id','=','actividadMetasMes.idProyecto')
								->where('proyectos.idEstatusProyecto','=',5)
								->where('proyectos.ejercicio','=',$anio_captura)
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

	public function store(){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		try{
			$usuario = Sentry::getUser();

			$parametros = Input::all();
			$validacion = Validador::validar($parametros, $this->reglasPlanMejora);

			if($validacion === TRUE){
				$fechas = $this->validar_fechas($parametros['fecha-inicio-jurisdiccion'], $parametros['fecha-termino-jurisdiccion'], $parametros['fecha-notificacion-jurisdiccion']);

				if(isset($fechas['error'])){
					$respuesta['data']['data'] = [$fechas['data']];
					$respuesta['data']['code'] = 'U00';
					$respuesta['http_status'] = 409;
				}else{
					$plan_mejora = new PlanMejoraJurisdiccion;

					if($parametros['tipo'] == 'componente'){
						$plan_mejora->nivel = 1;
					}else{
						$plan_mejora->nivel = 2;
					}
					
					$plan_mejora->idProyecto = $parametros['id-proyecto-plan-mejora'];
					$plan_mejora->idNivel = $parametros['id-accion-plan-mejora'];
					$plan_mejora->mes = $parametros['mes-plan-mejora'];

					if($usuario->claveJurisdiccion){
						$plan_mejora->claveJurisdiccion = $usuario->claveJurisdiccion;
					}elseif($parametros['jurisdiccion-plan-mejora']){
						$plan_mejora->claveJurisdiccion = $parametros['jurisdiccion-plan-mejora'];
					}else{
						throw new Exception("No se encontró Jurisdicción", 1);
					}

					$plan_mejora->justificacionAcumulada 		= $parametros['justificacion-acumulada-jurisdiccion'];
					$plan_mejora->accionMejora 					= $parametros['accion-mejora-jurisdiccion'];
					$plan_mejora->grupoTrabajo 					= $parametros['grupo-trabajo-jurisdiccion'];
					$plan_mejora->documentacionComprobatoria 	= $parametros['documentacion-comprobatoria-jurisdiccion'];
					$plan_mejora->fechaInicio 					= $fechas['inicio'];
					$plan_mejora->fechaTermino 					= $fechas['termino'];
					$plan_mejora->fechaNotificacion 			= $fechas['notificacion'];
					$plan_mejora->responsableInformacion		= $parametros['responsable-informacion'];
					$plan_mejora->cargoResponsableInformacion 	= $parametros['cargo-responsable-informacion'];

					if($plan_mejora->save()){
						$respuesta['data']['data'] = $plan_mejora;
					}else{
						throw new Exception("Error Processing Request", 1);
						
					}
				}
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
			}
		}catch(Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>"Ocurrió un error en el servidor, intente de nuevo mas tarde o pongase en contacto con el administrador del sistema.",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	public function update($id){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		try{
			$usuario = Sentry::getUser();

			$parametros = Input::all();
			$validacion = Validador::validar($parametros, $this->reglasPlanMejora);

			if($validacion === TRUE){
				$fechas = $this->validar_fechas($parametros['fecha-inicio-jurisdiccion'], $parametros['fecha-termino-jurisdiccion'], $parametros['fecha-notificacion-jurisdiccion']);

				if(isset($fechas['error'])){
					$respuesta['data']['data'] = [$fechas['data']];
					$respuesta['data']['code'] = 'U00';
					$respuesta['http_status'] = 409;
				}else{
					$plan_mejora = PlanMejoraJurisdiccion::find($id);

					if(!$plan_mejora){
						throw new Exception("Registro no encontrado", 1);
					}

					$plan_mejora->justificacionAcumulada 		= $parametros['justificacion-acumulada-jurisdiccion'];
					$plan_mejora->accionMejora 					= $parametros['accion-mejora-jurisdiccion'];
					$plan_mejora->grupoTrabajo 					= $parametros['grupo-trabajo-jurisdiccion'];
					$plan_mejora->documentacionComprobatoria 	= $parametros['documentacion-comprobatoria-jurisdiccion'];
					$plan_mejora->fechaInicio 					= $fechas['inicio'];
					$plan_mejora->fechaTermino 					= $fechas['termino'];
					$plan_mejora->fechaNotificacion 			= $fechas['notificacion'];
					$plan_mejora->responsableInformacion		= $parametros['responsable-informacion'];
					$plan_mejora->cargoResponsableInformacion 	= $parametros['cargo-responsable-informacion'];
					
					if($plan_mejora->save()){
						$respuesta['data']['data'] = $plan_mejora;
					}else{
						throw new Exception("Error Processing Request", 1);
					}
				}
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
			}
		}catch(Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>"Ocurrió un error en el servidor, intente de nuevo mas tarde o pongase en contacto con el administrador del sistema.",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	private function validar_fechas($fecha_inicial, $fecha_final, $fecha_noti){
		$fecha_inicio = DateTime::createFromFormat('d/m/Y',$fecha_inicial);
		$fecha_termino = DateTime::createFromFormat('d/m/Y',$fecha_final);
		$fecha_notificacion = DateTime::createFromFormat('d/m/Y',$fecha_noti);

		if(!$fecha_inicio){ $fecha_inicio = DateTime::createFromFormat('Y-m-d',$fecha_inicial); }

		if(!$fecha_termino){ $fecha_termino = DateTime::createFromFormat('Y-m-d',$fecha_final); }

		if(!$fecha_notificacion){ $fecha_notificacion = DateTime::createFromFormat('Y-m-d',$fecha_noti); }

		if(!$fecha_inicio){
			return array('error'=>true,'data'=>'{"field":"fecha-inicio-jurisdiccion","error":"La fecha de inicio del periodo de ejecución no tiene el formato correcto."}');
		}

		if(!$fecha_termino){
			return array('error'=>true,'data'=>'{"field":"fecha-termino-jurisdiccion","error":"La fecha final del periodo de ejecución no tiene el formato correcto."}');
		}

		if(!$fecha_notificacion){
			return array('error'=>true,'data'=>'{"field":"fecha-notificacion-jurisdiccion","error":"La fecha final del periodo de ejecución no tiene el formato correcto."}');
		}		

		if($fecha_termino < $fecha_inicio){
			return array('error'=>true,'data'=>'{"field":"fecha-termino-jurisdiccion","error":"La fecha de termino no puede ser menor que la de inicio."}');
		}

		if($fecha_notificacion < $fecha_termino){
			return array('error'=>true,'data'=>'{"field":"fecha-notificacion-jurisdiccion","error":"La fecha de notificación no puede ser menor que la de termino."}');
		}

		return array('inicio' =>$fecha_inicio, 'termino' => $fecha_termino, 'notificacion' => $fecha_notificacion);
	}
}