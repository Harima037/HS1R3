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
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime, File;
use Proyecto, Componente, Actividad, Beneficiario, FIBAP, ComponenteMetaMes, ActividadMetaMes, Region, Municipio, Jurisdiccion, 
	FibapDatosProyecto, Directorio, ComponenteDesglose, AntecedenteFinanciero, DesgloseMetasMes, DistribucionPresupuesto,Accion,
	PropuestaFinanciamiento, DesgloseBeneficiario, ProyectoFinanciamiento, ProyectoFinanciamientoSubFuente, 
	ActividadDesglose, ActividadDesgloseBeneficiario, ActividadDesgloseMetasMes;

class InversionController extends ProyectosController {
	private $reglasFibap = array(
		/*
		'organismo-publico'			=> 'required',
		'sector'					=> 'required',
		'subcomite'					=> 'required',
		'grupo-trabajo'				=> 'required',
		'justificacion-proyecto'	=> 'required',
		'descripcion-proyecto'		=> 'required',
		'objetivo-proyecto'			=> 'required',
		'alineacion-especifica'		=> 'required',*/
		'presupuesto-requerido'		=> 'required',
		'periodo-ejecucion-inicio'	=> 'required',
		'periodo-ejecucion-final'	=> 'required'
		//'documento-soporte'			=> 'required|array|min:1'
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
		//'total-beneficiarios'		=> 'required',
		//'beneficiarios'				=> 'required|array|min:1',
		'jurisdiccion-accion'		=> 'required',
		'municipio-accion'			=> 'required_if:jurisdiccion-accion,01,02,03,04,05,06,07,08,09,10',
		'localidad-accion'			=> 'required_if:jurisdiccion-accion,01,02,03,04,05,06,07,08,09,10',
		'cantidad-meta'				=> 'required|numeric|min:0.001',
		//'cantidad-presupuesto'		=> 'required|numeric|min:1',
		'meta-mes'					=> 'required|array|min:1',
		//'mes'						=> 'required|array|min:1'
	);

	private $reglasAccionPresupuesto = array(
		'accion-presupuesto-requerido'	=> 'numeric',
		'objeto-gasto-presupuesto'		=> 'array'
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
				if($parametros['nivel'] == 'componente'){
					$rows = ComponenteDesglose::listarDatos()->where('idAccion','=',$parametros['idAccion']);
					$totales = ComponenteDesglose::listarDatos()->where('idAccion','=',$parametros['idAccion']);
					$table = 'componenteDesglose';
				}else{
					$rows = ActividadDesglose::listarDatos()->where('idAccion','=',$parametros['idAccion']);
					$totales = ActividadDesglose::listarDatos()->where('idAccion','=',$parametros['idAccion']);
					$table = 'actividadDesglose';
				}
				
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
				}
				//$totales = $rows;
				$totales = $totales->select(DB::raw('count('.$table.'.id) AS cuantos'),DB::raw('sum('.$table.'.presupuesto) AS totalPresupuesto'))->first();
				//$totales = $totales[0];
				
				$total = $totales->cuantos;
				$recurso['resultados'] = $totales->cuantos;
				$recurso['total_presupuesto'] = $totales->totalPresupuesto;
				$recurso['data'] = $rows->orderBy($table.'.id', 'desc')
							->skip(($pagina-1)*10)->take(10)
							->get();
				//$queries = DB::getQueryLog();
				//var_dump(end($queries),true);die;
				$data = $recurso;
			}else{
				$rows = Proyecto::getModel();
				$rows = $rows->where('idClasificacionProyecto','=',2)
							->whereIn('idEstatusProyecto',[1,2,3,4,5]);

				$usuario = Sentry::getUser();
				
				if($usuario->idDepartamento != 3){
					if($usuario->filtrarCaratulas){
						$rows = $rows->where('proyectos.idUsuarioCaptura','=',$usuario->id);
					}
				}else{
					$rows = $rows->where('proyectos.idUsuarioCaptura','=',$usuario->id);
				}
				
				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}
				
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}
				
				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),'fibap.presupuestoRequerido',
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
			
			//return Response::json($data,$http_status);
		}elseif(isset($parametros['listar'])){
			if($parametros['listar'] == 'municipios'){
				//listar municipios
				$id_proyecto = $parametros['id-proyecto'];
				$clave_jurisdiccion = $parametros['jurisdiccion'];

				$proyecto = Proyecto::find($id_proyecto);
				if($proyecto->idCobertura == 1){ 
				//Cobertura Estado => Todos las Jurisdicciones
					$jurisdiccion = Jurisdiccion::with(array('municipios'=>function($query){
						$query->select('idJurisdiccion','idEntidad','idRegion','id','clave','nombre');
					}))->where('clave','=',$clave_jurisdiccion)->first();
					$recurso = $jurisdiccion->municipios;
				}elseif($proyecto->idCobertura == 2){ 
				//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
					$recurso = Municipio::select('idJurisdiccion','idEntidad','idRegion','id','clave','nombre')
										->where('clave','=',$proyecto->claveMunicipio)->get();
				}elseif($proyecto->idCobertura == 3){ 
				//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
					$jurisdiccion = Jurisdiccion::where('clave','=',$clave_jurisdiccion)->first();

					$region = Region::with(array('municipios'=>function($query) use ($jurisdiccion){
						$query->select('idJurisdiccion','idEntidad','idRegion','id','clave','nombre')
							->where('idJurisdiccion','=',$jurisdiccion->id);
					}))->where('region','=',$proyecto->claveRegion)->first();
					$recurso = $region->municipios;
				}
			}elseif($parametros['listar'] == 'localidades'){
				//listar localidades
				$municipio = $parametros['municipio'];
				$recurso = Municipio::with(array('localidades'=>function($query){
					$query->select('id','idMunicipio','clave','nombre','idEntidad');
				}))->where('clave','=',$municipio)->first();
				$recurso = $recurso->localidades;
			}
			$data = array('data'=>$recurso);
			//return Response::json($data,$http_status);
		}else{
			$rows = Proyecto::all();
			if(count($rows) == 0){
				$http_status = 404;
				$data = array("data"=>"No hay datos",'code'=>'W00');
			}else{
				$data = array("data"=>$rows->toArray());
			}
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

		try{

			if(isset($parametros['mostrar'])){
				if($parametros['mostrar'] == 'detalles-proyecto'){
					/*$data['extra'] = Proyecto::contenidoReporte()
										->with('componentesCompletoDescripcion.metasMes','beneficiariosDescripcion')
										->find($id);
					$queries = count(DB::getQueryLog());
					$data['total_queries_reporte'] = $queries;
					*/
					$recurso = Proyecto::contenidoCompleto()->find($id);
					if($recurso){
						$recurso->load('fibap');
						if($recurso->fibap){
							$recurso->fibap->load('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado');
							$recurso->fibap->distribucionPresupuestoAgrupado->load('objetoGasto');
						}
						$recurso->componentes->load('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable','entregableTipo','entregableAccion');
						foreach ($recurso->componentes as $key => $componente) {
							$recurso->componentes[$key]->actividades->load('formula','dimension','frecuencia','tipoIndicador','unidadMedida');
						}
					}
					/*$queries = count(DB::getQueryLog()) - $queries;
					$data['total_queries'] = $queries;*/
				}elseif($parametros['mostrar'] == 'editar-proyecto'){
					$recurso = Proyecto::with('jefeInmediato','liderProyecto','jefePlaneacion','coordinadorGrupoEstrategico',
										'fibap.documentos','beneficiarios.tipoBeneficiario',
										'fuentesFinanciamiento.fondoFinanciamiento',
										'fuentesFinanciamiento.fuenteFinanciamiento',
										'fuentesFinanciamiento.subFuentesFinanciamiento')
										->find($id);
					if($recurso->idEstatusProyecto == 3){
						$recurso->load('comentarios');
					}
					if($recurso->fibap){
						$recurso->fibap->load('antecedentesFinancieros','acciones','distribucionPresupuestoAgrupado.objetoGasto');
						$recurso->fibap->acciones->load('propuestasFinanciamiento','datosComponenteDetalle','datosActividadDetalle');
					}
					if(!is_null($recurso)){
						$extras = array();
						if($recurso->idCobertura == 1){ 
						//Cobertura Estado => Todos las Jurisdicciones
							$extras['jurisdicciones'] = Jurisdiccion::all();
						}elseif($recurso->idCobertura == 2){ 
						//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
							$extras['jurisdicciones'] = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
						}elseif($recurso->idCobertura == 3){ 
						//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
							$extras['jurisdicciones'] = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
						}
						$extras['responsables'] = Directorio::responsablesActivos($recurso->unidadResponsable)->get();
						$data["extras"] = $extras;
					}

				}elseif($parametros['mostrar'] == 'editar-beneficiario'){
					$recurso = Beneficiario::where('idProyecto','=',$parametros['id-proyecto'])
											->where('idTipoBeneficiario','=',$id)->get();
				}elseif ($parametros['mostrar'] == 'editar-antecedente') {
					$recurso = AntecedenteFinanciero::find($id);
				}elseif ($parametros['mostrar'] == 'editar-accion') {
					//$recurso = Accion::with('componente.actividades.unidadMedida','componente.metasMes','partidas','propuestasFinanciamiento')->find($id);
					$recurso = Accion::with('componente.metasMes','actividad.metasMes','partidas','propuestasFinanciamiento')->find($id);
				}elseif ($parametros['mostrar'] == 'desglose-accion') {
					$recurso = Accion::with('datosComponenteDetalle','datosActividadDetalle','partidas')->find($id);
				}elseif ($parametros['mostrar'] == 'editar-presupuesto'){
					/***
					*	Obtiene los datos del desglose del presupuesto de la Accion (GET)
					*
					*	- Obtiene la distribución de presupuesto concentrado por mes y partida
					* 	- Obtiene los datos del desglose del componete (Metas,Beneficiarios,Localidad)
					*	- Obtiene el desglose de metas por mes del componente
					*	- Obtiene datos generales de la accion
					*	- Obtiene la lista de municipios de la jurisdiccion seleccionada (Para llenar los selects)
					*	- Obtiene la lista de localiades del municipio seleccionado (Para llenar los selects)
					*
					***/
					if($parametros['nivel'] == 'componente'){
						$desglose = ComponenteDesglose::with('metasMes','beneficiarios')->find($id);
					}else{
						$desglose = ActividadDesglose::with('metasMes','beneficiarios')->find($id);
					}
					

					$recurso = Accion::with('partidas')->find($desglose->idAccion);
					
					$calendarizado = DistribucionPresupuesto::where('idAccion','=',$desglose->idAccion)
														->whereIn('idObjetoGasto',$recurso->partidas->lists('id'))
														->where('claveJurisdiccion','=',$desglose->claveJurisdiccion);
					if($desglose->claveJurisdiccion != 'OC'){
						$calendarizado = $calendarizado->where('claveMunicipio','=',$desglose->claveMunicipio)
													->where('claveLocalidad','=',$desglose->claveLocalidad);
					}
					$calendarizado = $calendarizado->get();

					$recurso['desglose'] = $desglose;
					$recurso['calendarizado'] = $calendarizado;

					/*
						Para obtener el grupo de municipios de la jurisdiccion seleccionada  y las localidades del
						municipio selccionado
					*/

					$municipio = Municipio::where('clave','=',$desglose->claveMunicipio)->first();

					$proyecto = Proyecto::find($parametros['id-proyecto']);
					if($proyecto->idCobertura == 1){ 

					//Cobertura Estado => Todos las Jurisdicciones
						$jurisdiccion = Jurisdiccion::with(array('municipios'=>function($query){
							$query->select('idJurisdiccion','idEntidad','idRegion','id','clave','nombre');
						},'municipios.localidades'=>function($query) use ($municipio){
							$query->select('idMunicipio','idEntidad','id','clave','nombre')
									->where('idMunicipio','=',$municipio->id);
						}))->where('clave','=',$desglose->claveJurisdiccion)->first();
						
						if($jurisdiccion){
							$recurso['municipios'] = $jurisdiccion->municipios;
						}else{
							$recurso['municipios'] = array();
						}
						
					}elseif($proyecto->idCobertura == 2){ 

					//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$recurso['municipios'] = Municipio::select('idJurisdiccion','idEntidad','idRegion','id','clave','nombre')
															->with(array('localidades'=>function($query) use ($municipio){
																$query->select('idMunicipio','idEntidad','id','clave','nombre')
																		->where('idMunicipio','=',$municipio->id);
															}))->where('clave','=',$desglose->claveMunicipio)->get();

					}elseif($proyecto->idCobertura == 3){ 

					//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$jurisdiccion = Jurisdiccion::where('clave','=',$desglose->claveJurisdiccion)->first();
						
						if($jurisdiccion){
							$region = Region::with(array('municipios'=>function($query) use ($jurisdiccion){
								$query->select('idJurisdiccion','idEntidad','idRegion','id','clave','nombre')
										->where('idJurisdiccion','=',$jurisdiccion->id);
							},'municipios.localidades'=>function($query) use ($municipio){
								$query->select('idMunicipio','idEntidad','id','clave','nombre')
										->where('idMunicipio','=',$municipio->id);
							}))->where('region','=',$proyecto->claveRegion)->first();
							$recurso['municipios'] = $region->municipios;
						}else{
							$recurso['municipios'] = array();
						}
						
					}
				}
			}elseif(isset($parametros['ver'])){
				if($parametros['ver'] == 'financiamiento'){
					$recurso = ProyectoFinanciamiento::with('subFuentesFinanciamiento')->find($id);
				}
			}

			if(is_null($recurso)){
				$http_status = 404;
				$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}else{
				$data['data'] = $recurso;
			}

		}catch(\Exception $ex){
			$http_status = 500;	
			if(isset($data['data'])){
				if($data['data'] == ''){
					$data['data'] = 'Ocurrio un error al intentar mostrar los datos';
				}
			}else{
				$data['data'] = 'Ocurrio un error al intentar mostrar los datos';
			}
			
			if(strpos($ex->getMessage(), '{"field":') !== FALSE){
				$data['code'] = 'U00';
				$data['data'] = $ex->getMessage();
			}else{
				$data['ex'] = $ex->getMessage();
			}
			$data['line'] = $ex->getLine();
			if(!isset($data['code'])){
				$data['code'] = 'S03';
			}
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
					}elseif($recurso->idCobertura == 2){ 
					//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$extras['jurisdicciones'] = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
					}elseif($recurso->idCobertura == 3){ 
					//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$extras['jurisdicciones'] = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
					}
					$responsables = Directorio::responsablesActivos($recurso->unidadResponsable)->get();
					$extras['responsables'] = $responsables;
					$respuesta['data']['extras'] = $extras;
					$recurso['liderProyecto'] = $respuesta['data']['nombre-lider-proyecto'];
				}
			}elseif($parametros['guardar'] == 'accion'){
				$parametros['clasificacion'] = 2;
				$respuesta = parent::guardar_datos_componente('accion',$parametros);
				//
				if($respuesta['http_status'] == 200){
					$elemento_accion = $respuesta['data']['data']; //Puede ser actividad o componente
					$resultado = $this->guardar_datos_accion_presupuesto($parametros,$elemento_accion->id);
					if($resultado['data']){
						$respuesta['data']['data'] = $resultado['data'];
					}
					$respuesta['data']['acciones'] = $resultado['acciones'];
				}
			}elseif($parametros['guardar'] == 'proyecto-beneficiario'){
				$respuesta = parent::guardar_datos_beneficiario($parametros);
			}elseif ($parametros['guardar'] == 'datos-fibap') {
				//Guardar nuevo FIBAP
				$validacion = Validador::validar(Input::all(), $this->reglasFibap);

				if($validacion === TRUE){
					
					$fechas = $this->validar_fechas($parametros['periodo-ejecucion-inicio'],$parametros['periodo-ejecucion-final']);

					$recurso = new FIBAP;

					//$recurso->claveUnidadResponsable = Sentry::getUser()->claveUnidad;
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
					
					if(isset($parametros['documento-soporte'])){
						$documentos = $parametros['documento-soporte'];
					}else{
						$documentos = array();
					}
					
					$respuesta['data'] = DB::transaction(function() use ($recurso, $documentos){
						if($recurso->save()){
							if(count($documentos)){
								$recurso->documentos()->attach($documentos);
							}
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
			}elseif($parametros['guardar'] == 'cargar-archivo-desglose'){
				/***
				*	Carga los datos del desglose del componente ya sea programación de Metas, Beneficiarios o Presupueeto
				*
				*	- Si el desglose ya se encuentra capturado lo actualiza.
				* 	- Si el tipo de archivo es el correcto almacena la programación de metas por mes.
				***/
				$respuesta = $this->importar_archivo($parametros);
			}elseif($parametros['guardar'] == 'financiamiento'){
				$respuesta = parent::guardar_datos_financiamiento($parametros);
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
				//if($parametros['guardar'] != 'proyecto'){
				if(isset($parametros['id-proyecto'])){
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
					}elseif($recurso->idCobertura == 2 && $recurso->claveMunicipio != $datos_anteriores['claveMunicipio']){ 
					//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$extras['jurisdicciones'] = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
					}elseif($recurso->idCobertura == 3 && $recurso->claveRegion != $datos_anteriores['claveRegion']){ 
					//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$extras['jurisdicciones'] = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
					}

					if(count($extras)){
						if($recurso->fibap){
							$recurso->fibap->load('acciones.datosComponenteDetalle','distribucionPresupuestoAgrupado.objetoGasto');
							$recurso->fibap->acciones->load('propuestasFinanciamiento');
						}
					}

					if(isset($respuesta['data']['nombre-lider-proyecto'])){
						$responsables = Directorio::responsablesActivos($recurso->unidadResponsable)->get();
						$extras['responsables'] = $responsables;
					}

					$respuesta['data']['extras'] = $extras;
					if(isset($respuesta['data']['nombre-lider-proyecto'])){
						$recurso['liderProyecto'] = $respuesta['data']['nombre-lider-proyecto'];
					}
				}
			}elseif($parametros['guardar'] == 'fuenteinformacion'){
				$respuesta = parent::guardar_fuente_informacion($parametros,$id);
			}elseif($parametros['guardar'] == 'proyecto-beneficiario'){
				$respuesta = parent::guardar_datos_beneficiario($parametros,$id);
			}elseif($parametros['guardar'] == 'accion'){
				/***
				*	Editar datos generales de un acción (Componente o Actividad) (PUT)
				*
				*	- Actualiza datos del componente o de la actividad
				*	- Actualiza datos de la acción
				*	- En caso de eliminar partidas se recalculan los montos capturados en el desglose y se eliminana aquellos asignados a dichas partidas
				*
				***/
				$parametros['clasificacion'] = 2;
				if($parametros['nivel'] == 'componente'){
					$id_elemento = $parametros['id-componente']; 
				}else{
					$id_elemento = $parametros['id-actividad'];
				}
				$respuesta = parent::guardar_datos_componente('accion',$parametros,$id_elemento);
				//
				if($respuesta['http_status'] == 200){
					$elemento_accion = $respuesta['data']['data']; //Puede ser actividad o componente
					$resultado = $this->guardar_datos_accion_presupuesto($parametros,$elemento_accion->id);
					if($resultado['data']){
						$respuesta['data']['data'] = $resultado['data'];
					}
					$respuesta['data']['acciones'] = $resultado['acciones'];
					if(isset($resultado['distribucion_total'])){
						$respuesta['data']['distribucion_total'] = $resultado['distribucion_total'];
					}
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

					//$recurso->claveUnidadResponsable = Sentry::getUser()->claveUnidad;
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
					
					if(isset($parametros['documento-soporte'])){
						$documentos = $parametros['documento-soporte'];
					}else{
						$documentos = array();
					}
					
					$documentos_anteriores = $recurso->documentos->lists('id');

					$docs['nuevos'] = array_diff($documentos, $documentos_anteriores);
					$docs['borrar'] = array_diff($documentos_anteriores, $documentos);

					$respuesta['data'] = DB::transaction(function() use ($recurso, $docs, $periodo_ant){
						if($recurso->save()){
							if(count($docs['borrar'])){
								$recurso->documentos()->detach($docs['borrar']);
							}
							if(count($docs['nuevos'])){
								$recurso->documentos()->attach($docs['nuevos']);
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
									$desgloses_completo_componente = ComponenteDesglose::whereIn('idAccion',$recurso->acciones->lists('id'))->get();
									if(count($desgloses_completo_componente)){
										//Eliminamos las metas por mes del desglose de los componentes
										DesgloseMetasMes::whereIn('idComponenteDesglose',$desgloses_completo_componente->lists('id'))
														->where(function($query) use ($mes){
															$query->where('mes','<',$mes['inicio'])
																->orWhere('mes','>',$mes['fin']);
														})->delete();
									}
									
									$desgloses_completo_actividad = ActividadDesglose::whereIn('idAccion',$recurso->acciones->lists('id'))->get();
									if(count($desgloses_completo_actividad)){
										//Eliminamos las metas por mes del desglose de las actividades
										ActividadDesgloseMetasMes::whereIn('idActividadDesglose',$desgloses_completo_actividad->lists('id'))
														->where(function($query) use ($mes){
															$query->where('mes','<',$mes['inicio'])
																->orWhere('mes','>',$mes['fin']);
														})->delete();
									}
									
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
									$suma_presupuesto_eliminado = 0;
									foreach ($desgloses_completo_componente as $desglose) {
										if(isset($cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad])){
											$suma_presupuesto_eliminado += $cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad]; 
											//$desglose->presupuesto -= $cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad];
											//$desglose->save();
										}
									}
									foreach ($desgloses_completo_actividad as $desglose) {
										if(isset($cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad])){
											$suma_presupuesto_eliminado += $cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad];
											//$desglose->presupuesto -= $cantidades_desglose[$desglose->idAccion.'.'.$desglose->claveJurisdiccion.'.'.$desglose->claveMunicipio.'.'.$desglose->claveLocalidad];
											//$desglose->save();
										}
									}
									$desglose->presupuesto -= $suma_presupuesto_eliminado; 
									$desglose->save();
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
				*	Formulario de datos del desglose del presupuesto de la Accion (PUT)
				*
				*	- Guarda una nueva distribución de presupuesto por mes y partida
				* 	- Guarda datos del desglose del componete (datos a exportar al proyecto) (Metas,Beneficiarios,Localidad)
				*	- Guardar el desglose de metas por mes del componente
				***/
				$respuesta = $this->guardar_datos_desglose_presupuesto($parametros,$id);
			}elseif($parametros['guardar'] == 'financiamiento'){
				$respuesta = parent::guardar_datos_financiamiento($parametros,$id);
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
				$respuesta['data']['line'] = $ex->getLine();
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
		$data = array('data'=>'');

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
					* 	- Borrar los componentes y actividades asociados 
					*	- Borrar la propuesta de financiamiento
					*	- Borrar la distribución del presupuesto
					* 	- Borrar datos del desglose del componete y actividades (Metas,Beneficiarios,Localidad)
					*
					***/
					$id_padre = $parametros['id-fibap'];
					$rows = DB::transaction(function() use ($ids){
						$acciones = Accion::whereIn('id',$ids)->get();
						
						$ids_componentes = $acciones->lists('idComponente');
						$ids_actividades = $acciones->lists('idActividad');
						
						if(count($ids_componentes)){
							//Eliminamos las metas de los componentes
							ComponenteMetaMes::whereIn('idComponente',$ids_componentes)->delete();
							//Eliminamos los componenetes
							Componente::whereIn('id',$ids_componentes)->delete();
							//Eliminamos el desglose de los componentes
							$desgloses = ComponenteDesglose::whereIn('idComponente',$ids_componentes)->lists('id');
							if(count($desgloses) > 0){
								DesgloseMetasMes::whereIn('idComponenteDesglose',$desgloses)->delete();
								DesgloseBeneficiario::whereIn('idComponenteDesglose',$desgloses)->delete();
							}
							ComponenteDesglose::whereIn('idComponente',$ids_componentes)->delete();
							
							//Quitamos la relación de las actividades asignadas a los componentes eliminados
							Actividad::whereIn('idComponente',$ids_componentes)->update(array('idComponente'=>null));
						}
						
						if(count($ids_actividades)){
							//Eliminamos las metas de las actividades
							ActividadMetaMes::whereIn('idActividad',$ids_actividades)->delete();
							//Eliminamos las actividades
							Actividad::whereIn('id',$ids_actividades)->delete();
							//Eliminamos el desglose de las actividades
							$desgloses = ActividadDesglose::whereIn('idActividad',$ids_actividades)->lists('id');
							if(count($desgloses) > 0){
								ActividadDesgloseMetasMes::whereIn('idActividadDesglose',$desgloses)->delete();
								ActividadDesgloseBeneficiario::whereIn('idActividadDesglose',$desgloses)->delete();
							}
							ActividadDesglose::whereIn('idActividad',$ids_actividades)->delete();
						}
						
						PropuestaFinanciamiento::whereIn('idAccion',$ids)->delete();
						DistribucionPresupuesto::whereIn('idAccion',$ids)->delete();
						
						foreach ($acciones as $accion) {
							$accion->partidas()->detach();
						}
						return Accion::whereIn('id',$ids)->delete();
					});
				}elseif($parametros['eliminar'] == 'desglose-presupuesto'){ //Eliminar Distribucion del Presupuesto
					/***
					*	Eliminar desglose del presupuesto de la Accion (DELETE)
					*
					* 	- Borrar datos del desglose del componete o actividad (Metas,Beneficiarios,Localidad)
					*	- Borrar la distribución de presupuesto por mes y partida
					*	- Borrar el desglose de metas por mes del componete o actividad
					*	- Borrar el desglose de beneficiarios del componete o actividad
					*	- Actualiza las metas por mes del componete o actividad
					***/

					$id_padre = $parametros['id-accion'];
					$rows = DB::transaction(function() use ($ids,$parametros){
						$rows = 0;
						$desgloses_eliminar = array();
						
						if($parametros['nivel'] == 'componente'){
							$desgloses_eliminar = ComponenteDesglose::with('metasMes')->whereIn('id',$ids)->get();
						}elseif($parametros['nivel'] == 'actividad'){
							$desgloses_eliminar = ActividadDesglose::with('metasMes')->whereIn('id',$ids)->get();
						}
						
						$datos_jurisdiccion_metas = array();
						$datos_trimestre_metas = array(1=>0,2=>0,3=>0,4=>0);
						$suma_metas = 0;
						
						if(count($desgloses_eliminar)){
							$distribucion_eliminar = DistribucionPresupuesto::getModel();
							//Añadir idComponenteDesglose a la tabla
							foreach ($desgloses_eliminar as $key => $desglose) {
								foreach ($desglose->metasMes as $meta_mes) {
									if(!isset($datos_jurisdiccion_metas[$desglose->claveJurisdiccion][$meta_mes->mes])){
										$datos_jurisdiccion_metas[$desglose->claveJurisdiccion][$meta_mes->mes] = $meta_mes->meta;
									}else{
										$datos_jurisdiccion_metas[$desglose->claveJurisdiccion][$meta_mes->mes] += $meta_mes->meta;
									}
									$datos_trimestre_metas[ceil($meta_mes->mes/3)] += $meta_mes->meta;
									$suma_metas += $meta_mes->meta;
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
							
							if($parametros['nivel'] == 'componente'){
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
								
								$componente_metas_mes->valorNumerador = $componente_metas_mes->valorNumerador - $suma_metas;
								$componente_metas_mes->numeroTrim1 = $componente_metas_mes->numeroTrim1 - $datos_trimestre_metas[1];
								$componente_metas_mes->numeroTrim2 = $componente_metas_mes->numeroTrim2 - $datos_trimestre_metas[2];
								$componente_metas_mes->numeroTrim3 = $componente_metas_mes->numeroTrim3 - $datos_trimestre_metas[3];
								$componente_metas_mes->numeroTrim4 = $componente_metas_mes->numeroTrim4 - $datos_trimestre_metas[4];
								
								$componente_metas_mes->save();
								
								DesgloseBeneficiario::whereIn('idComponenteDesglose',$ids)->delete();
								DesgloseMetasMes::whereIn('idComponenteDesglose',$ids)->delete();
								$rows = ComponenteDesglose::whereIn('id',$ids)->delete();
							}elseif($parametros['nivel'] == 'actividad'){
								$id_actividad = $desgloses_eliminar[0]->idActividad;
								$actividad_metas_mes = Actividad::with('metasMes')->find($id_actividad);
								$actualizar_metas_mes = array();
								foreach ($actividad_metas_mes->metasMes as $metas_mes) {
									if(isset($datos_jurisdiccion_metas[$metas_mes->claveJurisdiccion][$metas_mes->mes])){
										$metas_mes->meta -= $datos_jurisdiccion_metas[$metas_mes->claveJurisdiccion][$metas_mes->mes];
										$actualizar_metas_mes[] = $metas_mes;
									}
								}
								$actividad_metas_mes->metasMes()->saveMany($actualizar_metas_mes);
								
								$actividad_metas_mes->valorNumerador = $actividad_metas_mes->valorNumerador - $suma_metas;
								$actividad_metas_mes->numeroTrim1 = $actividad_metas_mes->numeroTrim1 - $datos_trimestre_metas[1];
								$actividad_metas_mes->numeroTrim2 = $actividad_metas_mes->numeroTrim2 - $datos_trimestre_metas[2];
								$actividad_metas_mes->numeroTrim3 = $actividad_metas_mes->numeroTrim3 - $datos_trimestre_metas[3];
								$actividad_metas_mes->numeroTrim4 = $actividad_metas_mes->numeroTrim4 - $datos_trimestre_metas[4];
								
								$actividad_metas_mes->save();
								
								ActividadDesgloseBeneficiario::whereIn('idActividadDesglose',$ids)->delete();
								ActividadDesgloseMetasMes::whereIn('idActividadDesglose',$ids)->delete();
								$rows = ActividadDesglose::whereIn('id',$ids)->delete();
							}
						}
						return $rows;
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
						$componentes = Componente::where('idProyecto','=',$id_padre)->lists('id');
						$actividades = Actividad::where('idProyecto','=',$id_padre)->lists('id');
						
						$componente_desgloses = ComponenteDesglose::whereIn('idComponente',$componentes)->lists('id');
						$actividad_desgloses = ActividadDesglose::whereIn('idActividad',$actividades)->lists('id');
						
						DesgloseBeneficiario::whereIn('idComponenteDesglose',$componente_desgloses)
											->whereIn('idTipoBeneficiario',$ids)
											->delete();
						ActividadDesgloseBeneficiario::whereIn('idActividadDesglose',$actividad_desgloses)
											->whereIn('idTipoBeneficiario',$ids)
											->delete();
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
				}elseif($parametros['eliminar'] == 'financiamiento'){
					$id_padre = $parametros['id-proyecto'];
					$rows = DB::transaction(function() use ($ids){
						$financiamiento = ProyectoFinanciamiento::whereIn('id',$ids)->get();
						foreach ($financiamiento as $fuente) {
							$fuente->subFuentesFinanciamiento()->detach();
						}
						return ProyectoFinanciamiento::whereIn('id',$ids)->delete();
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
				* 	- Borrar datos del desglose de los componentes y actividades (Metas,Beneficiarios,Localidad)
				*	- Borrar el desglose de metas por mes de los componentes y actividades
				*	- Borrar el desglose de beneficiarios de los componentes y actividades
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
						
						$desgloses = ActividadDesglose::whereIn('idAccion',$acciones->lists('id'))->get();
						if(count($desgloses) > 0){
							ActividadDesgloseMetasMes::whereIn('idActividadDesglose',$desgloses->lists('id'))->delete();
							ActividadDesgloseBeneficiario::whereIn('idActividadDesglose',$desgloses->lists('id'))->delete();
						}
						ActividadDesglose::whereIn('idAccion',$acciones->lists('id'))->delete();
					}
					Accion::whereIn('idFibap',$ids_fibaps)->delete();

					FIBAP::whereIn('id',$ids_fibaps)->delete();

					//Eliminamos las metas por mes de los componentes y actividades
					ComponenteMetaMes::whereIn('idProyecto',$ids)->delete();
					ActividadMetaMes::whereIn('idProyecto',$ids)->delete();
					
					//Eliminamos componentes, actividades y beneficiarios
					Componente::whereIn('idProyecto',$ids)->delete();
					Actividad::whereIn('idProyecto',$ids)->delete();
					Beneficiario::whereIn('idProyecto',$ids)->delete();

					$fuentes_financiamiento = ProyectoFinanciamiento::whereIn('idProyecto',$ids)->get();
					foreach ($fuentes_financiamiento as $fuente) {
						$fuente->subFuentesFinanciamiento()->detach();
					}
					ProyectoFinanciamiento::whereIn('idProyecto',$ids)->delete();

					return Proyecto::whereIn('id',$ids)->delete();
				});
			}

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
				if(isset($parametros['eliminar'])){
					//if($parametros['eliminar'] == 'actividad'){
						/***
						*	Eliminar Actividad (DELETE)
						* 	- Se regresa lista de actividades
						***/
						//$data['actividades'] = Actividad::with('usuario')->where('idComponente',$id_padre)->get();
					//}else
					if($parametros['eliminar'] == 'proyecto-beneficiario'){
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
						$accion = Accion::with('fibap.distribucionPresupuestoAgrupado.objetoGasto')
										->find($id_padre);
						if($accion->idComponente){
							$data['id_componente'] = $accion->idComponente;
						}else{
							$data['id_actividad'] = $accion->idActividad;
						}
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
						$fibap->acciones->load('datosComponenteDetalle','datosActividadDetalle','propuestasFinanciamiento');
						$data['acciones'] = $fibap->acciones;
						$data['distribucion_total'] = $fibap->distribucionPresupuestoAgrupado;
					}elseif($parametros['eliminar'] == 'financiamiento'){
						$data['financiamiento'] = ProyectoFinanciamiento::with('destinoGasto','fuenteFinanciamiento','subFuentesFinanciamiento')
																		->where('idProyecto','=',$id_padre)->get();
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
			$data['line'] = $ex->getLine();
			if($data['data'] == ''){
				$data['data'] = "No se pueden borrar los registros";
			}
		}

		return Response::json($data,$http_status);
	}

	public function importar_archivo($parametros){
		$respuesta = array();
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');	
		
		if($parametros['nivel'] == 'componente'){
			$idElemento = 'idComponente';
			$elemento = 'componente';
			$tablaElemento = 'proyectoComponentes';
			$tablaMetasMes = 'desgloseMetasMes';
			$tablaBeneficiarios = 'desgloseBeneficiarios';
		}else{
			$idElemento = 'idActividad';
			$elemento = 'actividad';
			$tablaElemento = 'componenteActividades';
			$tablaMetasMes = 'actividadDesgloseMetasMes';
			$tablaBeneficiarios = 'actividadDesgloseBeneficiarios';
		}
		
		$usuario = Sentry::getUser();
		$id_accion = $parametros['id-accion'];
		$id_fibap = $parametros['id-fibap'];

		if (Input::hasFile('datoscsv')){			
			$finfo = finfo_open(FILEINFO_MIME_TYPE); 
			$archivoConDatos = Input::file('datoscsv');
			$type = finfo_file($finfo, $archivoConDatos); 
			$archivo_desglose = storage_path().'/archivoscsv/archivo_desglose_'.$usuario->id.'.csv';
			$archivo_mes = storage_path().'/archivoscsv/archivo_mes_'.$usuario->id.'.csv';

			$conteo_queries = array();

			$idInsertado ='';
			$numeroRegistros = '';

			//Si el Mime coincide con CSV
			if($type=="text/plain"){
				$row = 1;
				$datos_archivo = array();
				//Valida que la codificación del archivo sea UTF-8 - No se necesita ya que no se capturara texto
				//if(mb_detect_encoding(file_get_contents($archivoConDatos), 'UTF-8', true)){
					if (($handle = fopen($archivoConDatos, "r")) !== FALSE && ($datos_desglose = fopen($archivo_desglose, "w")) !== FALSE && ($datos_mes = fopen($archivo_mes, "w")) !== FALSE) {
						if($parametros['nivel'] == 'componente'){
							$accion = Accion::with(array('desgloseComponente'=>function($query){
									$query->select('*',DB::raw('CONCAT_WS("_",claveMunicipio,claveLocalidad) AS claveCompuesta'));
								}))->find($id_accion);
							$accion->desglose = $accion->desgloseComponente;
						}else{
							$accion = Accion::with(array('desgloseActividad'=>function($query){
									$query->select('*',DB::raw('CONCAT_WS("_",claveMunicipio,claveLocalidad) AS claveCompuesta'));
								}))->find($id_accion);
							$accion->desglose = $accion->desgloseActividad;
						}
						
						$desgloses_capturados = array();
						foreach($accion->desglose as $desglose){
							$desgloses_capturados[$desglose->claveCompuesta] = $desglose;
						}
						
						if($parametros['tipo-archivo'] == 'metas'){
							$accion->desglose->load('metasMes');
						}elseif($parametros['tipo-archivo'] == 'presupuesto'){
							$accion->load(array('distribucionPresupuesto'=>function($query){
								$query->select(
									'id','idAccion','idObjetoGasto',
									'claveJurisdiccion',
									'creadoPor','creadoAl',
									DB::raw('CONCAT_WS("_",claveMunicipio,claveLocalidad,claveObjetoGasto,mes) AS claveCompuesta'));
							}));
							$distribucion_guardada = array();
							foreach($accion->distribucionPresupuesto as $presupuesto){
								$distribucion_guardada[$presupuesto->claveCompuesta] = $presupuesto->toArray();
							}
						}elseif($parametros['tipo-archivo'] == 'beneficiarios'){
							$accion->desglose->load(array('beneficiarios'=>function($query) use ($idElemento){
								$query->select('id',$idElemento.'Desglose','creadoPor','creadoAl',
										DB::raw('CONCAT_WS("_",claveMunicipio,claveLocalidad,idTipoBeneficiario) AS claveCompuesta'));
							}));
						}
						//Agregar tambien para partidas y tipos de beneficiarios
						$localidades_validas = array();
						$proyecto = Proyecto::find($parametros['id-proyecto']);	
						if($proyecto->idCobertura != 1){
							$recurso = Municipio::join('vistaLocalidades AS localidad',function($join){
													$join->on('localidad.idMunicipio','=','vistaMunicipios.id')
															->whereNull('localidad.borradoAl');
													})
													->select(DB::raw('CONCAT_WS("_",vistaMunicipios.clave,localidad.clave) AS claveCompuesta'),
														'vistaMunicipios.id AS valido')
													->orderBy('claveCompuesta','ASC');
							if($proyecto->idCobertura == 2){ 
							//Cobertura Municipio
								$recurso = $recurso->where('vistaMunicipios.clave','=',$proyecto->claveMunicipio)->get();
							}elseif($proyecto->idCobertura == 3){ 
							//Cobertura Region
								$recurso = $recurso->join('vistaRegiones AS region',function($join)use($proyecto){
														$join->on('vistaMunicipios.idRegion','=','region.id')
															->where('region.region','=',$proyecto->claveRegion)
															->whereNull('region.borradoAl');
													})->get();
							}
							$localidades_validas = $recurso->lists('valido','claveCompuesta');
							//$recurso = NULL;
							unset($recurso);
						}
						//$proyecto = NULL;
						unset($proyecto);
						
						if($parametros['tipo-archivo'] != 'beneficiarios'){
							$fibap = FIBAP::select('id','periodoEjecucionInicio','periodoEjecucionFinal')->where('id',$accion->idFibap)->first();
							$mes_incial = date("n",strtotime($fibap->periodoEjecucionInicio));
							$mes_final = date("n",strtotime($fibap->periodoEjecucionFinal));
						}else{
							$beneficiarios = array('id'=>0,'datos'=>array());
						}
						
						$desgloses_agregados = array();
						$nuevos_desgloses = array();
						$row = 1;
						while (($csv_data = fgetcsv($handle, 0, ",")) !== FALSE) {
							if($row == 1){
								$row++;
								continue;
							}
							
							if(count($localidades_validas)){
								if(!isset($localidades_validas[$csv_data[0]])){
									continue;
								}
							}
							
							$claves = explode('_',$csv_data[0]);

							$clave_municipio = $claves[0];
							$clave_localidad = $claves[1];
							
							if($parametros['tipo-archivo'] == 'presupuesto'){
								for($i = $mes_incial; $i <= $mes_final; $i++){
									if(isset($distribucion_guardada[$csv_data[0].'_'.$csv_data[3].'_'.$i])){
										$presupuesto = $distribucion_guardada[$csv_data[0].'_'.$csv_data[3].'_'.$i];
										if($presupuesto){
											//$distribucion_guardada[$csv_data[0].'_'.$csv_data[3].'_'.$i] = NULL;
											fputcsv($datos_mes,[
												$presupuesto['id'],
												$id_fibap,
												$presupuesto['idAccion'],
												$presupuesto['idObjetoGasto'],
												$csv_data[3],
												$presupuesto['claveJurisdiccion'],
												$clave_municipio,
												$clave_localidad,
												$i,
												$csv_data[(3+$i)],
												$presupuesto['creadoPor'],
												$presupuesto['creadoAl']
											]);
											unset($distribucion_guardada[$csv_data[0].'_'.$csv_data[3].'_'.$i]);
											unset($presupuesto);
										}
									}else{
										if($csv_data[(3+$i)]){
											fputcsv($datos_mes,[
												NULL,
												$id_fibap,
												$id_accion,
												NULL,
												$csv_data[3],
												NULL,
												$clave_municipio,
												$clave_localidad,
												$i,
												$csv_data[(3+$i)],
												NULL,
												NULL
											]);
										}
									}
								}
							}
							
							if(isset($desgloses_capturados[$csv_data[0]])){
								$desglose = $desgloses_capturados[$csv_data[0]];
								
								if(!isset($desgloses_agregados[$desglose->id])){
									$desgloses_agregados[$desglose->id] = 'agregado';
									fputcsv($datos_desglose,[
										$desglose->id,
										($desglose->idComponente)?$desglose->idComponente:$desglose->idActividad,
										$desglose->idAccion,
										$desglose->claveJurisdiccion,
										$desglose->claveMunicipio,
										$desglose->claveLocalidad,
										$desglose->presupuesto,
										$desglose->beneficiariosF,
										$desglose->beneficiariosM,
										$desglose->creadoPor,
										$desglose->creadoAl
									]);
								}
								
								if($parametros['tipo-archivo'] == 'metas'){
									$metas_mes = array();
									foreach($desglose->metasMes as $metas){
										$metas_mes[$metas->mes] = $metas;
									}
									for($i = $mes_incial; $i <= $mes_final; $i++){
										if(isset($metas_mes[$i])){
											$meta_mes = $metas_mes[$i];
											fputcsv($datos_mes,[
												$meta_mes->id,
			                                    ($meta_mes->idComponenteDesglose)?$meta_mes->idComponenteDesglose:$meta_mes->idActividadDesglose,
			                                    ($meta_mes->idComponente)?$meta_mes->idComponente:$meta_mes->idActividad,
			                                    $meta_mes->claveMunicipio,
												$meta_mes->claveLocalidad,
												$meta_mes->mes,
												$csv_data[(2+$i)]
			                                ]);
											unset($meta_mes);
											unset($metas_mes[$i]);
										}else{
											if($csv_data[(2+$i)]){
												fputcsv($datos_mes,[
													NULL,
				                                    $desglose->id,
				                                    ($accion->idComponente)?$accion->idComponente:$accion->idActividad,
				                                    $clave_municipio,
													$clave_localidad,
													$i,
													($csv_data[(2+$i)])?$csv_data[(2+$i)]:0
				                                ]);
											}
										}
									}
									unset($metas_mes);
								}elseif($parametros['tipo-archivo'] == 'beneficiarios'){
									
									if($beneficiarios['id'] != $desglose->id){
										$beneficiarios['id'] = $desglose->id;
										$beneficiarios['datos'] = array();
										foreach($desglose->beneficiarios as $beneficiario){
											$beneficiarios['datos'][$beneficiario->claveCompuesta] = $beneficiario->toArray();
										}
									}
									
									if(isset($beneficiarios['datos'][$csv_data[0].'_'.$csv_data[3]])){
										$beneficiario = $beneficiarios['datos'][$csv_data[0].'_'.$csv_data[3]];
										fputcsv($datos_mes,[
											$beneficiario['id'],
											$beneficiario[$idElemento.'Desglose'],
											($accion->idComponente)?$accion->idComponente:$accion->idActividad,
											$clave_municipio,
											$clave_localidad,
											$csv_data[3],
											$csv_data[5],
											$csv_data[6],
											$beneficiario['creadoPor'],
											$beneficiario['creadoAl']
										]);
									}else{
										if($csv_data[5] || $csv_data[6]){
											fputcsv($datos_mes,[
												NULL,
												$desglose->id,
												($accion->idComponente)?$accion->idComponente:$accion->idActividad,
												$clave_municipio,
												$clave_localidad,
												$csv_data[3],
												$csv_data[5],
												$csv_data[6],
												NULL,
												NULL
											]);
										}
									}
								}
							}else{
								if(!isset($nuevos_desgloses[$csv_data[0]])){
									$nuevos_desgloses[$csv_data[0]] = 'registrado';
									fputcsv($datos_desglose,[
										NULL,
										($accion->idComponente)?$accion->idComponente:$accion->idActividad,
										$accion->id,
										NULL,
										$clave_municipio,
										$clave_localidad,
										NULL,
										NULL,
										NULL,
										NULL,
										NULL
									]);
								}
								
								if($parametros['tipo-archivo'] == 'metas'){
									for($i = $mes_incial; $i <= $mes_final; $i++){
										if($csv_data[(2+$i)]){
											fputcsv($datos_mes,[
												NULL,
												NULL,
												($accion->idComponente)?$accion->idComponente:$accion->idActividad,
												$clave_municipio,
												$clave_localidad,
												$i,
												$csv_data[(2+$i)]
											]);
										}
									}
								}elseif($parametros['tipo-archivo'] == 'beneficiarios'){
									if($csv_data[5] || $csv_data[6]){
										fputcsv($datos_mes,[
											NULL,
											NULL,
											($accion->idComponente)?$accion->idComponente:$accion->idActividad,
											$clave_municipio,
											$clave_localidad,
											$csv_data[3],
											$csv_data[5],
											$csv_data[6],
											NULL,
											NULL
										]);
									}
								}
							}
						}
						fclose($datos_desglose);
						fclose($datos_mes);
						/*
						$distribucion_guardada = NULL;
						$desgloses_capturados = NULL;
						$beneficiarios = NULL;
						$desgloses_agregados = NULL;
						$nuevos_desgloses = NULL;
						*/
						unset($distribucion_guardada);
						unset($desgloses_capturados);
						unset($beneficiarios);
						unset($desgloses_agregados);
						unset($nuevos_desgloses);
						
						try {
							DB::connection()->getPdo()->beginTransaction();

							$id_usuario = $usuario->id;
							
							//Cargar Archivo de Desgloses
							$query = sprintf("
								LOAD DATA local INFILE '%s' REPLACE 
								INTO TABLE ".$elemento."Desglose 
								FIELDS TERMINATED BY ',' 
								OPTIONALLY ENCLOSED BY '\"' 
								ESCAPED BY '\"' 
								LINES TERMINATED BY '\\n'
								(
								@vid,
								@v".$idElemento.",
								@vidAccion,
								@vclaveJurisdiccion,
								@vclaveMunicipio,
								@vclaveLocalidad,
								@vpresupuesto,
								@vbeneficiariosF,
								@vbeneficiariosM,
								@vcreadoPor,
								@vcreadoAl
								)
								set actualizadoPor='%s', modificadoAl = CURRENT_TIMESTAMP,
								id = nullif(@vid,''),
								".$idElemento." = @v".$idElemento.",
								idAccion = @vidAccion,
								claveJurisdiccion = nullif(@vclaveJurisdiccion,''),
								claveMunicipio = @vclaveMunicipio,
								claveLocalidad = @vclaveLocalidad,
								presupuesto = nullif(@vpresupuesto,''),
								beneficiariosF = nullif(@vbeneficiariosF,''),
								beneficiariosM = nullif(@vbeneficiariosM,''),
								creadoPor = ifnull(nullif(@vcreadoPor,''),%s),
								creadoAl = ifnull(nullif(@vcreadoAl,''),CURRENT_TIMESTAMP)
								", addslashes($archivo_desglose),$id_usuario,$id_usuario);
							DB::connection()->getpdo()->exec($query);
							
							//Colocar la clave de las jurisdicciones correspondientes
							$query = sprintf("
								UPDATE ".$elemento."Desglose, vistaMunicipios, vistaJurisdicciones
								SET ".$elemento."Desglose.claveJurisdiccion = vistaJurisdicciones.clave
								WHERE 
								".$elemento."Desglose.claveMunicipio = vistaMunicipios.clave AND
								vistaMunicipios.idJurisdiccion = vistaJurisdicciones.id AND 
								".$elemento."Desglose.claveJurisdiccion Is NULL AND 
								".$elemento."Desglose.idAccion = %s
								", $id_accion);
							DB::connection()->getpdo()->exec($query);
							
							if($parametros['tipo-archivo'] == 'presupuesto'){
								//Cargar Archivo de Presupuesto x mes
								$query = sprintf("
									LOAD DATA local INFILE '%s' REPLACE 
									INTO TABLE fibapDistribucionPresupuesto 
									FIELDS TERMINATED BY ',' 
									OPTIONALLY ENCLOSED BY '\"' 
									ESCAPED BY '\"' 
									LINES TERMINATED BY '\\n'
									(
									@vid,
									@vidFibap,
									@vidAccion,
									@vidObjetoGasto,
									@vclaveObjetoGasto,
									@vclaveJurisdiccion,
									@vclaveMunicipio,
									@vclaveLocalidad,
									@vmes,
									@vcantidad,
									@vcreadoPor,
									@vcreadoAl
									)
									set actualizadoPor='%s', modificadoAl = CURRENT_TIMESTAMP, 
									id = nullif(@vid,''),
									idFibap = @vidFibap,
									idAccion = @vidAccion,
									idObjetoGasto = nullif(@vidObjetoGasto,''),
									claveObjetoGasto = @vclaveObjetoGasto,
									claveJurisdiccion = nullif(@vclaveJurisdiccion,''),
									claveMunicipio = @vclaveMunicipio,
									claveLocalidad = @vclaveLocalidad,
									mes = @vmes,
									cantidad = @vcantidad,
									creadoPor = ifnull(nullif(@vcreadoPor,''),%s),
									creadoAl = ifnull(nullif(@vcreadoAl,''),CURRENT_TIMESTAMP)
									", addslashes($archivo_mes),$id_usuario,$id_usuario);
								DB::connection()->getpdo()->exec($query);
								
								//Actualizar los ids de los objetos del gasto (partidas)
								$query = sprintf("
									UPDATE fibapDistribucionPresupuesto, catalogoObjetosGasto
									SET fibapDistribucionPresupuesto.idObjetoGasto = catalogoObjetosGasto.id
									WHERE 
									fibapDistribucionPresupuesto.claveObjetoGasto = catalogoObjetosGasto.clave AND
									fibapDistribucionPresupuesto.idObjetoGasto IS NULL AND 
									fibapDistribucionPresupuesto.idAccion = %s
									",$id_accion);
								DB::connection()->getpdo()->exec($query);
								
								//Actualizar las claves de jurisdiccion
								$query = sprintf("
									UPDATE fibapDistribucionPresupuesto, ".$elemento."Desglose
									SET fibapDistribucionPresupuesto.claveJurisdiccion = ".$elemento."Desglose.claveJurisdiccion
									WHERE 
									fibapDistribucionPresupuesto.claveMunicipio = ".$elemento."Desglose.claveMunicipio AND
									fibapDistribucionPresupuesto.claveLocalidad = ".$elemento."Desglose.claveLocalidad AND
									fibapDistribucionPresupuesto.idAccion = ".$elemento."Desglose.idAccion AND
									fibapDistribucionPresupuesto.claveJurisdiccion IS NULL AND 
									fibapDistribucionPresupuesto.idAccion = %s
									",$id_accion);
								DB::connection()->getpdo()->exec($query);
								
								//Limpiar el presupuesto del desglose para poder actualizar
								$query = sprintf("
									UPDATE ".$elemento."Desglose
									SET ".$elemento."Desglose.presupuesto = NULL, actualizadoPor = %s, modificadoAl = CURRENT_TIMESTAMP
									WHERE 
									".$elemento."Desglose.idAccion = %s AND 
									".$elemento."Desglose.borradoAl IS NULL 
									",$id_usuario,$id_accion);
								DB::connection()->getpdo()->exec($query);
								
								//Actualizar las cantidades del presupuesto en el desglose
								$query = sprintf("
									UPDATE ".$elemento."Desglose,
									(
										SELECT claveMunicipio, claveLocalidad, sum(cantidad) AS presupuesto
										FROM fibapDistribucionPresupuesto
										WHERE idAccion = %s AND borradoAl IS NULL
										GROUP BY claveMunicipio, claveLocalidad
									) AS sumasPresupuesto
									SET ".$elemento."Desglose.presupuesto = sumasPresupuesto.presupuesto, 
										".$elemento."Desglose.actualizadoPor = %s, 
										".$elemento."Desglose.modificadoAl = CURRENT_TIMESTAMP
									WHERE 
									".$elemento."Desglose.claveMunicipio = sumasPresupuesto.claveMunicipio AND
									".$elemento."Desglose.claveLocalidad = sumasPresupuesto.claveLocalidad AND
									".$elemento."Desglose.idAccion = %s AND 
									".$elemento."Desglose.borradoAl IS NULL 
									",$id_accion,$id_usuario,$id_accion);
								DB::connection()->getpdo()->exec($query);
								
							}elseif($parametros['tipo-archivo'] == 'metas'){
								//Cargar Archivo de Metas x mes
								$query = sprintf("
									LOAD DATA local INFILE '%s' REPLACE 
									INTO TABLE ".$tablaMetasMes." 
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
									@vmeta
									)
									set creadoPor='%s', actualizadoPor='%s',
									id = nullif(@vid,''),
									".$idElemento."Desglose = nullif(@v".$idElemento."Desglose,''),
									".$idElemento." = nullif(@v".$idElemento.",''),
									claveMunicipio = nullif(@vclaveMunicipio,''),
									claveLocalidad = nullif(@vclaveLocalidad,''),
									mes = @vmes,
									meta = @vmeta
									", addslashes($archivo_mes),$id_usuario,$id_usuario);
								DB::connection()->getpdo()->exec($query);
								
								//Actualizar la relacion de los desgloses con las metas
								$query = sprintf("
									UPDATE ".$tablaMetasMes.", ".$elemento."Desglose
									SET ".$tablaMetasMes.".".$idElemento."Desglose = ".$elemento."Desglose.id
									WHERE 
									".$tablaMetasMes.".".$idElemento." = ".$elemento."Desglose.".$idElemento." AND
									".$tablaMetasMes.".claveMunicipio = ".$elemento."Desglose.claveMunicipio AND
									".$tablaMetasMes.".claveLocalidad = ".$elemento."Desglose.claveLocalidad AND 
									".$tablaMetasMes.".".$idElemento."Desglose IS NULL
									");
								DB::connection()->getpdo()->exec($query);
								
								//Se insertan/reemplazan las metas por mes del componente con los valores importados del archivo
								$query = sprintf("
									REPLACE INTO ".$elemento."MetasMes
									SELECT 
									cMetas.id, comp.idProyecto, desglose.".$idElemento.", desglose.claveJurisdiccion, metasMes.mes, 
									SUM(metasMes.meta) as meta, cMetas.avance, ifnull(cMetas.creadoPor,%s) AS creadoPor, 
									ifnull(cMetas.actualizadoPor,%s) AS actualizadoPor, cMetas.creadoAl, cMetas.modificadoAl, 
									NULL AS borradoAl
									FROM ".$elemento."Desglose AS desglose
									LEFT JOIN ".$tablaMetasMes." AS metasMes 
									ON metasMes.claveMunicipio = desglose.claveMunicipio 
									AND desglose.id = metasMes.".$idElemento."Desglose
									AND metasMes.borradoAl IS NULL
									LEFT JOIN ".$elemento."MetasMes AS cMetas
									ON cMetas.".$idElemento." = desglose.".$idElemento."
									AND cMetas.claveJurisdiccion = desglose.claveJurisdiccion
									AND cMetas.mes = metasMes.mes
									AND cMetas.borradoAl IS NULL
									JOIN ".$tablaElemento." AS comp ON comp.id = desglose.".$idElemento."
									WHERE desglose.idAccion = %s AND desglose.borradoAl IS NULL
									GROUP BY desglose.claveJurisdiccion, metasMes.mes;
								",$id_usuario,$id_usuario,$id_accion);
								DB::connection()->getpdo()->exec($query);
	
								//Se actualizan las sumas por trimestre en el componente
								$query = sprintf("
									UPDATE ".$tablaElemento.", (
										SELECT 
										SUM(IF(mes/3 <= 1,meta,0)) AS meta1,
										SUM(IF(mes/3 > 1 AND mes/3 <= 2,meta,0)) AS meta2,
										SUM(IF(mes/3 > 2 AND mes/3 <= 3,meta,0)) AS meta3,
										SUM(IF(mes/3 > 3 AND mes/3 <= 4,meta,0)) AS meta4
										FROM ".$elemento."MetasMes
										WHERE ".$idElemento." = %s AND borradoAl IS NULL
									) AS metasTrim
									SET 
									numeroTrim1 = metasTrim.meta1,
									numeroTrim2 = metasTrim.meta2,
									numeroTrim3 = metasTrim.meta3,
									numeroTrim4 = metasTrim.meta4,
									valorNumerador = (metasTrim.meta1 + metasTrim.meta2 + metasTrim.meta3 + metasTrim.meta4)
									WHERE ".$tablaElemento.".id = %s
								",($accion->idComponente)?$accion->idComponente:$accion->idActividad,($accion->idComponente)?$accion->idComponente:$accion->idActividad);
								DB::connection()->getpdo()->exec($query);
							}elseif($parametros['tipo-archivo'] == 'beneficiarios'){
								//Cargar Archivo de Beneficiarios por tipo
								$query = sprintf("
									LOAD DATA local INFILE '%s' REPLACE 
									INTO TABLE ".$tablaBeneficiarios." 
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
									@vidTipoBeneficiario,
									@vtotalF,
									@vtotalM,
									@vcreadoPor,
									@vcreadoAl
									)
									set actualizadoPor='%s', modificadoAl = CURRENT_TIMESTAMP, 
									id = nullif(@vid,''),
									".$idElemento."Desglose = nullif(@v".$idElemento."Desglose,''),
									".$idElemento." = nullif(@v".$idElemento.",''),
									claveMunicipio = nullif(@vclaveMunicipio,''),
									claveLocalidad = nullif(@vclaveLocalidad,''),
									idTipoBeneficiario = @vidTipoBeneficiario,
									totalF = nullif(@vtotalF,''),
									totalM = nullif(@vtotalM,''),
									creadoPor = ifnull(nullif(@vcreadoPor,''),%s),
									creadoAl = ifnull(nullif(@vcreadoAl,''),CURRENT_TIMESTAMP)
									", addslashes($archivo_mes),$id_usuario,$id_usuario);
								DB::connection()->getpdo()->exec($query);
							}
							
							DB::connection()->getPdo()->commit();

							File::delete($archivo_desglose);
							File::delete($archivo_mes);

							$respuesta['data']['data'] = 'Elementos almacenados con exito';
							if($parametros['tipo-archivo'] == 'presupuesto'){
								$fibap->load('distribucionPresupuestoAgrupado.objetoGasto');
								$respuesta['data']['extras']['distribucion_total'] = $fibap->distribucionPresupuestoAgrupado; 
							}
							//$respuesta['data']['data'] = array('totalRegistros'=>$total_registros,'idCarga'=>$idCarga);
							//$respuesta['data'] = array('data'=>$desgloses_guardar,'conteo'=>$conteo_queries,'metas'=>$metas_mes_guardar);
						}catch (\PDOException $e){
							File::delete($archivo_desglose);
							File::delete($archivo_mes);
							$respuesta['http_status'] = 404;
							$respuesta['data'] = array("data"=>"Ha ocurrido un error, no se pudieron cargar los datos. Verfique su conexión a Internet.",'code'=>'U06');
						    DB::connection()->getPdo()->rollBack();
						    throw $e;
						}catch(Exception $e){
							$respuesta['http_status'] = 500;
							$respuesta['data'] = array("data"=>"",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
						}
					}else{
						fclose($datos_desglose);
						fclose($datos_mes);
					}
					fclose($handle);
				/*}else{
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"La codificación del archivo debe ser UTF-8",'code'=>'U06');
				}*/
			}else{
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"Formato de archivo incorrecto.",'code'=>'U06');
			}
		}else{
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No se encontró el archivo.",'code'=>'U06');
		}
		//return Response::json($respuesta['data'],$respuesta['http_status']);
		return $respuesta;
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
				$clave_localidad = $parametros['localidad-accion'];
			}else{
				$clave_municipio = '0';
				$clave_localidad = '0';
			}
			
			if($id){
				$es_editar = TRUE;
				if($parametros['nivel-desglose'] == 'componente'){
					$desglose = ComponenteDesglose::with('metasMes','beneficiarios')->find($id);
				}else{
					$desglose = ActividadDesglose::with('metasMes','beneficiarios')->find($id);
				}
				$accion_id = $desglose->idAccion;
			}else{
				if($parametros['nivel-desglose'] == 'componente'){
					$desglose = new ComponenteDesglose;
				}else{
					$desglose = new ActividadDesglose;
				}
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
			$accion_partidas = $accion->partidas->lists('clave','id');

			if(isset($parametros['mes'])){
				$meses_capturados = $parametros['mes'];
			}else{
				$meses_capturados = array();
			}
			

			if($es_editar){
				if(isset($parametros['meses-capturados'])){
					//Elementos ya capturados por tanto se actualizaran
					$meses_ids = $parametros['meses-capturados'];
				}else{
					$meses_ids = array();
				}
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
							$recurso->claveObjetoGasto = $accion_partidas[$partida];
							$recurso->mes = $mes;
						}else{
							$recurso = FALSE;
						}

						if($recurso){
							$recurso->claveJurisdiccion = $clave_jurisdiccion;
							$recurso->claveMunicipio = $clave_municipio;
							$recurso->claveLocalidad = $clave_localidad;
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
			if(!isset($parametros['beneficiarios'])){
				$parametros['beneficiarios'] = array();
			}

			if($es_editar){
				$editar_beneficiarios = FALSE;
				$beneficiarios_f = $desglose->beneficiarios->lists('totalF','idTipoBeneficiario');
				$beneficiarios_m = $desglose->beneficiarios->lists('totalM','idTipoBeneficiario');
				
				foreach ($parametros['beneficiarios'] as $tipo_beneficiario => $desglose_sexo) {
					if(!isset($beneficiarios_f[$tipo_beneficiario])){
						$beneficiarios_f[$tipo_beneficiario] = 0;
					}
					if(!isset($beneficiarios_m[$tipo_beneficiario])){
						$beneficiarios_m[$tipo_beneficiario] = 0;
					}
				}

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
				//Obtener todos los beneficiarios ya capturados
				$beneficiarios_componentes = DesgloseBeneficiario::whereIn('idComponente',$fibap->acciones->lists('idComponente'))
												->where('claveLocalidad','!=',$clave_localidad)->where('claveMunicipio','!=',$clave_municipio)
												->select('idTipoBeneficiario',DB::raw('sum(totalF) AS totalF'),DB::raw('sum(totalM) AS totalM'))
												->groupBy('idTipoBeneficiario')
												->get();
				$beneficiarios_actividades = ActividadDesgloseBeneficiario::whereIn('idActividad',$fibap->acciones->lists('idActividad'))
												->where('claveLocalidad','!=',$clave_localidad)->where('claveMunicipio','!=',$clave_municipio)
												->select('idTipoBeneficiario',DB::raw('sum(totalF) AS totalF'),DB::raw('sum(totalM) AS totalM'))
												->groupBy('idTipoBeneficiario')
												->get();
				
				$suma_benef = array();
				//$suma_total = array('f'=>0,'m'=>0);
				foreach ($beneficiarios_componentes as $beneficiario) {
					if(!isset($suma_benef[$beneficiario->idTipoBeneficiario])){
						$suma_benef[$beneficiario->idTipoBeneficiario] = array('f'=>0,'m'=>0);
					}
					$suma_benef[$beneficiario->idTipoBeneficiario]['f'] += $beneficiario->totalF;
					$suma_benef[$beneficiario->idTipoBeneficiario]['m'] += $beneficiario->totalM;
				}
				foreach ($beneficiarios_actividades as $beneficiario) {
					if(!isset($suma_benef[$beneficiario->idTipoBeneficiario])){
						$suma_benef[$beneficiario->idTipoBeneficiario] = array('f'=>0,'m'=>0);
					}
					$suma_benef[$beneficiario->idTipoBeneficiario]['f'] += $beneficiario->totalF;
					$suma_benef[$beneficiario->idTipoBeneficiario]['m'] += $beneficiario->totalM;
				}
				
				foreach ($parametros['beneficiarios'] as $tipo_beneficiario => $desglose_sexo) {
					if(isset($suma_benef[$tipo_beneficiario])){
						$suma_benef[$tipo_beneficiario]['f'] += $desglose_sexo['f'];
						$suma_benef[$tipo_beneficiario]['m'] += $desglose_sexo['m'];
						//$suma_total['f'] += $desglose_sexo['f'];
						//$suma_total['m'] += $desglose_sexo['m'];
					}else{
						$suma_benef[$tipo_beneficiario]['f'] = 0;
						$suma_benef[$tipo_beneficiario]['m'] = 0;
					}
				}
				
				foreach ($fibap->proyecto->beneficiarios as $beneficiario) {
					if($suma_benef[$beneficiario->idTipoBeneficiario][$beneficiario->sexo] > $beneficiario->total){
						//var_dump($suma_benef[$beneficiario->idTipoBeneficiario][$beneficiario->sexo]);
						//var_dump($beneficiario->toArray);die;
						throw new Exception('{"field":"beneficiarios-'.$beneficiario->idTipoBeneficiario.'-'.$beneficiario->sexo.'","error":"La cantidad especificada sobrepasa al limite de beneficiarios especificados para el proyecto."}', 1);
					}
				}
				
				/******************************      Fin validación de beneficiarios        *******************************/

				foreach ($parametros['beneficiarios'] as $tipo_beneficiario => $desglose_sexo) {
					if($es_editar){
						$desglose_benef = $desglose->beneficiarios->filter(function($item) use ($tipo_beneficiario){
							if($item->idTipoBeneficiario == $tipo_beneficiario){ return true; }
						})->first();
					}else{
						$desglose_benef = FALSE;
					}
					if(!$desglose_benef){
						if($parametros['nivel-desglose'] == 'componente'){
							$desglose_benef = new DesgloseBeneficiario;
						}else{
							$desglose_benef = new ActividadDesgloseBeneficiario;
						}
						$desglose_benef->idTipoBeneficiario = $tipo_beneficiario;
						$nuevo_desglose = true;
					}else{
						$nuevo_desglose = false;
					}
					if($parametros['nivel-desglose'] == 'componente'){
						$desglose_benef->idComponente = $accion->idComponente;
					}else{
						$desglose_benef->idActividad = $accion->idActividad;
					}
					$desglose_benef->claveMunicipio = $clave_municipio;
					$desglose_benef->claveLocalidad = $clave_localidad;
					$desglose_benef->totalF = $desglose_sexo['f'];
					$desglose_benef->totalM = $desglose_sexo['m'];
					
					if(!$nuevo_desglose || ($desglose_sexo['f'] || $desglose_sexo['m'])){
						$beneficiarios_desglose[] = $desglose_benef;
					}
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
					$trimestres[ceil(($mes/3))] += ($meta - $metas_anteriores[$mes]);
					$suma_metas += ($meta - $metas_anteriores[$mes]);
				}elseif($meta > 0 ){
					if($parametros['nivel-desglose'] == 'componente'){
						$recurso = new DesgloseMetasMes;
					}else{
						$recurso = new ActividadDesgloseMetasMes;
					}
					$recurso->mes = $mes;
					$recurso->meta = $meta;

					$metas_mes[$mes] = $recurso;
					$trimestres[ceil(($mes/3))] += $meta;
					$suma_metas += $meta;
				}
			}
			
			if($parametros['nivel-desglose'] == 'componente'){
				$componente = $accion->componente;
			}else{
				$componente = $accion->actividad;
			}
			
			$componente->valorNumerador = $componente->valorNumerador + $suma_metas;
			$componente->numeroTrim1 = $componente->numeroTrim1 + $trimestres[1];
			$componente->numeroTrim2 = $componente->numeroTrim2 + $trimestres[2];
			$componente->numeroTrim3 = $componente->numeroTrim3 + $trimestres[3];
			$componente->numeroTrim4 = $componente->numeroTrim4 + $trimestres[4];

			//Obtenemos las metas por mes del componente de la jurisdicción a capturar, estos serían el concentrado de metas del desglose
			if($parametros['nivel-desglose'] == 'componente'){
				$componente_metas_mes_capturados = ComponenteMetaMes::where('idComponente','=',$accion->idComponente)
																->where('claveJurisdiccion','=',$clave_jurisdiccion)
																->get();
			}else{
				$componente_metas_mes_capturados = ActividadMetaMes::where('idActividad','=',$accion->idActividad)
																->where('claveJurisdiccion','=',$clave_jurisdiccion)
																->get();
			}
			
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
					if($parametros['nivel-desglose'] == 'componente'){
						$meta_mes = new ComponenteMetaMes;
					}else{
						$meta_mes = new ActividadMetaMes;
					}
					$meta_mes->idProyecto = $fibap->proyecto->id;
					$meta_mes->mes = $meta->mes;
					$meta_mes->meta = $meta->meta;
					$meta_mes->claveJurisdiccion = $clave_jurisdiccion;

					$componente_metas_mes[$meta->mes] = $meta_mes;
				}
			}
			
			$desglose->idAccion = $accion->id;
			$desglose->claveJurisdiccion = $clave_jurisdiccion;
			$desglose->claveMunicipio 	= $clave_municipio;
			$desglose->claveLocalidad 	= $clave_localidad;
			$desglose->presupuesto 		= $suma_presupuesto;
			/*
			if($editar_beneficiarios){
				$desglose->beneficiariosF 	= $suma_total['f'];
				$desglose->beneficiariosM 	= $suma_total['m'];
			}
			*/
			$nivel_desglose = $parametros['nivel-desglose'];
			$respuesta['data'] = DB::transaction(function() use ($accion,$distribucion,$desglose,$metas_mes,$beneficiarios_desglose,$componente_metas_mes,$componente,$nivel_desglose){
				$componente->save();
				
				if($nivel_desglose == 'componente'){
					$accion->componente->desglose()->save($desglose);
					$accion->componente->metasMes()->saveMany($componente_metas_mes);
				}else{
					$accion->actividad->desglose()->save($desglose);
					$accion->actividad->metasMes()->saveMany($componente_metas_mes);
				}
				
				if(count($distribucion)){
					$accion->distribucionPresupuesto()->saveMany($distribucion);
				}

				if(count($metas_mes)){
					$desglose->metasMes()->saveMany($metas_mes);
					if($nivel_desglose == 'componente'){
						DesgloseMetasMes::where('idComponenteDesglose',$desglose->id)
										->whereNull('idComponente')
										->update(array(
											'idComponente'=>$desglose->idComponente,
											'claveMunicipio'=>$desglose->claveMunicipio,
											'claveLocalidad'=>$desglose->claveLocalidad
										));
					}else{
						ActividadDesgloseMetasMes::where('idActividadDesglose',$desglose->id)
												->whereNull('idActividad')
												->update(array(
													'idActividad'=>$desglose->idActividad,
													'claveMunicipio'=>$desglose->claveMunicipio,
													'claveLocalidad'=>$desglose->claveLocalidad
												));
					}
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

	//public function guardar_datos_accion_presupuesto($parametros,$componente,$id = NULL){
	public function guardar_datos_accion_presupuesto($parametros,$id = NULL){
		if($id){
			$es_editar = TRUE;
		}else{
			$es_editar = FALSE;
		}
		$respuesta = array('data'=>NULL);
		$fibap = FIBAP::find($parametros['id-fibap']);
		$validacion = Validador::validar(Input::all(), $this->reglasAccionPresupuesto);
		if($validacion === TRUE){
			//$respuesta = DB::transaction(function() use ($fibap,$parametros,$componente,$id,$es_editar){
			$respuesta = DB::transaction(function() use ($fibap,$parametros,$id,$es_editar){
				$selector = $parametros['nivel'];
				$valores_respuesta = array();
				if($fibap){
					if($id){
						if($selector == 'componente'){
							$accion = Accion::with('propuestasFinanciamiento','partidas','componente')
									->where('idComponente','=',$id)->first();
						}else{
							$accion = Accion::with('propuestasFinanciamiento','partidas','actividad')
									->where('idActividad','=',$id)->first();
						}
					}else{
						$accion = new Accion;
						if($selector == 'componente'){
							$accion->idComponente = $parametros['id-componente'];
						}else{
							$accion->idActividad = $parametros['id-actividad'];
						}
					}
					
					/********************************       Partidas Presupuestarias       *********************************/
					
					if(isset($parametros['objeto-gasto-presupuesto'])){
						$partidas_formulario = $parametros['objeto-gasto-presupuesto'];
					}else{
						$partidas_formulario = array();
					}
					
					if($es_editar){
						$partidas_anteriores = $accion->partidas->lists('id');
					}else{
						$partidas_anteriores = array();
					}
					
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
								
								if($selector == 'componente'){
									$desgloses = ComponenteDesglose::where('idAccion','=',$accion->id)->get();
								}else{
									$desgloses = ActividadDesglose::where('idAccion','=',$accion->id)->get();
								}
								
								$desgloses_editar = array();
								foreach ($desgloses as $desglose) {
									if(isset($restar_de_distribucion[$desglose->claveMunicipio][$desglose->claveLocalidad])){
										$desglose->presupuesto -= $restar_de_distribucion[$desglose->claveMunicipio][$desglose->claveLocalidad];
										$desgloses_editar[] = $desglose;
									}
								}
								
								if($selector == 'componente'){
									$accion->componente->desglose()->saveMany($desgloses_editar);
								}else{
									$accion->actividad->desglose()->saveMany($desgloses_editar);
								}
								
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
						if(count($guardar_origenes)){
							$accion->propuestasFinanciamiento()->saveMany($guardar_origenes);
						}
					}
				}else{
					throw new Exception("No se pudo encontrar la FIBAP", 1);
				}
				
				$valores_respuesta['data'] = $accion;

				return $valores_respuesta;
			});
		}else{
			$respuesta = $validacion;
		}
		$fibap->load('acciones.datosComponenteDetalle','acciones.datosActividadDetalle');
		$fibap->acciones->load('propuestasFinanciamiento');
		$respuesta['acciones'] = $fibap->acciones;
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