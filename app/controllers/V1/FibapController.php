<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Exception, DateTime;
use FIBAP, Proyecto, FibapDatosProyecto, PropuestaFinanciamiento, AntecedenteFinanciero, DistribucionPresupuesto, OrigenFinanciamiento;
use Jurisdiccion, Municipio, Region;

class FibapController extends BaseController {
	private $reglasFibap = array(
		'organismo-publico'			=> 'required',
		'sector'					=> 'required',
		'subcomite'					=> 'required',
		'grupo-trabajo'				=> 'required',
		'justificacion-proyecto'	=> 'required',
		'descripcion-proyecto'		=> 'required',
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
		'total-beneficiarios-m'		=> 'required_without:proyecto-id'
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

	private $reglasAntecedentes = array(
		'anio-antecedente'				=> 'required|integer|min:1000',
		'autorizado-antecedente'		=> 'required',
		'ejercido-antecedente'			=> 'required',
		'fecha-corte-antecedente'		=> 'required'
	);

	private $reglasPresupuesto = array(
		'objeto-gasto-presupuesto'	=> 'required',
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
		$data = array();

		$parametros = Input::all();
		$calendarizado = FALSE;
		$clave_presupuestaria = FALSE;
		$jurisdicciones = FALSE;
		if($parametros){
			if($parametros['ver'] == 'fibap'){
				$recurso = FIBAP::with('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado','acciones')->find($id);
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
				}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
					$jurisdicciones = Municipio::obtenerJurisdicciones($proyecto->claveMunicipio)->get();
				}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
					$jurisdicciones = Region::obtenerJurisdicciones($proyecto->claveRegion)->get();
				}
			}elseif($parametros['ver'] == 'antecedente'){
				$recurso = AntecedenteFinanciero::find($id);
			}elseif($parametros['ver'] == 'distribucion-presupuesto'){
				$recurso = DistribucionPresupuesto::find($id);
				//Actualziar despues
				//$calendarizado = Ministracion::where('idFibap',$recurso->idFibap)->where('idObjetoGasto',$recurso->idObjetoGasto)->get();
				$calendarizado = DistribucionPresupuesto::where('idFibap',$recurso->idFibap)->where('idObjetoGasto',$recurso->idObjetoGasto)->get();
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
			if($jurisdicciones){
				$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
			}
			$data = array("data"=>$recurso);
			if($calendarizado){
				$data['calendarizado'] = $calendarizado->toArray();
			}
			if($clave_presupuestaria){
				$data['clavePresupuestaria'] = $clave_presupuestaria;
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
			}

			if($validacion === TRUE){
				try{
					if($parametros['formulario'] == 'form-fibap-datos'){
						if(!isset($parametros['documento-soporte'])){
							$respuesta['data']['data'] = 'Debe seleccionar al menos un documento.';
							throw new Exception("Error Processing Request", 1);
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
						$recurso->alineacionEspecifica 	 = $parametros['alineacion-especifica'];
						$recurso->alineacionGeneral 	 = $parametros['alineacion-general'];
						$recurso->idEstatusProyecto 	 = 1;

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
								if($proyecto){
									if(!$recurso->datosProyecto()->save($proyecto)){
										$respuesta['data']['code'] = 'S01';
										throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado del proyecto", 1);
									}
								}
								return array('data'=>$recurso);
							}else{
								//No se pudieron guardar los datos del proyecto
								$respuesta['data']['code'] = 'S01';
								throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado de la ficha", 1);
							}
							
						});
						
					}elseif($parametros['formulario'] == 'form-antecedente'){
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
								Nuevo Presupuesto
						****/

						$fibap = FIBAP::with('distribucionPresupuesto')->find($parametros['fibap-id']);

						$idObjetoGasto = $parametros['objeto-gasto-presupuesto'];

						$capturados = $fibap->distribucionPresupuesto->filter(function($item) use ($idObjetoGasto){
							if($item->idObjetoGasto == $idObjetoGasto){
								return true;
							}
						});

						if(count($capturados)){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"objeto-gasto-presupuesto","error":"Esta partida ya fue capturada para este proyecto."}';
							throw new Exception('Se encontraron '.count($capturados).' elementos capturados',1);
						}

						$mes_incial = date("n",strtotime($fibap->periodoEjecucionInicio));
						$mes_final = date("n",strtotime($fibap->periodoEjecucionFinal));

						$distribucion = array();
						$suma_presupuesto = 0;
						foreach ($parametros['mes'] as $mes => $cantidad) {
							if($cantidad > 0 && $mes >= $mes_incial && $mes <= $mes_final){
								$recurso = new DistribucionPresupuesto;
								$recurso->idObjetoGasto = $idObjetoGasto;
								$recurso->mes = $mes;
								$recurso->cantidad = $cantidad;
								$distribucion[] = $recurso;
								$suma_presupuesto += $cantidad;
							}
						}

						$suma_distribucion = $fibap->distribucionPresupuesto->sum('cantidad');
						//parametros['cantidad-presupuesto']
						if(($suma_distribucion + $suma_presupuesto) > $fibap->presupuestoRequerido){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"cantidad-presupuesto","error":"La distribución del presupuesto sobrepasa el presupuesto requerido."}';
							throw new Exception('La distribución del presupuesto sobrepasa el presupuesto requerido.', 1);
						}

						$respuesta['data'] = DB::transaction(function() use ($fibap,$distribucion){
							$fibap->distribucionPresupuesto()->saveMany($distribucion);
							$fibap->load('distribucionPresupuestoAgrupado');
							$fibap->distribucionPresupuestoAgrupado->load('objetoGasto');
							return array('data'=>'Presupuesto almacenado','distribucion' => $fibap->distribucionPresupuestoAgrupado);
						});
					}
				}catch(\Exception $ex){
					$respuesta['http_status'] = 500;
					if($respuesta['data']['data'] == ''){
						$respuesta['data']['data'] = 'Ocurrio un error en el servidor al guardar la actividad.';
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
			}elseif($parametros['formulario'] == 'form-fibap-presupuesto'){
				$validacion = Validador::validar(Input::all(), $this->reglasFibapPresupuesto);
			}elseif($parametros['formulario'] == 'form-antecedente'){
				$validacion = Validador::validar(Input::all(), $this->reglasAntecedentes);
			}elseif($parametros['formulario'] == 'form-presupuesto'){
				$validacion = Validador::validar(Input::all(), $this->reglasPresupuesto);
			}

			if($validacion === TRUE){
				try{
					if($parametros['formulario'] == 'form-fibap-datos'){
						if(!isset($parametros['documento-soporte'])){
							$respuesta['data']['data'] = 'Debe seleccionar al menos un documento.';
							throw new Exception("Error Processing Request", 1);
						}
						$recurso = FIBAP::with('documentos')->find($id);
						$proyecto = FALSE;

						$recurso->organismoPublico = $parametros['organismo-publico'];
						$recurso->sector = $parametros['sector'];
						$recurso->subcomite = $parametros['subcomite'];
						$recurso->grupoTrabajo = $parametros['grupo-trabajo'];
						$recurso->justificacionProyecto = $parametros['justificacion-proyecto'];
						$recurso->descripcionProyecto = $parametros['descripcion-proyecto'];
						$recurso->alineacionEspecifica = $parametros['alineacion-especifica'];
						$recurso->alineacionGeneral = $parametros['alineacion-general'];

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
								if($proyecto){
									if(!$proyecto->save()){
										$respuesta['data']['code'] = 'S01';
										throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado del proyecto", 1);
									}
								}
								return array('data'=>$recurso);
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
					}elseif($parametros['formulario'] == 'form-fibap-presupuesto'){ //Agregar/Editar datos del presupuesto
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

						$recurso = FIBAP::with('propuestasFinanciamiento')->find($id);
						$meses_borrar = array();
						if($recurso->periodoEjecucionInicio != $fecha_inicio || $recurso->periodoEjecucionFinal != $fecha_fin){
							$meses_borrar['inicio'] = $fecha_inicio->format("n");
							$meses_borrar['fin'] = $fecha_fin->format("n");
						}

						$recurso->periodoEjecucionInicio = $fecha_inicio;
						$recurso->periodoEjecucionFinal = $fecha_fin;

						$origenes = $parametros['origen'];
						$origenes_ids = array();

						if(isset($parametros['origen-captura-id'])){
							$origenes_ids = $parametros['origen-captura-id'];
						}

						$respuesta['data'] = DB::transaction(function() use ($origenes, $origenes_ids, $recurso, $meses_borrar){
							$guardar_origenes = array();
							$presupuesto_suma = 0;
							foreach ($origenes as $origen => $valor) {
								if(isset($origenes_ids[$origen])){
									$origen_finan = $recurso->propuestasFinanciamiento()->find($origenes_ids[$origen]);
									$origen_finan->cantidad = ($valor)? $valor:0;
									$guardar_origenes[] = $origen_finan;
								}elseif($valor > 0){
									$origen_finan = new PropuestaFinanciamiento;
									$origen_finan->idOrigenFinanciamiento = $origen;
									$origen_finan->cantidad = $valor;
									$guardar_origenes[] = $origen_finan;
								}
								$presupuesto_suma += $valor;
							}

							$recurso->presupuestoRequerido = $presupuesto_suma;

							if(count($meses_borrar)){
								$recurso->distribucionPresupuesto()
										->where('mes','<',$meses_borrar['inicio'])
										->orWhere('mes','>',$meses_borrar['fin'])
										->delete();
							}

							$recurso->propuestasFinanciamiento()->saveMany($guardar_origenes);

							if($recurso->save()){
								$recurso->load('distribucionPresupuestoAgrupado');
								$recurso->distribucionPresupuestoAgrupado->load('objetoGasto');
								return array('data'=>$recurso,'distribucion' => $recurso->distribucionPresupuestoAgrupado);
							}else{
								//No se pudieron guardar los datos del proyecto
								throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado de los datos del presupuesto", 1);
							}
						});
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

						/****
							Editar presupuesto
						****/

						$fibap = FIBAP::with('distribucionPresupuesto')->find($parametros['fibap-id']);
						$presupuesto_base = DistribucionPresupuesto::find($id);

						if(isset($parametros['meses-capturados'])){
							$recursos_ids = $parametros['meses-capturados'];
						}else{
							$recursos_ids = array();
						}

						$idObjetoGasto = $parametros['objeto-gasto-presupuesto'];

						if($idObjetoGasto != $presupuesto_base->idObjetoGasto){
							$capturados = $fibap->distribucionPresupuesto->filter(function($item) use ($idObjetoGasto, $recursos_ids){
								$buscar_id = $item->id;
								$encontrado = array_first($recursos_ids, function($key,$value)use($buscar_id){ 
										return $value == $buscar_id;
									});
								if($item->idObjetoGasto == $idObjetoGasto && !$encontrado ){
									return true;
								}
							});

							if(count($capturados)){
								$respuesta['data']['code'] = 'U00';
								$respuesta['data']['data'] = '{"field":"objeto-gasto-presupuesto","error":"Esta partida ya fue capturada para este proyecto."}';
								throw new Exception('Se encontraron '.count($capturados).' elementos capturados',1);
							}
						}
						
						$mes_incial = date("n",strtotime($fibap->periodoEjecucionInicio));
						$mes_final = date("n",strtotime($fibap->periodoEjecucionFinal));

						$calendarizado = array();
						$suma_presupuesto = 0;
						foreach ($parametros['mes'] as $mes => $cantidad) {
							if(isset($recursos_ids[$mes])){
								$recurso = DistribucionPresupuesto::find($recursos_ids[$mes]);
								$recurso->idObjetoGasto = $parametros['objeto-gasto-presupuesto'];
								$recurso->mes = $mes;
								if($mes >= $mes_incial && $mes <= $mes_final){
									$recurso->cantidad = $cantidad;
								}else{
									$recurso->cantidad = 0;
								}
								$calendarizado[] = $recurso;
							}elseif($cantidad > 0){
								if($mes >= $mes_incial && $mes <= $mes_final){
									$recurso = new DistribucionPresupuesto;
									$recurso->idObjetoGasto = $parametros['objeto-gasto-presupuesto'];
									$recurso->mes = $mes;
									$recurso->cantidad = $cantidad;
									$calendarizado[] = $recurso;
								}
							}
							$suma_presupuesto += $cantidad;
						}

						//
						$sumatoria = $fibap->distribucionPresupuesto->filter(function($item) use ($presupuesto_base){
							if($item->idObjetoGasto != $presupuesto_base->idObjetoGasto){
								return true;
							}
						});

						//$suma_distribucion = $fibap->distribucionPresupuesto->sum('cantidad');
						$suma_distribucion = $sumatoria->sum('cantidad');

						if(($suma_distribucion + $suma_presupuesto) > $fibap->presupuestoRequerido){
							$respuesta['data']['code'] = 'U00';
							$respuesta['data']['data'] = '{"field":"cantidad-presupuesto","error":"La distribución del presupuesto sobrepasa el presupuesto requerido."}';
							throw new Exception('La distribución del presupuesto sobrepasa el presupuesto requerido.', 1);
						}

						$respuesta['data'] = DB::transaction(function() use ($fibap,$calendarizado){
							$fibap->distribucionPresupuesto()->saveMany($calendarizado);
							$fibap->load('distribucionPresupuestoAgrupado');
							$fibap->distribucionPresupuestoAgrupado->load('objetoGasto');
							return array('data'=>'Presupuesto Editado','distribucion' => $fibap->distribucionPresupuestoAgrupado);
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
					$id_padre = $parametros['id-fibap'];
					$rows = DB::transaction(function() use ($ids,$id_padre){
						//Obtenemos los ids de las actividades de los componentes seleccionados
						$objetos_gastos_ids = DistribucionPresupuesto::wherein('id',$ids)->lists('idObjetoGasto');
						//Eliminamos la distribucion del presupuesto
						return DistribucionPresupuesto::where('idFibap',$id_padre)
													  ->whereIn('idObjetoGasto',$objetos_gastos_ids)
													  ->delete();
					});
				}
				if($parametros['eliminar'] == 'antecedente'){ //Eliminar Antecedente(s)
					$id_padre = $parametros['id-fibap'];
					$rows = DB::transaction(function() use ($ids){
						//Eliminamos las actividades
						return AntecedenteFinanciero::wherein('id',$ids)->delete();
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
					}
					if($parametros['eliminar'] == 'presupuesto'){
						$fibap = FIBAP::find($id_padre);
						$fibap->load('distribucionPresupuesto');
						$fibap->distribucionPresupuesto->load('objetoGasto');
						$data['presupuesto'] = $fibap->distribucionPresupuesto;
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