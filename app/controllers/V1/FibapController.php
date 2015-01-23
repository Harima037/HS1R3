<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Exception, DateTime;
use FIBAP, Proyecto, FibapDatosProyecto, PropuestaFinanciamiento, AntecedenteFinanciero, DistribucionPresupuesto, OrigenFinanciamiento;
use Jurisdiccion, Municipio, Region, Accion, FibapDatosComponente, DesgloseMetasMes, ComponenteDesglose;

class FibapController extends BaseController {
	private $reglasFibap = array(
		'organismo-publico'			=> 'required',
		'sector'					=> 'required',
		'subcomite'					=> 'required',
		'grupo-trabajo'				=> 'required',
		'justificacion-proyecto'	=> 'required',
		'descripcion-proyecto'		=> 'required',
		'objetivo-proyecto'			=> 'required',
		'alineacion-especifica'		=> 'required',
		'tipo-proyecto'				=> 'required_without:proyecto-id',
		'proyecto'					=> 'required_without:proyecto-id',
		'programa-presupuestal'		=> 'required_without:proyecto-id',
		'vinculacion-ped'			=> 'required_without:proyecto-id',
		'cobertura'					=> 'required_without:proyecto-id',
		'municipio'					=> 'required_if:cobertura,2',
		'region'					=> 'required_if:cobertura,3',
		'tipo-beneficiario'			=> 'required_without:proyecto-id',
		'total-beneficiarios-f'		=> 'required_without:proyecto-id',
		'total-beneficiarios-m'		=> 'required_without:proyecto-id',
		'presupuesto-requerido'		=> 'required',
		'periodo-ejecucion-inicio'	=> 'required',
		'periodo-ejecucion-final'	=> 'required',
	);

	private $reglasFibapAntecedentes = array(
		'resultados-obtenidos'		=> 'required',
		'resultados-esperados'		=> 'required'
	);

	private $reglasFibapPresupuesto = array(
		'presupuesto-requerido'		=> 'required',
		'periodo-ejecucion-inicio'	=> 'required',
		'periodo-ejecucion-final'	=> 'required'
	);

	private $reglasAccion = array(
		//'objeto-gasto-presupuesto'		=> 'required',
		'indicador'						=> 'required',
		'unidad-medida'					=> 'required',
		'accion-presupuesto-requerido'	=> 'required|numeric|min:1',
		'entregable' 					=> 'required',
		'tipo-componente' 				=> 'required',
		'accion-componente' 			=> 'required',
	);

	private $reglasAntecedentes = array(
		'anio-antecedente'				=> 'required|integer|min:1000',
		'autorizado-antecedente'		=> 'required',
		'ejercido-antecedente'			=> 'required',
		'fecha-corte-antecedente'		=> 'required'
	);

	private $reglasPresupuesto = array(
		'beneficiarios-f'			=> 'required',
		'beneficiarios-m'			=> 'required',
		'jurisdiccion-accion'		=> 'required',
		'municipio-accion'			=> 'required',
		'localidad-accion'			=> 'required',
		'cantidad-meta'				=> 'required|numeric|min:1',
		'cantidad-presupuesto'		=> 'required|numeric|min:1'
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
			$rows = FIBAP::getModel();
			$rows = $rows->where('claveUnidadResponsable','=',Sentry::getUser()->claveUnidad);

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

			if(isset($parametros['buscar'])){
				$rows = $rows->where('fibap.sector','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.subcomite','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.grupoTrabajo','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.alineacionEspecifica','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.alineacionGeneral','like','%'.$parametros['buscar'].'%')
							 ->where('fibap.descripcionProyecto','like','%'.$parametros['buscar'].'%')
							 ->where('p.nombreTecnico','like','%'.$parametros['buscar'].'%')
							 ->where('Proyecto','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{
				$total = $rows->count();
			}

			$rows = $rows->select('fibap.id',DB::raw('if(p.id,concat(p.unidadResponsable,p.finalidad,p.funcion,p.subfuncion,p.subsubfuncion,p.programaSectorial,p.programaPresupuestario,p.programaEspecial,p.actividadInstitucional,p.proyectoEstrategico,LPAD(p.numeroProyectoEstrategico,3,"0")),"No asignada") as Proyecto'),
								DB::raw('if(p.id,p.nombreTecnico,fp.nombreTecnico) AS nombreTecnico'),
								DB::raw('if(p.idTipoProyecto,tp.descripcion,ftp.descripcion) AS tipoProyecto'),
								'descripcionProyecto','catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username',
								'fibap.modificadoAl')
								->leftjoin('sentryUsers','sentryUsers.id','=','fibap.creadoPor')
								->leftjoin('proyectos AS p','p.id','=','fibap.idProyecto')
								->leftjoin('fibapDatosProyecto AS fp','fp.idFibap','=','fibap.id')
								->leftjoin('catalogoTiposProyectos AS tp','tp.id','=','p.idTipoProyecto')
								->leftjoin('catalogoTiposProyectos AS ftp','ftp.id','=','fp.idTipoProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','fibap.idEstatusProyecto')
								->orderBy('fibap.id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			//Para obtener la ultima consulta ejecutada...
			//$queries = DB::getQueryLog();
			//print_r(end($queries));
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

			return Response::json($data,$http_status);
		}elseif(isset($parametros['lista_fibap'])){
			$rows = FIBAP::getModel();
			$rows = $rows->select('fibap.id','fp.nombreTecnico','ftp.descripcion AS tipoProyecto',
								'descripcionProyecto','fp.idTipoProyecto')
								->leftjoin('fibapDatosProyecto AS fp','fp.idFibap','=','fibap.id')
								->leftjoin('catalogoTiposProyectos AS ftp','ftp.id','=','fp.idTipoProyecto')
								->whereNull('fibap.idProyecto')
								->where('claveUnidadResponsable','=',Sentry::getUser()->claveUnidad)
								->orderBy('fibap.id', 'desc')
								->get();

			$data = array('data'=>$rows);

			return Response::json($data,$http_status);
		}

		$rows = FIBAP::all();

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
	public function show($id)
	{
		//
		$http_status = 200;
		$data = array('data'=>'');

		$parametros = Input::all();
		$calendarizado = FALSE;
		$clave_presupuestaria = FALSE;
		$jurisdicciones = FALSE;
		$municipios = FALSE;
		$desglose = FALSE;
	try{
		if($parametros){
			if($parametros['ver'] == 'fibap'){
				$recurso = FIBAP::with('documentos','antecedentesFinancieros','distribucionPresupuestoAgrupado','acciones')->find($id);
				$recurso->acciones->load('datosComponente','propuestasFinanciamiento');
				$recurso->distribucionPresupuestoAgrupado->load('objetoGasto');
				$proyecto = NULL;
				if($recurso->idProyecto){
					$recurso->load('proyecto');
					$clave_presupuestaria = $recurso->proyecto->clavePresupuestaria;
					$proyecto = $recurso->proyecto;
				}else{
					$recurso->load('datosProyecto');
					$proyecto = $recurso->datosProyecto;
				}
				if($proyecto->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
					$jurisdicciones = Jurisdiccion::all();
					$municipios = Municipio::with('localidades')->get(); //Todos los municipios
				}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
					$jurisdicciones = Municipio::obtenerJurisdicciones($proyecto->claveMunicipio)->get();
					$municipios = Municipio::with('localidades')->where('clave','=',$proyecto->claveMunicipio)->get(); //Obtenemos el municipio seleccionado
				}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
					$jurisdicciones = Region::obtenerJurisdicciones($proyecto->claveRegion)->get();
					$region = Region::with('municipios')->where('region','=',$proyecto->claveRegion)->get();
					$region[0]->municipios->load('localidades');
					$municipios = $region[0]->municipios;
				}
			}elseif($parametros['ver'] == 'antecedente'){
				$recurso = AntecedenteFinanciero::find($id);
			}elseif($parametros['ver'] == 'accion'){
				/***
				*	Obtiene los datos de una Acción (GET)
				*
				*	- Obtiene los datos del componente la Acción
				*	- Obtiene la propuesta de financiamiento
				*	- Obtiene las partidas seleccionadas para la Acción
				* 	
				***/
				$recurso = Accion::with('datosComponente','propuestasFinanciamiento','partidas')->find($id);
			}elseif($parametros['ver'] == 'distribucion-presupuesto'){
				/***
				*	Obtiene la distribución del presupuesto de una Acción (GET)
				*
				*	- Obtiene la distribución de presupuesto concentrado por Localidad
				* 	
				***/
				$recurso = Accion::with('distribucionPresupuestoAgrupado','partidas')->find($id);
				if($recurso->distribucionPresupuestoAgrupado){
					$recurso->distribucionPresupuestoAgrupado->load('jurisdiccion');
				}

			}elseif($parametros['ver'] == 'distribucion-presupuesto-metas'){
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
				
				$desglose = ComponenteDesglose::with('metasMes')->where('idAccion','=',$distribucion_base->idAccion)
												->where('claveMunicipio','=',$distribucion_base->claveMunicipio)
												->where('claveLocalidad','=',$distribucion_base->claveLocalidad)->get();
				//
				$calendarizado = DistribucionPresupuesto::where('idFibap','=',$distribucion_base->idFibap)
													->where('idAccion','=',$distribucion_base->idAccion)
													->whereIn('idObjetoGasto',$recurso->partidas->lists('id'))
													->where('claveJurisdiccion','=',$distribucion_base->claveJurisdiccion)
													->where('claveMunicipio','=',$distribucion_base->claveMunicipio)
													->where('claveLocalidad','=',$distribucion_base->claveLocalidad)->get();
				//

			}elseif($parametros['ver'] == 'datos-proyecto'){
				$recurso = Proyecto::find($id);
				$clave_presupuestaria = $recurso->clavePresupuestaria;
			}
		}else{
			$recurso = FIBAP::with('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado')->find($id);
			$recurso->distribucionPresupuestoAgrupado->load('objetoGasto');
			$recurso->propuestasFinanciamiento->load('origen');
			if($recurso->idProyecto){
				$recurso->load('proyectoCompleto');
				$clave_presupuestaria = $recurso->proyectoCompleto->clavePresupuestaria;
			}else{
				$recurso->load('datosProyectoCompleto');
			}
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();
			if($municipios){
				$recurso['municipios'] = $municipios;
			}
			if($jurisdicciones){
				$recurso['jurisdicciones'] = $jurisdicciones; //array('OC'=>'O.C.') + 
			}
			if($desglose){
				$recurso['desglose_componente'] = $desglose[0];
			}
			$data = array("data"=>$recurso);
			if($calendarizado){
				$data['calendarizado'] = $calendarizado->toArray();
			}
			if($clave_presupuestaria){
				$data['clavePresupuestaria'] = $clave_presupuestaria;
			}
		}
	}catch(\Exception $ex){
			$http_status = 500;
			if($data['data'] == ''){
				$data['data'] = 'Ocurrio un error en el servidor al guardar la acción.';
			}
			$data['ex'] = $ex->getMessage();
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
	public function store()
	{
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();
		if(isset($parametros['formulario'])){

			if($parametros['formulario'] == 'form-fibap-datos'){
				$validacion = Validador::validar(Input::all(), $this->reglasFibap);
			}elseif($parametros['formulario'] == 'form-antecedente'){
				$validacion = Validador::validar(Input::all(), $this->reglasAntecedentes);
			}elseif($parametros['formulario'] == 'form-presupuesto'){
				$validacion = Validador::validar(Input::all(), $this->reglasPresupuesto);
			}elseif($parametros['formulario'] == 'form-accion'){
				$validacion = Validador::validar(Input::all(), $this->reglasAccion);
			}

			if($validacion === TRUE){
				try{
					if($parametros['formulario'] == 'form-fibap-datos'){

						/**
						*	Formulario de datos generales del FIBAP (POST)
						**/

						if(!isset($parametros['documento-soporte'])){
							$respuesta['data']['data'] = 'Debe seleccionar al menos un documento.';
							throw new Exception("Error Processing Request", 1);
						}

						$fecha_inicio = DateTime::createFromFormat('d/m/Y',Input::get('periodo-ejecucion-inicio'));
						$fecha_fin = DateTime::createFromFormat('d/m/Y',Input::get('periodo-ejecucion-final'));

						if(!$fecha_inicio){
							$fecha_inicio = DateTime::createFromFormat('Y-m-d',Input::get('periodo-ejecucion-inicio'));
						}
						if(!$fecha_fin){
							$fecha_fin = DateTime::createFromFormat('Y-m-d',Input::get('periodo-ejecucion-final'));
						}

						if(!$fecha_inicio){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"periodo-ejecucion-inicio","error":"La fecha de inicio del periodo de ejecución no tiene el formato correcto."}';
							throw new Exception('La fecha no tiene un formato valido');
						}
						if(!$fecha_fin){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"periodo-ejecucion-final","error":"La fecha final del periodo de ejecución no tiene el formato correcto."}';
							throw new Exception('La fecha no tiene un formato valido');
						}

						if($fecha_fin < $fecha_inicio){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"periodo-ejecucion-final","error":"La fecha final del periodo de ejecución no puede ser menor que la de inicio."}';
							throw new Exception('La fecha final es menor a la de inicio');
						}

						$recurso = new FIBAP;
						$proyecto = FALSE;

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
						$recurso->periodoEjecucionInicio = $fecha_inicio;
						$recurso->periodoEjecucionFinal  = $fecha_fin;
						$recurso->presupuestoRequerido 	 = $parametros['presupuesto-requerido'];

						if(isset($parametros['proyecto-id'])){
							$recurso->idProyecto = $parametros['proyecto-id'];
						}else{
							$proyecto = new FibapDatosProyecto;
							$proyecto->nombreTecnico = $parametros['proyecto'];
							$proyecto->idClasificacionProyecto = 2;
							$proyecto->idTipoProyecto = $parametros['tipo-proyecto'];
							$proyecto->programaPresupuestario = $parametros['programa-presupuestal'];
							$proyecto->idCobertura = $parametros['cobertura'];

							if($parametros['cobertura'] == 2){
								$proyecto->claveMunicipio = $parametros['municipio'];
							}elseif($parametros['cobertura'] == 3){
								$proyecto->claveRegion = $parametros['region'];
							}

							$proyecto->idObjetivoPED = $parametros['vinculacion-ped'];
							$proyecto->idTipoBeneficiario = $parametros['tipo-beneficiario'];
							$proyecto->totalBeneficiarios = $parametros['total-beneficiarios-f']+$parametros['total-beneficiarios-m'];
							$proyecto->totalBeneficiariosF = $parametros['total-beneficiarios-f'];
							$proyecto->totalBeneficiariosM = $parametros['total-beneficiarios-m'];
						}

						$documentos = $parametros['documento-soporte'];

						$respuesta['data'] = DB::transaction(function() use ($recurso, $proyecto, $documentos){
							if($recurso->save()){
								$recurso->documentos()->attach($documentos);
								$datos_extra = array();
								if($proyecto){
									if(!$recurso->datosProyecto()->save($proyecto)){
										$respuesta['data']['code'] = 'S01';
										throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado del proyecto", 1);
									}
									if($proyecto->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
										$datos_extra['municipios'] = Municipio::with('localidades')->get(); //Todos los municipios
									}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
										$datos_extra['municipios'] = Municipio::with('localidades')->where('clave','=',$proyecto->claveMunicipio)->get(); //Obtenemos el municipio seleccionado
									}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
										$region = Region::with('municipios')->where('region','=',$proyecto->claveRegion)->get();
										$region[0]->municipios->load('localidades');
										$datos_extra['municipios'] = $region[0]->municipios;
									}
								}
								return array('data'=>$recurso,'extras'=>$datos_extra);
							}else{
								//No se pudieron guardar los datos del proyecto
								$respuesta['data']['code'] = 'S01';
								throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado de la ficha", 1);
							}

						});

					}elseif($parametros['formulario'] == 'form-antecedente'){

						/**
						*	Formulario de datos especificos de Antecedente del FIBAP (POST)
						**/

						$fecha_corte = DateTime::createFromFormat('d/m/Y',Input::get('fecha-corte-antecedente'));

						if(!$fecha_corte){
							$fecha_corte = DateTime::createFromFormat('Y-m-d',Input::get('fecha-corte-antecedente'));
						}

						if(!$fecha_corte){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"fecha-corte-antecedente","error":"La fecha de corte no tiene el formato correcto."}';
							throw new Exception('La fecha no tiene un formato valido');
						}

						$recurso = new AntecedenteFinanciero;
						$recurso->anio = $parametros['anio-antecedente'];
						$recurso->autorizado = $parametros['autorizado-antecedente'];
						$recurso->ejercido = $parametros['ejercido-antecedente'];
						$recurso->fechaCorte = $fecha_corte;
						$recurso->porcentaje = ($recurso->ejercido * 100) / $recurso->autorizado;

						$fibap = FIBAP::find($parametros['fibap-id']);
						$fibap->antecedentesFinancieros()->save($recurso);
						$fibap->load('antecedentesFinancieros');
						$respuesta['data'] = array('data'=>$recurso,'antecedentes' => $fibap->antecedentesFinancieros);

					}elseif($parametros['formulario'] == 'form-presupuesto'){ //Nuevo Presupuesto
						/***
						*	Formulario de datos del desglose del presupuesto de la Accion (POST)
						*
						*	- Guarda una nueva distribución de presupuesto por mes y partida
						* 	- Guarda datos del desglose del componete (datos a exportar al proyecto) (Metas,Beneficiarios,Localidad)
						*	- Guardar el desglose de metas por mes del componente
						***/

						$accion_id = $parametros['accion-id'];
						$accion = Accion::with('distribucionPresupuesto','datosComponente','partidas')->find($accion_id);
						$fibap = FIBAP::find($accion->idFibap);

						$clave_municipio = $parametros['municipio-accion'];
						$clave_localidad = $parametros['localidad-accion'];
						$municipio = Municipio::with('jurisdiccion')->where('clave','=',$clave_municipio)->get();
						$clave_jurisdiccion = $municipio[0]->jurisdiccion->clave;

						//Se buscan si la Localidad ya fue capturada
						$capturados = $accion->distribucionPresupuesto->filter(function($item) use ($clave_localidad){
							if($item->claveLocalidad == $clave_localidad){
								return true;
							}
						});

						if(count($capturados)){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"localidad-accion","error":"Esta localidad ya fue capturada para esta acción."}';
							throw new Exception('Se encontraron '.count($capturados).' elementos capturados',1);
						}

						$mes_incial = date("n",strtotime($fibap->periodoEjecucionInicio));
						$mes_final = date("n",strtotime($fibap->periodoEjecucionFinal));

						$formul_partidas = $parametros['partidas'];
						$accion_partidas = $accion->partidas->lists('id');
						$meses_capturados = $parametros['mes'];

						//Se calcula la suma total de la distribucion del presupeusto y se crea un arreglo con los elementos a almacenar
						$distribucion = array();
						$suma_presupuesto = 0;
						foreach ($accion_partidas as $partida_id) {
							$indx = array_search($partida_id, $formul_partidas);
							foreach ($meses_capturados[$indx] as $mes => $cantidad) {
								if($cantidad > 0 && $mes >= $mes_incial && $mes <= $mes_final){
									$recurso = new DistribucionPresupuesto;
									$recurso->idFibap = $accion->idFibap;
									$recurso->claveMunicipio = $clave_municipio;
									$recurso->claveLocalidad = $clave_localidad;
									$recurso->claveJurisdiccion = $clave_jurisdiccion;
									$recurso->idObjetoGasto = $partida_id;
									$recurso->mes = $mes;
									$recurso->cantidad = $cantidad;
									$distribucion[] = $recurso;
									$suma_presupuesto += $cantidad;
								}
							}
						}
						
						$suma_distribucion = $accion->distribucionPresupuesto->sum('cantidad');

						if(($suma_distribucion + $suma_presupuesto) > $accion->presupuestoRequerido){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"cantidad-presupuesto","error":"La distribución del presupuesto sobrepasa el presupuesto requerido."}';
							throw new Exception('La distribución del presupuesto sobrepasa el presupuesto requerido.', 1);
						}

						$fibap->load('acciones','datosProyecto');
						$beneficiarios_capturados = ComponenteDesglose::whereIn('idAccion',$fibap->acciones->lists('id'))
													->select(DB::raw('(sum(beneficiariosF)/count(claveLocalidad)) AS beneficiariosF'),
														DB::raw('(sum(beneficiariosM)/count(claveLocalidad)) AS beneficiariosM'))
													->where('claveLocalidad','!=',$clave_localidad)
													->groupBy('claveLocalidad')
													->get();
						
						if($beneficiarios_capturados){
							$suma_benef_f = $beneficiarios_capturados->sum('beneficiariosF');
							$suma_benef_m = $beneficiarios_capturados->sum('beneficiariosM');
						}else{
							$suma_benef_f = 0;
							$suma_benef_m = 0;
						}
						
						if(($suma_benef_f + $parametros['beneficiarios-f']) > $fibap->datosProyecto->totalBeneficiariosF){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"beneficiarios-f","error":"La suma de los beneficiarios sobrepasa la cantidad especificada al capturar la FIBAP."}';
							throw new Exception('La suma de beneficiarios sobrepasa los beneficiarios capturados al inicio.', 1);
						}

						if(($suma_benef_m + $parametros['beneficiarios-m']) > $fibap->datosProyecto->totalBeneficiariosM){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"beneficiarios-m","error":"La suma de los beneficiarios sobrepasa la cantidad especificada al capturar la FIBAP."}';
							throw new Exception('La suma de beneficiarios sobrepasa los beneficiarios capturados al inicio.', 1);
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
						
						$desglose = new ComponenteDesglose;
						$desglose->claveMunicipio 	= $clave_municipio;
						$desglose->claveLocalidad 	= $clave_localidad;
						$desglose->presupuesto 		= $suma_presupuesto;
						$desglose->meta 			= $suma_metas;
						$desglose->trim1 			= $trimestres[1];
						$desglose->trim2 			= $trimestres[2];
						$desglose->trim3 			= $trimestres[3];
						$desglose->trim4 			= $trimestres[4];
						$desglose->beneficiariosF 	= $parametros['beneficiarios-f'];
						$desglose->beneficiariosM 	= $parametros['beneficiarios-m'];

						$respuesta['data'] = DB::transaction(function() use ($accion,$distribucion, $desglose,$metas_mes){
							$accion->datosComponente->desgloseComponente()->save($desglose);
							$accion->distribucionPresupuesto()->saveMany($distribucion);
							$desglose->metasMes()->saveMany($metas_mes);
							
							$accion->load('distribucionPresupuestoAgrupado.jurisdiccion');
							return array('data'=>$accion);
						});

					}elseif($parametros['formulario'] == 'form-accion'){ //Nueva Accion

						/***
						*	Formulario de datos generales de Accion (POST)
						*
						*	- Guarda una nueva acción
						*	- Guarda una relacion de la acción con 2 partidas prespuestarias
						* 	- Guarda datos del componete (datos a exportar al proyecto)
						*	- Guarda la propuesta del financiamiento
						***/

						//Obtenemos las partidas (Deben ser 2 en un arreglo)
						$partidas = array();
						foreach ($parametros['objeto-gasto-presupuesto'] as $partida) {
							if($partida){
								$partidas[] = $partida;
							}
						}

						if(count($partidas) < 2){
							$index = count($partidas);
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"objeto-gasto-presupuesto_' . ($index + 1) . '","error":"Se deben seleccionar dos Partidas"}';
							throw new Exception("Se deben seleccionar al menos dos partidas presupuestales", 1);
						}

						$fibap = FIBAP::with('acciones')->find($parametros['fibap-id']);
						$accion = new Accion;
						
						//Datos del componente (Estos datos se eportarán al crear el proyecto, para generar los componentes)
						$componente = new FibapDatosComponente;
						$componente->idFibap 			= $fibap->id;
						$componente->idEntregable 		= $parametros['entregable'];
						$componente->idTipoComponente 	= $parametros['tipo-componente'];
						$componente->idAccionComponente = $parametros['accion-componente'];
						$componente->idUnidadMedida		= $parametros['unidad-medida'];
						$componente->indicador			= $parametros['indicador'];

						//Obtenemos los origenes del presupuesto
						$origenes = $parametros['accion-origen'];
						//Arreglo con los objetos a guardar en la base de datos, relacionados a la Accion
						$guardar_origenes = array();
						$presupuesto_suma = 0;
						foreach ($origenes as $origen => $valor) {
							if($valor > 0){
								$origen_finan = new PropuestaFinanciamiento;
								$origen_finan->idOrigenFinanciamiento = $origen;
								$origen_finan->cantidad = $valor;
								$origen_finan->idFibap = $fibap->id;
								$guardar_origenes[] = $origen_finan;
								$presupuesto_suma += $valor;
							}
						}
						$accion->presupuestoRequerido = $presupuesto_suma;

						//Obtenemos la suma de los presupuestos ya capturados y sumamos el nuevo presupuesto
						$total_presupuesto = $fibap->acciones->sum('presupuestoRequerido');
						$total_presupuesto += $presupuesto_suma;

						if($total_presupuesto > $fibap->presupuestoRequerido){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"accion-presupuesto-requerido","error":"El presupuesto capturado sobrepasa el Presupuesto Requerido asignado al proyecto."}';
							throw new Exception("El presupuesto sobrepasa el PresupuestoRequerido de la FIBAP", 1);
						}

						//Se inicia la transacción
						$respuesta['data'] = DB::transaction(function() use ($guardar_origenes, $fibap, $accion, $componente, $partidas){
							if($fibap->acciones()->save($accion)){
								$accion->partidas()->attach($partidas);
								$accion->datosComponente()->save($componente);
								$accion->propuestasFinanciamiento()->saveMany($guardar_origenes);

								$fibap->load('acciones');
								$fibap->acciones->load('datosComponente','propuestasFinanciamiento');
								return array('data'=>$accion,'acciones' => $fibap->acciones);
							}else{
								//No se pudieron guardar los datos del proyecto
								throw new Exception("Error al intentar guardar los datos de la acción: Error en el guardado de los datos de la acción", 1);
							}
						});
					}
				}catch(\Exception $ex){
					$respuesta['http_status'] = 500;
					if($respuesta['data']['data'] == ''){
						$respuesta['data']['data'] = 'Ocurrio un error en el servidor al guardar la acción.';
					}
					$respuesta['data']['ex'] = $ex->getMessage();
					if(!isset($respuesta['data']['code'])){
						$respuesta['data']['code'] = 'S03';
					}
				}
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
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
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();
		if(isset($parametros['formulario'])){
			if($parametros['formulario'] == 'form-fibap-datos'){
				$validacion = Validador::validar(Input::all(), $this->reglasFibap);
			}elseif($parametros['formulario'] == 'form-fibap-antecedentes'){
				$validacion = Validador::validar(Input::all(), $this->reglasFibapAntecedentes);
			}elseif($parametros['formulario'] == 'form-fibap-presupuesto'){ //No estoy usando
				$validacion = Validador::validar(Input::all(), $this->reglasFibapPresupuesto);
			}elseif($parametros['formulario'] == 'form-antecedente'){
				$validacion = Validador::validar(Input::all(), $this->reglasAntecedentes);
			}elseif($parametros['formulario'] == 'form-presupuesto'){
				$validacion = Validador::validar(Input::all(), $this->reglasPresupuesto);
			}elseif($parametros['formulario'] == 'form-accion'){
				$validacion = Validador::validar(Input::all(), $this->reglasAccion);
			}

			if($validacion === TRUE){
				try{
					if($parametros['formulario'] == 'form-fibap-datos'){
						if(!isset($parametros['documento-soporte'])){
							$respuesta['data']['data'] = 'Debe seleccionar al menos un documento.';
							throw new Exception("Error Processing Request", 1);
						}

						$fecha_inicio = DateTime::createFromFormat('d/m/Y',Input::get('periodo-ejecucion-inicio'));
						$fecha_fin = DateTime::createFromFormat('d/m/Y',Input::get('periodo-ejecucion-final'));

						if(!$fecha_inicio){
							$fecha_inicio = DateTime::createFromFormat('Y-m-d',Input::get('periodo-ejecucion-inicio'));
						}
						if(!$fecha_fin){
							$fecha_fin = DateTime::createFromFormat('Y-m-d',Input::get('periodo-ejecucion-final'));
						}

						if(!$fecha_inicio){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"periodo-ejecucion-inicio","error":"La fecha de inicio del periodo de ejecución no tiene el formato correcto."}';
							throw new Exception('La fecha no tiene un formato valido');
						}
						if(!$fecha_fin){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"periodo-ejecucion-final","error":"La fecha final del periodo de ejecución no tiene el formato correcto."}';
							throw new Exception('La fecha no tiene un formato valido');
						}

						if($fecha_fin < $fecha_inicio){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"periodo-ejecucion-final","error":"La fecha final del periodo de ejecución no puede ser menor que la de inicio."}';
							throw new Exception('La fecha final es menor a la de inicio');
						}

						$recurso = FIBAP::with('documentos')->find($id);
						$proyecto = FALSE;

						$recurso->organismoPublico 			= $parametros['organismo-publico'];
						$recurso->sector 					= $parametros['sector'];
						$recurso->subcomite 				= $parametros['subcomite'];
						$recurso->grupoTrabajo 				= $parametros['grupo-trabajo'];
						$recurso->justificacionProyecto 	= $parametros['justificacion-proyecto'];
						$recurso->descripcionProyecto 		= $parametros['descripcion-proyecto'];
						$recurso->objetivoProyecto			= $parametros['objetivo-proyecto'];
						$recurso->alineacionEspecifica 		= $parametros['alineacion-especifica'];
						$recurso->alineacionGeneral 		= $parametros['alineacion-general'];
						$recurso->periodoEjecucionInicio 	= $fecha_inicio;
						$recurso->periodoEjecucionFinal  	= $fecha_fin;
						$recurso->presupuestoRequerido 	 	= $parametros['presupuesto-requerido'];

						if(!isset($parametros['proyecto-id'])){
							$recurso->load('datosProyecto');
							$proyecto = $recurso->datosProyecto;
							$proyecto->nombreTecnico = $parametros['proyecto'];
							$proyecto->idTipoProyecto = $parametros['tipo-proyecto'];
							$proyecto->programaPresupuestario = $parametros['programa-presupuestal'];
							$proyecto->idCobertura = $parametros['cobertura'];

							if($parametros['cobertura'] == 2){
								$proyecto->claveRegion = NULL;
								$proyecto->claveMunicipio = $parametros['municipio'];
							}elseif($parametros['cobertura'] == 3){
								$proyecto->claveMunicipio = NULL;
								$proyecto->claveRegion = $parametros['region'];
							}else{
								$proyecto->claveMunicipio = NULL;
								$proyecto->claveRegion = NULL;
							}

							$proyecto->idObjetivoPED = $parametros['vinculacion-ped'];
							$proyecto->idTipoBeneficiario = $parametros['tipo-beneficiario'];
							$proyecto->totalBeneficiarios = $parametros['total-beneficiarios-f']+$parametros['total-beneficiarios-m'];
							$proyecto->totalBeneficiariosF = $parametros['total-beneficiarios-f'];
							$proyecto->totalBeneficiariosM = $parametros['total-beneficiarios-m'];
						}

						$documentos = $parametros['documento-soporte'];
						$documentos_anteriores = $recurso->documentos->lists('id');

						$docs_nuevos = array_diff($documentos, $documentos_anteriores);
						$docs_borrar = array_diff($documentos_anteriores, $documentos);

						$respuesta['data'] = DB::transaction(function() use ($recurso, $proyecto, $docs_nuevos, $docs_borrar){
							if($recurso->save()){
								if(count($docs_borrar)){
									$recurso->documentos()->detach($docs_borrar);
								}
								if(count($docs_nuevos)){
									$recurso->documentos()->attach($docs_nuevos);
								}
								$datos_extra = array();
								if($proyecto){
									if(!$proyecto->save()){
										$respuesta['data']['code'] = 'S01';
										throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado del proyecto", 1);
									}
									if($proyecto->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
										$datos_extra['municipios'] = Municipio::with('localidades')->get(); //Todos los municipios
									}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
										$datos_extra['municipios'] = Municipio::with('localidades')->where('clave','=',$proyecto->claveMunicipio)->get(); //Obtenemos el municipio seleccionado
									}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
										$region = Region::with('municipios')->where('region','=',$proyecto->claveRegion)->get();
										$region[0]->municipios->load('localidades');
										$datos_extra['municipios'] = $region[0]->municipios;
									}
								}
								return array('data'=>$recurso, 'extras'=>$datos_extra);
							}else{
								//No se pudieron guardar los datos del proyecto
								$respuesta['data']['code'] = 'S01';
								throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado de la ficha", 1);
							}
						});
					}elseif($parametros['formulario'] == 'form-fibap-antecedentes'){
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
					}elseif($parametros['formulario'] == 'form-antecedente'){ //Editar antecedente
						$fecha_corte = DateTime::createFromFormat('d/m/Y',Input::get('fecha-corte-antecedente'));

						if(!$fecha_corte){
							$fecha_corte = DateTime::createFromFormat('Y-m-d',Input::get('fecha-corte-antecedente'));
						}

						if(!$fecha_corte){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"fecha-corte-antecedente","error":"La fecha de corte no tiene el formato correcto."}';
							throw new Exception('La fecha no tiene un formato valido');
						}

						$recurso = AntecedenteFinanciero::find($id);
						$recurso->anio = $parametros['anio-antecedente'];
						$recurso->autorizado = $parametros['autorizado-antecedente'];
						$recurso->ejercido = $parametros['ejercido-antecedente'];
						$recurso->fechaCorte = $fecha_corte;
						$recurso->porcentaje = ($recurso->ejercido * 100) / $recurso->autorizado;

						if($recurso->save()){
							$fibap = FIBAP::with('antecedentesFinancieros')->find($parametros['fibap-id']);
							$respuesta['data'] = array('data'=>$recurso,'antecedentes' => $fibap->antecedentesFinancieros);
						}else{
							//No se pudieron guardar los datos del proyecto
							throw new Exception("Error al intentar guardar los datos del antecedente", 1);
						}
					}elseif($parametros['formulario'] == 'form-presupuesto'){ //Editar presupuesto
						/***
						*	Formulario de datos del desglose del presupuesto de la Accion (PUT)
						*
						*	- Actualiza la distribución de presupuesto por mes y partida
						* 	- Actualiza datos del desglose del componete (datos a exportar al proyecto) (Metas,Beneficiarios,Localidad)
						*	- Actualiza el desglose de metas por mes del componente
						***/
						
						$desglose = ComponenteDesglose::with('metasMes')->find($parametros['id-desglose']);

						$accion_id = $desglose->idAccion;
						$clave_municipio = $desglose->claveMunicipio;
						$clave_localidad = $desglose->claveLocalidad;
						$clave_jurisdiccion = FALSE;

						$accion = Accion::with('distribucionPresupuesto','datosComponente','partidas')->find($accion_id);
						$fibap = FIBAP::find($accion->idFibap);

						$nueva_clave_municipio = $parametros['municipio-accion'];
						$nueva_clave_localidad = $parametros['localidad-accion'];

						//Obtenemos la clave de la jurisdiccion para los nuevos elementos a capturar
						$municipio = Municipio::with('jurisdiccion')->where('clave','=',$nueva_clave_municipio)->get();
						$clave_jurisdiccion = $municipio[0]->jurisdiccion->clave;

						//SI cambio la localidad hay que buscar si la nueva localidad ya fue capturada
						if($nueva_clave_localidad != $clave_localidad){
							//Se buscan si la Localidad ya fue capturada
							$capturados = $accion->distribucionPresupuesto->filter(function($item) use ($nueva_clave_localidad){
								if($item->claveLocalidad == $nueva_clave_localidad){
									return true;
								}
							});

							if(count($capturados)){
								$respuesta['data']['code'] = 'U00';
								$respuesta['data']['data'] = '{"field":"localidad-accion","error":"Esta localidad ya fue capturada para esta acción."}';
								throw new Exception('Se encontraron '.count($capturados).' elementos capturados',1);
							}
						}

						$mes_incial = date("n",strtotime($fibap->periodoEjecucionInicio));
						$mes_final = date("n",strtotime($fibap->periodoEjecucionFinal));

						//Las partidas del formulario
						$formul_partidas = $parametros['partidas'];
						//Las partidas de la base de datos
						$accion_partidas = $accion->partidas->lists('id');
						$meses_capturados = $parametros['mes'];
						$meses_ids = $parametros['meses-capturados']; //Elementos ya capturados por tanto se actualizaran

						//Se calcula la suma total de la distribucion del presupeusto y se crea un arreglo con los elementos a almacenar
						$distribucion = array();
						$suma_presupuesto = 0;
						foreach ($accion_partidas as $partida_id) {
							$indx = array_search($partida_id, $formul_partidas);
							foreach ($meses_capturados[$indx] as $mes => $cantidad) {
								if(isset($meses_ids[$indx][$mes])){
									$recurso = $accion->distribucionPresupuesto->find($meses_ids[$indx][$mes]);
									$recurso->claveMunicipio = $nueva_clave_municipio;
									$recurso->claveLocalidad = $nueva_clave_localidad;
									$recurso->claveJurisdiccion = $clave_jurisdiccion;
									$recurso->cantidad = $cantidad;
									$distribucion[] = $recurso;
									//Aqui me quede llenando la edicion de la distribucion del presupuesto
								}elseif($cantidad > 0 && $mes >= $mes_incial && $mes <= $mes_final){
									$recurso = new DistribucionPresupuesto;
									$recurso->idFibap = $accion->idFibap;
									$recurso->claveMunicipio = $nueva_clave_municipio;
									$recurso->claveLocalidad = $nueva_clave_localidad;
									$recurso->claveJurisdiccion = $clave_jurisdiccion;
									$recurso->idObjetoGasto = $partida_id;
									$recurso->mes = $mes;
									$recurso->cantidad = $cantidad;
									$distribucion[] = $recurso;
								}
								$suma_presupuesto += $cantidad;
							}
						}
						
						$sumatoria = $accion->distribucionPresupuesto->filter(function($item) use ($clave_municipio,$clave_localidad){
							if($item->claveLocalidad != $clave_localidad && $item->claveMunicipio != $clave_municipio){
								return true;
							}
						});

						$suma_distribucion = $sumatoria->sum('cantidad');

						if(($suma_distribucion + $suma_presupuesto) > $accion->presupuestoRequerido){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"cantidad-presupuesto","error":"La distribución del presupuesto sobrepasa el presupuesto requerido."}';
							throw new Exception('La distribución del presupuesto sobrepasa el presupuesto requerido.', 1);
						}

						//Si el numero de beneficiarios cambia, hay que checar si el nuevo numero no sobrepasa al anterior
						if ($desglose->beneficiariosF != $parametros['beneficiarios-f'] || $desglose->beneficiariosM != $parametros['beneficiarios-m']) {
							$fibap->load('acciones','datosProyecto');
							$beneficiarios_capturados = ComponenteDesglose::whereIn('idAccion',$fibap->acciones->lists('id'))
														->select(DB::raw('(sum(beneficiariosF)/count(claveLocalidad)) AS beneficiariosF'),
															DB::raw('(sum(beneficiariosM)/count(claveLocalidad)) AS beneficiariosM'))
														->where('claveLocalidad','!=',$clave_localidad)
														->groupBy('claveLocalidad')
														->get();
							
							if($beneficiarios_capturados){
								$suma_benef_f = $beneficiarios_capturados->sum('beneficiariosF');
								$suma_benef_m = $beneficiarios_capturados->sum('beneficiariosM');
							}else{
								$suma_benef_f = 0;
								$suma_benef_m = 0;
							}
							
							if(($suma_benef_f + $parametros['beneficiarios-f']) > $fibap->datosProyecto->totalBeneficiariosF){
								$respuesta['data']['code'] = 'U00';
								$respuesta['data']['data'] = '{"field":"beneficiarios-f","error":"La suma de los beneficiarios sobrepasa la cantidad especificada al capturar la FIBAP."}';
								throw new Exception('La suma de beneficiarios sobrepasa los beneficiarios capturados al inicio.', 1);
							}

							if(($suma_benef_m + $parametros['beneficiarios-m']) > $fibap->datosProyecto->totalBeneficiariosM){
								$respuesta['data']['code'] = 'U00';
								$respuesta['data']['data'] = '{"field":"beneficiarios-m","error":"La suma de los beneficiarios sobrepasa la cantidad especificada al capturar la FIBAP."}';
								throw new Exception('La suma de beneficiarios sobrepasa los beneficiarios capturados al inicio.', 1);
							}
						}
						
						$suma_metas = 0;
						$trimestres = array( 1=>0 , 2=>0 , 3=>0 , 4=>0);
						$metas_mes = array();
						$metas_ids = $parametros['metas-capturadas']; //Elementos ya capturados por tanto se actualizaran
						foreach ($parametros['meta-mes'] as $mes => $meta) {
							if(isset($metas_ids[$mes])){
								$recurso = $desglose->metasMes->find($metas_ids[$mes]);
								$recurso->meta = $meta;
								$metas_mes[] = $recurso;
							}elseif($meta > 0 ){
								$recurso = new DesgloseMetasMes;
								$recurso->mes = $mes;
								$recurso->meta = $meta;
								$metas_mes[] = $recurso;
							}
							$trimestres[ceil(($mes/3))] += $meta;
							$suma_metas += $meta;
						}
						
						$desglose->claveMunicipio 	= $nueva_clave_municipio;
						$desglose->claveLocalidad 	= $nueva_clave_localidad;
						$desglose->presupuesto 		= $suma_presupuesto;
						$desglose->meta 			= $suma_metas;
						$desglose->trim1 			= $trimestres[1];
						$desglose->trim2 			= $trimestres[2];
						$desglose->trim3 			= $trimestres[3];
						$desglose->trim4 			= $trimestres[4];
						$desglose->beneficiariosF 	= $parametros['beneficiarios-f'];
						$desglose->beneficiariosM 	= $parametros['beneficiarios-m'];

						//throw new Exception(json_encode(print_r($desglose->toArray(),true)), 1);

						$respuesta['data'] = DB::transaction(function() use ($accion,$distribucion, $desglose,$metas_mes){
							$accion->datosComponente->desgloseComponente()->save($desglose);
							$accion->distribucionPresupuesto()->saveMany($distribucion);
							$desglose->metasMes()->saveMany($metas_mes);
							
							$accion->load('distribucionPresupuestoAgrupado.jurisdiccion');
							return array('data'=>$accion);
						});

					}elseif($parametros['formulario'] == 'form-accion'){ //Editar Accion

						/***
						*	Formulario de datos generales de Acción (PUT)
						*
						*	- Actualiza datoa de una acción
						*	- Actualiza la relación de la acción con 2 partidas prespuestarias
						* 	- Actualiza datos del componete (datos a exportar al proyecto)
						*	- Actualiza la propuesta del financiamiento
						***/

						$fibap = FIBAP::with('acciones')->find($parametros['fibap-id']);
						$accion = Accion::with('propuestasFinanciamiento','partidas','datosComponente')->find($id);
						
						//Se obtienen la prupuesta del financiamiento y los ids de los elementos ya capturados
						$origenes = $parametros['accion-origen'];
						$origenes_ids = array();
						if(isset($parametros['origen-captura-id'])){
							$origenes_ids = $parametros['origen-captura-id'];
						}
						
						$componente = $accion->datosComponente;
						$componente->idEntregable 		= $parametros['entregable'];
						$componente->idTipoComponente 	= $parametros['tipo-componente'];
						$componente->idAccionComponente = $parametros['accion-componente'];
						$componente->idUnidadMedida 	= $parametros['unidad-medida'];
						$componente->indicador 			= $parametros['indicador'];

						//Obtenemos las partidas seleccionadas en el formulario y las partidas ya capturadas
						$partidas_formulario = $parametros['objeto-gasto-presupuesto'];
						$partidas_anteriores = $accion->partidas->lists('id');

						//Sacamos las diferencias de las partidas seleccionadas y las ya capturadas
						$partidas['nuevas'] = array_diff($partidas_formulario, $partidas_anteriores);
						$partidas['borrar'] = array_diff($partidas_anteriores, $partidas_formulario);

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
						
						if($presupuesto_suma != $accion->presupuestoRequerido){
							//Obtenemos la suma de los presupuestos ya capturados y sumamos el nuevo presupuesto
							$total_presupuesto = $fibap->acciones->sum('presupuestoRequerido');
							$total_presupuesto += $presupuesto_suma;
							//Quitamos el presupuesto anterior, de lo contrario sumara un presupuesto de más
							$total_presupuesto -= $accion->presupuestoRequerido;

							if($total_presupuesto > $fibap->presupuestoRequerido){
								$respuesta['data']['code'] = 'U00';
								$respuesta['data']['data'] = '{"field":"accion-presupuesto-requerido","error":"El presupuesto capturado sobrepasa el Presupuesto Requerido asignado al proyecto."}';
								throw new Exception("El presupuesto sobrepasa el PresupuestoRequerido de la FIBAP", 1);
							}

							$accion->presupuestoRequerido = $presupuesto_suma;
						}
						
						$respuesta['data'] = DB::transaction(function() use ($guardar_origenes, $fibap, $accion, $componente, $partidas){
							if($accion->save()){
								if(count($partidas['borrar'])){
									$accion->partidas()->detach($partidas['borrar']);
								}
								if(count($partidas['nuevas'])){
									$accion->partidas()->attach($partidas['nuevas']);
								}
								$accion->datosComponente()->save($componente);
								$accion->propuestasFinanciamiento()->saveMany($guardar_origenes);

								$fibap->load('acciones');
								$fibap->acciones->load('datosComponente','propuestasFinanciamiento');
								return array('data'=>$accion,'acciones' => $fibap->acciones);
							}else{
								//No se pudieron guardar los datos del proyecto
								throw new Exception("Error al intentar guardar los datos de la acción: Error en el guardado de los datos de la acción", 1);
							}
						});
					}
				}catch(\Exception $ex){
					$respuesta['http_status'] = 500;
					if($respuesta['data']['data'] == ''){
						$respuesta['data']['data'] = 'Ocurrio un error en el servidor al guardar los datos.';
					}
					$respuesta['data']['ex'] = $ex->getMessage();
					if(!isset($respuesta['data']['code'])){
						$respuesta['data']['code'] = 'S03';
					}
				}
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
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

			if(isset($parametros['eliminar'])){ //Con parametros, el delete viene de dentro de Editar Fibap
				if($parametros['eliminar'] == 'presupuesto'){ //Eliminar Distribucion del Presupuesto
					/***
					*	Eliminar desglose del presupuesto de la Accion (DELETE)
					*
					* 	- Borrar datos del desglose del componete (Metas,Beneficiarios,Localidad)
					*	- Borrar la distribución de presupuesto por mes y partida
					*	- Borrar el desglose de metas por mes del componente
					***/
					$id_padre = $parametros['id-accion'];
					$rows = DB::transaction(function() use ($ids){
						//Obtenemos los ids de las actividades de los componentes seleccionados
						$distribuciones = DistribucionPresupuesto::whereIn('id',$ids)
																	->select('idAccion','claveMunicipio','claveLocalidad')
																	//->groupBy('idAccion','claveMunicipio','claveLocalidad')
																	->get();
						//Eliminamos la distribucion del presupuesto
						$distribucion_eliminar = DistribucionPresupuesto::getModel();
						$desgloses_eliminar = ComponenteDesglose::getModel();
						foreach ($distribuciones as $distribucion) {
							$distribucion_eliminar = $distribucion_eliminar->orWhere(function($query) use ($distribucion){
								$query->where('idAccion','=',$distribucion->idAccion)
										->where('claveMunicipio','=',$distribucion->claveMunicipio)
										->where('claveLocalidad','=',$distribucion->claveLocalidad);
							});
							$desgloses_eliminar = $desgloses_eliminar->orWhere(function($query) use ($distribucion){
								$query->where('idAccion','=',$distribucion->idAccion)
										->where('claveMunicipio','=',$distribucion->claveMunicipio)
										->where('claveLocalidad','=',$distribucion->claveLocalidad);
							});
						}

						$distribucion_eliminar->delete();
						$desgloses_ids = $desgloses_eliminar->get();
						DesgloseMetasMes::whereIn('idComponenteDesglose',$desgloses_ids->lists('id'))->delete();
						return $desgloses_eliminar->delete();
					});
				}elseif($parametros['eliminar'] == 'antecedente'){ //Eliminar Antecedente(s)
					$id_padre = $parametros['id-fibap'];
					$rows = DB::transaction(function() use ($ids){
						//Eliminamos las actividades
						return AntecedenteFinanciero::wherein('id',$ids)->delete();
					});
				}elseif($parametros['eliminar'] == 'accion'){
					/***
					*	Eliminar Acción (DELETE)
					*
					*	- Borrar datos de una acción
					*	- Borrar la relación de la acción con las partidas prespuestarias
					* 	- Borrar datos del componete (datos a exportar al proyecto)
					*	- Borrar la propuesta de financiamiento
					*	- Borrar la distribución del presupuesto
					* 	- Borrar datos del desglose del componete (Metas,Beneficiarios,Localidad)
					*	- Borrar el desglose de metas por mes del componente
					*
					***/
					$id_padre = $parametros['id-fibap'];
					$rows = DB::transaction(function() use ($ids){
						FibapDatosComponente::whereIn('idAccion',$ids)->delete();
						PropuestaFinanciamiento::whereIn('idAccion',$ids)->delete();
						DistribucionPresupuesto::whereIn('idAccion',$ids)->delete();
						$desgloses = ComponenteDesglose::whereIn('idAccion',$ids)->get();
						$desgloses->metasMes()->delete();
						$desgloses->delete();
						$acciones = Accion::whereIn('id',$ids)->get();
						foreach ($acciones as $accion) {
							$accion->partidas()->detach();
						}
						return Accion::whereIn('id',$ids)->delete();
					});
				}
			}else{ //Sin parametros el delete viene de la lista de fibaps
				$rows = DB::transaction(function() use ($ids){
					//Eliminamos los datos del proyecto, en caso de que el FIBAP no haya sido asignado a ningun proyecto
					FibapDatosProyecto::whereIn('idFibap',$ids)->delete();
					//Eliminamos los documentos de soporte asignados al fibap
					$fibaps = FIBAP::whereIn('id',$ids)->with('documentos')->get();
					foreach ($fibaps as $fibap) {
						$fibap->documentos()->detach();
					}
					//Eliminamos los antecedentes financieros
					AntecedenteFinanciero::whereIn('idFibap',$ids)->delete();
					//Eliminamos la distribución del presupuesto
					DistribucionPresupuesto::whereIn('idFibap',$ids)->delete();
					//Eliminamos la propuesta de financiamiento
					PropuestaFinanciamiento::whereIn('idFibap',$ids)->delete();
					//Eliminamos los FIBAPs
					return FIBAP::whereIn('id',$ids)->delete();
				});
			}

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
				if(isset($parametros['eliminar'])){
					if($parametros['eliminar'] == 'antecedente'){
						$data['antecedentes'] = AntecedenteFinanciero::where('idFibap',$id_padre)->get();
					}elseif($parametros['eliminar'] == 'presupuesto'){
						$data['accion'] = Accion::with('distribucionPresupuestoAgrupado.jurisdiccion')->find($id_padre);
					}elseif($parametros['eliminar'] == 'accion'){
						//mako037
						$fibap = FIBAP::with('acciones')->find($id_padre);
						$fibap->acciones->load('datosComponente','propuestasFinanciamiento');
						$data['acciones'] = $fibap->acciones;
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
}
