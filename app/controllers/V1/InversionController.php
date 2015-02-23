<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Proyecto, Componente, Actividad, Beneficiario, FIBAP, ComponenteMetaMes, ActividadMetaMes, Region, Municipio, Jurisdiccion, 
	FibapDatosProyecto, Titular, ComponenteDesglose, AntecedenteFinanciero, DesgloseMetasMes, DistribucionPresupuesto;

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
					$recurso->fibap->load('antecedentesFinancieros');
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
				$respuesta = parent::guardar_datos_componente('componente',$parametros);
				//Llenar datos adicionales
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