<?php

namespace V1;

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Proyecto,Componente,Actividad,Beneficiario,RegistroAvanceMetas,ComponenteMetaMes,ActividadMetaMes,RegistroAvanceBeneficiario,EvaluacionAnalisisFuncional,EvaluacionProyectoMes,
	EvaluacionPlanMejora,ComponenteDesglose,DesgloseMetasMes;

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
		'analisis-resultado'	=> 'required',
		'beneficiarios'			=> 'required',
		'justificacion-global'	=> 'required'
	);

	private $reglasPlanMejora = array(
		'accion-mejora'					=> 'required',
		'grupo-trabajo'					=> 'required',
		'documentacion-comprobatoria'	=> 'required',
		'fecha-inicio'					=> 'required|date',
		'fecha-termino'					=> 'required|date',
		'fecha-notificacion'			=> 'required|date'
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
					$rows = Proyecto::with('componentes.actividades.registroAvance')->find($parametros['idProyecto']);
					$rows->componentes->load('registroAvance');
					$total = count($rows);
				}elseif($parametros['grid'] == 'rendicion-beneficiarios'){
					$rows = Beneficiario::with(array('registroAvance'=>function($query){
						$query->select('id','idProyectoBeneficiario','idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))
								->groupBy('idTipoBeneficiario','sexo');
					},'tipoBeneficiario'))->where('idProyecto','=',$parametros['idProyecto'])->get();
					$total = count($rows);
				}
			}else{
				$mes_actual = Util::obtenerMesActual();

				$rows = Proyecto::getModel();
				$rows = $rows->where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto'])
							->where('unidadResponsable','=',Sentry::getUser()->claveUnidad);
							//->where('idClasificacionProyecto','=',$)
				//$rows = $rows->with('registroAvance');
				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'),DB::raw('count(idNivel) as registros'))->groupBy('idProyecto','mes');
				},'evaluacionMeses'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				}));
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}
				
				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto','proyectos.idEstatusProyecto',
					'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl')
									->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
									->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
									->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
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
				$recurso = Proyecto::with('datosFuncion','datosSubFuncion','datosProgramaPresupuestario','componentes.metasMesAgrupado'
					,'componentes.registroAvance','componentes.actividades.metasMesAgrupado','componentes.actividades.registroAvance','beneficiarios.registroAvance','beneficiarios.tipoBeneficiario')->find($id);
			}elseif ($parametros['mostrar'] == 'datos-municipio-avance') {
				//$id = idComponente y $parametros['clave-municipio'] y $parametros['nivel'] = 'componente'
				$mes_actual = Util::obtenerMesActual();
				if($parametros['nivel'] == 'componente'){
					$recurso = ComponenteDesglose::listarDatos()->where('claveMunicipio','=',$parametros['clave-municipio'])
													->where('idComponente','=',$id);
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
				$recurso = $recurso->with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMes' => function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'planMejora'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'unidadMedida'))->find($id);

				if($parametros['nivel'] == 'componente'){
					$recurso->load('desgloseMunicipios');
					//$queries = DB::getQueryLog();
					//throw new Exception(print_r(end($queries),true), 1);
				}
			}elseif($parametros['mostrar'] == 'datos-beneficiarios-avance'){
				$mes_actual = Util::obtenerMesActual();
				$recurso['acumulado'] = RegistroAvanceBeneficiario::where('idProyecto','=',$parametros['id-proyecto'])
														->where('idTipoBeneficiario','=',$id)
														->where('mes','<',$mes_actual)->groupBy('idTipoBeneficiario','sexo')
														->select('idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))->get();
				$recurso['beneficiario'] = Beneficiario::with(array('tipoBeneficiario','registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				}))->where('idProyecto','=',$parametros['id-proyecto'])->where('idTipoBeneficiario','=',$id)->get();
			}elseif ($parametros['mostrar'] == 'analisis-funcional') {
				$recurso = EvaluacionAnalisisFuncional::find($id);
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
			
			
			if($seguimiento_mes){
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
						$recurso->finalidadProyecto		= $parametros['finalidad-proyecto'];
						$recurso->analisisResultado 	= $parametros['analisis-resultado'];
						$recurso->beneficiarios 		= $parametros['beneficiarios'];
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
				$seguimiento_mes = EvaluacionProyectoMes::where('idProyecto','=',$id)->where('mes','=',$mes_actual)->first();
				if(!$seguimiento_mes){
					$seguimiento_mes = new EvaluacionProyectoMes;
					$seguimiento_mes->idEstatus = 1;
					$seguimiento_mes->mes = $mes_actual;
					$seguimiento_mes->idProyecto = $id;
				}

				if($seguimiento_mes->idEstatus == 1 || $seguimiento_mes->idEstatus == 3){
					//$seguimiento_mes->load('beneficiarios','componentes','actividades');
					$seguimiento_mes->idEstatus = 2;
					$seguimiento_mes->save();
					$respuesta['data'] = 'El Proyecto fue enviado a Revisión';
					return $respuesta;
				}
			}else{
				$seguimiento_mes = EvaluacionProyectoMes::where('idProyecto','=',$parametros['id-proyecto'])
														->where('mes','=',$mes_actual)
														->first();
			}

			if($seguimiento_mes){
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
						$recurso->finalidadProyecto		= $parametros['finalidad-proyecto'];
						$recurso->analisisResultado 	= $parametros['analisis-resultado'];
						$recurso->beneficiarios 		= $parametros['beneficiarios'];
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
			foreach ($sexos_registrados as $sexo) {
				$suma_zona		= $parametros['urbana'.$sexo] + $parametros['rural'.$sexo];
				$suma_poblacion	= $parametros['mestiza'.$sexo] + $parametros['indigena'.$sexo] + $parametros['inmigrante'.$sexo] + $parametros['otros'.$sexo];
				$suma_marginacion	= $parametros['muyalta'.$sexo] + $parametros['alta'.$sexo] + $parametros['media'.$sexo] + $parametros['baja'.$sexo] + $parametros['muybaja'.$sexo];

				if(($suma_zona != $suma_poblacion) || ($suma_poblacion != $suma_marginacion) || ($suma_marginacion != $suma_zona)){
					$respuesta['data'] = array('data'=>array(json_encode(array('field'=>'errorbeneficiarios','error'=>'Los totales capturados no corresponden entre si.'))),'code'=>'U00');
					$respuesta['http_status'] = 500;
					return $respuesta;
				}
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
					$avance->inmigrante 		= $parametros['inmigrante'.$sexo];
					$avance->otros 				= $parametros['otros'.$sexo];

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
			throw new Exception("Elemento no soportado por el momento", 1);
		}
		
		$guardar_metas_localidades = array(); //Para guardar nuevo/editar
		$faltan_campos = array(); //faltan localidades por capturar

		$avance_anterior = 0; //avance anterior
		$avance_total = 0; //avance nuevo

		$clave_jurisdiccion = $accion->desglose[0]->claveJurisdiccion;

		foreach ($accion->desglose as $desglose) {
			if($parametros['localidad-avance-mes'][$desglose->claveLocalidad] == ''){
				$faltan_campos[] = json_encode(array('field'=>'localidad_avance_mes_'.$desglose->claveLocalidad,'error'=>'Este campo es requerido'));
			}else{
				if(count($desglose->metasMes)){
					if($desglose->metasMes[0]->avance){
						$avance_anterior += $desglose->metasMes[0]->avance;
					}
					$desglose->metasMes[0]->avance = $parametros['localidad-avance-mes'][$desglose->claveLocalidad];
					$guardar_metas_localidades[] = $desglose->metasMes[0];
				}else{
					$nueva_meta = new DesgloseMetasMes;
					$nueva_meta->idComponenteDesglose = $desglose->id;
					$nueva_meta->mes = $mes_actual;
					$nueva_meta->meta = 0;
					$nueva_meta->avance = $parametros['localidad-avance-mes'][$desglose->claveLocalidad];
					$guardar_metas_localidades[] = $nueva_meta;
				}
				$avance_total += $parametros['localidad-avance-mes'][$desglose->claveLocalidad];
			}
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
			$meta_jurisdiccion = new ComponenteMetaMes;
			$meta_jurisdiccion->claveJurisdiccion = $jurisdiccion;
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
		if($parametros['nivel'] == 'componente'){
			$accion_metas->load('desglose');
			if(count($accion_metas->desglose)){
				$tiene_desglose = TRUE;
				$id_desgloses = $accion_metas->desglose->lists('id');
				$avances_faltantes = DesgloseMetasMes::whereIn('idComponenteDesglose',$id_desgloses)
													->where('mes','=',$mes_actual)
													->whereNull('avance')
													->get();
				//
				if(count($avances_faltantes)){
					$respuesta['http_status'] = 500;
					$respuesta['data']['code'] = 'S01';
					$respuesta['data']['data'] = 'Aún hay avances que no han sido capturados, capturalos para poder guardar los datos.';
					return $respuesta;
				}
			}
		}

		if(!$tiene_desglose){
			foreach ($accion_metas->metasMes as $metas) {
				if($parametros['avance'][$metas->claveJurisdiccion] == ''){
					$faltan_campos[] = json_encode(array('field'=>'avance_'.$metas->claveJurisdiccion,'error'=>'Este campo es requerido'));
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
		
		if($porcentaje_avance < 90 || $porcentaje_avance > 110){
			$conteo_alto_bajo_avance++;
		}

		if($conteo_alto_bajo_avance){
			if(trim($parametros['justificacion-acumulada']) == ''){
				$faltan_campos[] = json_encode(array('field'=>'justificacion-acumulada','error'=>'Este campo es requerido.'));
			}else{
				$registro_avance->justificacionAcumulada = $parametros['justificacion-acumulada'];
			}
			$registro_avance->planMejora = 1;
		}else{
			$registro_avance->justificacionAcumulada = 'El avance se encuentra dentro de los parametros establecidos';
			$registro_avance->planMejora = 0;
		}

		if(trim($parametros['analisis-resultados']) == ''){
			$faltan_campos[] = json_encode(array('field'=>'analisis-resultados','error'=>'Este campo es requerido.'));
		}else{
			$registro_avance->analisisResultados = $parametros['analisis-resultados'];
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