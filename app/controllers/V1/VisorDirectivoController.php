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
use Proyecto,ComponenteMetaMes,ActividadMetaMes,Componente,Actividad;

class VisorDirectivoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if(isset($parametros['graficageneral'])){
			$mes_actual = Util::obtenerMesActual();
			if($mes_actual == 0){
				$mes_actual = date('n') -1;
			}

			$usuario = Sentry::getUser();
			if($usuario->claveJurisdiccion){
				//Se Agrupan por proyecto y se sacan solo los de la jurisdiccion del usuario
				$clave_jurisdiccion = $usuario->claveJurisdiccion;
			}else{
				$clave_jurisdiccion = '00';
			}

			$componentes = ComponenteMetaMes::where('claveJurisdiccion','=',$clave_jurisdiccion)
											->where('mes','<=',$mes_actual)
											->where(function($query){
												$query->where('meta','>',0.00)->orWhere('avance','>',0.00);
											})
											->groupBy('idComponente')
											->select('id','idComponente AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
												DB::raw('sum(avance) AS avance'))
											->get();
			
			$actividades = ActividadMetaMes::where('claveJurisdiccion','=',$clave_jurisdiccion)
											->where('mes','<=',$mes_actual)
											->where(function($query){
												$query->where('meta','>',0.00)->orWhere('avance','>',0.00);
											})
											->groupBy('idActividad')
											->select('id','idActividad AS idElemento','idProyecto',DB::raw('sum(meta) AS meta'),
												DB::raw('sum(avance) AS avance'))
											->get();

			$data = array();
			$data['componentes'] = $componentes;
			$data['actividades'] = $actividades;

			/*$elementos = $componentes->toArray() + $actividades->toArray();
			$total = count($elementos);
			$data = array('resultados2'=>$total,'data2'=>$elementos);*/
			/*
			$rows = Proyecto::getModel();
			$rows = $rows->where('idEstatusProyecto','=',5);
			
			$rows = $rows->with(array(
				'componentesMetasMes'=>function($query) use ($mes_actual,$clave_jurisdiccion){
					$query->where('claveJurisdiccion','=',$clave_jurisdiccion)
						->where('mes','<=',$mes_actual)
						->groupBy('idProyecto')
						->select('id','idComponente','idProyecto',DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'),DB::raw('count(distinct idComponente) AS totalComponentes'));
				},
				'actividadesMetasMes'=>function($query) use ($mes_actual,$clave_jurisdiccion){
					$query->where('claveJurisdiccion','=',$clave_jurisdiccion)
						->where('mes','<=',$mes_actual)
						->groupBy('idProyecto')
						->select('id','idActividad','idProyecto',DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'),DB::raw('count(distinct idActividad) AS totalActividades'));
				}
			));*/
			/*
			$rows = $rows->leftjoin('componenteMetasMes AS compMetasMes',function($join)use($mes_actual,$clave_jurisdiccion){
							$join->on('compMetasMes.idProyecto','=','proyectos.id')
								->where('compMetasMes.mes','<=',$mes_actual)
								->where('compMetasMes.claveJurisdiccion','=',$clave_jurisdiccion)
								->where(function($where){
									$where->where('compMetasMes.meta','>',0.00)->orWhere('compMetasMes.avance','>',0.00);
								})
								->whereNull('compMetasMes.borradoAl');
						})->leftjoin('actividadMetasMes AS actMetasMes',function($join)use($mes_actual,$clave_jurisdiccion){
							$join->on('actMetasMes.idProyecto','=','proyectos.id')
								->where('actMetasMes.mes','<=',$mes_actual)
								->where('actMetasMes.claveJurisdiccion','=',$clave_jurisdiccion)
								->where(function($where){
									$where->where('compMetasMes.meta','>',0.00)->orWhere('compMetasMes.avance','>',0.00);
								})
								->whereNull('actMetasMes.borradoAl');
						})
						//->groupBy('proyectos.id')
						->having(DB::raw('count(actMetasMes.claveJurisdiccion) + count(compMetasMes.claveJurisdiccion)'),'>',0)
						->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
							'nombreTecnico','proyectos.idEstatusProyecto'
							,DB::raw('count(distinct compMetasMes.idComponente) AS componentes')
							,DB::raw('count(distinct actMetasMes.idActividad) AS actividades'))
						->get()
						;
			*/
			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;

			//$total = count($rows);

			//$data = array('resultados'=>$total,'data'=>$rows);
			//$data['resultados'] = $total;
			//$data['data'] = $rows;

			/*if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}*/

			return Response::json($data,$http_status);
		}elseif(isset($parametros['formatogrid'])){

			if(isset($parametros['grid'])){
				if($parametros['grid'] == 'rendicion-acciones'){
					$rows = Proyecto::with('componentes.comentarios','componentes.registroAvance','componentes.actividades.comentarios','componentes.actividades.registroAvance')
									->find($parametros['idProyecto']);
					//$rows->componentes->load('registroAvance');
					//$rows['responsables'] = Directorio::responsablesActivos($rows->unidadResponsable)->get();
					
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
					$tabla = 'componenteMetasMes.';
					$campo = 'idComponente';
				}else{
					$recurso = Actividad::getModel();
					$tabla = 'actividadMetasMes.';
					$campo = 'idActividad';
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
				 	'metasMes'=>function($query) use ($claveJurisdiccion){
						$query->where('claveJurisdiccion','=',$claveJurisdiccion)
							->orderBy('mes','asc');
					},'metasMesJurisdiccion'=>function($query) use ($mes_actual,$tabla,$campo){
						$query->select($tabla.'id','idProyecto',$campo,'claveJurisdiccion',DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'),'vistaJurisdicciones.nombre AS jurisdiccion')
							->where('mes','<=',$mes_actual)
							->leftjoin('vistaJurisdicciones','vistaJurisdicciones.clave','=','claveJurisdiccion');
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