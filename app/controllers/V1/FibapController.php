<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use FIBAP, Proyecto, FibapDatosProyecto, PropuestaFinanciamiento, AntecedenteFinanciero, Exception;

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
		'periodo-ejecucion'			=> 'required'
	);

	private $reglasAntecedentes = array(
		'anio-antecedente'				=> 'required',
		'autorizado-antecedente'		=> 'required',
		'ejercido-antecedente'			=> 'required',
		'fecha-corte-antecedente'		=> 'required'
	);

	private $reglasPresupuesto = array(
		'presupuesto-requerido'		=> 'required',
		'periodo-ejecucion'			=> 'required'
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
			
			$rows = $rows->select('fibap.id',DB::raw('concat(p.unidadResponsable,p.finalidad,p.funcion,p.subfuncion,p.subsubfuncion,p.programaSectorial,p.programaPresupuestario,p.programaEspecial,p.actividadInstitucional,p.proyectoEstrategico,LPAD(p.numeroProyectoEstrategico,3,"0")) as Proyecto'),'p.nombreTecnico',DB::raw('NULL as tipo'),'descripcionProyecto','sentryUsers.username','fibap.modificadoAl')
								->leftjoin('sentryUsers','sentryUsers.id','=','fibap.creadoPor')
								->leftjoin('proyectos AS p','p.id','=','fibap.idProyecto')
								->orderBy('id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
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

		if($parametros){
			if($parametros['ver'] == 'fibap'){
				$recurso = FIBAP::with('documentos','propuestasFinanciamiento','antecedentesFinancieros')->find($id);
				if($recurso->idProyecto){
					$recurso->load('proyecto');
				}else{
					$recurso->load('datosProyecto');
				}
			}
		}else{
			
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();
			$data = array("data"=>$recurso);
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
			}elseif($parametros['formulario'] == 'form-fibap-presupuesto'){
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

						//$recurso->tipo = $parametros['tipo'];
						$recurso->organismoPublico = $parametros['organismo-publico'];
						$recurso->sector = $parametros['sector'];
						$recurso->subcomite = $parametros['subcomite'];
						$recurso->grupoTrabajo = $parametros['grupo-trabajo'];
						$recurso->justificacionProyecto = $parametros['justificacion-proyecto'];
						$recurso->descripcionProyecto = $parametros['descripcion-proyecto'];
						$recurso->alineacionEspecifica = $parametros['alineacion-especifica'];
						$recurso->alineacionGeneral = $parametros['alineacion-general'];

						if($parametros['proyecto-id']){
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
						$respuesta['data'] = 'guardado';
						$recurso = new AntecedenteFinanciero;
						$recurso->anio = $parametros['anio-antecedente'];
						$recurso->autorizado = $parametros['autorizado-antecedente'];
						$recurso->ejercido = $parametros['ejercido-antecedente'];
						$recurso->fechaCorte = $parametros['fecha-corte-antecedente'];
						$recurso->porcentaje = ($recurso->ejercido * 100) / $recurso->autorizado;

						$fibap = FIBAP::with('antecedentesFinancieros')->find($parametros['fibap-id']);
						$fibap->antecedentesFinancieros()->save($recurso);

						$respuesta['data'] = array('data'=>$recurso,'antecedentes' => $fibap->antecedentesFinancieros);
					}elseif($parametros['formulario'] == 'form-fibap-presupuesto'){
						
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
					}elseif($parametros['formulario'] == 'form-fibap-presupuesto'){
						$recurso = FIBAP::with('propuestasFinanciamiento')->find($id);
						$recurso->presupuestoRequerido = $parametros['presupuesto-requerido'];
						$recurso->periodoEjecucion = $parametros['periodo-ejecucion'];

						$origenes = $parametros['origen'];
						$origenes_ids = array();

						if(isset($parametros['origen-captura-id'])){
							$origenes_ids = $parametros['origen-captura-id'];
						}

						$respuesta['data'] = DB::transaction(function() use ($origenes, $origenes_ids, $recurso){
							$guardar_origenes = array();
							foreach ($origenes as $origen => $valor) {
								if(isset($origenes_ids[$origen])){
									$origen_finan = $recurso->propuestasFinanciamiento()->find($origenes_ids[$origen]);
									$origen_finan->cantidad = $valor;
									$guardar_origenes[] = $origen_finan;
								}elseif($valor > 0){
									$origen_finan = new PropuestaFinanciamiento;
									$origen_finan->idOrigenFinanciamiento = $origen;
									$origen_finan->cantidad = $valor;
									$guardar_origenes[] = $origen_finan;
								}
							}

							$recurso->propuestasFinanciamiento()->saveMany($guardar_origenes);

							if($recurso->save()){
								return array('data'=>$recurso);
							}else{
								//No se pudieron guardar los datos del proyecto
								throw new Exception("Error al intentar guardar los datos de la ficha: Error en el guardado de los datos antecedentes", 1);
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
}