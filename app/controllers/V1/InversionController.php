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

			$rows = Proyecto::getModel();
			$rows = $rows->where('unidadResponsable','=',Sentry::getUser()->claveUnidad)
						->where('idClasificacionProyecto','=',2);
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}
			
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),'fibap.presupuestoRequerido',
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl')
								->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
								->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->leftjoin('fibap','proyectos.id','=','fibap.idProyecto')
								->orderBy('id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			$proyectos = array();
			foreach ($rows as $row) {
				# code...
				$proyectos[] = array(
						'id' 					=> $row->id,
						'clavePresup' 			=> $row->clavePresup,
						'nombreTecnico' 		=> $row->nombreTecnico,
						'presupuestoRequerido'	=> $row->presupuestoRequerido,
						'estatusProyecto'		=> $row->estatusProyecto,
						'username'				=> $row->username,
						'modificadoAl'			=> date_format($row->modificadoAl,'d/m/Y')
					);
			}
			$data = array('resultados'=>$total,'data'=>$proyectos);

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
			if($parametros['mostrar'] == 'editar-proyecto'){
				$recurso = Proyecto::with('jefeInmediato','liderProyecto','jefePlaneacion','coordinadorGrupoEstrategico',
									'fibap.documentos','beneficiarios.tipoBeneficiario')
									->find($id);
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
				$recurso = Accion::with('componente.actividades','componente.metasMes','partidas','propuestasFinanciamiento')->find($id);
			}elseif ($parametros['mostrar'] == 'desglose-componente') {
				# code...
				$recurso = Accion::with('distribucionPresupuestoAgrupado','datosComponenteDetalle','partidas')->find($id);
				if($recurso->distribucionPresupuestoAgrupado){
					$recurso->distribucionPresupuestoAgrupado->load('jurisdiccion');
				}
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
				$distribucion_base = DistribucionPresupuesto::find($id);

				$recurso = Accion::with('partidas')->find($distribucion_base->idAccion);
				
				$desglose = ComponenteDesglose::with('metasMes','beneficiarios')->where('idAccion','=',$distribucion_base->idAccion)
												->where('claveMunicipio','=',$distribucion_base->claveMunicipio)
												->where('claveLocalidad','=',$distribucion_base->claveLocalidad)->first();
				//
				$calendarizado = DistribucionPresupuesto::where('idFibap','=',$distribucion_base->idFibap)
													->where('idAccion','=',$distribucion_base->idAccion)
													->whereIn('idObjetoGasto',$recurso->partidas->lists('id'))
													->where('claveJurisdiccion','=',$distribucion_base->claveJurisdiccion)
													->where('claveMunicipio','=',$distribucion_base->claveMunicipio)
													->where('claveLocalidad','=',$distribucion_base->claveLocalidad)->get();
				//
				$recurso['desglose'] = $desglose;
				$recurso['calendarizado'] = $calendarizado;
			}
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			//$recurso = $recurso->toArray();
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
				$parametros['datos_presupuesto'] = TRUE;
				$respuesta = parent::guardar_datos_componente('componente',$parametros);
				//
				if($respuesta['data']['data']){
					$componente = $respuesta['data']['data'];
					$respuesta['data'] = $this->guardar_datos_accion_presupuesto($parametros,$componente);
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
					$recurso->idEstatusProyecto 	 = 1;
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
				$validacion = Validador::validar(Input::all(), $this->reglasPresupuesto);

				if($validacion === TRUE){
					/***
					*	Formulario de datos del desglose del presupuesto de la Accion (POST)
					*
					*	- Guarda una nueva distribución de presupuesto por mes y partida
					* 	- Guarda datos del desglose del componete (datos a exportar al proyecto) (Metas,Beneficiarios,Localidad)
					*	- Guardar el desglose de metas por mes del componente
					***/
					$accion_id = $parametros['id-accion'];
					$accion = Accion::with('distribucionPresupuesto','partidas')->find($accion_id);
					$fibap = FIBAP::find($accion->idFibap);

					$clave_jurisdiccion = $parametros['jurisdiccion-accion'];
					if($clave_jurisdiccion != 'OC'){
						$clave_municipio = $parametros['municipio-accion'];
						//La clave de la localidad se envia concatenada con la clave del municipio municipio|localidad
						$clave_localidad = explode('|',$parametros['localidad-accion']);
						$clave_localidad = $clave_localidad[1];
					}else{
						$clave_municipio = NULL;
						$clave_localidad = NULL;
					}
					

					//Se buscan si la Localidad ya fue capturada
					if($clave_jurisdiccion != 'OC'){
						$capturados = $accion->distribucionPresupuesto->filter(function($item) use ($clave_localidad){
							if($item->claveLocalidad == $clave_localidad){
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
							$respuesta['data']['data'] = '{"field":"localidad-accion","error":"Esta localidad ya fue capturada para esta acción."}';
						}else{
							$respuesta['data']['data'] = '{"field":"jurisdiccion-accion","error":"Esta jurisdicción ya fue capturada para esta acción."}';
						}
						
						throw new Exception('Se encontraron '.count($capturados).' elementos capturados',1);
					}

					$mes_incial = date("n",strtotime($fibap->periodoEjecucionInicio));
					$mes_final = date("n",strtotime($fibap->periodoEjecucionFinal));

					//$formul_partidas = $parametros['partidas'];
					$accion_partidas = $accion->partidas->lists('id','id');
					$meses_capturados = $parametros['mes'];

					//Se calcula la suma total de la distribucion del presupeusto y se crea un arreglo con los elementos a almacenar
					$distribucion = array();
					$suma_presupuesto = 0;
					
					foreach ($meses_capturados as $mes => $partidas) {
						//$indx = array_search($partida_id, $partidas);
						foreach ($partidas as $partida => $cantidad) {
							//Si la partida enviada desde el formulario se encuantra entre las partidas seleccionadas para el proyecto
							if(isset($accion_partidas[$partida])){
								if($cantidad > 0 && $mes >= $mes_incial && $mes <= $mes_final){
									$recurso = new DistribucionPresupuesto;
									$recurso->idFibap = $accion->idFibap;
									$recurso->claveJurisdiccion = $clave_jurisdiccion;
									if($clave_jurisdiccion != 'OC'){
										$recurso->claveMunicipio = $clave_municipio;
										$recurso->claveLocalidad = $clave_localidad;
									}
									$recurso->idObjetoGasto = $partida;
									$recurso->mes = $mes;
									$recurso->cantidad = $cantidad;
									$distribucion[] = $recurso;
									$suma_presupuesto += $cantidad;
								}
							}
						}
					}
					
					$suma_distribucion = $accion->distribucionPresupuesto->sum('cantidad');

					if(($suma_distribucion + $suma_presupuesto) > $accion->presupuestoRequerido){
						throw new Exception('{"field":"cantidad-presupuesto","error":"La distribución del presupuesto sobrepasa el presupuesto requerido."}', 1);
					}

					//Para calcular total de beneficiarios y validar si no sobrepasa
					$fibap->load('acciones','proyecto.beneficiarios');

					$beneficiarios_capturados = ComponenteDesglose::whereIn('idAccion',$fibap->acciones->lists('id'))
												/*->select(DB::raw('(sum(beneficiariosF)/count(claveLocalidad)) AS beneficiariosF'),
													DB::raw('(sum(beneficiariosM)/count(claveLocalidad)) AS beneficiariosM'))
												->leftjoin('')*/
												->groupBy('claveLocalidad','claveMunicipio');
					//
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
							//$suma_total['f'] += $beneficiario->totalF;
							//$suma_total['m'] += $beneficiario->totalM;
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

					$beneficiarios_desglose = array();
					foreach ($parametros['beneficiarios'] as $tipo_beneficiario => $desglose_sexo) {
						if(isset($suma_benef[$tipo_beneficiario])){
							$desglose_benef = new DesgloseBeneficiario;
							$desglose_benef->idTipoBeneficiario = $tipo_beneficiario;
							$desglose_benef->totalF = $desglose_sexo['f'];
							$desglose_benef->totalM = $desglose_sexo['m'];

							$beneficiarios_desglose[] = $desglose_benef;
						}
					}

					$suma_metas = 0;
					$trimestres = array( 1=>0 , 2=>0 , 3=>0 , 4=>0);
					$metas_mes = array();
					foreach ($parametros['meta-mes'] as $mes => $meta) {
						if($meta > 0 ){
							$recurso = new DesgloseMetasMes;
							$recurso->mes = $mes;
							$recurso->meta = $meta;
							$metas_mes[] = $recurso;
							$suma_metas += $meta;
							$trimestres[ceil(($mes/3))] += $meta;
						}
					}

					$componente_metas_mes_capturados = ComponenteMetaMes::where('idComponente','=',$accion->idComponente)
																		->where('claveJurisdiccion','=',$clave_jurisdiccion)
																		->get();
					$componente_metas_mes = array();
					if(count($componente_metas_mes_capturados)){
						//Con las metas por mes ya capturadas, buscamos las que vienen del formulario para actualizar
						foreach ($componente_metas_mes_capturados as $meta_mes) {
							if(isset($parametros['meta-mes'][$meta_mes->mes])){
								if($parametros['meta-mes'][$meta_mes->mes] > 0){
									$meta_mes->meta += $parametros['meta-mes'][$meta_mes->mes];

									$componente_metas_mes[$meta_mes->mes] = $meta_mes;
								}
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
					
					$desglose = new ComponenteDesglose;
					$desglose->idAccion = $accion->id;
					$desglose->claveJurisdiccion = $clave_jurisdiccion;
					if($clave_jurisdiccion != 'OC'){
						$desglose->claveMunicipio 	= $clave_municipio;
						$desglose->claveLocalidad 	= $clave_localidad;
					}
					$desglose->presupuesto 		= $suma_presupuesto;
					$desglose->beneficiariosF 	= $suma_total['f'];
					$desglose->beneficiariosM 	= $suma_total['m'];

					$respuesta['data'] = DB::transaction(function() use ($accion,$distribucion,$desglose,$metas_mes,$beneficiarios_desglose,$componente_metas_mes){
						$accion->componente->desglose()->save($desglose);
						$accion->componente->metasMes()->saveMany($componente_metas_mes);
						$accion->distribucionPresupuesto()->saveMany($distribucion);
						$desglose->metasMes()->saveMany($metas_mes);
						$desglose->beneficiarios()->saveMany($beneficiarios_desglose);
						
						$accion->load('distribucionPresupuestoAgrupado.jurisdiccion');
						return array('data'=>$accion);
					});

					$fibap->load('distribucionPresupuestoAgrupado.objetoGasto');
					$respuesta['data']['extras']['distribucion_total'] = $fibap->distribucionPresupuestoAgrupado;
				}else{
					$respuesta['http_status'] 	= $validacion['http_status'];
					$respuesta['data'] 			= $validacion['data'];
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
			if(!isset($respuesta['data']['code'])){
				$respuesta['data']['code'] = 'S03';
			}
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	public function guardar_datos_accion_presupuesto($parametros,$componente,$id = NULL){
		$es_editar = FALSE;
		$respuesta = DB::transaction(function() use ($parametros,$componente,$id){
			$fibap = FIBAP::find($parametros['id-fibap']);
			if($fibap){
				if($id){
					$accion = Accion::with('propuestasFinanciamiento','partidas','componente')->find($id);
					$es_editar = TRUE;
				}else{
					$accion = new Accion;
				}
				
				$accion->idComponente = $componente->id;

/**********************************************       Partidas Presupuestarias       **********************************************/
				
				$partidas_formulario = $parametros['objeto-gasto-presupuesto'];
				if($es_editar){
					$partidas_anteriores = $accion->partidas->lists('id');
				}else{
					$partidas_anteriores = array();
				}

				if($partidas_formulario[0] == $partidas_formulario[1]){
					throw new Exception('{"field":"objeto-gasto-presupuesto","error":"Las partidas no deben ser iguales"}', 1);
				}

				//Sacamos las diferencias de las partidas seleccionadas y las ya capturadas
				$partidas['nuevas'] = array_diff($partidas_formulario, $partidas_anteriores);
				$partidas['borrar'] = array_diff($partidas_anteriores, $partidas_formulario);

/***********************************       Origenes del presupuesto y presupuesto Requerido      ************************************/
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
					$distribucion_total = NULL;
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
							$distribucion_total = $fibap->distribucionPresupuestoAgrupado;
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
			return array('data'=>$accion,'acciones' => $fibap->acciones);
		});
		return $respuesta;
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
					
					$respuesta['data']['extras'] = $extras;
				}
			}elseif($parametros['guardar'] == 'proyecto-beneficiario'){
				$respuesta = parent::guardar_datos_beneficiario($parametros,$id);
			}elseif($parametros['guardar'] == 'componente'){
				$parametros['clasificacion'] = 2;
				$parametros['datos_presupuesto'] = TRUE;
				$respuesta = parent::guardar_datos_componente('componente',$parametros,$parametros['id-componente']);
				//Llenar datos adicionales
				if($respuesta['data']['data']){
					$componente = $respuesta['data']['data'];
					$respuesta['data'] = $this->guardar_datos_accion_presupuesto($parametros,$componente,$id);
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

								if(count($recurso->acciones)){
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
								}
								
								//Eliminamos la distribucion del presupuesto
								$distribucion_borrada = DistribucionPresupuesto::where('idFibap','=',$recurso->id)
														->where(function($query) use ($mes){
															$query->where('mes','<',$mes['inicio'])
																->orWhere('mes','>',$mes['fin']);
														})->delete();
								//
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
				if($parametros['eliminar'] == 'proyecto-beneficiario'){
					$id_padre = $parametros['id-proyecto'];
					$rows = DB::transaction(function() use ($ids,$id_padre){
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
			}

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
				if(isset($parametros['eliminar'])){
					if($parametros['eliminar'] == 'proyecto-beneficiario'){
						$data['beneficiarios'] = Beneficiario::with('tipoBeneficiario')->where('idProyecto',$id_padre)->get();
					}elseif($parametros['eliminar'] == 'antecedente'){
						$data['antecedentes'] = AntecedenteFinanciero::where('idFibap',$id_padre)->get();
					}
				}
			}else{
				$http_status = 404;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}	
		}catch(Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'ex'=>$ex->getMessage(),'code'=>'S03');	
		}

		return Response::json($data,$http_status);
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