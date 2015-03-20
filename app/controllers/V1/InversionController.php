<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Proyecto, Componente, Actividad, Beneficiario, FIBAP, ComponenteMetaMes, ActividadMetaMes, Region, Municipio, Jurisdiccion, 
	FibapDatosProyecto, Titular, ComponenteDesglose, AntecedenteFinanciero, DesgloseMetasMes, DistribucionPresupuesto,Accion,
	PropuestaFinanciamiento, DesgloseBeneficiario;

class InversionController extends ProyectosController {
	private $reglasFibap = array(
		'organismo-publico'			=> 'required',
		'sector'					=> 'required',
		'subcomite'					=> 'required',
		'grupo-trabajo'				=> 'required',
		'justificacion-proyecto'	=> 'required',
		'descripcion-proyecto'		=> 'required',
		'objetivo-proyecto'			=> 'required',
		'alineacion-especifica'		=> 'required',
		'presupuesto-requerido'		=> 'required',
		'periodo-ejecucion-inicio'	=> 'required',
		'periodo-ejecucion-final'	=> 'required',
		'documento-soporte'			=> 'required|array|min:1'
	);

	private $reglasFibapAntecedentes = array(
		'resultados-obtenidos'		=> 'required',
		'resultados-esperados'		=> 'required'
	);

	private $reglasAntecedentes = array(
		'anio-antecedente'				=> 'required|integer|min:1000',
		'autorizado-antecedente'		=> 'required',
		'ejercido-antecedente'			=> 'required',
		'fecha-corte-antecedente'		=> 'required'
	);

	private $reglasPresupuesto = array(
		'total-beneficiarios'		=> 'required',
		'beneficiarios'				=> 'required|array|min:1',
		'jurisdiccion-accion'		=> 'required',
		'municipio-accion'			=> 'required_if:jurisdiccion-accion,01,02,03,04,05,06,07,08,09,10',
		'localidad-accion'			=> 'required_if:jurisdiccion-accion,01,02,03,04,05,06,07,08,09,10',
		'cantidad-meta'				=> 'required|numeric|min:1',
		'cantidad-presupuesto'		=> 'required|numeric|min:1',
		'meta-mes'					=> 'required|array|min:1',
		'mes'						=> 'required|array|min:1'
	);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){
			if(isset($parametros['desglosegrid'])){
				$recurso = array();

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				$pagina = $parametros['pagina'];

				//$rows = ComponenteDesglose::listarDatos()->where('idComponente','=',$parametros['id-componente']);
				$rows = ComponenteDesglose::listarDatos()->where('idComponente','=',$parametros['idComponente']);
				$totales = ComponenteDesglose::listarDatos()->where('idComponente','=',$parametros['idComponente']);
				if(isset($parametros['buscar'])){
					$rows = $rows->where(function($query) use ($parametros){
									$query->where('jurisdiccion.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('municipio.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('localidad.nombre','like','%'.$parametros['buscar'].'%');
								});
					$totales = $totales->where(function($query) use ($parametros){
									$query->where('jurisdiccion.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('municipio.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('localidad.nombre','like','%'.$parametros['buscar'].'%');
								});
					//$total = $rows->count();
					/*$queries = DB::getQueryLog();
					$data['query'] = print_r(end($queries),true);*/
				}/*else{				
					//$total = $rows->count();						
				}*/
				//$totales = $rows;
				$totales = $totales->select(DB::raw('count(componenteDesglose.id) AS cuantos'),DB::raw('sum(componenteDesglose.presupuesto) AS totalPresupuesto'))->get();
				$totales = $totales[0];
				
				$total = $totales->cuantos;
				$recurso['resultados'] = $totales->cuantos;
				$recurso['total_presupuesto'] = $totales->totalPresupuesto;
				$recurso['data'] = $rows->orderBy('componenteDesglose.id', 'desc')
							->skip(($pagina-1)*10)->take(10)
							->get();

				$data = $recurso;
			}else{
				$rows = Proyecto::getModel();
				$rows = $rows->where('unidadResponsable','=',Sentry::getUser()->claveUnidad)
							->where('idClasificacionProyecto','=',2)
							->whereIn('idEstatusProyecto',[1,2,3,4]);
				
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}
				
				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),'fibap.presupuestoRequerido',
					'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto','proyectos.idEstatusProyecto',
					'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl')
									->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
									->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
									->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
									->leftjoin('fibap','proyectos.id','=','fibap.idProyecto')
									->orderBy('id', 'desc')
									->skip(($parametros['pagina']-1)*10)->take(10)
									->get();
				$data = array('resultados'=>$total,'data'=>$rows);
			}

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
			if($parametros['mostrar'] == 'detalles-proyecto'){
				$recurso = Proyecto::contenidoCompleto()->find($id);
				if($recurso){
					$recurso->load('fibap');
					if($recurso->fibap){
						$recurso->fibap->load('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado');
						$recurso->fibap->distribucionPresupuestoAgrupado->load('objetoGasto');
					}
					$recurso->componentes->load('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable','entregableTipo','entregableAccion','desgloseCompleto');
					foreach ($recurso->componentes as $key => $componente) {
						$recurso->componentes[$key]->actividades->load('formula','dimension','frecuencia','tipoIndicador','unidadMedida');
					}
				}
			}elseif($parametros['mostrar'] == 'editar-proyecto'){
				$recurso = Proyecto::with('jefeInmediato','liderProyecto','jefePlaneacion','coordinadorGrupoEstrategico',
									'fibap.documentos','beneficiarios.tipoBeneficiario')
									->find($id);
				if($recurso->idEstatusProyecto == 3){
					$recurso->load('comentarios');
				}
				if($recurso->fibap){
					$recurso->fibap->load('antecedentesFinancieros','acciones.datosComponenteDetalle','distribucionPresupuestoAgrupado.objetoGasto');
					$recurso->fibap->acciones->load('propuestasFinanciamiento');
				}
				if(!is_null($recurso)){
					$extras = array();
					if($recurso->idCobertura == 1){ 
					//Cobertura Estado => Todos las Jurisdicciones
						$extras['jurisdicciones'] = Jurisdiccion::all();
						$extras['municipios'] = Municipio::with('localidades')->get(); //Todos los municipios
					}elseif($recurso->idCobertura == 2){ 
					//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$extras['jurisdicciones'] = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
						$extras['municipios'] = Municipio::with('localidades')->where('clave','=',$recurso->claveMunicipio)->get(); //Obtenemos el municipio seleccionado
					}elseif($recurso->idCobertura == 3){ 
					//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$extras['jurisdicciones'] = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
						$region = Region::with('municipios.localidades')->where('region','=',$recurso->claveRegion)->get();
						$extras['municipios'] = $region[0]->municipios;
					}
					$data["extras"] = $extras;
				}
			}elseif($parametros['mostrar'] == 'editar-beneficiario'){
				$recurso = Beneficiario::where('idProyecto','=',$parametros['id-proyecto'])
										->where('idTipoBeneficiario','=',$id)->get();
			}elseif ($parametros['mostrar'] == 'editar-antecedente') {
				$recurso = AntecedenteFinanciero::find($id);
			}elseif ($parametros['mostrar'] == 'editar-componente') {
				# code...
				$recurso = Accion::with('componente.actividades.unidadMedida','componente.metasMes','partidas','propuestasFinanciamiento')->find($id);
			}elseif ($parametros['mostrar'] == 'editar-actividad') {
				# code...
				$recurso = Actividad::with('metasMes')->find($id);
			}elseif ($parametros['mostrar'] == 'desglose-componente') {
				# code...
				//$recurso = Accion::with('datosComponenteDetalle','partidas','desglosePresupuesto')->find($id);
				$recurso = Accion::with('datosComponenteDetalle','partidas')->find($id);
				/*
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				$pagina = $parametros['pagina'];

				$rows = ComponenteDesglose::listarDatos()->where('idComponente','=',$recurso->idComponente);

				if(isset($parametros['buscar'])){
					$rows = $rows->where(function($query) use ($parametros){
									$query->where('jurisdiccion.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('municipio.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('localidad.nombre','like','%'.$parametros['buscar'].'%');
								});
					$total = $rows->count();
					/*$queries = DB::getQueryLog();
					$data['query'] = print_r(end($queries),true);*/
				/*}else{				
					$total = $rows->count();						
				}*/
				/*$recurso['total_desglose'] = $total;
				$recurso['desglosePresupuesto'] = $rows->orderBy('id', 'desc')
							->skip(($pagina-1)*10)->take(10)
							->get();*/
				/*if($recurso->distribucionPresupuestoAgrupado){
					$recurso->distribucionPresupuestoAgrupado->load('jurisdiccion');
				}*/
			}elseif ($parametros['mostrar'] == 'editar-presupuesto'){
				/***
				*	Obtiene los datos del desglose del presupuesto de la Accion (GET)
				*
				*	- Obtiene la distribución de presupuesto concentrado por mes y partida
				* 	- Obtiene los datos del desglose del componete (datos a exportar al proyecto) (Metas,Beneficiarios,Localidad)
				*	- Obtiene el desglose de metas por mes del componente
				*	- Obtiene datos generales de la accion
				*
				***/
				$desglose = ComponenteDesglose::with('metasMes','beneficiarios')->find($id);

				$recurso = Accion::with('partidas')->find($desglose->idAccion);
				
				$calendarizado = DistribucionPresupuesto::where('idAccion','=',$desglose->idAccion)
													->whereIn('idObjetoGasto',$recurso->partidas->lists('id'))
													->where('claveJurisdiccion','=',$desglose->claveJurisdiccion);
				if($desglose->claveJurisdiccion != 'OC'){
					$calendarizado = $calendarizado->where('claveMunicipio','=',$desglose->claveMunicipio)
												->where('claveLocalidad','=',$desglose->claveLocalidad);
				}
				$calendarizado = $calendarizado->get();
				//
				$recurso['desglose'] = $desglose;
				$recurso['calendarizado'] = $calendarizado;
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
			if($parametros['guardar'] != 'proyecto'){
				$proyecto = Proyecto::find($parametros['id-proyecto']);
				if($proyecto->idEstatusProyecto != 1 && $proyecto->idEstatusProyecto != 3){
					switch ($proyecto->idEstatusProyecto) {
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

			if($parametros['guardar'] == 'proyecto'){
			//
				$respuesta = parent::guardar_datos_proyecto($parametros);

				if($respuesta['http_status'] == 200){
					$recurso = $respuesta['data']['data'];

					$extras = array();
					if($recurso->idCobertura == 1){ 
					//Cobertura Estado => Todos las Jurisdicciones
						$extras['jurisdicciones'] = Jurisdiccion::all();
						$extras['municipios'] = Municipio::with('localidades')->get(); //Todos los municipios
					}elseif($recurso->idCobertura == 2){ 
					//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$extras['jurisdicciones'] = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
						$extras['municipios'] = Municipio::with('localidades')->where('clave','=',$recurso->claveMunicipio)->get(); //Obtenemos el municipio seleccionado
					}elseif($recurso->idCobertura == 3){ 
					//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$extras['jurisdicciones'] = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
						$region = Region::with('municipios.localidades')->where('region','=',$recurso->claveRegion)->get();
						$extras['municipios'] = $region[0]->municipios;
					}
					
					$respuesta['data']['extras'] = $extras;
				}
			}elseif($parametros['guardar'] == 'componente'){
				$parametros['clasificacion'] = 2;
				$respuesta = parent::guardar_datos_componente('componente',$parametros);
				//
				if($respuesta['data']['data']){
					$componente = $respuesta['data']['data'];
					$respuesta['data'] = $this->guardar_datos_accion_presupuesto($parametros,$componente);
				}
			}elseif($parametros['guardar'] == 'actividad'){
				$parametros['clasificacion'] = 2;
				$respuesta = parent::guardar_datos_componente('actividad',$parametros);
				if($respuesta['http_status'] == 200){
					$componente = Componente::with('actividades.unidadMedida')->find($parametros['id-componente']);
					$respuesta['data']['extras']['actividades'] = $componente->actividades;
				}
			}elseif($parametros['guardar'] == 'proyecto-beneficiario'){
				$respuesta = parent::guardar_datos_beneficiario($parametros);
			}elseif ($parametros['guardar'] == 'datos-fibap') {
				//Guardar nuevo FIBAP
				$validacion = Validador::validar(Input::all(), $this->reglasFibap);

				if($validacion === TRUE){
					
					$fechas = $this->validar_fechas($parametros['periodo-ejecucion-inicio'],$parametros['periodo-ejecucion-final']);

					$recurso = new FIBAP;

					$recurso->claveUnidadResponsable = Sentry::getUser()->claveUnidad;
					$recurso->organismoPublico 		 = $parametros['organismo-publico'];
					$recurso->sector 				 = $parametros['sector'];
					$recurso->subcomite 			 = $parametros['subcomite'];
					$recurso->grupoTrabajo 			 = $parametros['grupo-trabajo'];
					$recurso->justificacionProyecto  = $parametros['justificacion-proyecto'];
					$recurso->descripcionProyecto 	 = $parametros['descripcion-proyecto'];
					$recurso->objetivoProyecto		 = $parametros['objetivo-proyecto'];
					$recurso->alineacionEspecifica 	 = $parametros['alineacion-especifica'];
					$recurso->alineacionGeneral 	 = $parametros['alineacion-general'];
					$recurso->periodoEjecucionInicio = $fechas['inicio'];
					$recurso->periodoEjecucionFinal  = $fechas['fin'];
					$recurso->presupuestoRequerido 	 = $parametros['presupuesto-requerido'];
					$recurso->idProyecto 			 = $parametros['id-proyecto'];

					$documentos = $parametros['documento-soporte'];

					$respuesta['data'] = DB::transaction(function() use ($recurso, $documentos){
						if($recurso->save()){
							$recurso->documentos()->attach($documentos);
							return array('data'=>$recurso);
						}else{
							//No se pudieron guardar los datos del proyecto
							$respuesta['data']['code'] = 'S01';
							throw new Exception("Error al guardar los datos de la FIBAP: Error en el guardado de la ficha", 1);
						}
					});
				}else{
					$respuesta['http_status'] 	= $validacion['http_status'];
					$respuesta['data'] 			= $validacion['data'];
				}
			}elseif ($parametros['guardar'] == 'datos-antecedente'){
			/**
			*	Formulario de datos especificos de Antecedente del FIBAP (POST)
			*	(Alimenta el datagrid de antecedentes en la caratula de captura del fibap)
			**/
				$validacion = Validador::validar(Input::all(), $this->reglasAntecedentes);

				if($validacion === TRUE){
					$fecha_corte = DateTime::createFromFormat('d/m/Y',Input::get('fecha-corte-antecedente'));

					if(!$fecha_corte){
						$fecha_corte = DateTime::createFromFormat('Y-m-d',Input::get('fecha-corte-antecedente'));
					}

					if(!$fecha_corte){
						throw new Exception('{"field":"fecha-corte-antecedente","error":"La fecha de corte no tiene el formato correcto."}');
					}

					$recurso = new AntecedenteFinanciero;
					$recurso->anio = $parametros['anio-antecedente'];
					$recurso->autorizado = $parametros['autorizado-antecedente'];
					$recurso->ejercido = $parametros['ejercido-antecedente'];
					$recurso->fechaCorte = $fecha_corte;
					$recurso->porcentaje = ($recurso->ejercido * 100) / $recurso->autorizado;

					$fibap = FIBAP::find($parametros['id-fibap']);
					$fibap->antecedentesFinancieros()->save($recurso);
					$fibap->load('antecedentesFinancieros');
					$respuesta['data'] = array('data'=>$recurso,'antecedentes' => $fibap->antecedentesFinancieros);
				}else{
					$respuesta['http_status'] 	= $validacion['http_status'];
					$respuesta['data'] 			= $validacion['data'];
				}
			}elseif ($parametros['guardar'] == 'desglose-presupuesto') {
				/***
				*	Formulario de datos del desglose del presupuesto de la Accion (POST)
				*
				*	- Guarda una nueva distribución de presupuesto por mes y partida
				* 	- Guarda datos del desglose del componete (datos a exportar al proyecto) (Metas,Beneficiarios,Localidad)
				*	- Guardar el desglose de metas por mes del componente
				***/
				$respuesta = $this->guardar_datos_desglose_presupuesto($parametros);
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

			if($parametros['guardar'] == 'validar-proyecto'){
				$proyecto = Proyecto::find($id);
				if($proyecto->idEstatusProyecto == 1 || $proyecto->idEstatusProyecto == 3){
					$proyecto->load('beneficiarios','componentes','actividades');
					if(count($proyecto->beneficiarios) == 0){
						$respuesta['data'] = array('data'=>'El proyecto debe tener al menos un beneficiario capturado.');
						throw new Exception("No hay beneficiarios", 1);
					}elseif(count($proyecto->componentes) == 0){
						$respuesta['data'] = array('data'=>'El proyecto debe tener al menos un componente capturado.');
						throw new Exception("No hay componentes", 1);
					}elseif(count($proyecto->actividades) == 0){
						$respuesta['data'] = array('data'=>'El proyecto debe tener al menos una actividad capturada.');
						throw new Exception("No hay actividades", 1);
					}
					$proyecto->idEstatusProyecto = 2;
					$proyecto->save();
					$respuesta['data'] = array('data'=>'El Proyecto fue enviado a Revisión');
				}elseif($proyecto->idEstatusProyecto == 2){
					$respuesta['data'] = array('data'=>'El Proyecto ya se encuentra en proceso de Revisión');
				}else{
					$respuesta['data'] = array('data'=>'Este Proyecto no es editable');
				}
			}else{
				if($parametros['guardar'] != 'proyecto'){
					$proyecto = Proyecto::find($parametros['id-proyecto']);
				}else{
					$proyecto = Proyecto::find($id);
				}
	
				if($proyecto->idEstatusProyecto != 1 && $proyecto->idEstatusProyecto != 3){
					switch ($proyecto->idEstatusProyecto) {
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

			if($parametros['guardar'] == 'datos-fibap-antecedentes'){
				/***
				*	Editar datos extras del FIBAP, datos de los antecedentes (PUT)
				*
				*	- Actualiza datos del FIBAP
				*
				***/
				$validacion = Validador::validar(Input::all(), $this->reglasFibapAntecedentes);

				if($validacion === TRUE){
					$recurso = FIBAP::find($id);
					$recurso->resultadosObtenidos = $parametros['resultados-obtenidos'];
					$recurso->resultadosEsperados = $parametros['resultados-esperados'];
					if($recurso->save()){
						$respuesta['data']['data'] = $recurso;
					}else{
						//No se pudieron guardar los datos del proyecto
						$respuesta['data']['code'] = 'S01';
						throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado de los datos antecedentes", 1);
					}
				}else{
					$respuesta['http_status'] 	= $validacion['http_status'];
					$respuesta['data'] 			= $validacion['data'];
				}
			}elseif ($parametros['guardar'] == 'proyecto'){
				/***
				*	Editar datos generales del Proyecto de Inversión (PUT)
				*
				*	- Actualiza datos del Proyecto
				* 	- En caso de modificar la cobertura
				*		-> Se eliminan las metas por mes de los Componentes y Actividades del proyecto cuyas Jurisdicciones 
				*			sean diferentes de las nuevas Jurisdicciones seleccionadas
				***/

				//La validacion del formulario se lleva dentro de la función
				$respuesta = parent::guardar_datos_proyecto($parametros,$id);

				if($respuesta['http_status'] == 200){
					$recurso = $respuesta['data']['data'];
					$datos_anteriores = $respuesta['data']['datos-anteriores'];

					$extras = array();
					if($recurso->idCobertura == 1 && ($datos_anteriores['claveMunicipio'] != NULL || $datos_anteriores['claveRegion'] != NULL)){
					//Cobertura Estado => Todos las Jurisdicciones
						$extras['jurisdicciones'] = Jurisdiccion::all();
						$extras['municipios'] = Municipio::with('localidades')->get(); //Todos los municipios
					}elseif($recurso->idCobertura == 2 && $recurso->claveMunicipio != $datos_anteriores['claveMunicipio']){ 
					//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$extras['jurisdicciones'] = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
						$extras['municipios'] = Municipio::with('localidades')->where('clave','=',$recurso->claveMunicipio)->get();
					}elseif($recurso->idCobertura == 3 && $recurso->claveRegion != $datos_anteriores['claveRegion']){ 
					//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$extras['jurisdicciones'] = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
						$region = Region::with('municipios.localidades')->where('region','=',$recurso->claveRegion)->get();
						$extras['municipios'] = $region[0]->municipios;
					}

					if(count($extras)){
						if($recurso->fibap){
							$recurso->fibap->load('acciones.datosComponenteDetalle','distribucionPresupuestoAgrupado.objetoGasto');
							$recurso->fibap->acciones->load('propuestasFinanciamiento');
						}
					}
					
					$respuesta['data']['extras'] = $extras;
				}
			}elseif($parametros['guardar'] == 'proyecto-beneficiario'){
				$respuesta = parent::guardar_datos_beneficiario($parametros,$id);
			}elseif($parametros['guardar'] == 'componente'){
				$parametros['clasificacion'] = 2;
				$respuesta = parent::guardar_datos_componente('componente',$parametros,$parametros['id-componente']);
				//Llenar datos adicionales
				if($respuesta['data']['data']){
					$componente = $respuesta['data']['data'];
					$respuesta['data'] = $this->guardar_datos_accion_presupuesto($parametros,$componente,$id);
				}
			}elseif($parametros['guardar'] == 'actividad'){
				$parametros['clasificacion'] = 2;
				$respuesta = parent::guardar_datos_componente('actividad',$parametros,$parametros['id-actividad']);
				//Llenar datos adicionales
				if($respuesta['http_status'] == 200){
					$componente = Componente::with('actividades.unidadMedida')->find($parametros['id-componente']);
					$respuesta['data']['extras']['actividades'] = $componente->actividades;
				}
			}elseif($parametros['guardar'] == 'datos-fibap'){
				/***
				*	Editar datos generales de la FIBAP (PUT)
				*
				*	- Actualiza datos de la FIBAP
				*	- Modifica la relación entre la FIBAP y los documentos de soporte (Agregar o Eliminar según sea el caso)
				* 	- En caso de modificar las fechas del periodo de ejecución:
				*		-> Se elminan las metas por mes del desglose del componente que hayan quedado fuera del periodo
				*		-> Se elmina la distribución del presupuesto que haya quedado fuera del periodo
				***/
				$validacion = Validador::validar(Input::all(), $this->reglasFibap);

				if($validacion === TRUE){
					$recurso = FIBAP::with('documentos')->find($id);

					$fechas = $this->validar_fechas($parametros['periodo-ejecucion-inicio'],$parametros['periodo-ejecucion-final']);

					$periodo_ant['inicio']	= $recurso->periodoEjecucionInicio;
					$periodo_ant['fin'] 	= $recurso->periodoEjecucionFinal;

					$recurso->claveUnidadResponsable = Sentry::getUser()->claveUnidad;
					$recurso->organismoPublico 		 = $parametros['organismo-publico'];
					$recurso->sector 				 = $parametros['sector'];
					$recurso->subcomite 			 = $parametros['subcomite'];
					$recurso->grupoTrabajo 			 = $parametros['grupo-trabajo'];
					$recurso->justificacionProyecto  = $parametros['justificacion-proyecto'];
					$recurso->descripcionProyecto 	 = $parametros['descripcion-proyecto'];
					$recurso->objetivoProyecto		 = $parametros['objetivo-proyecto'];
					$recurso->alineacionEspecifica 	 = $parametros['alineacion-especifica'];
					$recurso->alineacionGeneral 	 = $parametros['alineacion-general'];
					$recurso->periodoEjecucionInicio = $fechas['inicio'];
					$recurso->periodoEjecucionFinal  = $fechas['fin'];
					$recurso->presupuestoRequerido 	 = $parametros['presupuesto-requerido'];

					$documentos = $parametros['documento-soporte'];
					$documentos_anteriores = $recurso->documentos->lists('id');

					$docs['nuevos'] = array_diff($documentos, $documentos_anteriores);
					$docs['borrar'] = array_diff($documentos_anteriores, $documentos);

					$respuesta['data'] = DB::transaction(function() use ($recurso, $docs, $periodo_ant){
						if($recurso->save()){
							if(count($docs['borrar'])){
								$recurso->documentos()->detach($docs_borrar);
							}
							if(count($docs['nuevos'])){
								$recurso->documentos()->attach($docs_nuevos);
							}

							$distribucion_borrada = 0;
							//Si se cambiaron las fechas del periodo de ejecucion hay que eliminar las metas y presupuestos que no entren en el periodo
							if($recurso->periodoEjecucionInicio != $periodo_ant['inicio'] || $recurso->periodoEjecucionFinal != $periodo_ant['fin']){
								$recurso->load('acciones');
								
								$mes['inicio'] = date_format($recurso->periodoEjecucionInicio,"n");
								$mes['fin'] = date_format($recurso->periodoEjecucionFinal,"n");

								ComponenteMetaMes::where('idProyecto',$recurso->idProyecto)
												->where(function($query) use ($mes){
													$query->where('mes','<',$mes['inicio'])
														->orWhere('mes','>',$mes['fin']);
												})->delete();
								//$queries = DB::getQueryLog();
								//print_r(end($queries));
								
								ActividadMetaMes::where('idProyecto',$recurso->idProyecto)
												->where(function($query) use ($mes){
													$query->where('mes','<',$mes['inicio'])
														->orWhere('mes','>',$mes['fin']);
												})->delete();

								//Obtenemos la distribucion del presupuesto que vamos a borrar
								$distribucion_borrada = DistribucionPresupuesto::where('idFibap','=',$recurso->id)
														->where(function($query) use ($mes){
															$query->where('mes','<',$mes['inicio'])
																->orWhere('mes','>',$mes['fin']);
														})->get();
								if(count($distribucion_borrada) && count($recurso->acciones)){
									//Obtenemos los desgloses
									$desgloses_completo = ComponenteDesglose::whereIn('idAccion',$recurso->acciones->lists('id'))->get();
									
									if(count($desgloses_completo)){
										//Eliminamos las metas por mes
										DesgloseMetasMes::whereIn('idComponenteDesglose',$desgloses_completo->lists('id'))
														->where(function($query) use ($mes){
															$query->where('mes','<',$mes['inicio'])
																->orWhere('mes','>',$mes['fin']);
														})->delete();
									}
									//
									$cantidades_desglose = array();
									foreach ($distribucion_borrada as $distribucion) {
										if($distribucion->claveJurisdiccion != 'OC'){
											$index = $distribucion->idAccion.'.'.$distribucion->claveJurisdiccion.'.'.$distribucion->claveMunicipio.'.'.$distribucion->claveLocalidad;
										}else{
											$index = $distribucion->idAccion.'.'.$distribucion->claveJurisdiccion.'.0.0';
										}
										
										if(!isset($cantidades_desglose[$index])){
											$cantidades_desglose[$index] = $distribucion->cantidad;
										}else{
											$cantidades_desglose[$index] += $distribucion->cantidad;
										}
									}

									//$actualizar_desglose = array();
									foreach ($desgloses_completo as $desglose) {
										if(isset($cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad])){
											$desglose->presupuesto -= $cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad];
											$desglose->save();
										}
									}
									//ComponenteDesglose::saveMany($actualizar_desglose);
									$distribucion_borrada = DistribucionPresupuesto::where('idFibap','=',$recurso->id)
															->where(function($query) use ($mes){
																$query->where('mes','<',$mes['inicio'])
																	->orWhere('mes','>',$mes['fin']);
															})->delete();
								}else{
									$distribucion_borrada = 0;
								}
							}
							if($distribucion_borrada > 0){
								$recurso->load('distribucionPresupuestoAgrupado.objetoGasto');
							}

							return array('data'=>$recurso);
						}else{
							//No se pudieron guardar los datos del proyecto
							$respuesta['data']['code'] = 'S01';
							throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado de la ficha", 1);
						}
						//
					});
				}else{
					$respuesta['http_status'] 	= $validacion['http_status'];
					$respuesta['data'] 			= $validacion['data'];
				}
			}elseif ($parametros['guardar'] == 'datos-antecedente'){
				/***
				*	Editar datos de un Antecedente Financiero (PUT)
				*
				*	- Actualiza datos del Antecedente Financiero
				*
				***/
				$validacion = Validador::validar(Input::all(), $this->reglasAntecedentes);

				if($validacion === TRUE){
					$fecha_corte = DateTime::createFromFormat('d/m/Y',Input::get('fecha-corte-antecedente'));

					if(!$fecha_corte){
						$fecha_corte = DateTime::createFromFormat('Y-m-d',Input::get('fecha-corte-antecedente'));
					}

					if(!$fecha_corte){
						throw new Exception('{"field":"fecha-corte-antecedente","error":"La fecha de corte no tiene el formato correcto."}');
					}

					$recurso = AntecedenteFinanciero::find($id);
					$recurso->anio = $parametros['anio-antecedente'];
					$recurso->autorizado = $parametros['autorizado-antecedente'];
					$recurso->ejercido = $parametros['ejercido-antecedente'];
					$recurso->fechaCorte = $fecha_corte;
					$recurso->porcentaje = ($recurso->ejercido * 100) / $recurso->autorizado;

					$fibap = FIBAP::find($parametros['id-fibap']);
					$fibap->antecedentesFinancieros()->save($recurso);
					$fibap->load('antecedentesFinancieros');
					$respuesta['data'] = array('data'=>$recurso,'antecedentes' => $fibap->antecedentesFinancieros);
				}else{
					$respuesta['http_status'] 	= $validacion['http_status'];
					$respuesta['data'] 			= $validacion['data'];
				}
			}elseif ($parametros['guardar'] == 'desglose-presupuesto') {
				/***
				*	Formulario de datos del desglose del presupuesto de la Accion (POST)
				*
				*	- Guarda una nueva distribución de presupuesto por mes y partida
				* 	- Guarda datos del desglose del componete (datos a exportar al proyecto) (Metas,Beneficiarios,Localidad)
				*	- Guardar el desglose de metas por mes del componente
				***/
				$respuesta = $this->guardar_datos_desglose_presupuesto($parametros,$id);
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
			if(!isset($respuesta['data']['code'])){
				$respuesta['data']['code'] = 'S03';
			}
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

			$parametros = Input::all();

			$ids = $parametros['rows'];
			$id_padre = 0;

			if(isset($parametros['eliminar'])){
				$proyectos = Proyecto::where('id','=',$parametros['id-proyecto'])->get();
			}else{
				$proyectos = Proyecto::whereIn('id',$ids)->get();
			}
			
			foreach ($proyectos as $proyecto) {
				if($proyecto->idEstatusProyecto != 1 && $proyecto->idEstatusProyecto != 3){
					switch ($proyecto->idEstatusProyecto) {
						case 2:
							$data['data'] = 'El proyecto se encuentra en proceso de revisión, por tanto no es posible editarlo';
							break;
						case 4:
							$data['data'] = 'El proyecto se encuentra registrado, por tanto no es posible editarlo';
							break;
						case 5:
							$data['data'] = 'El proyecto ya fue firmado, por tanto no es posible editarlo';
							break;
						default:
							$data['data'] = 'El estatus del proyecto es desconocido';
							break;
					}
					throw new Exception("El proyecto se encuentra en un estatus en el que no esta disponible para edición", 1);
				}
			}
			
			if(isset($parametros['eliminar'])){
				if($parametros['eliminar'] == 'accion'){
					/***
					*	Eliminar Acción (DELETE)
					*
					*	- Borrar datos de una acción
					*	- Borrar la relación de la acción con las partidas prespuestarias
					* 	- Borrar los componetes asociados y el desglose de metas por mes
					*	- Borrar la propuesta de financiamiento
					*	- Borrar la distribución del presupuesto
					* 	- Borrar datos del desglose del componete (Metas,Beneficiarios,Localidad)
					*	- Borrar las actividades del componente y el desglose de metas por mes
					*
					***/
					$id_padre = $parametros['id-fibap'];
					$rows = DB::transaction(function() use ($ids){
						$acciones = Accion::whereIn('id',$ids)->get();
						$id_componentes = $acciones->lists('idComponente');

						$actividades = Actividad::whereIn('idComponente',$id_componentes)->lists('id');
						if(count($actividades) > 0){
							//Eliminamos las metas de dichas actividades
							ActividadMetaMes::whereIn('idActividad',$actividades)->delete();
							//Eliminamos las actividades de los componentes
							Actividad::whereIn('idComponente',$id_componentes)->delete();
						}
						//Eliminamos las metas de los componentes
						ComponenteMetaMes::whereIn('idComponente',$id_componentes)->delete();
						//Eliminamos los componenetes
						Componente::whereIn('id',$id_componentes)->delete();

						PropuestaFinanciamiento::whereIn('idAccion',$ids)->delete();
						DistribucionPresupuesto::whereIn('idAccion',$ids)->delete();

						$desgloses = ComponenteDesglose::whereIn('idAccion',$ids)->get();

						if(count($desgloses) > 0){
							DesgloseMetasMes::whereIn('idComponenteDesglose',$desgloses->lists('id'))->delete();
							DesgloseBeneficiario::whereIn('idComponenteDesglose',$desgloses->lists('id'))->delete();
						}
						
						ComponenteDesglose::whereIn('idAccion',$ids)->delete();
						//$acciones->partidas->detach();
						foreach ($acciones as $accion) {
							$accion->partidas()->detach();
						}
						return Accion::whereIn('id',$ids)->delete();
					});
				}elseif($parametros['eliminar'] == 'actividad'){
					/***
					*	Eliminar actividad de un Componente (Accion) (DELETE)
					*
					* 	- Borrar datos de la actividad del componete
					*	- Borrar la distribución de metas por mes
					*
					***/
					$id_padre = $parametros['id-componente'];
					$rows = DB::transaction(function() use ($ids){
						//Eliminamos las metas de las actividades seleccionadas
						ActividadMetaMes::whereIn('idActividad',$ids)->delete();
						//Eliminamos las actividades
						return Actividad::whereIn('id',$ids)->delete();
					});
				}elseif($parametros['eliminar'] == 'desglose-presupuesto'){ //Eliminar Distribucion del Presupuesto
					/***
					*	Eliminar desglose del presupuesto de la Accion (DELETE)
					*
					* 	- Borrar datos del desglose del componete (Metas,Beneficiarios,Localidad)
					*	- Borrar la distribución de presupuesto por mes y partida
					*	- Borrar el desglose de metas por mes del componente
					*	- Borrar el desglose de beneficiarios del componente
					*	- Actualiza las metas por mes del componente
					***/

					$id_padre = $parametros['id-accion'];
					$rows = DB::transaction(function() use ($ids){

						$desgloses_eliminar = ComponenteDesglose::with('metasMes')->whereIn('id',$ids)->get();

						$datos_jurisdiccion_metas = array();
						$distribucion_eliminar = DistribucionPresupuesto::getModel();
						//Añadir idComponenteDesglose a la tabla
						foreach ($desgloses_eliminar as $key => $desglose) {
							foreach ($desglose->metasMes as $meta_mes) {
								if(!isset($datos_jurisdiccion_metas[$desglose->claveJurisdiccion][$meta_mes->mes])){
									$datos_jurisdiccion_metas[$desglose->claveJurisdiccion][$meta_mes->mes] = $meta_mes->meta;
								}else{
									$datos_jurisdiccion_metas[$desglose->claveJurisdiccion][$meta_mes->mes] += $meta_mes->meta;
								}
							}

							if($key == 0){
								$distribucion_eliminar = $distribucion_eliminar->where(function($query) use ($desglose){
									$query->where('idAccion','=',$desglose->idAccion)
											->where('claveJurisdiccion','=',$desglose->claveJurisdiccion)
											->where('claveMunicipio','=',$desglose->claveMunicipio)
											->where('claveLocalidad','=',$desglose->claveLocalidad);
								});
							}else{
								$distribucion_eliminar = $distribucion_eliminar->orWhere(function($query) use ($desglose){
									$query->where('idAccion','=',$desglose->idAccion)
											->where('claveJurisdiccion','=',$desglose->claveJurisdiccion)
											->where('claveMunicipio','=',$desglose->claveMunicipio)
											->where('claveLocalidad','=',$desglose->claveLocalidad);
								});
							}
						}
						$distribucion_eliminar->delete();
						//$desgloses_ids = $desgloses_eliminar->get();

						$id_componente = $desgloses_eliminar[0]->idComponente;
						$componente_metas_mes = Componente::with('metasMes')->find($id_componente);
						$actualizar_metas_mes = array();
						foreach ($componente_metas_mes->metasMes as $metas_mes) {
							if(isset($datos_jurisdiccion_metas[$metas_mes->claveJurisdiccion][$metas_mes->mes])){
								$metas_mes->meta -= $datos_jurisdiccion_metas[$metas_mes->claveJurisdiccion][$metas_mes->mes];
								$actualizar_metas_mes[] = $metas_mes;
							}
						}
						$componente_metas_mes->metasMes()->saveMany($actualizar_metas_mes);

						DesgloseBeneficiario::whereIn('idComponenteDesglose',$ids)->delete();
						DesgloseMetasMes::whereIn('idComponenteDesglose',$ids)->delete();
						return ComponenteDesglose::whereIn('id',$ids)->delete();
					});
				}elseif($parametros['eliminar'] == 'proyecto-beneficiario'){
					/***
					*	Eliminar Beneficiarios del Proyecto (DELETE)
					*
					* 	- Borrar Beneficiarios asignados al proyecto
					*	- Borra el desglose del componente en donde se asignan beneficiarios
					*	- Se ajustan los totales en el desglose del componente
					*
					***/
					$id_padre = $parametros['id-proyecto'];

					$rows = DB::transaction(function() use ($ids,$id_padre){
						$componentes = Componente::where('idProyecto','=',$id_padre)->get();
						$desgloses = ComponenteDesglose::whereIn('idComponente',$componentes->lists('id'))->get();
						
						$beneficiarios_borrar = DesgloseBeneficiario::whereIn('idComponenteDesglose',$desgloses->lists('id'))
																	->whereIn('idTipoBeneficiario',$ids)->get();
						//
						$ajuste_totales = array();
						foreach ($beneficiarios_borrar as $item) {
							if(!isset($ajuste_totales[$item->idComponenteDesglose])){
								$ajuste_totales[$item->idComponenteDesglose] = array();
								$ajuste_totales[$item->idComponenteDesglose]['f'] = $item->totalF;
								$ajuste_totales[$item->idComponenteDesglose]['m'] = $item->totalM;
							}else{
								$ajuste_totales[$item->idComponenteDesglose]['f'] += $item->totalF;
								$ajuste_totales[$item->idComponenteDesglose]['m'] += $item->totalM;
							}
						}
						//$actualizar_desgloses = array();
						foreach ($desgloses as $desglose) {
							if(isset($ajuste_totales[$desglose->id])){
								$desglose->beneficiariosF -= $ajuste_totales[$desglose->id]['f'];
								$desglose->beneficiariosM -= $ajuste_totales[$desglose->id]['m'];
								$desglose->save();
								//$actualizar_desgloses[] = $desglose;
							}
						}

						DesgloseBeneficiario::whereIn('idComponenteDesglose',$desgloses->lists('id'))
											->whereIn('idTipoBeneficiario',$ids)
											->delete();
						//
						return Beneficiario::whereIn('idTipoBeneficiario',$ids)
									->where('idProyecto','=',$id_padre)
									->delete();
					});
				}elseif($parametros['eliminar'] == 'antecedente'){ //Eliminar Antecedente(s)
					/***
					*	Eliminar Antecedente Financiero (DELETE)
					*
					* 	- Borrar antecedentes finanacieros de una FIBAP
					*
					***/
					$id_padre = $parametros['id-fibap'];
					$rows = DB::transaction(function() use ($ids){
						//Eliminamos las actividades
						return AntecedenteFinanciero::wherein('id',$ids)->delete();
					});
				}
			}else{  //Sin parametros el delete viene de la lista de Proyectos de Inversión
				/***
				*	Eliminar Proyecto de Inversion (DELETE)
				*	-> FIBAP
				*	- Borrar la relacion de de documentos de soporte con las FIBAPs
				* 	- Borrar antecedentes finanacieros de las FIBAPs
				*	- Borrar la distribución del presupuesto
				*	- Borrar las propuestas de financiamiento
				*	- Borrar las acciones de las FIBAPs -
				*	- Borrar la relación de la acción con las partidas prespuestarias
				* 	- Borrar datos del desglose del componete (Metas,Beneficiarios,Localidad)
				*	- Borrar el desglose de metas por mes del componente
				*	- Borrar el desglose de beneficiarios del componente
				*
				*	-> Proyecto de Inversión
				*	- Borrar las metas por mes de los componentes del proyecto
				*	- Borrar las metas por mes de las actividades del proyecto
				*	- Borrar los componentes del proyecto
				*	- Borrar las actividades del proyecto
				*	- Borrar los beneficiarios asignados al proyecto
				*
				***/
				$rows = DB::transaction(function() use ($ids){
					//Eliminamos los datos del proyecto y los componentes, en caso de que el FIBAP no haya sido asignado a ningun proyecto
					$lista_proyectos = Proyecto::whereIn('id',$ids)->get();
					$lista_fibaps = FIBAP::with('documentos')->whereIn('idProyecto',$ids)->get();
					$ids_fibaps = $lista_fibaps->lists('id');
					//Eliminamos los documentos de soporte asignados al fibap
					foreach ($lista_fibaps as $fibap) {
						$fibap->documentos()->detach();
					}

					//Datos relacionados a la fibap
					AntecedenteFinanciero::whereIn('idFibap',$ids_fibaps)->delete();
					DistribucionPresupuesto::whereIn('idFibap',$ids_fibaps)->delete();
					PropuestaFinanciamiento::whereIn('idFibap',$ids_fibaps)->delete();

					$acciones = Accion::whereIn('idFibap',$ids_fibaps)->get();
					if(count($acciones) > 0){
						foreach ($acciones as $accion) {
							$accion->partidas()->detach();
						}

						$desgloses = ComponenteDesglose::whereIn('idAccion',$acciones->lists('id'))->get();
						if(count($desgloses) > 0){
							DesgloseMetasMes::whereIn('idComponenteDesglose',$desgloses->lists('id'))->delete();
							DesgloseBeneficiario::whereIn('idComponenteDesglose',$desgloses->lists('id'))->delete();
						}
						ComponenteDesglose::whereIn('idAccion',$acciones->lists('id'))->delete();
					}
					Accion::whereIn('idFibap',$ids_fibaps)->delete();

					FIBAP::whereIn('id',$ids_fibaps)->delete();

					//Eliminamos las metas por mes de los componentes y actividades
					ComponenteMetaMes::whereIn('idProyecto',$ids)->delete();
					ActividadMetaMes::whereIn('idProyecto',$ids)->delete();

					$ids_componentes = Componente::whereIn('idProyecto',$ids)->lists('id');
					if(count($ids_componentes) > 0){
						Actividad::whereIn('idComponente',$ids_componentes)->delete();
					}
					
					Componente::whereIn('idProyecto',$ids)->delete();
					Beneficiario::whereIn('idProyecto',$ids)->delete();

					return Proyecto::whereIn('id',$ids)->delete();
				});
			}

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
				if(isset($parametros['eliminar'])){
					if($parametros['eliminar'] == 'actividad'){
						/***
						*	Eliminar Actividad (DELETE)
						* 	- Se regresa lista de actividades
						***/
						$data['actividades'] = Actividad::with('usuario')->where('idComponente',$id_padre)->get();
					}elseif($parametros['eliminar'] == 'proyecto-beneficiario'){
						/***
						*	Eliminar Beneficiario (DELETE)
						* 	- Se regresa lista de beneficiarios
						***/
						$data['beneficiarios'] = Beneficiario::with('tipoBeneficiario')->where('idProyecto',$id_padre)->get();
					}elseif($parametros['eliminar'] == 'desglose-presupuesto'){
						/***
						*	Eliminar Desglose del Componente (DELETE)
						* 	- Se regresa el desglose del componente, la distribucion total del presupuesto por partida y el presupuesto requerido de la Accion(Componente)
						***/
						$accion = Accion::with('fibap.distribucionPresupuestoAgrupado.objetoGasto','desglosePresupuesto')
										->find($id_padre);
						$data['id_componete'] = $accion->idComponente;
						//$data['desglose_presupuesto'] = $accion->desglosePresupuesto;
						$data['distribucion_total'] = $accion->fibap->distribucionPresupuestoAgrupado;
						$data['presupuesto_requerido'] = $accion->presupuestoRequerido;
					}elseif($parametros['eliminar'] == 'antecedente'){
						/***
						*	Eliminar Antecedente del Proyecto (DELETE)
						* 	- Se regresa la lista de antecedentes
						***/
						$data['antecedentes'] = AntecedenteFinanciero::where('idFibap',$id_padre)->get();
					}elseif($parametros['eliminar'] == 'accion'){
						/***
						*	Eliminar Accion(Componente) del Proyecto (DELETE)
						* 	- Se regresa la lista de Acciones(Componentes), la distribucion total del presupuesto por partida
						***/
						$fibap = FIBAP::with('acciones','distribucionPresupuestoAgrupado.objetoGasto')->find($id_padre);
						$fibap->acciones->load('datosComponenteDetalle','propuestasFinanciamiento');
						$data['acciones'] = $fibap->acciones;
						$data['distribucion_total'] = $fibap->distribucionPresupuestoAgrupado;
					}
				}
			}else{
				$http_status = 404;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}	
		}catch(Exception $ex){
			$http_status = 500;	
			$data['code'] = 'S03';
			$data['ex'] = $ex->getMessage();
			if($data['data'] == ''){
				$data['data'] = "No se pueden borrar los registros";
			}
		}

		return Response::json($data,$http_status);
	}

	public function guardar_datos_desglose_presupuesto($parametros,$id = NULL){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		$es_editar = FALSE;

		$validacion = Validador::validar(Input::all(), $this->reglasPresupuesto);

		if($validacion === TRUE){
			
			$clave_jurisdiccion = $parametros['jurisdiccion-accion'];
			if($clave_jurisdiccion != 'OC'){
				$clave_municipio = $parametros['municipio-accion'];
				//La clave de la localidad se envia concatenada con la clave del municipio municipio|localidad
				$clave_localidad = explode('|',$parametros['localidad-accion']);
				$clave_localidad = $clave_localidad[1];
			}else{
				$clave_municipio = '0';
				$clave_localidad = '0';
			}

			if($id){
				$es_editar = TRUE;
				$desglose = ComponenteDesglose::with('metasMes','beneficiarios')->find($id);
				$accion_id = $desglose->idAccion;
			}else{
				$desglose = new ComponenteDesglose;
				$accion_id = $parametros['id-accion'];
				$desglose->claveMunicipio = '0';
				$desglose->claveLocalidad = '0';
				$desglose->claveJurisdiccion = '0';
			}
			
			$accion = Accion::with('distribucionPresupuesto','partidas')->find($accion_id);
			$fibap = FIBAP::find($accion->idFibap);

			if( $clave_localidad != $desglose->claveLocalidad || $clave_municipio != $desglose->claveMunicipio){
			//Se buscan si la Localidad ya fue capturada
				if($clave_jurisdiccion != 'OC'){
					$capturados = $accion->distribucionPresupuesto->filter(function($item) use ($clave_localidad, $clave_municipio){
						if($item->claveLocalidad == $clave_localidad && $item->claveMunicipio == $clave_municipio){
							return true;
						}
					});
				}else{
					//Si la jurisdiccion es Oficina Central se busca por jurisdicción
					$capturados = $accion->distribucionPresupuesto->filter(function($item) use ($clave_jurisdiccion){
						if($item->claveJurisdiccion == $clave_jurisdiccion){
							return true;
						}
					});
				}
				
				if(count($capturados)){
					$respuesta['data']['code'] = 'U00';
					if($clave_municipio){
						throw new Exception('{"field":"localidad-accion","error":"Esta localidad ya fue capturada para esta acción."}', 1);
					}else{
						throw new Exception('{"field":"jurisdiccion-accion","error":"Esta jurisdicción ya fue capturada para esta acción."}', 1);
					}
				}
			}

			$mes_incial = date("n",strtotime($fibap->periodoEjecucionInicio));
			$mes_final = date("n",strtotime($fibap->periodoEjecucionFinal));

			/******************************      Creación/Edición de elementos del desglose        *******************************/
			$accion_partidas = $accion->partidas->lists('id','id');
			$meses_capturados = $parametros['mes'];

			if($es_editar){
				$meses_ids = $parametros['meses-capturados']; //Elementos ya capturados por tanto se actualizaran
			}else{
				$meses_ids = array();
			}

			//Se calcula la suma total de la distribucion del presupeusto y se crea un arreglo con los elementos a almacenar
			$distribucion = array();
			$suma_presupuesto = 0;
			
			foreach ($meses_capturados as $mes => $partidas) {
				foreach ($partidas as $partida => $cantidad) {
					//Si la partida enviada desde el formulario se encuantra entre las partidas seleccionadas para el proyecto
					if(isset($accion_partidas[$partida])){
						if(isset($meses_ids[$mes][$partida])){
							$recurso = $accion->distribucionPresupuesto->find($meses_ids[$mes][$partida]);
							//Aqui me quede llenando la edicion de la distribucion del presupuesto
						}elseif($cantidad > 0 && $mes >= $mes_incial && $mes <= $mes_final){
							$recurso = new DistribucionPresupuesto;
							$recurso->idFibap = $accion->idFibap;
							$recurso->idObjetoGasto = $partida;
							$recurso->mes = $mes;
						}else{
							$recurso = FALSE;
						}

						if($recurso){
							$recurso->claveJurisdiccion = $clave_jurisdiccion;
							if($clave_jurisdiccion != 'OC'){
								$recurso->claveMunicipio = $clave_municipio;
								$recurso->claveLocalidad = $clave_localidad;
							}else{
								$recurso->claveMunicipio = NULL;
								$recurso->claveLocalidad = NULL;
							}
							$recurso->cantidad = $cantidad;
							$distribucion[] = $recurso;
							$suma_presupuesto += $cantidad;
						}
					}
				}
			}
			
			/******************************      Validación de presupuesto        *******************************/
			if($es_editar){
				$sumatoria = $accion->distribucionPresupuesto->filter(function($item) use ($clave_municipio,$clave_localidad){
					if($item->claveLocalidad != $clave_localidad || $item->claveMunicipio != $clave_municipio){
						return true;
					}
				});
				$suma_distribucion = $sumatoria->sum('cantidad');
			}else{
				$suma_distribucion = $accion->distribucionPresupuesto->sum('cantidad');
			}

			if(($suma_distribucion + $suma_presupuesto) > $accion->presupuestoRequerido){
				throw new Exception('{"field":"cantidad-presupuesto","error":"La distribución del presupuesto sobrepasa el presupuesto requerido."}', 1);
			}
			/******************************      Fin validación de presupuesto        *******************************/

			/******************************      Validación de beneficiarios        *******************************/
			$fibap->load('acciones','proyecto.beneficiarios');

			if($es_editar){
				$editar_beneficiarios = FALSE;
				$beneficiarios_f = $desglose->beneficiarios->lists('totalF','idTipoBeneficiario');
				$beneficiarios_m = $desglose->beneficiarios->lists('totalM','idTipoBeneficiario');
				
				foreach ($parametros['beneficiarios'] as $tipo_beneficiario => $desglose_sexo) {
					if($beneficiarios_f[$tipo_beneficiario] != $desglose_sexo['f'] || $beneficiarios_m[$tipo_beneficiario] != $desglose_sexo['m']){
						$editar_beneficiarios = TRUE;
					}
				}
			}else{
				$editar_beneficiarios = TRUE;
			}

			$beneficiarios_desglose = array();
			if($editar_beneficiarios){
				//Se obtienen todos los beneficiarios capturados para el proyecto
				$beneficiarios_capturados = ComponenteDesglose::whereIn('idAccion',$fibap->acciones->lists('id'))
											->groupBy('claveLocalidad','claveMunicipio');
				//Se filtran por localidad o jurisdicción, para obtener los beneficiarios de las diferentes localidades capturadas
				if($clave_jurisdiccion != 'OC'){
					$beneficiarios_capturados = $beneficiarios_capturados->where('claveLocalidad','!=',$clave_localidad)
																		->where('claveMunicipio','!=',$clave_municipio);
				}else{
					$beneficiarios_capturados = $beneficiarios_capturados->where('claveJurisdiccion','!=',$clave_jurisdiccion);
				}
				$beneficiarios_capturados = $beneficiarios_capturados->get();

				$suma_benef = array();
				$suma_total = array('f'=>0,'m'=>0);
				if(count($beneficiarios_capturados)){
					$beneficiarios_raw = DesgloseBeneficiario::whereIn('idComponenteDesglose',$beneficiarios_capturados->lists('id'))
															->select('idTipoBeneficiario',DB::raw('sum(totalF) AS totalF'),DB::raw('sum(totalM) AS totalM'))
															->groupBy('idTipoBeneficiario')
															->get();
					foreach ($beneficiarios_raw as $beneficiario) {
						$suma_benef[$beneficiario->idTipoBeneficiario]['f'] = $beneficiario->totalF;
						$suma_benef[$beneficiario->idTipoBeneficiario]['m'] = $beneficiario->totalM;
					}
				}else{
					$beneficiarios_ids = $fibap->proyecto->beneficiarios->lists('idTipoBeneficiario');
					foreach ($beneficiarios_ids as $value) {
						$suma_benef[$value] = array('f'=>0,'m'=>0);
					}
				}
				
				foreach ($parametros['beneficiarios'] as $tipo_beneficiario => $desglose_sexo) {
					if(isset($suma_benef[$tipo_beneficiario])){
						$suma_benef[$tipo_beneficiario]['f'] += $desglose_sexo['f'];
						$suma_benef[$tipo_beneficiario]['m'] += $desglose_sexo['m'];
						$suma_total['f'] += $desglose_sexo['f'];
						$suma_total['m'] += $desglose_sexo['m'];
					}
				}

				foreach ($fibap->proyecto->beneficiarios as $beneficiario) {
					if($suma_benef[$beneficiario->idTipoBeneficiario][$beneficiario->sexo] > $beneficiario->total){
						throw new Exception('{"field":"beneficiarios-'.$beneficiario->idTipoBeneficiario.'-'.$beneficiario->sexo.'","error":"La cantidad especificada sobrepasa al limite de beneficiarios especificados para el proyecto."}', 1);
					}
				}

				/******************************      Fin validación de beneficiarios        *******************************/

				foreach ($parametros['beneficiarios'] as $tipo_beneficiario => $desglose_sexo) {
					if($es_editar){
						$desglose_benef = $desglose->beneficiarios->filter(function($item) use ($tipo_beneficiario){
							if($item->idTipoBeneficiario == $tipo_beneficiario){ return true; }
						})->first();
						//$desglose_benef = $desglose_benef[0];
					}else{
						$desglose_benef = FALSE;
					}
					if(!$desglose_benef){
						$desglose_benef = new DesgloseBeneficiario;
						$desglose_benef->idTipoBeneficiario = $tipo_beneficiario;
					}
					$desglose_benef->totalF = $desglose_sexo['f'];
					$desglose_benef->totalM = $desglose_sexo['m'];

					$beneficiarios_desglose[] = $desglose_benef;
				}
			}

			$suma_metas = 0;
			$trimestres = array( 1=>0 , 2=>0 , 3=>0 , 4=>0);
			$metas_mes = array();
			$metas_anteriores = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0); //Para obtener la diferencia

			if($es_editar && isset($parametros['metas-capturadas'])){
				$metas_ids = $parametros['metas-capturadas']; //Elementos ya capturados por tanto se actualizaran
			}else{
				$metas_ids = array();
			}

			foreach ($parametros['meta-mes'] as $mes => $meta) {
				if(isset($metas_ids[$mes])){
					$recurso = $desglose->metasMes->find($metas_ids[$mes]);
					
					$metas_anteriores[$mes] = $recurso->meta;
					$recurso->meta = $meta;

					$metas_mes[$mes] = $recurso;
					$trimestres[ceil(($mes/3))] += $meta;
					$suma_metas += $meta;
				}elseif($meta > 0 ){
					$recurso = new DesgloseMetasMes;
					$recurso->mes = $mes;
					$recurso->meta = $meta;

					$metas_mes[$mes] = $recurso;
					$trimestres[ceil(($mes/3))] += $meta;
					$suma_metas += $meta;
				}
			}

			$componente = $accion->componente;
			$componente->valorNumerador = $suma_metas;
			$componente->numeroTrim1 = $trimestres[1];
			$componente->numeroTrim2 = $trimestres[2];
			$componente->numeroTrim3 = $trimestres[3];
			$componente->numeroTrim4 = $trimestres[4];

			//Obtenemos las metas por mes del componente de la jurisdicción a capturar, estos serían el concentrado de mestas del desglose
			$componente_metas_mes_capturados = ComponenteMetaMes::where('idComponente','=',$accion->idComponente)
																->where('claveJurisdiccion','=',$clave_jurisdiccion)
																->get();
			$componente_metas_mes = array();
			if(count($componente_metas_mes_capturados)){
				//Con las metas por mes ya capturadas, buscamos las que vienen del formulario para actualizar
				foreach ($componente_metas_mes_capturados as $mes_capturado) {
					if(isset($metas_mes[$mes_capturado->mes])){
						$mes_capturado->meta += ($metas_mes[$mes_capturado->mes]->meta - $metas_anteriores[$mes_capturado->mes]);
						$componente_metas_mes[$mes_capturado->mes] = $mes_capturado;
					}
				}
			}

			foreach ($metas_mes as $meta) {
				//las que no se hayan actualizado se crean
				if(!isset($componente_metas_mes[$meta->mes])){
					$meta_mes = new ComponenteMetaMes;
					$meta_mes->idProyecto = $fibap->proyecto->id;
					$meta_mes->mes = $meta->mes;
					$meta_mes->meta = $meta->meta;
					$meta_mes->claveJurisdiccion = $clave_jurisdiccion;

					$componente_metas_mes[$meta->mes] = $meta_mes;
				}
				
			}
			//$respuesta['data']['data'] = json_encode($componente_metas_mes);
			//throw new Exception(json_encode($componente_metas_mes_capturados), 1);
			
			$desglose->idAccion = $accion->id;
			$desglose->claveJurisdiccion = $clave_jurisdiccion;
			if($clave_jurisdiccion != 'OC'){
				$desglose->claveMunicipio 	= $clave_municipio;
				$desglose->claveLocalidad 	= $clave_localidad;
			}
			$desglose->presupuesto 		= $suma_presupuesto;
			if($editar_beneficiarios){
				$desglose->beneficiariosF 	= $suma_total['f'];
				$desglose->beneficiariosM 	= $suma_total['m'];
			}
			
			$respuesta['data'] = DB::transaction(function() use ($accion,$distribucion,$desglose,$metas_mes,$beneficiarios_desglose,$componente_metas_mes,$componente){
				$componente->save();

				$accion->componente->desglose()->save($desglose);

				$accion->componente->metasMes()->saveMany($componente_metas_mes);

				if(count($distribucion)){
					$accion->distribucionPresupuesto()->saveMany($distribucion);
				}

				if(count($metas_mes)){
					$desglose->metasMes()->saveMany($metas_mes);
				}

				if(count($beneficiarios_desglose)){
					$desglose->beneficiarios()->saveMany($beneficiarios_desglose);
				}
				
				//$accion->load('desglosePresupuesto');
				return array('data'=>$accion);
			});

			$fibap->load('distribucionPresupuestoAgrupado.objetoGasto');
			$respuesta['data']['extras']['distribucion_total'] = $fibap->distribucionPresupuestoAgrupado;
		}else{
			$respuesta['http_status'] 	= $validacion['http_status'];
			$respuesta['data'] 			= $validacion['data'];
		}
		return $respuesta;
	}

	public function guardar_datos_accion_presupuesto($parametros,$componente,$id = NULL){
		$es_editar = FALSE;
		$respuesta = DB::transaction(function() use ($parametros,$componente,$id,$es_editar){
			$valores_respuesta = array();
			$fibap = FIBAP::find($parametros['id-fibap']);
			if($fibap){
				if($id){
					$accion = Accion::with('propuestasFinanciamiento','partidas','componente')
								->where('idComponente','=',$id)->first();
					$es_editar = TRUE;
				}else{
					$accion = new Accion;
					$accion->idComponente = $componente->id;
				}
				
				/********************************       Partidas Presupuestarias       *********************************/
				
				$partidas_formulario = $parametros['objeto-gasto-presupuesto'];
				if($es_editar){
					$partidas_anteriores = $accion->partidas->lists('id');
				}else{
					$partidas_anteriores = array();
				}

				/*if($partidas_formulario[0] == $partidas_formulario[1]){ //ciclo
					throw new Exception('{"field":"objeto-gasto-presupuesto","error":"Las partidas no deben ser iguales"}', 1);
				}*/

				//Sacamos las diferencias de las partidas seleccionadas y las ya capturadas
				$partidas['nuevas'] = array_diff($partidas_formulario, $partidas_anteriores);
				$partidas['borrar'] = array_diff($partidas_anteriores, $partidas_formulario);

				/********************       Origenes del presupuesto y presupuesto Requerido      ************************/
				//Obtenemos los origenes del presupuesto
				$origenes = $parametros['accion-origen'];
				$origenes_ids = array();
				if(isset($parametros['origen-captura-id'])){
					$origenes_ids = $parametros['origen-captura-id'];
				}

				//Arreglo con los objetos a guardar en la base de datos, relacionados a la Accion
				$guardar_origenes = array();
				$presupuesto_suma = 0;
				foreach ($origenes as $origen => $valor) {
					if(isset($origenes_ids[$origen])){
						$origen_finan = $accion->propuestasFinanciamiento()->find($origenes_ids[$origen]);
						$origen_finan->cantidad = ($valor)? $valor:0;
						$guardar_origenes[] = $origen_finan;
					}elseif($valor > 0){
						$origen_finan = new PropuestaFinanciamiento;
						$origen_finan->idOrigenFinanciamiento = $origen;
						$origen_finan->cantidad = $valor;
						$origen_finan->idFibap = $fibap->id;
						$guardar_origenes[] = $origen_finan;
					}
					$presupuesto_suma += $valor;
				}

				if($es_editar){
					if($presupuesto_suma != $accion->presupuestoRequerido){
						//Obtenemos la suma de los presupuestos ya capturados y sumamos el nuevo presupuesto
						$total_presupuesto = $fibap->acciones->sum('presupuestoRequerido');
						$total_presupuesto += $presupuesto_suma;
						//Quitamos el presupuesto anterior, de lo contrario sumara un presupuesto de más
						$total_presupuesto -= $accion->presupuestoRequerido;

						if($total_presupuesto > $fibap->presupuestoRequerido){
							throw new Exception('{"field":"accion-presupuesto-requerido","error":"El presupuesto capturado sobrepasa el Presupuesto Requerido asignado al proyecto."}', 1);
						}

						$accion->presupuestoRequerido = $presupuesto_suma;
					}
				}else{
					//Obtenemos la suma de los presupuestos ya capturados y sumamos el nuevo presupuesto
					$total_presupuesto = $fibap->acciones->sum('presupuestoRequerido');
					$total_presupuesto += $presupuesto_suma;

					if($total_presupuesto > $fibap->presupuestoRequerido){
						throw new Exception('{"field":"accion-presupuesto-requerido","error":"El presupuesto capturado sobrepasa el Presupuesto Requerido asignado al proyecto."}', 1);
					}

					$accion->presupuestoRequerido = $presupuesto_suma;
				}
				
				if($fibap->acciones()->save($accion)){
					//$distribucion_total = NULL;
					if(count($partidas['borrar'])){
						$restar_de_distribucion = array();
						$distribuciones = $accion->distribucionPresupuesto()
												 ->whereIn('idObjetoGasto',$partidas['borrar'])->get();
						if(count($distribuciones) > 0){
							foreach ($distribuciones as $distribucion) {
								if(isset($restar_de_distribucion[$distribucion->claveMunicipio][$distribucion->claveLocalidad])){
									$restar_de_distribucion[$distribucion->claveMunicipio][$distribucion->claveLocalidad] += $distribucion->cantidad;
								}else{
									$restar_de_distribucion[$distribucion->claveMunicipio][$distribucion->claveLocalidad] = $distribucion->cantidad;
								}
							}
							$desgloses = ComponenteDesglose::where('idAccion','=',$accion->id)->get();
							$desgloses_editar = array();
							foreach ($desgloses as $desglose) {
								if(isset($restar_de_distribucion[$desglose->claveMunicipio][$desglose->claveLocalidad])){
									$desglose->presupuesto -= $restar_de_distribucion[$desglose->claveMunicipio][$desglose->claveLocalidad];
									$desgloses_editar[] = $desglose;
								}
							}

							$accion->componente->desglose()->saveMany($desgloses_editar);
							$accion->distribucionPresupuesto()->whereIn('idObjetoGasto',$partidas['borrar'])->delete();

							$fibap->load('distribucionPresupuestoAgrupado.objetoGasto');
							$valores_respuesta['distribucion_total'] = $fibap->distribucionPresupuestoAgrupado;
							//$distribucion_total = $fibap->distribucionPresupuestoAgrupado;
						}
						$accion->partidas()->detach($partidas['borrar']);
					}
					if(count($partidas['nuevas'])){
						$accion->partidas()->attach($partidas['nuevas']);
					}
					
					$accion->propuestasFinanciamiento()->saveMany($guardar_origenes);
				}
			}else{
				throw new Exception("No se pudo encontrar la FIBAP", 1);
			}
			$fibap->load('acciones.datosComponenteDetalle');
			$fibap->acciones->load('propuestasFinanciamiento');

			$valores_respuesta['acciones'] = $fibap->acciones;
			$valores_respuesta['data'] = $accion;

			return $valores_respuesta;
		});
		return $respuesta;
	}

	private function validar_fechas($fecha_inicial, $fecha_final){
		$fecha_inicio = DateTime::createFromFormat('d/m/Y',$fecha_inicial);
		$fecha_fin = DateTime::createFromFormat('d/m/Y',$fecha_final);

		if(!$fecha_inicio){
			$fecha_inicio = DateTime::createFromFormat('Y-m-d',Input::get('periodo-ejecucion-inicio'));
		}

		if(!$fecha_fin){
			$fecha_fin = DateTime::createFromFormat('Y-m-d',Input::get('periodo-ejecucion-final'));
		}

		if(!$fecha_inicio){
			throw new Exception('{"field":"periodo-ejecucion-inicio","error":"La fecha de inicio del periodo de ejecución no tiene el formato correcto."}');
		}

		if(!$fecha_fin){
			throw new Exception('{"field":"periodo-ejecucion-final","error":"La fecha final del periodo de ejecución no tiene el formato correcto."}');
		}

		if($fecha_fin < $fecha_inicio){
			throw new Exception('{"field":"periodo-ejecucion-final","error":"La fecha final del periodo de ejecución no puede ser menor que la de inicio."}');
		}
		return array('inicio'=>$fecha_inicio, 'fin'=>$fecha_fin);
	}
}