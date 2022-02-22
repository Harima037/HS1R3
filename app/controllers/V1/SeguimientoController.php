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
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime,Mail,File;
use Proyecto,Componente,Actividad,Beneficiario,RegistroAvanceMetas,ComponenteMetaMes,ActividadMetaMes,RegistroAvanceBeneficiario,EvaluacionAnalisisFuncional,EvaluacionProyectoMes,
	EvaluacionPlanMejora,ComponenteDesglose,DesgloseMetasMes,ActividadDesgloseMetasMes,Directorio,SysConfiguracionVariable,BitacoraValidacionSeguimiento,ActividadDesglose,Accion;

class SeguimientoController extends BaseController {
	private $reglasBeneficiarios = array(
		'id-beneficiario'			=> 'required',
		'altaf' 					=> 'sometimes|required|integer|min:0',
		'altam' 					=> 'sometimes|required|integer|min:0',
		'bajaf' 					=> 'sometimes|required|integer|min:0',
		'bajam' 					=> 'sometimes|required|integer|min:0',
		'indigenaf'					=> 'sometimes|required|integer|min:0',
		'indigenam'					=> 'sometimes|required|integer|min:0',
		'inmigrantef' 				=> 'sometimes|required|integer|min:0',
		'inmigrantem' 				=> 'sometimes|required|integer|min:0',
		'mediaf' 					=> 'sometimes|required|integer|min:0',
		'mediam' 					=> 'sometimes|required|integer|min:0',
		'mestizaf' 					=> 'sometimes|required|integer|min:0',
		'mestizam'					=> 'sometimes|required|integer|min:0',
		'muyaltaf' 					=> 'sometimes|required|integer|min:0',
		'muyaltam' 					=> 'sometimes|required|integer|min:0',
		'muybajaf' 					=> 'sometimes|required|integer|min:0',
		'muybajam' 					=> 'sometimes|required|integer|min:0',
		'otrosf' 					=> 'sometimes|required|integer|min:0',
		'otrosm' 					=> 'sometimes|required|integer|min:0',
		'ruralf' 					=> 'sometimes|required|integer|min:0',
		'ruralm' 					=> 'sometimes|required|integer|min:0',
		'urbanaf' 					=> 'sometimes|required|integer|min:0',
		'urbanam' 					=> 'sometimes|required|integer|min:0'
	);

	private $reglasAnalisisFuncional = array(
		//'finalidad'		=> 'required',
		'analisis-resultado'		=> 'required',
		'analisis-beneficiarios'	=> 'required',
		'justificacion-global'		=> 'required'
	);

	private $reglasPlanMejora = array(
		'accion-mejora'					=> 'required',
		'grupo-trabajo'					=> 'required',
		'documentacion-comprobatoria'	=> 'required',
		'fecha-inicio'					=> 'required|date',
		'fecha-termino'					=> 'required|date',
		'fecha-notificacion'			=> 'required|date'
	);

	private $reglasFuenteInformacion = array(
		'fuente-informacion'	=> 'required',
		'responsable'			=> 'required'
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
				if($parametros['grid'] == 'rendicion-acciones'){
					$rows = Proyecto::with(array('componentes.comentarios','componentes.registroAvance',
												'componentes.actividades.comentarios','componentes.actividades.registroAvance',
												'componentes.observaciones'=>function($query){
													$query->groupBy('idElemento')->select(DB::raw('count(idElemento) AS total'),'idElemento','nivel','id');
												},'componentes.actividades.observaciones'=>function($query){
													$query->groupBy('idElemento')->select(DB::raw('count(idElemento) AS total'),'idElemento','nivel','id');
												}))
									->find($parametros['idProyecto']);
					//$rows->componentes->load('registroAvance');
					$rows['responsables'] = Directorio::responsablesActivos($rows->unidadResponsable)->get();
					
					$total = count($rows);
				}elseif($parametros['grid'] == 'rendicion-beneficiarios'){
					$rows = Beneficiario::with(array('registroAvance'=>function($query){
						$query->select('id','idProyectoBeneficiario','idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))
								->groupBy('idTipoBeneficiario','sexo');
					},'tipoBeneficiario','tipoCaptura','comentarios'))->where('idProyecto','=',$parametros['idProyecto'])->get();
					$total = count($rows);
				}
			}else{
				$mes_actual = Util::obtenerMesActual();
				$anio_captura = Util::obtenerAnioCaptura();

				$rows = Proyecto::getModel();
				$rows = $rows->where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto'])
							->where('ejercicio','=',$anio_captura);
				
				$usuario = Sentry::getUser();
				
				if($usuario->idDepartamento == 2){
					if($usuario->filtrarProyectos){
						$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
					}
				}else{
					$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
				}
				
				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}

				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'),DB::raw('count(idNivel) as registros'))->groupBy('idProyecto','mes');
				},'evaluacionMeses'=>function($query) use ($mes_actual){
					if($mes_actual == 0){
						$mes_actual = date('n')-1;
						if($mes_actual==0){$mes_actual = 12;} //En caso de estar en enero pasar a diciembre del año anterior
						$query->where('evaluacionProyectoMes.mes','<=',$mes_actual)->where('idEstatus','=',4);
					}else{
						$query->where('evaluacionProyectoMes.mes','<=',$mes_actual);
					}
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

				//$avances_anteriores = EvaluacionProyectoMes::where('idProyecto')

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					//$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
					$rows = $rows->where(function($query)use($parametros){
						$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
							->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
					});
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}
				
				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto','proyectos.idEstatusProyecto',
					'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl',DB::raw('count(observaciones.id) AS observaciones'))
					->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
					->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
					->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
					->leftjoin('observacionRendicionCuenta AS observaciones',function($join){
						$join->on('observaciones.idProyecto','=','proyectos.id')
							->whereNull('observaciones.borradoAl');
					})
					->groupBy('proyectos.id')
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
							$mes_actual = date('n')-1;
							if($mes_actual==0){$mes_actual = 12;}
							$query->where('mes','=',$mes_actual)->where('idEstatus','=',4);
						}else{
							$query->where('mes','=',$mes_actual);
						}
					}))->find($id);
			}elseif ($parametros['mostrar'] == 'datos-municipio-avance') {
				//$id = idComponente y $parametros['clave-municipio'] y $parametros['nivel'] = 'componente'
				$mes_actual = Util::obtenerMesActual();
				if($parametros['nivel'] == 'componente'){
					$recurso = ComponenteDesglose::listarDatos()->where('claveMunicipio','=',$parametros['clave-municipio'])
													->where('idComponente','=',$id);
				}elseif($parametros['nivel'] == 'actividad'){
					$recurso = ActividadDesglose::listarDatos()->where('claveMunicipio','=',$parametros['clave-municipio'])
													->where('idActividad','=',$id);
				}
				$recurso = $recurso->with(array('metasMes'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMesAcumuladas'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				}))->get();
			}elseif($parametros['mostrar'] == 'datos-metas-avance'){
				$mes_actual = Util::obtenerMesActual();
				if($parametros['nivel'] == 'componente'){
					$recurso = Componente::getModel();
				}else{
					$recurso = Actividad::getModel();
				}
				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = $recurso->with(array(
				'metasMesJurisdiccion'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMes' => function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'planMejora'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'observaciones'=>function($query){
					$query->orderBy('modificadoAl','desc');
				},'unidadMedida','comentarios'))->find($id);

				$recurso->load('desgloseMunicipios');

				$trimestre = ceil($mes_actual/3);
				$metas_mes = [];
				$metas_mes_trimestre = [
					'meta' => 0,
					'avance' => 0
				];
				foreach ($recurso->metasMes as $meta_mes) {
					if($meta_mes->mes == $mes_actual){
						$metas_mes[] = $meta_mes;
					}
					if(ceil($meta_mes->mes/3) == $trimestre){
						$metas_mes_trimestre['meta'] += $meta_mes->meta;
						if($meta_mes->mes < $mes_actual){
							$metas_mes_trimestre['avance'] += $meta_mes->avance;
						}
					}
				}

				$recurso = $recurso->toArray();
				$recurso['metas_mes'] = $metas_mes;
				$recurso['metas_mes_acumulado_trimestre'] = $metas_mes_trimestre;
				
			}elseif($parametros['mostrar'] == 'datos-beneficiarios-avance'){
				$mes_actual = Util::obtenerMesActual();
				$recurso['acumulado'] = RegistroAvanceBeneficiario::where('idProyecto','=',$parametros['id-proyecto'])
														->where('idTipoBeneficiario','=',$id)
														->where('mes','<',$mes_actual)->groupBy('idTipoBeneficiario','sexo')
														->select('idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))->get();
				$recurso['beneficiario'] = Beneficiario::with(array('tipoBeneficiario','registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'comentarios'))->where('idProyecto','=',$parametros['id-proyecto'])->where('idTipoBeneficiario','=',$id)->get();
			}elseif ($parametros['mostrar'] == 'analisis-funcional') {
				$recurso = EvaluacionAnalisisFuncional::with('comentarios')->find($id);
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
			$mes_actual = Util::obtenerMesActual();
			$seguimiento_mes = EvaluacionProyectoMes::where('idProyecto','=',$parametros['id-proyecto'])
													->where('mes','=',$mes_actual)
													->first();
			//
			if($seguimiento_mes){
				if($seguimiento_mes->idEstatus == 6){
					$seguimiento_mes->idEstatus = 1;
					$seguimiento_mes->save();
				}
				if($seguimiento_mes->idEstatus != 1 && $seguimiento_mes->idEstatus != 3){
					switch ($seguimiento_mes->idEstatus) {
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
			}else{
				if(!$seguimiento_mes){
					$anio_captura = Util::obtenerAnioCaptura();
					$seguimiento_mes = new EvaluacionProyectoMes;
					$seguimiento_mes->idEstatus = 1;
					$seguimiento_mes->mes = $mes_actual;
					$seguimiento_mes->anio = $anio_captura;
					$seguimiento_mes->idProyecto = $parametros['id-proyecto'];
					$seguimiento_mes->save();
				}
			}

			if($parametros['guardar'] == 'avance-metas'){
				$respuesta = $this->guardarAvance($parametros);
				if($respuesta['http_status'] != 200){
					throw new Exception("Error al procesar los datos", 1);
				}
			}elseif ($parametros['guardar'] == 'avance-localidad-metas') {
				$respuesta = $this->guardarAvanceLocalidad($parametros);
				if($respuesta['http_status'] != 200){
					throw new Exception("Error al procesar los datos", 1);
				}
			}elseif($parametros['guardar'] == 'avance-beneficiarios'){
				$respuesta = $this->guardarAvanceBeneficiario($parametros);
			}elseif ($parametros['guardar'] == 'analisis-funcional') {
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre != 3){
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array('data'=>'Los datos del Analisis Funcional solo se pueden capturar en el ultimo mes del trimestre','code'=>'S01');
				}else{
					$validacion = Validador::validar(Input::all(), $this->reglasAnalisisFuncional);
					if($validacion === TRUE){
						//
						$mes_actual = Util::obtenerMesActual();
						$recurso = new EvaluacionAnalisisFuncional;
						$recurso->mes 					= $mes_actual;
						$recurso->idProyecto 			= $parametros['id-proyecto'];
						//$recurso->finalidadProyecto		= $parametros['finalidad'];
						$recurso->analisisResultado 	= $parametros['analisis-resultado'];
						$recurso->beneficiarios 		= $parametros['analisis-beneficiarios'];
						$recurso->justificacionGlobal 	= $parametros['justificacion-global'];

						if($recurso->save()){
							$respuesta['data'] = array('data'=>$recurso);
						}else{
							throw new Exception("Ocurrio un error al intentar guardar los datos", 1);
						}
					}else{
						$respuesta['http_status'] = $validacion['http_status'];
						$respuesta['data'] = $validacion['data'];
					}
				}
			}elseif($parametros['guardar'] == 'cargar-archivo-avances'){
				$respuesta = $this->importar_archivo_avance($parametros,$parametros['id-accion']);
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
		try{
			$parametros = Input::all();

			$mes_actual = Util::obtenerMesActual();

			if($parametros['guardar'] == 'validar-seguimiento'){
				$recurso = Proyecto::with(array(
					'actividades.metasMes'=>function($query)use($mes_actual){
						$query->where('mes','=',$mes_actual);
					},'actividades.registroAvance'=>function($query)use($mes_actual){
						$query->where('mes','=',$mes_actual);
					},'componentes.metasMes'=>function($query)use($mes_actual){
						$query->where('mes','=',$mes_actual);
					},'componentes.registroAvance'=>function($query)use($mes_actual){
						$query->where('mes','=',$mes_actual);
					}
				))->find($id);

				/*
				,'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				}
				*/

				$elementos_programados = 0;
				$elementos_capturados = 0;

				foreach ($recurso->componentes as $componente) {
					if(count($componente->metasMes)){
						$elementos_programados++;
						if(count($componente->registroAvance)){
							$elementos_capturados++;
						}
					}
				}
				foreach ($recurso->actividades as $actividad) {
					if(count($actividad->metasMes)){
						$elementos_programados++;
						if(count($actividad->registroAvance)){
							$elementos_capturados++;
						}
					}
				}

				//$elementos = count($recurso->componentes);
				//$elementos += count($recurso->actividades);
				//$registro_elementos = count($recurso->registroAvance);

				//if($elementos != $registro_elementos){
				if($elementos_programados != $elementos_capturados){
					$respuesta['data']['data']='No se ha podido enviar el proyecto a revisión ya que hacen falta avances por capturar';
					throw new Exception('Se han capturado el avance en '.$elementos_capturados.' de '.$elementos_programados.' elementos.', 1);
				}

				if(($mes_actual % 3) == 0){
					$recurso->load(array('beneficiarios','analisisFuncional'=>function($query) use ($mes_actual){
						$query->where('mes','=',$mes_actual);
					},'registroAvanceBeneficiarios'=>function($query) use ($mes_actual){
						$query->where('mes','=',$mes_actual);
					}));

					if(count($recurso->beneficiarios) != count($recurso->registroAvanceBeneficiarios)){
						$respuesta['data']['data']='No se ha podido enviar el proyecto a revisión ya que no se ha capturado en su totalidad el avance de beneficiarios';
						throw new Exception('Se han capturado '.count($recurso->registroAvanceBeneficiarios).' de '.count($recurso->beneficiarios).' Beneficiarios.', 1);
					}

					if(count($recurso->analisisFuncional) == 0){
						$respuesta['data']['data']='No se ha podido enviar el proyecto a revisión ya que no se ha capturado el Analisis Funcional';
						throw new Exception('falta analisis funcional.', 1);
					}
				}

				$seguimiento_mes = EvaluacionProyectoMes::where('idProyecto','=',$id)->where('mes','=',$mes_actual)->first();
				/*if(!$seguimiento_mes){
					$seguimiento_mes = new EvaluacionProyectoMes;
					$seguimiento_mes->idEstatus = 1;
					$seguimiento_mes->mes = $mes_actual;
					$seguimiento_mes->anio = date('Y');
					$seguimiento_mes->idProyecto = $id;
				}*/

				if($seguimiento_mes->idEstatus == 1 || $seguimiento_mes->idEstatus == 3){
					$seguimiento_mes->idEstatus = 2;
					if($seguimiento_mes->save()){
						$id_proyecto = $recurso->id;
						$usuario = Sentry::getUserProvider()->createModel();

						$usuario = $usuario->where('idDepartamento','=',2)
											->where('id','=',$recurso->idUsuarioValidacionSeg)
											->select('sentryUsers.id','sentryUsers.nombres','sentryUsers.email',
													'sentryUsers.apellidoPaterno','sentryUsers.apellidoMaterno')
											->first();
						if($usuario){
							if($usuario->email){
								/*$data['usuario'] = $usuario;
								$data['proyecto'] = $recurso;
								$data['mes_captura'] = Util::obtenerDescripcionMes(Util::obtenerMesActual());

								Mail::send('emails.rendicion-cuentas.proyecto-a-revision', $data, function($message) use ($usuario){
									$message->to($usuario->email,$usuario->nombres)->subject('SIRE:: Seguimiento enviado a revisión');
								});
								$respuesta['notas'] = 'Con correo enviado';	*/
								$respuesta['notas'] = 'Envio de correos desactivado temporalmente';	
							}else{
								$respuesta['notas'] = 'El usuario no tiene un correo electronico asignado';	
							}
						}else{
							$respuesta['notas'] = 'Sin correo enviado';
						}
						
						$respuesta['data'] = 'El Proyecto fue enviado a Revisión';
						return $respuesta;
					}else{
						throw new Exception("Hubo un error al intentar enviar el proyecto a revisión", 1);
					}
				}
			}else{
				$seguimiento_mes = EvaluacionProyectoMes::where('idProyecto','=',$parametros['id-proyecto'])
														->where('mes','=',$mes_actual)
														->first();
			}

			if($seguimiento_mes){
				if($seguimiento_mes->idEstatus != 1 && $seguimiento_mes->idEstatus != 3){
					switch ($seguimiento_mes->idEstatus) {
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
				$respuesta = $this->guardarAvance($parametros,$id);
				if($respuesta['http_status'] != 200){
					throw new Exception("Error al procesar los datos", 1);
				}
			}elseif ($parametros['guardar'] == 'avance-localidad-metas') {
				$respuesta = $this->guardarAvanceLocalidad($parametros);
				if($respuesta['http_status'] != 200){
					throw new Exception("Error al procesar los datos", 1);
				}
			}elseif($parametros['guardar'] == 'avance-beneficiarios'){
				$respuesta = $this->guardarAvanceBeneficiario($parametros,TRUE);
			}elseif ($parametros['guardar'] == 'analisis-funcional') {
				//
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre != 3){
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array('data'=>'Los datos del Analisis Funcional solo se pueden capturar en el ultimo mes del trimestre','code'=>'S01');
				}else{
					$validacion = Validador::validar(Input::all(), $this->reglasAnalisisFuncional);
					if($validacion === TRUE){
						//
						$mes_actual = Util::obtenerMesActual();
						$recurso = EvaluacionAnalisisFuncional::find($id);
						//$recurso->finalidadProyecto		= $parametros['finalidad'];
						$recurso->analisisResultado 	= $parametros['analisis-resultado'];
						$recurso->beneficiarios 		= $parametros['analisis-beneficiarios'];
						$recurso->justificacionGlobal 	= $parametros['justificacion-global'];

						if($recurso->save()){
							$respuesta['data'] = array('data'=>$recurso);
						}else{
							throw new Exception("Ocurrio un error al intentar guardar los datos", 1);
						}
					}else{
						$respuesta['http_status'] = $validacion['http_status'];
						$respuesta['data'] = $validacion['data'];
					}
				}
			}elseif($parametros['guardar'] == 'datos-informacion'){
				$validacion = Validador::validar(Input::all(), $this->reglasFuenteInformacion);
				if($validacion === TRUE){
					$recurso = Proyecto::find($id);
					if($recurso){
						$recurso->fuenteInformacion = $parametros['fuente-informacion'];
						$recurso->idResponsable = $parametros['responsable'];

						if(!$recurso->save()){
							$respuesta['http_status'] = 500;
							$respuesta['data'] = array('data'=>'Ocurrió un error al intentar guardar los datos','code'=>'S01');
						}
					}else{
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array('data'=>'No se encontro el proyecto','code'=>'S01');
					}
				}else{
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

	public function guardarAvanceBeneficiario($parametros, $es_editar = FALSE){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$mes_del_trimestre = Util::obtenerMesTrimestre();
		if($mes_del_trimestre != 3){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array('data'=>'Los datos de seguimiento de Beneficiarios solo se pueden capturar en el ultimo mes del trimestre','code'=>'S01');
			return $respuesta;
		}

		$validacion = Validador::validar(Input::all(), $this->reglasBeneficiarios);

		if($validacion === TRUE){
			$mes_actual = Util::obtenerMesActual();
			$recurso = Beneficiario::with(array('registroAvance'=>function($query) use ($mes_actual){
							$query->where('mes','=',$mes_actual);
						}))->where('idProyecto','=',$parametros['id-proyecto'])
						->where('idTipoBeneficiario','=',$parametros['id-beneficiario'])
						->get();

			$sexos_registrados = $recurso->lists('sexo');
			$suma_total = 0;
			foreach ($sexos_registrados as $sexo) {
				$suma_zona		= $parametros['urbana'.$sexo] + $parametros['rural'.$sexo];
				$suma_poblacion	= $parametros['mestiza'.$sexo] + $parametros['indigena'.$sexo];
				// + $parametros['inmigrante'.$sexo] + $parametros['otros'.$sexo];
				$suma_marginacion	= $parametros['muyalta'.$sexo] + $parametros['alta'.$sexo] + $parametros['media'.$sexo] + $parametros['baja'.$sexo] + $parametros['muybaja'.$sexo];

				if(($suma_zona != $suma_poblacion) || ($suma_poblacion != $suma_marginacion) || ($suma_marginacion != $suma_zona)){
					$respuesta['data'] = array('data'=>array(json_encode(array('field'=>'errorbeneficiarios','error'=>'Los totales capturados no corresponden entre si.'))),'code'=>'U00');
					$respuesta['http_status'] = 500;
					return $respuesta;
				}
				$suma_total += $suma_zona;
			}

			$variables = SysConfiguracionVariable::obtenerVariables(array('poblacion-total'))->lists('valor','variable');
			if($suma_total > $variables['poblacion-total']){
				$respuesta['data'] = array('data'=>array(json_encode(array('field'=>'errorbeneficiarios','error'=>'El total de beneficiarios capturados supera al total de la poblacion del estado.'))),'code'=>'U00');
				$respuesta['http_status'] = 500;
				return $respuesta;
			}

			//
			//
			$respuesta['data'] = DB::transaction(function() use ($recurso,$es_editar,$mes_actual,$parametros){
				$advertencia = '';
				foreach ($recurso as $beneficiario) {
					$sexo = $beneficiario->sexo;
					if($es_editar){
						$avance = $beneficiario->registroAvance[0];
					}else{
						$avance = new RegistroAvanceBeneficiario;
					}
					$avance->idProyecto 		= $beneficiario->idProyecto;
					$avance->idTipoBeneficiario	= $beneficiario->idTipoBeneficiario;
					$avance->sexo 				= $sexo;
					$avance->mes 				= $mes_actual;
					$avance->total 				= $parametros['urbana'.$sexo] + $parametros['rural'.$sexo];

					$avance->urbana 			= $parametros['urbana'.$sexo];
					$avance->rural 				= $parametros['rural'.$sexo];

					$avance->mestiza 			= $parametros['mestiza'.$sexo];
					$avance->indigena 			= $parametros['indigena'.$sexo];
					//$avance->inmigrante 		= $parametros['inmigrante'.$sexo];
					//$avance->otros 			= $parametros['otros'.$sexo];

					$avance->muyAlta 			= $parametros['muyalta'.$sexo];
					$avance->alta 				= $parametros['alta'.$sexo];
					$avance->media 				= $parametros['media'.$sexo];
					$avance->baja 				= $parametros['baja'.$sexo];
					$avance->muyBaja 			= $parametros['muybaja'.$sexo];

					$beneficiario->registroAvance()->save($avance);
				}
				$total_beneficiarios = $recurso->lists('total','sexo');
				$beneficiarios_acumulados = RegistroAvanceBeneficiario::where('idProyecto','=',$parametros['id-proyecto'])
															->where('idTipoBeneficiario','=',$parametros['id-beneficiario'])
															->where('mes','<=',$mes_actual)->groupBy('idTipoBeneficiario','sexo')
															->select('idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))->get();
				foreach ($beneficiarios_acumulados as $acumulado) {
					if($acumulado->total > $total_beneficiarios[$acumulado->sexo]){
						$advertencia = 'Los datos del avance de beneficiarios han sido guardados, sin embargo algunos totales capturados son mayores a los programados en el proyecto';
					}
				}
				return array('advertencia'=>$advertencia);
			});
		}else{
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}
		return $respuesta;
	}
	
	public function importar_archivo_avance($parametros,$id){
		$respuesta = array();
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');	
		$mes_actual = Util::obtenerMesActual();
		
		if($parametros['nivel'] == 'componente'){
			$idElemento = 'idComponente';
			$elemento = 'componente';
			$tablaMetasMes = 'componenteMetasMes';
			$tablaDesgloseMetasMes = 'desgloseMetasMes';
			$tablaElemento = 'proyectoComponentes';
		}else{
			$idElemento = 'idActividad';
			$elemento = 'actividad';
			$tablaMetasMes = 'actividadMetasMes';
			$tablaDesgloseMetasMes = 'actividadDesgloseMetasMes';
			$tablaElemento = 'componenteActividades';
		}
		
		$usuario = Sentry::getUser();

		if (Input::hasFile('datoscsv')){
			$finfo = finfo_open(FILEINFO_MIME_TYPE); 
			$archivoConDatos = Input::file('datoscsv');
			$type = finfo_file($finfo, $archivoConDatos); 
			$archivo_avances = storage_path().'/archivoscsv/archivo_avances_'.$usuario->id.'.csv';
			
			//Si el Mime coincide con CSV
			if($type=="text/plain"){
				$row = 1;
				$datos_archivo = array();
				if (($handle = fopen($archivoConDatos, "r")) !== FALSE && ($datos_avance = fopen($archivo_avances, "w")) !== FALSE) {
					if($parametros['nivel'] == 'componente'){
						$accion = Accion::with(array('desgloseComponente'=>function($query){
								$query->select('*',DB::raw('CONCAT_WS("_",claveMunicipio,claveLocalidad) AS claveCompuesta'));
							}))->where('idComponente','=',$id)->first();
						$accion->desglose = $accion->desgloseComponente;
					}else{
						$accion = Accion::with(array('desgloseActividad'=>function($query){
								$query->select('*',DB::raw('CONCAT_WS("_",claveMunicipio,claveLocalidad) AS claveCompuesta'));
							}))->where('idActividad','=',$id)->first();
						$accion->desglose = $accion->desgloseActividad;
					}
					
					$desgloses_capturados = array();
					foreach($accion->desglose as $desglose){
						$desgloses_capturados[$desglose->claveCompuesta] = $desglose;
					}
					
					$accion->desglose->load(array('metasMes'=>function($query)use($mes_actual){
						$query->where('mes','=',$mes_actual);
					}));
					
					$row = 1;
					while (($csv_data = fgetcsv($handle, 0, ",")) !== FALSE) {
						if($row == 1){
							$row++;
							continue;
						}
						
						$claves = explode('_',$csv_data[0]);

						$clave_municipio = $claves[0];
						$clave_localidad = $claves[1];
						
						if(isset($desgloses_capturados[$csv_data[0]])){
							$desglose = $desgloses_capturados[$csv_data[0]];
							
							if(count($desglose->metasMes)){
								$meta_mes = $desglose->metasMes[0];
								fputcsv($datos_avance,[
									$meta_mes->id,
									$desglose->id,
									($desglose->idComponente)?$desglose->idComponente:$desglose->idActividad,
									$meta_mes->claveMunicipio,
									$meta_mes->claveLocalidad,
									$meta_mes->mes,
									$meta_mes->meta,
									floatval($csv_data[5]),
									$meta_mes->creadoPor,
									$meta_mes->creadoAl
								]);
							}else{
								if(floatval($csv_data[5])){
									fputcsv($datos_avance,[
										NULL,
										$desglose->id,
										($desglose->idComponente)?$desglose->idComponente:$desglose->idActividad,
										$clave_municipio,
										$clave_localidad,
										$mes_actual,
										0,
										floatval($csv_data[5]),
										NULL,
										NULL
									]);
								}
							}
						}
					}
					fclose($datos_avance);
					unset($desgloses_capturados);
					
					try {
						DB::connection()->getPdo()->beginTransaction();

						$id_usuario = $usuario->id;
						
						//Cargar Archivo de Metas x mes
						$query = sprintf("
							LOAD DATA local INFILE '%s' REPLACE 
							INTO TABLE ".$tablaDesgloseMetasMes." 
							FIELDS TERMINATED BY ',' 
							OPTIONALLY ENCLOSED BY '\"' 
							ESCAPED BY '\"' 
							LINES TERMINATED BY '\\n'
							(
							@vid,
							@v".$idElemento."Desglose,
							@v".$idElemento.",
							@vclaveMunicipio,
							@vclaveLocalidad,
							@vmes,
							@vmeta,
							@vavance,
							@vcreadoPor,
							@vcreadoAl
							)
							set
							id = nullif(@vid,''),
							".$idElemento."Desglose = @v".$idElemento."Desglose,
							".$idElemento." = @v".$idElemento.",
							claveMunicipio = @vclaveMunicipio,
							claveLocalidad = @vclaveLocalidad,
							mes = @vmes,
							meta = @vmeta,
							avance = nullif(@vavance,''),
							creadoPor = ifnull(nullif(@vcreadoPor,''),%s),
							creadoAl = ifnull(nullif(@vcreadoAl,''),CURRENT_TIMESTAMP),
							actualizadoPor = %s,
							modificadoAl = CURRENT_TIMESTAMP
							", addslashes($archivo_avances),$id_usuario,$id_usuario);
						DB::connection()->getpdo()->exec($query);
						
						//Se insertan/reemplazan las metas por mes del componente con los valores importados del archivo
						$query = sprintf("
							REPLACE INTO ".$tablaMetasMes."
							SELECT 
							cMetas.id, comp.idProyecto, desglose.".$idElemento.", desglose.claveJurisdiccion, metasMes.mes, 
							SUM(metasMes.meta) AS meta, SUM(metasMes.avance) AS avance, ifnull(cMetas.creadoPor,%s) AS creadoPor, 
							%s AS actualizadoPor, cMetas.creadoAl, cMetas.modificadoAl, NULL AS borradoAl
							FROM ".$elemento."Desglose AS desglose
							LEFT JOIN ".$tablaDesgloseMetasMes." AS metasMes 
							ON metasMes.claveMunicipio = desglose.claveMunicipio 
							AND desglose.id = metasMes.".$idElemento."Desglose
							AND metasMes.borradoAl IS NULL
							LEFT JOIN ".$tablaMetasMes." AS cMetas
							ON cMetas.".$idElemento." = desglose.".$idElemento."
							AND cMetas.claveJurisdiccion = desglose.claveJurisdiccion
							AND cMetas.mes = metasMes.mes
							AND cMetas.borradoAl IS NULL
							JOIN ".$tablaElemento." AS comp ON comp.id = desglose.".$idElemento."
							WHERE desglose.".$idElemento." = %s AND desglose.borradoAl IS NULL AND metasMes.mes = %s
							GROUP BY desglose.claveJurisdiccion, metasMes.mes;
						",$id_usuario,$id_usuario,$id,$mes_actual);
						DB::connection()->getpdo()->exec($query);
						
						DB::connection()->getPdo()->commit();

						File::delete($archivo_avances);
						
						if($parametros['nivel'] == 'componente'){
							$recurso = ComponenteMetaMes::where('mes','=',$mes_actual)->where('idComponente','=',$id)->get();
						}else{
							$recurso = ActividadMetaMes::where('mes','=',$mes_actual)->where('idActividad','=',$id)->get();
						}
						$respuesta['data']['data'] = $recurso;
						
					}catch (\PDOException $e){
						File::delete($archivo_avances);
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"Ha ocurrido un error, no se pudieron cargar los datos. Verfique su conexión a Internet.",'code'=>'U06');
						DB::connection()->getPdo()->rollBack();
						throw $e;
					}catch(Exception $e){
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array("data"=>"",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
					}
				}else{
					fclose($datos_avance);
				}
				fclose($handle);
			}else{
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"Formato de archivo incorrecto.",'code'=>'U06');
			}
		}else{
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No se encontró el archivo.",'code'=>'U06');
		}
		return $respuesta;
	}

	public function guardarAvanceLocalidad($parametros){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		$es_editar = FALSE;

		$mes_actual = Util::obtenerMesActual();
		$clave_municipio = $parametros['clave-municipio'];
		
		if($parametros['nivel'] == 'componente'){
			$accion = Componente::with(array('desglose'=>function($query) use ($clave_municipio){
				$query->where('claveMunicipio','=',$clave_municipio);
			},'desglose.metasMes'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			}))->find($parametros['id-accion']);
		}else{
			$accion = Actividad::with(array('desglose'=>function($query) use ($clave_municipio){
				$query->where('claveMunicipio','=',$clave_municipio);
			},'desglose.metasMes'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			}))->find($parametros['id-accion']);
			//throw new Exception("Elemento no soportado por el momento", 1);
		}
		
		$guardar_metas_localidades = array(); //Para guardar nuevo/editar
		$faltan_campos = array(); //faltan localidades por capturar

		$avance_anterior = 0; //avance anterior
		$avance_total = 0; //avance nuevo

		$clave_jurisdiccion = $accion->desglose[0]->claveJurisdiccion;

		foreach ($accion->desglose as $desglose) {
			/*if($parametros['localidad-avance-mes'][$desglose->claveLocalidad] == ''){
				$faltan_campos[] = json_encode(array('field'=>'localidad_avance_mes_'.$desglose->claveLocalidad,'error'=>'Este campo es requerido'));
			}else{*/
				if(count($desglose->metasMes)){
					if($desglose->metasMes[0]->avance){
						$avance_anterior += $desglose->metasMes[0]->avance;
					}
					$desglose->metasMes[0]->avance = $parametros['localidad-avance-mes'][$desglose->claveLocalidad];
					$guardar_metas_localidades[] = $desglose->metasMes[0];
				}else{
					if($parametros['nivel'] == 'componente'){
						$nueva_meta = new DesgloseMetasMes;
						$nueva_meta->idComponenteDesglose = $desglose->id;
					}else{
						$nueva_meta = new ActividadDesgloseMetasMes;
						$nueva_meta->idActividadDesglose = $desglose->id;
					}
					
					$nueva_meta->mes = $mes_actual;
					$nueva_meta->meta = 0;
					$nueva_meta->avance = $parametros['localidad-avance-mes'][$desglose->claveLocalidad];
					$guardar_metas_localidades[] = $nueva_meta;
				}
				$avance_total += $parametros['localidad-avance-mes'][$desglose->claveLocalidad];
			//}
		}

		if(count($faltan_campos)){
			$respuesta['http_status'] = 500;
			$respuesta['data']['code'] = 'U00';
			$respuesta['data']['data'] = $faltan_campos;
			return $respuesta;
		}

		$accion->load(array('metasMes' => function($query) use ($mes_actual,$clave_jurisdiccion){
			$query->where('mes','=',$mes_actual)->where('claveJurisdiccion','=',$clave_jurisdiccion);
		}));

		$meta_jurisdiccion = NULL;
		if(count($accion->metasMes)){
			$meta_jurisdiccion = $accion->metasMes[0];
		}else{
			if($parametros['nivel'] == 'componente'){
				$meta_jurisdiccion = new ComponenteMetaMes;
			}else{
				$meta_jurisdiccion = new ActividadMetaMes;
			}
			$meta_jurisdiccion->claveJurisdiccion = $clave_jurisdiccion;
			$meta_jurisdiccion->mes = $mes_actual;
			$meta_jurisdiccion->meta = 0;
			$meta_jurisdiccion->idProyecto = $accion->idProyecto;
		}
		$meta_jurisdiccion->avance -= $avance_anterior;
		$meta_jurisdiccion->avance += $avance_total;
		
		$respuesta['data'] = DB::transaction(function() use($accion, $guardar_metas_localidades, $meta_jurisdiccion){
			if($accion->metasMes()->save($meta_jurisdiccion)){
				foreach ($guardar_metas_localidades as $meta) {
					$meta->save();
				}
				//$accion->desglose()->metasMes()->saveMany($guardar_metas_localidades);
				return array('data'=>$meta_jurisdiccion);
			}else{
				//No se pudieron guardar los datos del proyecto
				$respuesta['data']['code'] = 'S01';
				throw new Exception("Error al guardar los datos de la FIBAP: Error en el guardado de la ficha", 1);
			}
		});

		return $respuesta;
	}

	public function guardarAvance($parametros,$id = NULL){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		$es_editar = FALSE;

		$mes_actual = Util::obtenerMesActual();

		if($id){
			$registro_avance = RegistroAvanceMetas::find($id);
			$es_editar = TRUE;
		}else{
			$registro_avance = new RegistroAvanceMetas;
		}

		//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
		if($parametros['nivel'] == 'componente'){
			$accion_metas = Componente::getModel();
			$registro_avance->nivel = 1;
		}else{
			$accion_metas = Actividad::getModel();
			$registro_avance->nivel = 2;
		}

		$accion_metas = $accion_metas->with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual);
		},'metasMes' => function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual);
		},'planMejora'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual);
		}))->find($parametros['id-accion']);

		$registro_avance->idProyecto = $accion_metas->idProyecto;
		$registro_avance->idNivel = $parametros['id-accion'];
		$registro_avance->mes = $mes_actual;

		$conteo_alto_bajo_avance = 0;
		$faltan_campos = array();
		
		//$metas_acumuladas = $accion_metas->metasMesJurisdiccion->lists('meta','claveJurisdiccion');
		//$avances_acumulados = $accion_metas->metasMesJurisdiccion->lists('avance','claveJurisdiccion');
		
		$guardar_metas = array();
		$total_avance = 0;
		$meta_acumulada = $accion_metas->metasMesJurisdiccion->sum('meta');
		$avance_acumulado = $accion_metas->metasMesJurisdiccion->sum('avance');
		$tiene_desglose = FALSE;
		//Checar desglose
		$accion_metas->load('desglose');
		if(count($accion_metas->desglose)){
			$tiene_desglose = TRUE;
			$id_desgloses = $accion_metas->desglose->lists('id');
			if($parametros['nivel'] == 'componente'){
				$avances_faltantes = DesgloseMetasMes::whereIn('idComponenteDesglose',$id_desgloses)
													->where('mes','=',$mes_actual)->whereNull('avance')->get();
			}else{
				$avances_faltantes = ActividadDesgloseMetasMes::whereIn('idActividadDesglose',$id_desgloses)
													->where('mes','=',$mes_actual)->whereNull('avance')->get();
			}
			if(count($avances_faltantes)){
				$respuesta['http_status'] = 500;
				$respuesta['data']['code'] = 'S01';
				$respuesta['data']['data'] = 'Aún hay avances que no han sido capturados, capturalos para poder guardar los datos.';
				return $respuesta;
			}
		}
		
		if(!$tiene_desglose){
			foreach ($accion_metas->metasMes as $metas) {
				if($parametros['avance'][$metas->claveJurisdiccion] == ''){
					$faltan_campos[] = json_encode(array('field'=>'avance_'.$metas->claveJurisdiccion,'error'=>'Este campo es requerido.'));
				}elseif($parametros['avance'][$metas->claveJurisdiccion] < 0){
					$faltan_campos[] = json_encode(array('field'=>'avance_'.$metas->claveJurisdiccion,'error'=>'El valor no puede ser negativo.'));
				}else{
					if($metas->avance){
						$avance_acumulado -= $metas->avance;
					}
					$total_avance += $parametros['avance'][$metas->claveJurisdiccion];
					$metas->avance = $parametros['avance'][$metas->claveJurisdiccion];
					$guardar_metas[] = $metas;
				}
			}
			
			//Si las metas capturadas no fueron puestas en la programación entonces ahi que agregarlas ya que no estan en la tabla
			$jurisdicciones_capturadas = $accion_metas->metasMes->lists('claveJurisdiccion');
			$jurisdicciones_formulario = array_keys($parametros['avance']);
			$metas_nuevas = array_diff($jurisdicciones_formulario, $jurisdicciones_capturadas);
			foreach ($metas_nuevas as $jurisdiccion) {
				if($parametros['avance'][$jurisdiccion] > 0){
					if($parametros['nivel'] == 'componente'){
						$meta = new ComponenteMetaMes;
					}else{
						$meta = new ActividadMetaMes;
					}
					$meta->claveJurisdiccion = $jurisdiccion;
					$meta->mes = $mes_actual;
					$meta->meta = 0;
					$meta->avance = $parametros['avance'][$jurisdiccion];
					$meta->idProyecto = $accion_metas->idProyecto;
					$guardar_metas[] = $meta;
					$total_avance += $parametros['avance'][$jurisdiccion];
				}elseif($parametros['avance'][$jurisdiccion] < 0){
					$faltan_campos[] = json_encode(array('field'=>'avance_'.$jurisdiccion,'error'=>'El valor no puede ser negativo.'));
				}
			}
			$registro_avance->avanceMes = $total_avance;
			$avance_acumulado += $total_avance;
		}else{
			$registro_avance->avanceMes = $accion_metas->metasMes->sum('avance');
		}

		if($avance_acumulado > 0 && $meta_acumulada == 0){
			$porcentaje_avance = 1;
		}elseif($meta_acumulada > 0){
			$porcentaje_avance = (( $avance_acumulado  / $meta_acumulada ) * 100);
		}else{
			$porcentaje_avance = 100;
		}
		
		if(round($porcentaje_avance,2) < 90 || round($porcentaje_avance,2) > 110){
			$conteo_alto_bajo_avance++;
		}
		
		if($conteo_alto_bajo_avance){
			if(trim($parametros['justificacion-acumulada']) == ''){
				$faltan_campos[] = json_encode(array('field'=>'justificacion-acumulada','error'=>'Este campo es requerido.'));
			}else{
				if(strlen($parametros['justificacion-acumulada']) > 500){
					$faltan_campos[] = json_encode(array('field'=>'justificacion-acumulada','error'=>'Solo se pueden capturar un máximo de 500 caracteres.'));
				}else{
					$registro_avance->justificacionAcumulada = $parametros['justificacion-acumulada'];
				}
			}
			if($mes_actual != 12){
				$registro_avance->planMejora = 1;
			}else{
				$registro_avance->planMejora = 0;
			}
		}else{
			if($mes_actual != 12){
				$registro_avance->justificacionAcumulada = 'El avance se encuentra dentro de lo programado';
			}else{
				$registro_avance->justificacionAcumulada = 'Se concluyó satisfactoriamente';
			}
			$registro_avance->planMejora = 0;
		}



		if(trim($parametros['analisis-resultados']) == ''){
			$faltan_campos[] = json_encode(array('field'=>'analisis-resultados','error'=>'Este campo es requerido.'));
		}else{
			if(strlen($parametros['analisis-resultados']) > 500){
				$faltan_campos[] = json_encode(array('field'=>'analisis-resultados','error'=>'Solo se pueden capturar un máximo de 500 caracteres.'));
			}else{
				$registro_avance->analisisResultados = $parametros['analisis-resultados'];
			}
		}

		$plan_mejora = NULL;

		if(count($faltan_campos)){
			$respuesta['http_status'] = 500;
			$respuesta['data']['code'] = 'U00';
			$respuesta['data']['data'] = $faltan_campos;
			return $respuesta;
			//throw new Exception("Error en la captura", 1);
		}

		$mes_del_trimestre = Util::obtenerMesTrimestre();

		if($mes_del_trimestre == 3){
			if(trim($parametros['analisis-resultados-trimestral']) == ''){
				$faltan_campos[] = json_encode(array('field'=>'analisis-resultados-trimestral','error'=>'Este campo es requerido.'));
			}else{
				if(strlen($parametros['analisis-resultados-trimestral']) > 500){
					$faltan_campos[] = json_encode(array('field'=>'analisis-resultados-trimestral','error'=>'Solo se pueden capturar un máximo de 500 caracteres.'));
				}else{
					$registro_avance->analisisResultadosTrimestral = $parametros['analisis-resultados-trimestral'];
				}
			}

			if(isset($parametros['justificacion-trimestral'])){
				if(trim($parametros['justificacion-trimestral']) != ''){
					if(strlen($parametros['justificacion-trimestral']) > 500){
						$faltan_campos[] = json_encode(array('field'=>'justificacion-trimestral','error'=>'Solo se pueden capturar un máximo de 500 caracteres.'));
					}else{
						$registro_avance->justificacionTrimestral = $parametros['justificacion-trimestral'];
					}
				}
			}else{
				if($mes_actual != 12){
					$registro_avance->justificacionTrimestral = 'El avance se encuentra dentro de lo programado';
				}else{
					$registro_avance->justificacionTrimestral = 'Se concluyó satisfactoriamente';
				}
			}
		}

		if($registro_avance->planMejora && $mes_del_trimestre == 3){
			$validacion = Validador::validar(Input::all(), $this->reglasPlanMejora);

			if($validacion === TRUE){

				$fechas = $this->validar_fechas($parametros['fecha-inicio'], $parametros['fecha-termino'], $parametros['fecha-notificacion']);

				if(count($accion_metas->planMejora)){
					$plan_mejora = $accion_metas->planMejora[0];
				}else{
					$plan_mejora = new EvaluacionPlanMejora;
					$plan_mejora->nivel = $registro_avance->nivel;
					$plan_mejora->idProyecto = $accion_metas->idProyecto;
					$plan_mejora->idNivel = $parametros['id-accion'];
					$plan_mejora->mes = $mes_actual;
				}
				$plan_mejora->accionMejora 					= $parametros['accion-mejora'];
				$plan_mejora->grupoTrabajo 					= $parametros['grupo-trabajo'];
				$plan_mejora->documentacionComprobatoria 	= $parametros['documentacion-comprobatoria'];
				$plan_mejora->fechaInicio 					= $fechas['inicio'];
				$plan_mejora->fechaTermino 					= $fechas['termino'];
				$plan_mejora->fechaNotificacion 			= $fechas['notificacion'];
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
				return $respuesta;
			}
		}
		
		$respuesta['data'] = DB::transaction(function() use ($registro_avance, $guardar_metas, $accion_metas, $plan_mejora){
			if($registro_avance->save()){
				if($registro_avance->planMejora && $plan_mejora){
					$plan_mejora->save();
				}elseif(!$registro_avance->planMejora && count($accion_metas->planMejora)){
					$accion_metas->planMejora[0]->delete();
				}
				if(count($guardar_metas)){
					$accion_metas->metasMes()->saveMany($guardar_metas);
				}
				return array('data'=>$registro_avance);
			}else{
				//No se pudieron guardar los datos del proyecto
				$respuesta['data']['code'] = 'S01';
				throw new Exception("Error al guardar los datos de la FIBAP: Error en el guardado de la ficha", 1);
			}
		});

		return $respuesta;
	}

	private function validar_fechas($fecha_inicial, $fecha_final, $fecha_noti){
		$fecha_inicio = DateTime::createFromFormat('d/m/Y',$fecha_inicial);
		$fecha_termino = DateTime::createFromFormat('d/m/Y',$fecha_final);
		$fecha_notificacion = DateTime::createFromFormat('d/m/Y',$fecha_noti);

		if(!$fecha_inicio){ $fecha_inicio = DateTime::createFromFormat('Y-m-d',$fecha_inicial); }

		if(!$fecha_termino){ $fecha_termino = DateTime::createFromFormat('Y-m-d',$fecha_final); }

		if(!$fecha_notificacion){ $fecha_notificacion = DateTime::createFromFormat('Y-m-d',$fecha_noti); }

		if(!$fecha_inicio){
			throw new Exception('{"field":"fecha-inicio","error":"La fecha de inicio del periodo de ejecución no tiene el formato correcto."}');
		}

		if(!$fecha_termino){
			throw new Exception('{"field":"fecha-termino","error":"La fecha final del periodo de ejecución no tiene el formato correcto."}');
		}

		if(!$fecha_notificacion){
			throw new Exception('{"field":"fecha-notificacion","error":"La fecha final del periodo de ejecución no tiene el formato correcto."}');
		}		

		if($fecha_termino < $fecha_inicio){
			throw new Exception('{"field":"fecha-termino","error":"La fecha de termino no puede ser menor que la de inicio."}');
		}

		if($fecha_notificacion < $fecha_termino){
			throw new Exception('{"field":"fecha-notificacion","error":"La fecha de notificación no puede ser menor que la de termino."}');
		}

		return array('inicio' =>$fecha_inicio, 'termino' => $fecha_termino, 'notificacion' => $fecha_notificacion);
	}
}