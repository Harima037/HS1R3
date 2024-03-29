<?php

namespace V1;

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Estrategia, RegistroAvanceEstrategia, EvaluacionEstrategiaTrimestre; 

class RendicionEstrategiaController extends BaseController {
	private $reglasAvance = array(
		'analisis-resultados'	=> 'required',
		'avance-trimestre'		=> 'required'
	);

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
				if($parametros['grid'] == 'rendicion-indicadores'){
					$rows = Estrategia::with('registroAvance.comentarios', "tipoIndicador")->where('id','=',$parametros['idEstrategia'])->get();
					
					$total = count($rows);
				}
			}else{
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre == 3){
					$trimestre_actual = Util::obtenerTrimestre();
				}else{
					$trimestre_actual = 0;
				}
				$anio_captura = Util::obtenerAnioCaptura();

				$rows = Estrategia::getModel();
				$rows = $rows->where('idEstatus','=',5)->where('ejercicio','=',$anio_captura);

				$usuario = Sentry::getUser();

				/*if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('claveUnidadResponsable',$unidades);
				}*/
				
				if($usuario->idDepartamento == 2){
					if($usuario->filtrarProgramas){
						$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
					}
				}else{
					$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
				}
                
               $rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idEstrategia','trimestre',DB::raw('sum(justificacion) AS justificacion'),
									DB::raw('count(idEstrategia) AS registros'))->groupBy('idEstrategia','trimestre');
				},'evaluacionTrimestre'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','=',$trimestre_actual);
				}));

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				$total = $rows->count();
				
				$rows = $rows->select('estrategia.id','estrategia.descripcionIndicador as descripcion', //'programaPresupuestario.descripcion AS programa','programaPresupuestario.clave',
									DB::raw('count(estrategia.trim1) AS trim1'),DB::raw('count(estrategia.trim2) AS trim2'),
									DB::raw('count(estrategia.trim3) AS trim3'),DB::raw('count(estrategia.trim4) AS trim4'))
									//->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','estrategia.claveProgramaPresupuestario')
									//->leftjoin('programaIndicador','programaIndicador.idPrograma','=','programa.id')
									->orderBy('id', 'desc')
									->groupBy('estrategia.id')
									->skip(($parametros['pagina']-1)*10)->take(10)
									->get();
			}

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

			return Response::json($data,$http_status);
		}
		
		$rows = Estrategia::all();

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
			if($parametros['mostrar'] == 'datos-estrategia-presupuestario'){
				$recurso = Estrategia::join('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','estrategia.claveUnidadResponsable')
					//leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','estrategia.claveProgramaPresupuestario')
					->select('estrategia.*','unidadResponsable.descripcion AS unidadResponsable')//,'programaPresupuestario.descripcion AS programaPresupuestario'
					->find($id);
			}elseif($parametros['mostrar'] == 'datos-estrategia-avance'){
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre == 3){
					$trimestre_actual = Util::obtenerTrimestre();
				}else{
					$trimestre_actual = 0;
				}
				$recurso = Estrategia::with(array('registroAvance',
					'evaluacionTrimestre'=>function($query) use ($trimestre_actual){
						$query->where('trimestre','=',$trimestre_actual);
                    }))
                    ->join('catalogoTiposIndicadores AS TipoIndicador','TipoIndicador.id','=','estrategia.idTipoIndicador')
					//->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','estrategia.claveProgramaPresupuestario')
					->join('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','estrategia.claveUnidadResponsable')
					->select('estrategia.*','unidadResponsable.descripcion AS unidadResponsable', 'TipoIndicador.descripcion as TipoIndicadorMeta')//'programaPresupuestario.descripcion AS programaPresupuestario',
					->find($id);
			}elseif($parametros['mostrar'] == 'datos-metas-avance'){
				$trimestre_actual = Util::obtenerTrimestre();
				$recurso = Estrategia::with(array('registroAvance'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','<=',$trimestre_actual);
				},'registroAvance.comentarios'))->join('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','estrategia.idUnidadMedida')
				->select('estrategia.*','unidadMedida.descripcion AS unidadMedida')
				->find($id);
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

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();

		try{
			$mes_del_trimestre = Util::obtenerMesTrimestre();
			if($mes_del_trimestre == 3){
				$trimestre_actual = Util::obtenerTrimestre();
			}else{
				$trimestre_actual = 0;
			}
			
			$estatus_avance = EvaluacionEstrategiaTrimestre::where('idEstrategia','=',$parametros['id-estrategia'])
															->where('trimestre','=',$trimestre_actual)
															->first();
			if($estatus_avance){
				if($estatus_avance->idEstatus != 1 && $estatus_avance->idEstatus != 3){
					switch ($estatus_avance->idEstatus) {
						case 2:
							$respuesta['data']['data'] = 'La estrategia institucional se encuentra en proceso de revisión, por tanto no es posible editarlo';
							break;
						case 4:
							$respuesta['data']['data'] = 'La estrategia institucional se encuentra registrado, por tanto no es posible editarlo';
							break;
						case 5:
							$respuesta['data']['data'] = 'La estrategia institucional ya fue firmado, por tanto no es posible editarlo';
							break;
						default:
							$respuesta['data']['data'] = 'El estatus de La estrategia institucional es desconocido';
							break;
					}
					throw new Exception("La estrategia institucional se encuentra en un estatus en el que no esta disponible para edición", 1);
				}
			}
			
			if($parametros['guardar'] == 'avance-metas'){
				$validacion = Validador::validar(Input::all(), $this->reglasAvance);

				if($validacion === TRUE){
					$recurso = new RegistroAvanceEstrategia;
					$recurso->idEstrategia = $parametros['id-estrategia'];
					$recurso->trimestre = $trimestre_actual;
					$recurso->analisisResultados = $parametros['analisis-resultados'];
					$recurso->avance = $parametros['avance-trimestre'];

					$indicador = Estrategia::with(array('registroAvance'=>function($query) use ($trimestre_actual){
						$query->where('trimestre','<',$trimestre_actual);
					}))->find($parametros['id-indicador']);

					//$avance_acumulado = $indicador->registroAvance->sum('avance');
					$avance_acumulado = $parametros['avance-trimestre'];

					$meta_acumulada = 0;
					$indicador_array = $indicador->toArray();
					$meta_acumulada += $indicador_array['trim'.$trimestre_actual];
					/*for ($i = 1; $i <= $trimestre_actual ; $i++) { 
						$meta_acumulada += $indicador_array['trim'.$i];
					}*/

					if($avance_acumulado > 0 && $meta_acumulada == 0){
						$porcentaje_avance = 1;
					}elseif($meta_acumulada > 0){
						$porcentaje_avance = (( $avance_acumulado  / $meta_acumulada ) * 100);
					}else{
						$porcentaje_avance = 100;
					}
					
					if($porcentaje_avance < 90 || $porcentaje_avance > 110){
						$necesita_justificar = true;
					}else{
						$necesita_justificar = false;
					}

					if($necesita_justificar){
						if(trim($parametros['justificacion-acumulada']) == ''){
							throw new Exception('{"field":"justificacion-acumulada","error":"Este campo es requerido."}', 1);
							//$faltan_campos[] = json_encode(array('field'=>'justificacion-acumulada','error'=>'Este campo es requerido.'));
						}else{
							$recurso->justificacionAcumulada = $parametros['justificacion-acumulada'];
							$recurso->justificacion = 1;
						}
					}else{
						$recurso->justificacionAcumulada = 'El avance se encuentra dentro de los parametros establecidos';
						$recurso->justificacion = 0;
					}

					DB::transaction(function() use ($recurso,$indicador,$respuesta,$estatus_avance){
						if($indicador->registroAvance()->save($recurso)){
							$respuesta['data']['data'] = $recurso;
							if(!$estatus_avance){
								$estatus_avance = new EvaluacionEstrategiaTrimestre;
								$estatus_avance->idEstrategia = $indicador->id;
								$estatus_avance->trimestre = $recurso->trimestre;
								$estatus_avance->anio = date('Y');
								$estatus_avance->idEstatus = 1;
								$estatus_avance->save();
							}
						}else{
							$respuesta['data']['data'] = 'Error al intentar guardar los datos';
							throw new Exception('Ocurrio un error al intentar guardar los datos del avance.', 1);
						}
					});
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
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

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id){
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();

		try{
			$mes_del_trimestre = Util::obtenerMesTrimestre();
			if($mes_del_trimestre == 3){
				$trimestre_actual = Util::obtenerTrimestre();
			}else{
				$trimestre_actual = 0;
			}

			if($parametros['guardar'] == 'validar-avance'){
				$id_estrategia = $id;
			}else{
				$id_estrategia = $parametros['id-estrategia'];
			}

			$estatus_avance = EvaluacionEstrategiaTrimestre::where('idEstrategia','=',$id_estrategia)
															->where('trimestre','=',$trimestre_actual)
															->first();
			//
			if($estatus_avance){
				if($estatus_avance->idEstatus != 1 && $estatus_avance->idEstatus != 3){
					switch ($estatus_avance->idEstatus) {
						case 2:
							$respuesta['data']['data'] = 'El avance se encuentra en proceso de revisión, por tanto no es posible editarlo';
							break;
						case 4:
							$respuesta['data']['data'] = 'El avance se encuentra registrado, por tanto no es posible editarlo';
							break;
						case 5:
							$respuesta['data']['data'] = 'El avance ya fue firmado, por tanto no es posible editarlo';
							break;
						default:
							$respuesta['data']['data'] = 'El estatus del avance es desconocido';
							break;
					}
					throw new Exception("El avance se encuentra en un estatus en el que no esta disponible para edición", 1);
				}
			}

			if($parametros['guardar'] == 'avance-metas'){
				$validacion = Validador::validar(Input::all(), $this->reglasAvance);

				if($validacion === TRUE){
					$recurso = RegistroAvanceEstrategia::find($id);
					$recurso->trimestre = $trimestre_actual;
					$recurso->analisisResultados = $parametros['analisis-resultados'];
					$recurso->avance = $parametros['avance-trimestre'];

					$indicador = Estrategia::with(array('registroAvance'=>function($query) use ($trimestre_actual){
						$query->where('trimestre','<',$trimestre_actual);
					}))->find($parametros['id-indicador']);

					//$avance_acumulado = $indicador->registroAvance->sum('avance');
					$avance_acumulado = $parametros['avance-trimestre'];

					$meta_acumulada = 0;
					$indicador_array = $indicador->toArray();
					$meta_acumulada += $indicador_array['trim'.$trimestre_actual];
					/*for ($i = 1; $i <= $trimestre_actual ; $i++) { 
						$meta_acumulada += $indicador_array['trim'.$i];
					}*/

					if($avance_acumulado > 0 && $meta_acumulada == 0){
						$porcentaje_avance = 1;
					}elseif($meta_acumulada > 0){
						$porcentaje_avance = (( $avance_acumulado  / $meta_acumulada ) * 100);
					}else{
						$porcentaje_avance = 100;
					}
					
					if($porcentaje_avance < 90 || $porcentaje_avance > 110){
						$necesita_justificar = true;
					}else{
						$necesita_justificar = false;
					}

					if($necesita_justificar){
						if(trim($parametros['justificacion-acumulada']) == ''){
							throw new Exception('{"field":"justificacion-acumulada","error":"Este campo es requerido."}', 1);
							//$faltan_campos[] = json_encode(array('field'=>'justificacion-acumulada','error'=>'Este campo es requerido.'));
						}else{
							$recurso->justificacionAcumulada = $parametros['justificacion-acumulada'];
							$recurso->justificacion = 1;
						}
					}else{
						$recurso->justificacionAcumulada = 'El avance se encuentra dentro de los parametros establecidos';
						$recurso->justificacion = 0;
					}

					if($indicador->registroAvance()->save($recurso)){
						$respuesta['data']['data'] = $recurso;
					}else{
						$respuesta['data']['data'] = 'Error al intentar guardar los datos';
						throw new Exception('Ocurrio un error al intentar guardar los datos del avance.', 1);
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}elseif($parametros['guardar'] == 'validar-avance'){
				$recurso = Estrategia::with(array('registroAvance'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','=',$trimestre_actual);
				}))->find($id);

				/*$elementos = count($recurso->indicadores);
				$registro_elementos = count($recurso->registroAvance);

				if($elementos != $registro_elementos){
					$respuesta['data']['data']='No se ha podido enviar el proyecto a revisión ya que hacen falta avances por capturar';
					throw new Exception('Se han capturado el avance en '.$registro_elementos.' de '.$elementos.' elementos.', 1);
				}*/

				$estatus_avance->idEstatus = 2;

				if($estatus_avance->save()){
					$respuesta['data']['data'] = 'La estrategia institucional fue enviado a Revisión';
				}else{
					$respuesta['data']['data'] = 'La estrategia institucional no ha sido enviado a Revisión';
				}
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