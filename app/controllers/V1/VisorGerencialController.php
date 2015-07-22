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
use Proyecto,Componente,Actividad,Beneficiario,RegistroAvanceMetas,ComponenteMetaMes,ActividadMetaMes,RegistroAvanceBeneficiario,EvaluacionAnalisisFuncional,EvaluacionProyectoMes,
	EvaluacionPlanMejora,ComponenteDesglose,DesgloseMetasMes,Directorio;

class VisorGerencialController extends BaseController {

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

			if(isset($parametros['grid'])){
				if($parametros['grid'] == 'rendicion-acciones'){
					$mes_actual = Util::obtenerMesActual();
					if($mes_actual == 0){
						$mes_actual = date('n') -1;
					}
					$usuario = Sentry::getUser();
					if($usuario->claveJurisdiccion){
						$clave_jurisdiccion = $usuario->claveJurisdiccion;
					}else{
						$clave_jurisdiccion = '00';
					}
					$rows = Proyecto::with(array(
								'componentes'=>function($query)use($clave_jurisdiccion){
									$query->select('proyectoComponentes.id','proyectoComponentes.idProyecto','proyectoComponentes.indicador')
										->join('componenteMetasMes AS metasMes',function($join)use($clave_jurisdiccion){
											$join->on('metasMes.idComponente','=','proyectoComponentes.id')
												->where('metasMes.claveJurisdiccion','=',$clave_jurisdiccion)
												->whereNull('metasMes.borradoAl');
										})
										->groupBy('metasMes.idComponente');
								},'componentes.metasMes'=>function($query)use($clave_jurisdiccion){
									$query->select('componenteMetasMes.id','componenteMetasMes.idComponente','componenteMetasMes.meta',
										'componenteMetasMes.mes','componenteMetasMes.avance')
										->where('componenteMetasMes.claveJurisdiccion','=',$clave_jurisdiccion);
								},
								'componentes.actividades'=>function($query)use($clave_jurisdiccion){
									$query->select('componenteActividades.id','componenteActividades.idComponente','componenteActividades.indicador')
										->join('actividadMetasMes AS metasMes',function($join)use($clave_jurisdiccion){
											$join->on('metasMes.idActividad','=','componenteActividades.id')
												->where('metasMes.claveJurisdiccion','=',$clave_jurisdiccion)
												->whereNull('metasMes.borradoAl');
										})
										->groupBy('metasMes.idActividad');
								},'componentes.actividades.metasMes'=>function($query)use($clave_jurisdiccion){
									$query->select('actividadMetasMes.id','actividadMetasMes.idActividad','actividadMetasMes.meta',
										'actividadMetasMes.mes','actividadMetasMes.avance')
										->where('actividadMetasMes.claveJurisdiccion','=',$clave_jurisdiccion);
								}
							)
						)->find($parametros['idProyecto']);
					//
					$total = count($rows);
				}
			}else{
				$mes_actual = Util::obtenerMesActual();
				if($mes_actual == 0){
					$mes_actual = date('n') -1;
				}
				$rows = Proyecto::getModel();
				$rows = $rows->where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto']);
				//
				$usuario = Sentry::getUser();
				if($usuario->claveJurisdiccion){
					//Se Agrupan por proyecto y se sacan solo los de la jurisdiccion del usuario
					$claveJurisdiccion = $usuario->claveJurisdiccion;
				}else{
					$claveJurisdiccion = '00';
				}

				$rows = $rows->leftjoin('componenteMetasMes AS compMetasMes',function($join)use($claveJurisdiccion){
							$join->on('compMetasMes.idProyecto','=','proyectos.id')
								->where('compMetasMes.claveJurisdiccion','=',$claveJurisdiccion)
								->where('compMetasMes.meta','>',0.00)
								->whereNull('compMetasMes.borradoAl');
						})->leftjoin('actividadMetasMes AS actMetasMes',function($join)use($claveJurisdiccion){
							$join->on('actMetasMes.idProyecto','=','proyectos.id')
								->where('actMetasMes.claveJurisdiccion','=',$claveJurisdiccion)
								->where('actMetasMes.meta','>',0.00)
								->whereNull('actMetasMes.borradoAl');
						})
						->groupBy('proyectos.id')
						->having(DB::raw('count(actMetasMes.claveJurisdiccion) + count(compMetasMes.claveJurisdiccion)'),'>',0)
						;

				$rows = $rows->with(array(
					'evaluacionMeses'=>function($query) use ($mes_actual){
						$query->where('evaluacionProyectoMes.mes','<=',$mes_actual)
							->whereIn('idEstatus',array(4,5));
					},
					'componentesMetasMes'=>function($query) use ($claveJurisdiccion){
						$query->where('claveJurisdiccion','=',$claveJurisdiccion)
							->orderBy('mes','asc');
					},
					'actividadesMetasMes'=>function($query) use ($claveJurisdiccion){
						$query->where('claveJurisdiccion','=',$claveJurisdiccion)
							->orderBy('mes','asc');
					}
				));
				
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){
					$rows = $rows->where(function($query)use($parametros){
						$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
							->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
					});
					
				}

				$total = $rows->select(DB::raw('count(distinct proyectos.id) AS conteo'))->get();
				$total = count($total);
				
				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
					'nombreTecnico','proyectos.idEstatusProyecto')
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
		
		$rows = Proyecto::all();

		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}else{
			$data = array("data"=>$rows->toArray());
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
		//
		$http_status = 200;
		$data = array();
		$parametros = Input::all();

		if(isset($parametros['mostrar'])){
			if($parametros['mostrar'] == 'datos-proyecto-avance'){
				$mes_actual = Util::obtenerMesActual();
				$recurso = Proyecto::with(array('datosFuncion','datosSubFuncion','datosProgramaPresupuestario','componentes.metasMesAgrupado','componentes.registroAvance','componentes.actividades.metasMesAgrupado','componentes.actividades.registroAvance','beneficiarios.registroAvance','beneficiarios.tipoBeneficiario',
					'evaluacionMeses'=>function($query) use ($mes_actual){
						if($mes_actual == 0){
							$mes_actual = date('n') - 1;
							$query->where('mes','=',$mes_actual)->where('idEstatus','=',4);
						}else{
							$query->where('mes','=',$mes_actual);
						}
					}))->find($id);
			}elseif ($parametros['mostrar'] == 'datos-municipio-avance') {
				//$id = idComponente y $parametros['clave-municipio'] y $parametros['nivel'] = 'componente'
				/*$mes_actual = Util::obtenerMesActual();
				if($parametros['nivel'] == 'componente'){
					$recurso = ComponenteDesglose::listarDatos()->where('claveMunicipio','=',$parametros['clave-municipio'])
													->where('idComponente','=',$id);
				}
				$recurso = $recurso->with(array('metasMes'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMesAcumuladas'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				}))->get();*/
			}elseif($parametros['mostrar'] == 'datos-metas-avance'){
				$mes_actual = Util::obtenerMesActual();
				if($mes_actual == 0){
					$mes_actual = date('n')-1;
				}
				if($parametros['nivel'] == 'componente'){
					$recurso = Componente::getModel();
				}else{
					$recurso = Actividad::getModel();
				}

				$usuario = Sentry::getUser();
				if($usuario->claveJurisdiccion){
					//Se Agrupan por proyecto y se sacan solo los de la jurisdiccion del usuario
					$claveJurisdiccion = $usuario->claveJurisdiccion;
				}else{
					$claveJurisdiccion = '00';
				}

				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = $recurso->with(array(
				 	'metasMes'=>function($query) use ($mes_actual,$claveJurisdiccion){
						$query->where('claveJurisdiccion','=',$claveJurisdiccion)
							->orderBy('mes','asc');
					},'metasMesJurisdiccion'=>function($query) use ($mes_actual){
						$query->where('mes','<=',$mes_actual);
					},'unidadMedida'
				))->find($id);

				/*if($parametros['nivel'] == 'componente'){
					$recurso->load('desgloseMunicipios');
					//$queries = DB::getQueryLog();
					//throw new Exception(print_r(end($queries),true), 1);
				}*/
			}elseif($parametros['mostrar'] == 'datos-jurisdicciones-avances'){
				$mes_actual = Util::obtenerMesActual();
				if($parametros['nivel'] == 'componente'){
					$recurso = Componente::getModel();
				}else{
					$recurso = Actividad::getModel();
				}
				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = $recurso->with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMes' => function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'planMejora'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'unidadMedida','comentarios'))->find($id);

				if($parametros['nivel'] == 'componente'){
					$recurso->load('desgloseMunicipios');
					//$queries = DB::getQueryLog();
					//throw new Exception(print_r(end($queries),true), 1);
				}
			}
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data["data"] = $recurso;
		}

		return Response::json($data,$http_status);
	}
}