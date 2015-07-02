<?php
/* 
*	SIRE
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2014, Instituto de Salud.
*	@author 			Mario Cabrera
*	@package 			sire
*	@version 			1.0 
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Validador, SSA\Utilerias\Util;
use Illuminate\Database\QueryException, \Exception;
use BaseController, Input, Response, DB, Sentry,Proyecto, EvaluacionProyectoMes,ComponenteMetaMes,ActividadMetaMes;
use EvaluacionAnalisisFuncional,EvaluacionComentario,EvaluacionPlanMejora,RegistroAvanceBeneficiario,RegistroAvanceMetas;

class PurgarSeguimientoController extends \BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$respuesta = array('http_status'=>200,'data'=>'');

		$parametros = Input::all();
		
		try{
			if(isset($parametros['formatogrid'])){

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
					
				if($parametros['mes']){
					$mes_actual = $parametros['mes'];
				}else{
					$mes_actual = 0;
				}
				
				$rows = Proyecto::getModel();
				$rows = $rows->where('idEstatusProyecto','=',5);

				if($parametros['clasificacionProyecto']){
					$rows = $rows->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto']);
				}
				
				$rows = $rows->join('evaluacionProyectoMes', function($join) use($mes_actual){
									$join->on('proyectos.id', '=', 'evaluacionProyectoMes.idProyecto')
									->where('evaluacionProyectoMes.mes', '=', $mes_actual)
									->where('evaluacionProyectoMes.anio', '=', date('Y'));
								});
				
				$rows = $rows->whereIn('evaluacionProyectoMes.idEstatus', array(1,2,3));

				$usuario = Sentry::getUser();
				
				if($usuario->filtrarProyectos){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}

				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}

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
					'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto',
					'evaluacionProyectoMes.idEstatus','catalogoEstatusProyectos.descripcion AS estatusAvance',
					'proyectos.fuenteInformacion','proyectos.idResponsable',
					'responsable.username AS validacion','enlace.username AS enlace')
					->leftjoin('sentryUsers AS responsable','responsable.id','=','proyectos.idUsuarioValidacionSeg')
					->leftjoin('sentryUsers AS enlace','enlace.id','=','proyectos.idUsuarioRendCuenta')
					->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')									
					->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','evaluacionProyectoMes.idEstatus')
					->orderBy('id', 'desc')
					->skip(($parametros['pagina']-1)*10)->take(10)
					->get();
				//
				$data = array('resultados'=>$total,'data'=>$rows);
				$respuesta['data'] = $data;

				if($total<=0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
			}
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S01');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
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

		try{
			$recurso = IndicadorFASSA::with('metasDetalle')->find($id);
			if($recurso){
				$ejercicio_actual = date('Y');
				foreach ($recurso->metasDetalle as $index => $meta) {
					if($meta->ejercicio == $ejercicio_actual){
						$responsables = Directorio::responsablesActivos($meta->claveUnidadResponsable)->get();
						$recurso->metasDetalle[$index]['responsables'] = $responsables;
					}
				}
				$recurso['ejercicio_actual'] = intval(date('Y'));
				$data['data'] = $recurso;
			}else{
				$http_status = 404;
				$data = array("data"=>"No se ha podido encontrar el recurso solicitado.",'code'=>'S01');
			}
		}catch(\Exception $e){
			$http_status = 500;
			$data = array("data"=>'Error al obtener los datos','code'=>'S03','ex'=>$e->getMessage());
		}
		return Response::json($data,$http_status);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$respuesta = Validador::validar(Input::all(), $this->reglas);
		
		if($respuesta === true){
			try{
				$respuesta = array();
				$parametros = Input::all();

				$recurso = new IndicadorFASSA;

				$recurso->claveNivel 				= $parametros['nivel-indicador'];
				$recurso->indicador 				= $parametros['indicador'];
				$recurso->claveTipoFormula 			= $parametros['tipo-formula'];
				$recurso->formula 					= $parametros['formula'];
				$recurso->fuenteInformacion 		= $parametros['fuente-informacion'];
				$recurso->idEstatus					= 1;

				$recurso_meta = new IndicadorFASSAMeta;
				$recurso_meta->ejercicio				= date('Y');
				$recurso_meta->claveFrecuencia			= 'A';
				$recurso_meta->claveUnidadResponsable 	= $parametros['unidad-responsable'];
				$recurso_meta->idResponsableInformacion	= $parametros['responsable-informacion'];
				$recurso_meta->idEstatus				= 1;

				$titular = Directorio::titularesActivos(array($parametros['unidad-responsable']))->first();
				$recurso_meta->idLiderPrograma = $titular->id;
				
				$respuesta = DB::transaction(function() use ($recurso,$recurso_meta){
					$respuesta_transaction = array();

					if($recurso->save()){
						$recurso->metas()->save($recurso_meta);
						$respuesta_transaction['http_status'] = 200;
						$recurso['meta'] = $recurso_meta;
						$respuesta_transaction['data'] = array("data"=>$recurso);
					}else{
						$respuesta_transaction['http_status'] = 500;
						$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la informacion','code'=>'S01');
					}
					return $respuesta_transaction;
				});
				
			}catch(\Exception $e){
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
			}
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//en id recibo el mes
		$respuesta = array();
		try{
			//$parametros = Input::all();
			//$valid_result = Validador::validar($parametros, array('mes'=>'required'));

			if(!$id){
				throw new Exception("El mes seleccionado no es valido", 1);
			}

			$mes_activo = Util::obtenerMesActual();
			$mes_actual = $id;

			if($mes_actual == $mes_activo){
				throw new Exception("El mes seleccionado aún está activo", 1);
			}
			//if($valid_result === true){
			$ids_proyectos = EvaluacionProyectoMes::where('mes','=',$mes_actual)
										->whereIn('idEstatus', array(1,2,3))
										->get()->lists('idProyecto');
			//
			$respuesta = DB::transaction(function() use ($ids_proyectos,$mes_actual){
				$respuesta_transaction = array();

				EvaluacionAnalisisFuncional::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->delete();
				EvaluacionComentario::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->delete();
				EvaluacionPlanMejora::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->delete();

				RegistroAvanceBeneficiario::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->delete();
				RegistroAvanceMetas::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->delete();

				ComponenteMetaMes::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->update(array('avance'=>null));
				ActividadMetaMes::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->update(array('avance'=>null));

				EvaluacionProyectoMes::whereIn('idProyecto',$ids_proyectos)->where('mes','=',$mes_actual)->update(array('idEstatus'=>6));

				$respuesta_transaction['http_status'] = 200;
				$respuesta_transaction['data'] = array('data' => $ids_proyectos);
				return $respuesta_transaction;
			});
			/*}else{
				$respuesta['http_status'] = $valid_result['http_status'];
				$respuesta['data'] = $valid_result['data'];
			}*/
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
		}
		
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		$http_status = 200;
		$data = array();

		try{
			$ids = Input::get('rows');
			
			$rows = DB::transaction(function()use($ids){
				IndicadorFASSAMeta::whereIn('idIndicadorFASSA',$ids)->delete();
				$rows = IndicadorFASSA::whereIn('id',$ids)->delete();
			});
			
			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
			}else{
				$http_status = 500;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}
		}catch(\Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');	
		}

		return Response::json($data,$http_status);
	}

}