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

use SSA\Utilerias\Validador,SSA\Utilerias\Util;
use Illuminate\Database\QueryException, \Exception;
use BaseController, Input, Response, DB, Sentry, IndicadorFASSA, RegistroAvanceIndicadorFASSA, IndicadorFASSAMeta,Directorio, IndicadorFASSAMetaTrimestre,SysConfiguracionVariable;

class RendicionFassaController extends \BaseController {

	private $reglas = array(
			'numerador'					=> 'sometimes|required|min:0',
			'denominador'				=> 'sometimes|required|min:0',
			'avance-numerador'			=> 'sometimes|required|min:0',
			'avance-denominador'		=> 'sometimes|required|min:0'
		);

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
				
				//$ejercicio_actual = date('Y');
				$ejercicio_actual = Util::obtenerAnioCaptura();
				//$rows = IndicadorFASSA::getModel();
				$rows = IndicadorFASSAMeta::getModel();
				$rows = $rows->with('registroAvance');
				$rows = $rows->indicadoresEjercicio()->where('ejercicio','=',$ejercicio_actual);
				
				$usuario = Sentry::getUser();
				if($usuario->idDepartamento == 2){
					if($usuario->filtrarIndicadores){
						$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
					}
				}else{
					$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
				}

				if(isset($parametros['buscar'])){
					if($parametros['buscar']){
						$rows = $rows->where(function($query) use ($parametros){
									$query->where('indicador','like','%'.$parametros['buscar'].'%')
										->orWhere('fuenteInformacion','like','%'.$parametros['buscar'].'%');
								});
					}
					$total = $rows->count();
				}else{
					$total = $rows->count();
				}

				$rows = $rows->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
				//
				$variables = SysConfiguracionVariable::obtenerVariables(array('captura-cierre-fassa'))->lists('valor','variable');
				if($variables['captura-cierre-fassa']){
					$cierre_fassa = intval($variables['captura-cierre-fassa']); //1 Abierto | 2 Cerrado
				}else{
					$cierre_fassa = null;
				}
				
				$data = array('resultados' => $total, 'data' => $rows, 'mes_actual' => Util::obtenerMesActual(), 'cierre_fassa' => $cierre_fassa);
				$respuesta['data'] = $data;

				if($total<=0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
			}else{
				$rows = IndicadorFASSA::all();
				if(count($rows) == 0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No hay datos",'code'=>'W00');
				}else{
					$respuesta['data'] = array("data"=>$rows);
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
			$recurso = IndicadorFASSAMeta::indicadorMetaDetalle()->with('metasTrimestre')->find($id);

			if($recurso){
				$variables = SysConfiguracionVariable::obtenerVariables(array('captura-cierre-fassa'))->lists('valor','variable');
				if($variables['captura-cierre-fassa']){
					$cierre_fassa = intval($variables['captura-cierre-fassa']); //1 Abierto | 2 Cerrado
				}else{
					$cierre_fassa = null;
				}

				if($cierre_fassa){
					$mes_actual = 12;
				}else{
					$mes_actual = intval(Util::obtenerMesActual());
				}
				
				$recurso->load(array('registroAvance'=>function($query)use($mes_actual){
					return $query->where('mes','<=',$mes_actual);
				},'comentario'));

				$recurso['mes_actual'] = $mes_actual;
				$recurso['cierre_fassa'] = $cierre_fassa;
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
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		$respuesta = array('http_status'=>200, 'data'=>array('data'=>''));
		try{
			$valid_result = Validador::validar(Input::all(), $this->reglas);
			if($valid_result === true){
				$parametros = Input::all();
				$validar = array('metas'=>false,'avance'=>false);
				$recurso = IndicadorFASSAMeta::with('indicador','metasTrimestre')->find($id);
				$recurso_avance = NULL;
				$metas_trimestre = [];
				$no_editables = 0;

				$variables = SysConfiguracionVariable::obtenerVariables(array('captura-cierre-fassa'))->lists('valor','variable');
				if($variables['captura-cierre-fassa']){
					$cierre_fassa = intval($variables['captura-cierre-fassa']); //1 Abierto | 2 Cerrado
				}else{
					$cierre_fassa = null;
				}

				$tipo_formula = $recurso->indicador->claveTipoFormula;
				
				if(isset($parametros['trimestre'])){
					if($recurso->idEstatus == 1 || $recurso->idEstatus == 3){
						$metas = $recurso->metasTrimestre->lists('id','trimestre');
						//$metas = $recurso->metasTrimestre;
						$total_numerador = 0;
						$total_denominador = 0;
						foreach ($parametros['trimestre'] as $trim => $meta) {
							if(isset($metas[$trim])){
								$meta_trimestre = $recurso->metasTrimestre()->find($metas[$trim]);
							}else{
								$meta_trimestre = new IndicadorFASSAMetaTrimestre;
							}

							$meta_trimestre->trimestre 		= $trim;
							$meta_trimestre->numerador 		= floatval($meta['numerador']);
							$meta_trimestre->denominador 	= floatval($meta['denominador']);
							$meta_trimestre->porcentaje 	= 0;

							$total_numerador += $meta_trimestre->numerador;
							$total_denominador += $meta_trimestre->denominador;

							if($meta_trimestre->numerador > 0){
								if(!$meta_trimestre->denominador){
									$meta_trimestre->denominador = 1;
								}

								if($tipo_formula == 'T'){
									$meta_trimestre->porcentaje = floatval(($meta_trimestre->numerador * $recurso->indicador->tasa)/$meta_trimestre->denominador);
								}else{
									$meta_trimestre->porcentaje = floatval(($meta_trimestre->numerador * 100)/$meta_trimestre->denominador);
								}
							}elseif (!$meta_trimestre->id) {
								$meta_trimestre = NULL;
							}
							
							if($meta_trimestre){
								$metas_trimestre[] = $meta_trimestre;
							}
						}

						$recurso->numerador 	= $meta_trimestre->numerador;
						$recurso->denominador 	= $meta_trimestre->denominador;
						$recurso->porcentaje 	= $meta_trimestre->porcentaje;

						if(isset($parametros['validar'])){
							$validar['metas'] = true;
						}
					}
				}elseif(isset($parametros['avance-numerador']) && !$cierre_fassa){
					$mes_actual = Util::obtenerMesActual();
					$metas = $recurso->metasTrimestre->lists('porcentaje','trimestre');
					$trimestre = ceil($mes_actual/3);

					if($recurso->claveFrecuencia == 'A'){
						if($mes_actual != 12){
							throw new Exception("El registro avances no esta actualmente disponible para este indicador.", 1);
						}
					}elseif($recurso->claveFrecuencia == 'S'){
						if($mes_actual != 6 && $mes_actual != 12){
							throw new Exception("El registro avances no esta actualmente disponible para este indicador.", 1);
						}
					}else{
						if(($mes_actual%3) != 0){
							throw new Exception("El registro avances no esta actualmente disponible para este indicador.", 1);
						}
					}

					if(!isset($metas[$trimestre])){
						throw new Exception("No hay metas registradas para este trimestre.", 1);
					}

					if($parametros['id-avance']){
						$recurso_avance = RegistroAvanceIndicadorFASSA::find($parametros['id-avance']);
					}else{
						$recurso_avance = new RegistroAvanceIndicadorFASSA;
						$recurso_avance->idIndicadorFASSAMeta = $id;
						$recurso_avance->idEstatus = 1;
						$recurso_avance->mes = $mes_actual;
					}

					if($recurso_avance->idEstatus == 1 || $recurso_avance->idEstatus == 3){
						$recurso_avance->numerador 			= $parametros['avance-numerador'];
						$recurso_avance->denominador 		= $parametros['avance-denominador'];

						$numerador 		= $parametros['avance-numerador'];
						$denominador 	= $parametros['avance-denominador'];

						if($numerador < 0){
							throw new Exception('{"field":"avance-numerador","error":"El valor no puede ser negativo."}', 1);
						}

						if($denominador < 0){
							throw new Exception('{"field":"avance-denominador","error":"El valor no puede ser negativo."}', 1);
						}

						if($tipo_formula == 'T'){
							$porcentaje = round(floatval(($numerador * $recurso->indicador->tasa)/$denominador),2);
						}else{
							$porcentaje = round(floatval(($numerador * 100)/$denominador),2);
						}
						$recurso_avance->porcentaje = $porcentaje;

						$porcentaje_total = ($recurso_avance->porcentaje / floatval($metas[$trimestre]))*100;

						if($porcentaje_total > 100 || $porcentaje_total < 100){
							$recurso_avance->justificacion = 1;
						}else{
							$recurso_avance->justificacion = 0;
						}

						if($recurso_avance->justificacion){
							if(isset($parametros['justificacion'])){
								if($parametros['justificacion']){
									$recurso_avance->justificacionAcumulada = $parametros['justificacion'];
								}else{
									throw new Exception('{"field":"justificacion","error":"Este campo es requerido."}', 1);
								}
							}else{
								throw new Exception('{"field":"justificacion","error":"Este campo es requerido."}', 1);
							}
						}else{
							$recurso_avance->justificacionAcumulada = 'El avance se encuentra dentro de lo programado';
						}

						if(isset($parametros['validar'])){
							$validar['avance'] = true;
						}
					}
				}else{
					if($cierre_fassa == 1){
						if(!$recurso->idEstatusCierre){
							$recurso->idEstatusCierre = 1;
						}
						if($recurso->idEstatusCierre == 1 || $recurso->idEstatusCierre == 3){
							$recurso->numeradorCierre 			= $parametros['avance-numerador'];
							$recurso->denominadorCierre 		= $parametros['avance-denominador'];

							$numerador 		= $parametros['avance-numerador'];
							$denominador 	= $parametros['avance-denominador'];

							if($numerador < 0){
								throw new Exception('{"field":"avance-numerador","error":"El valor no puede ser negativo."}', 1);
							}

							if($denominador < 0){
								throw new Exception('{"field":"avance-denominador","error":"El valor no puede ser negativo."}', 1);
							}

							if($tipo_formula == 'T'){
								$porcentaje = round(floatval(($numerador * $recurso->indicador->tasa)/$denominador),2);
							}else{
								$porcentaje = round(floatval(($numerador * 100)/$denominador),2);
							}
							$recurso->porcentajeCierre = $porcentaje;

							$porcentaje_total = ($recurso->porcentajeCierre / floatval($recurso->porcentaje))*100;

							if($porcentaje_total > 100 || $porcentaje_total < 100){
								$recurso->justificacionCierre = 1;
							}else{
								$recurso->justificacionCierre = 0;
							}

							if($recurso->justificacionCierre){
								if(isset($parametros['justificacion'])){
									if($parametros['justificacion']){
										$recurso->justificacionAcumuladaCierre = $parametros['justificacion'];
									}else{
										throw new Exception('{"field":"justificacion","error":"Este campo es requerido."}', 1);
									}
								}else{
									throw new Exception('{"field":"justificacion","error":"Este campo es requerido."}', 1);
								}
							}else{
								$recurso->justificacionAcumuladaCierre = 'El avance se encuentra dentro de lo programado';
							}

							if(isset($parametros['validar'])){
								$validar['avance'] = true;
							}
						}
					}else{
						throw new Exception("Ninguno de los elementos está disponble para edición.", 1);
					}
				}
				
				$no_editables += ($recurso->idEstatus == 2 || $recurso->idEstatus == 4 || $recurso->idEstatus == 5)?1:0;

				if($recurso_avance){
					$no_editables += ($recurso_avance->idEstatus == 2 || $recurso_avance->idEstatus == 4 || $recurso_avance->idEstatus == 5)?1:0;
				}else{
					$no_editables += 1;
				}

				if($no_editables == 2 && !$cierre_fassa){
					$respuesta['data']['data'] = 'Ninguno de los elementos está disponble para edición.';
					throw new Exception("Ninguno de los elementos está disponble para edición.", 1);
				}

				$respuesta = DB::transaction(function() use ($recurso, $recurso_avance, $metas_trimestre, $validar, $cierre_fassa){
					$respuesta_transaction = array();

					if($recurso->idEstatus == 1 || $recurso->idEstatus == 3){
						if($validar['metas']){
							$recurso->idEstatus = 2;
						}

						if($recurso->save()){
							$recurso->metasTrimestre()->saveMany($metas_trimestre);
						}else{
							$respuesta_transaction['http_status'] = 500;
							$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la información de la meta','code'=>'S01');
						}
					}

					if($recurso_avance){
						if($recurso->idEstatus == 4 || $recurso->idEstatus == 5){
							if($recurso_avance->idEstatus == 1 || $recurso_avance->idEstatus == 3){
								if($validar['avance']){
									$recurso_avance->idEstatus = 2;
								}

								if(!$recurso_avance->save()){
									$respuesta_transaction['http_status'] = 500;
									$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la información del avance','code'=>'S01');
								}
							}
						}
					}

					if($cierre_fassa == 1){
						if($recurso->idEstatusCierre == 1 || $recurso->idEstatusCierre == 3){
							if($validar['avance']){
								$recurso->idEstatusCierre = 2;
							}
							if(!$recurso->save()){
								$respuesta_transaction['http_status'] = 500;
								$respuesta_transaction['data'] = array("data"=>'Ocurrio un error al intentar guardar la información del avance','code'=>'S01');
							}
						}else{
							$respuesta_transaction['http_status'] = 500;
							$respuesta_transaction['data'] = array("data"=>'No se puede editar el avance','code'=>'S01');
						}
					}

					if(!isset($respuesta_transaction['http_status'])){
						$respuesta_transaction['http_status'] = 200;
						$respuesta_transaction['data'] = array("data"=>$recurso);
					}

					return $respuesta_transaction;
				});
				$respuesta['data']['debug'] = $metas_trimestre;
			}else{
				$respuesta['http_status'] = $valid_result['http_status'];
				$respuesta['data'] = $valid_result['data'];
			}
		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos';
			}
			if(strpos($ex->getMessage(), '{"field":') !== FALSE){
				$respuesta['data']['code'] = 'U00';
				$respuesta['data']['data'] = $ex->getMessage();
			}else{
				$respuesta['data']['ex'] = $ex->getMessage();
			}
			$respuesta['data']['line'] = $ex->getLine();
			if(!isset($respuesta['data']['code'])){
				$respuesta['data']['code'] = 'S03';
			}
		}
		
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

}