<?php

namespace V1;

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Programa, ProgramaIndicador, RegistroAvancePrograma, EvaluacionProgramaTrimestre; 

class RendicionProgramaController extends BaseController {
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
					$rows = ProgramaIndicador::with('registroAvance.comentarios')->where('idPrograma','=',$parametros['idPrograma'])->get();
					//$rows->componentes->load('registroAvance');
					$total = count($rows);
				}
			}else{
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre == 3){
					$trimestre_actual = Util::obtenerTrimestre();
				}else{
					$trimestre_actual = 0;
				}

				$rows = Programa::getModel();
				$rows = $rows->where('idEstatus','=',5);

				$usuario = Sentry::getUser();
				if($usuario->claveUnidad){
					$rows = $rows->where('claveUnidadResponsable','=',$usuario->claveUnidad);
				}
				
				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idPrograma','trimestre',DB::raw('sum(justificacion) AS justificacion'),
									DB::raw('count(idIndicador) AS registros'))->groupBy('idPrograma','trimestre');
				},'evaluacionTrimestre'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','=',$trimestre_actual);
				}));

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				$total = $rows->count();
				
				$rows = $rows->select('programa.id','programaPresupuestario.descripcion AS programa','programaPresupuestario.clave')
									->join('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','programa.claveProgramaPresupuestario')
									->orderBy('id', 'desc')
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
		
		$rows = Programa::all();

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
			if($parametros['mostrar'] == 'datos-programa-avance'){
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre == 3){
					$trimestre_actual = Util::obtenerTrimestre();
				}else{
					$trimestre_actual = 0;
				}
				$recurso = Programa::with(array('indicadores.registroAvance',
					'evaluacionTrimestre'=>function($query) use ($trimestre_actual){
						$query->where('trimestre','=',$trimestre_actual);
					}))
					->join('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','programa.claveProgramaPresupuestario')
					->join('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','programa.claveUnidadResponsable')
					->select('programa.*','programaPresupuestario.descripcion AS programaPresupuestario','unidadResponsable.descripcion AS unidadResponsable')
					->find($id);
			}elseif($parametros['mostrar'] == 'datos-metas-avance'){
				$trimestre_actual = Util::obtenerTrimestre();
				$recurso = ProgramaIndicador::with(array('registroAvance'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','<=',$trimestre_actual);
				},'registroAvance.comentarios'))->join('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','programaIndicador.idUnidadMedida')
				->select('programaIndicador.*','unidadMedida.descripcion AS unidadMedida')
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
			
			$estatus_avance = EvaluacionProgramaTrimestre::where('idPrograma','=',$parametros['id-programa'])
															->where('trimestre','=',$trimestre_actual)
															->first();
			if($estatus_avance){
				if($estatus_avance->idEstatus != 1 && $estatus_avance->idEstatus != 3){
					switch ($estatus_avance->idEstatus) {
						case 2:
							$respuesta['data']['data'] = 'El proyecto se encuentra en proceso de revisión, por tanto no es posible editarlo';
							break;
						case 4:
							$respuesta['data']['data'] = 'El proyecto se encuentra registrado, por tanto no es posible editarlo';
							break;
						case 5:
							$respuesta['data']['data'] = 'El proyecto ya fue firmado, por tanto no es posible editarlo';
							break;
						default:
							$respuesta['data']['data'] = 'El estatus del proyecto es desconocido';
							break;
					}
					throw new Exception("El proyecto se encuentra en un estatus en el que no esta disponible para edición", 1);
				}
			}
			
			if($parametros['guardar'] == 'avance-metas'){
				$validacion = Validador::validar(Input::all(), $this->reglasAvance);

				if($validacion === TRUE){
					$recurso = new RegistroAvancePrograma;
					$recurso->idPrograma = $parametros['id-programa'];
					$recurso->trimestre = $trimestre_actual;
					$recurso->analisisResultados = $parametros['analisis-resultados'];
					$recurso->avance = $parametros['avance-trimestre'];

					$indicador = ProgramaIndicador::with(array('registroAvance'=>function($query) use ($trimestre_actual){
						$query->where('trimestre','<',$trimestre_actual);
					}))->find($parametros['id-indicador']);

					$avance_acumulado = $indicador->registroAvance->sum('avance');
					$avance_acumulado += $parametros['avance-trimestre'];

					$meta_acumulada = 0;
					$indicador_array = $indicador->toArray();
					for ($i = 1; $i <= $trimestre_actual ; $i++) { 
						$meta_acumulada += $indicador_array['trim'.$i];
					}

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
								$estatus_avance = new EvaluacionProgramaTrimestre;
								$estatus_avance->idPrograma = $indicador->idPrograma;
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
				$id_programa = $id;
			}else{
				$id_programa = $parametros['id-programa'];
			}

			$estatus_avance = EvaluacionProgramaTrimestre::where('idPrograma','=',$id_programa)
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
					$recurso = RegistroAvancePrograma::find($id);
					$recurso->trimestre = $trimestre_actual;
					$recurso->analisisResultados = $parametros['analisis-resultados'];
					$recurso->avance = $parametros['avance-trimestre'];

					$indicador = ProgramaIndicador::with(array('registroAvance'=>function($query) use ($trimestre_actual){
						$query->where('trimestre','<',$trimestre_actual);
					}))->find($parametros['id-indicador']);

					$avance_acumulado = $indicador->registroAvance->sum('avance');
					$avance_acumulado += $parametros['avance-trimestre'];

					$meta_acumulada = 0;
					$indicador_array = $indicador->toArray();
					for ($i = 1; $i <= $trimestre_actual ; $i++) { 
						$meta_acumulada += $indicador_array['trim'.$i];
					}

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
				$recurso = Programa::with(array('indicadores','registroAvance'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','=',$trimestre_actual);
				}))->find($id);

				$elementos = count($recurso->indicadores);
				$registro_elementos = count($recurso->registroAvance);

				if($elementos != $registro_elementos){
					$respuesta['data']['data']='No se ha podido enviar el proyecto a revisión ya que hacen falta avances por capturar';
					throw new Exception('Se han capturado el avance en '.$registro_elementos.' de '.$elementos.' elementos.', 1);
				}

				$estatus_avance->idEstatus = 2;

				if($estatus_avance->save()){
					$respuesta['data']['data'] = 'El Proyecto fue enviado a Revisión';
				}else{
					$respuesta['data']['data'] = 'El Proyecto no ha sido enviado a Revisión';
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