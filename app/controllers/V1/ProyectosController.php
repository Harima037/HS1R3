<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception;
use Proyecto, Componente, Actividad, Beneficiario, FIBAP, ComponenteMetaMes, ActividadMetaMes, Region, Municipio, Jurisdiccion, 
	FibapDatosProyecto, Titular, ComponenteDesglose, Accion, PropuestaFinanciamiento, DistribucionPresupuesto, DesgloseMetasMes, 
	DesgloseBeneficiario;

class ProyectosController extends BaseController {
	private $reglasProyecto = array(
		'funciongasto'				=> 'required',
		'clasificacionproyecto'		=> 'required',
		'nombretecnico'				=> 'sometimes|required',
		'ejercicio'					=> 'required',
		'tipoproyecto'				=> 'required',
		'cobertura'					=> 'sometimes|required',
		'municipio'					=> 'sometimes|required_if:cobertura,2|digits_between:1,3',
		'region'					=> 'sometimes|required_if:cobertura,3|alpha',
		'tipoaccion'				=> 'required',
		'unidadresponsable'			=> 'required|digits:2',
		'programasectorial'			=> 'required|alpha_num|size:1',
		'programapresupuestario'	=> 'sometimes|required|alpha_num|size:3',
		'programaespecial'			=> 'required|alpha_num|size:3',
		'actividadinstitucional'	=> 'required|alpha_num|size:3',
		'proyectoestrategico'		=> 'required|alpha_num|size:1',
		'vinculacionped'			=> 'sometimes|required'
	);
	
	private $reglasBeneficiarios = array(
		'tipobeneficiario'			=> 'required',
		'totalbeneficiariosf'		=> 'required|integer|min:0',
		'totalbeneficiariosm'		=> 'required|integer|min:0',
		'altaf' 					=> 'required|integer|min:0',
		'altam' 					=> 'required|integer|min:0',
		'bajaf' 					=> 'required|integer|min:0',
		'bajam' 					=> 'required|integer|min:0',
		'indigenaf'					=> 'required|integer|min:0',
		'indigenam'					=> 'required|integer|min:0',
		'inmigrantef' 				=> 'required|integer|min:0',
		'inmigrantem' 				=> 'required|integer|min:0',
		'mediaf' 					=> 'required|integer|min:0',
		'mediam' 					=> 'required|integer|min:0',
		'mestizaf' 					=> 'required|integer|min:0',
		'mestizam'					=> 'required|integer|min:0',
		'muyaltaf' 					=> 'required|integer|min:0',
		'muyaltam' 					=> 'required|integer|min:0',
		'muybajaf' 					=> 'required|integer|min:0',
		'muybajam' 					=> 'required|integer|min:0',
		'otrosf' 					=> 'required|integer|min:0',
		'otrosm' 					=> 'required|integer|min:0',
		'ruralf' 					=> 'required|integer|min:0',
		'ruralm' 					=> 'required|integer|min:0',
		'urbanaf' 					=> 'required|integer|min:0',
		'urbanam' 					=> 'required|integer|min:0'
	);
	
	private $reglasAccionBase = array(
		'denominador-ind-' 	=> 'required',
		'descripcion-ind-' 	=> 'required',
		'descripcion-obj-' 	=> 'required',
		'dimension-' 		=> 'required',
		'formula-' 			=> 'required',
		'frecuencia-' 		=> 'required',
		'interpretacion-' 	=> 'required',
		'numerador-ind-' 	=> 'required',
		'supuestos-' 		=> 'required',
		'tipo-ind-' 		=> 'required',
		'anio-base-' 		=> 'integer|min:0',
		'linea-base-' 		=> 'numeric|min:0',
		'unidad-medida-' 	=> 'required',
		'verificacion-' 	=> 'required'
		//'trim1-componente' 				=> 'numeric',
		//'trim2-componente' 				=> 'numeric',
		//'trim3-componente' 				=> 'numeric',
		//'trim4-componente' 				=> 'numeric',
		//'denominador-componente' 		=> 'required_if:formula-componente,1,2,3,4,5,6|numeric|min:0',
		//'numerador-componente' 			=> 'required|numeric|min:1',
		//'meta-componente' 				=> 'required|numeric|min:0',
	);

	private $reglasComponente = array(
		'denominador-ind-componente' 	=> 'required',
		'descripcion-ind-componente' 	=> 'required',
		'descripcion-obj-componente' 	=> 'required',
		'dimension-componente' 			=> 'required',
		'formula-componente' 			=> 'required',
		'frecuencia-componente' 		=> 'required',
		'interpretacion-componente' 	=> 'required',
		'numerador-ind-componente' 		=> 'required',
		'supuestos-componente' 			=> 'required',
		'tipo-ind-componente' 			=> 'required',
		'anio-base-componente' 			=> 'integer|min:0',
		'linea-base-componente' 		=> 'numeric|min:0',
		'unidad-medida-componente' 		=> 'required',
		'verificacion-componente' 		=> 'required'
		//'trim1-componente' 				=> 'numeric',
		//'trim2-componente' 				=> 'numeric',
		//'trim3-componente' 				=> 'numeric',
		//'trim4-componente' 				=> 'numeric',
		//'denominador-componente' 		=> 'required_if:formula-componente,1,2,3,4,5,6|numeric|min:0',
		//'numerador-componente' 			=> 'required|numeric|min:1',
		//'meta-componente' 				=> 'required|numeric|min:0',
	);

	private $reglasActividad = array(
		'denominador-ind-actividad' 	=> 'required',
		'descripcion-ind-actividad' 	=> 'required',
		'descripcion-obj-actividad' 	=> 'required',
		'dimension-actividad' 			=> 'required',
		'formula-actividad' 			=> 'required',
		'frecuencia-actividad' 			=> 'required',
		'interpretacion-actividad' 		=> 'required',
		'meta-actividad' 				=> 'required|numeric|min:0',
		'numerador-actividad' 			=> 'required|numeric|min:1',
		'numerador-ind-actividad' 		=> 'required',
		'supuestos-actividad' 			=> 'required',
		'tipo-ind-actividad' 			=> 'required',
		'anio-base-actividad' 			=> 'integer|min:0',
		'denominador-actividad' 		=> 'required_if:formula-actividad,1,2,3,4,5,6|numeric|min:0',
		'linea-base-actividad' 			=> 'numeric|min:0',
		'trim1-actividad' 				=> 'numeric',
		'trim2-actividad' 				=> 'numeric',
		'trim3-actividad' 				=> 'numeric',
		'trim4-actividad' 				=> 'numeric',
		'unidad-medida-actividad' 		=> 'required',
		'verificacion-actividad' 		=> 'required'
	);
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){

			$rows = Proyecto::getModel();
			$rows = $rows->where('unidadResponsable','=',Sentry::getUser()->claveUnidad)
						->where('idClasificacionProyecto','=',1)
						->whereIn('idEstatusProyecto',[1,2,3,4]);
			
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
			/*$proyectos = array();
			foreach ($rows as $row) {
				# code...
				$proyectos[] = array(
						'id' 					=> $row->id,
						'clavePresup' 			=> $row->clavePresup,
						'nombreTecnico' 		=> $row->nombreTecnico,
						'clasificacionProyecto'	=> $row->clasificacionProyecto,
						'estatusProyecto'		=> $row->estatusProyecto,
						'username'				=> $row->username,
						'modificadoAl'			=> date_format($row->modificadoAl,'d/m/Y')
					);
			}*/
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
			return Response::json($data,$http_status);
		}elseif(isset($parametros['proyectos_inversion'])){
			$rows = Proyecto::getModel();
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username')
								->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
								->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->leftjoin('fibap','proyectos.id','=','fibap.idProyecto')
								->orderBy('proyectos.id','desc')
								->where('proyectos.idClasificacionProyecto','=',DB::raw('2'))
								->where('unidadResponsable','=',Sentry::getUser()->claveUnidad)
								->whereNull('fibap.id')
								->get();
			$data = array('data'=>$rows);
			
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
	public function show($id)
	{
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if($parametros){
			if($parametros['ver'] == 'componente'){
				$recurso = Componente::with('actividades.unidadMedida','metasMes')->find($id);
			}elseif ($parametros['ver'] == 'lista-desglose') {

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				$pagina = $parametros['pagina'];
				$rows = ComponenteDesglose::listarDatos()->where('idComponente','=',$id);

				if(isset($parametros['buscar'])){				
					$rows = $rows->where(function($query) use ($parametros){
									$query->where('jurisdiccion.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('municipio.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('localidad.nombre','like','%'.$parametros['buscar'].'%');
								});
					$total = $rows->count();

					$queries = DB::getQueryLog();
					$data['query'] = print_r(end($queries),true);
				}else{				
					$total = $rows->count();						
				}
				$data['total'] = $total;
				$recurso = $rows->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();

			}elseif($parametros['ver'] == 'actividad'){

				$recurso = Actividad::with('metasMes')->find($id);

			}elseif($parametros['ver'] == 'beneficiario'){
				$recurso = Beneficiario::where('idProyecto','=',$parametros['id-proyecto'])
										->where('idTipoBeneficiario','=',$id)->get();
			}elseif($parametros['ver'] == 'proyecto'){
				$recurso = Proyecto::contenidoCompleto()->find($id);
				if($recurso){
					/*if($recurso->idClasificacionProyecto == 2){
						$recurso->load('fibap');
						if($recurso->fibap){
							$recurso->fibap->load('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado');
							$recurso->fibap->distribucionPresupuestoAgrupado->load('objetoGasto');
						}
					}*/
					$recurso->componentes->load(array('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable','entregableTipo','entregableAccion','desgloseCompleto'));
					foreach ($recurso->componentes as $key => $componente) {
						$recurso->componentes[$key]->actividades->load(array('formula','dimension','frecuencia','tipoIndicador','unidadMedida'));
					}
				}

			}elseif($parametros['ver'] == 'datos-fibap'){
				$recurso = FibapDatosProyecto::where('idFibap','=',$id)->get();
				$recurso = $recurso[0];
			}
		}else{
			$recurso = Proyecto::contenidoCompleto()->find($id);
			$recurso->componentes->load('unidadMedida');
			if($recurso->idEstatusProyecto == 3){
				$recurso->load('comentarios');
			}
			if($recurso->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
				$jurisdicciones = Jurisdiccion::all();
			}elseif($recurso->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
				$jurisdicciones = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
			}elseif($recurso->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
				$jurisdicciones = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
			}
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();
			if(!$parametros){
				$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
			}
			$data["data"] = $recurso;
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

			if($parametros['guardar'] == 'actividad'){
				$componente = Componente::find(Input::get('id-componente'));
				$componente->load('actividades');

				if(!$componente){
					$respuesta['data']['data'] = 'No se ha podido encontrar el componente para agregar esta actividad, por favor verifique que el componente no haya sido eliminado.';
					throw new Exception("No se pudo encontrar el componente al que pertenece esta actividad", 1);
				}

				if(count($componente->actividades) == 5){
					$respuesta['data']['data'] = 'El componente no puede tener mas de 5 actividades, si desea agregar otra actividad deberá eliminar alguna de las actividades actuales.';
					throw new Exception("No esta permitido guardar mas de 5 actividades por cada componente", 1);
				}

				$validacion = Validador::validar(Input::all(), $this->reglasActividad);

				if($validacion === TRUE){
					$actividad = new Actividad;

					//$componente->idProyecto = $parametros['id-proyecto'];
					$actividad->objetivo 				= $parametros['descripcion-obj-actividad'];
					$actividad->mediosVerificacion 		= $parametros['verificacion-actividad'];
					$actividad->supuestos 				= $parametros['supuestos-actividad'];
					$actividad->indicador 				= $parametros['descripcion-ind-actividad'];
					$actividad->numerador 				= $parametros['numerador-ind-actividad'];
					$actividad->denominador 			= $parametros['denominador-ind-actividad'];
					$actividad->interpretacion 			= $parametros['interpretacion-actividad'];
					$actividad->idFormula 				= $parametros['formula-actividad'];
					$actividad->idDimensionIndicador 	= $parametros['dimension-actividad'];
					$actividad->idFrecuenciaIndicador 	= $parametros['frecuencia-actividad'];
					$actividad->idTipoIndicador			= $parametros['tipo-ind-actividad'];
					$actividad->idUnidadMedida 			= $parametros['unidad-medida-actividad'];
					$actividad->metaIndicador 			= $parametros['meta-actividad'];
					$actividad->numeroTrim1 			= ($parametros['trim1-actividad'])?$parametros['trim1-actividad']:NULL;
					$actividad->numeroTrim2 			= ($parametros['trim2-actividad'])?$parametros['trim2-actividad']:NULL;
					$actividad->numeroTrim3 			= ($parametros['trim3-actividad'])?$parametros['trim3-actividad']:NULL;
					$actividad->numeroTrim4 			= ($parametros['trim4-actividad'])?$parametros['trim4-actividad']:NULL;
					$actividad->valorNumerador 			= $parametros['numerador-actividad'];
					if($actividad->idFormula == 7){
						$actividad->valorDenominador 	= NULL;
					}else{
						$actividad->valorDenominador 	= $parametros['denominador-actividad'];
					}
					$actividad->lineaBase 				= ($parametros['linea-base-actividad'])?$parametros['linea-base-actividad']:NULL;
					$actividad->anioBase 				= ($parametros['anio-base-actividad'])?$parametros['anio-base-actividad']:NULL;
					$actividad->idProyecto 				= $componente->idProyecto;

					$respuesta['data'] = DB::transaction(function() use ($parametros, $componente, $actividad){
						if($componente->actividades()->save($actividad)){
							$actividad->load('usuario');
							$componente->actividades[] = $actividad;

							$jurisdicciones = $parametros['mes-actividad'];

							$metasMes = array();

							foreach ($jurisdicciones as $clave => $meses) {
								foreach ($meses as $mes => $valor) {
									if($valor > 0){
										$meta = new ActividadMetaMes;
										$meta->claveJurisdiccion = $clave;
										$meta->mes = $mes;
										$meta->meta = $valor;
										$meta->idProyecto = $componente->idProyecto;
										$metasMes[] = $meta;
									}
								}
							}
							
							$actividad->metasMes()->saveMany($metasMes);
							$componente->actividades->load('unidadMedida');
							return array('data'=>$actividad,'actividades'=>$componente->actividades,'metas'=>$metasMes);
						}else{
							throw new Exception("Ocurrió un error al guardar la actividad.", 1);
						}
					});

				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			//Guardar datos de la actividad
			}elseif($parametros['guardar'] == 'componente'){
				$proyecto = Proyecto::find(Input::get('id-proyecto'));
				$proyecto->load('componentes');

				if(!$proyecto){
					throw new Exception("No se pudo encontrar el proyecto al que pertenece este componente", 1);
				}

				if(count($proyecto->componentes) == 2){
					$respuesta['data']['data'] = 'El proyecto no puede tener mas de 2 componentes.';
					throw new Exception("No esta permitido guardar mas de 2 componentes por cada proyecto", 1);
				}

				if($parametros['clasificacion'] == 2){
					$this->reglasComponente['entregable'] = 'required';
					$this->reglasComponente['tipo-entregable'] = 'required';
					$this->reglasComponente['accion-entregable'] = 'required';
				}

				$validacion = Validador::validar(Input::all(), $this->reglasComponente);

				if($validacion === TRUE){
					$componente = new Componente;

					//$componente->idProyecto = $parametros['id-proyecto'];
					$componente->objetivo 				= $parametros['descripcion-obj-componente'];
					$componente->mediosVerificacion 	= $parametros['verificacion-componente'];
					$componente->supuestos 				= $parametros['supuestos-componente'];
					$componente->indicador 				= $parametros['descripcion-ind-componente'];
					$componente->numerador 				= $parametros['numerador-ind-componente'];
					$componente->denominador 			= $parametros['denominador-ind-componente'];
					$componente->interpretacion 		= $parametros['interpretacion-componente'];
					$componente->idFormula 				= $parametros['formula-componente'];
					$componente->idDimensionIndicador 	= $parametros['dimension-componente'];
					$componente->idFrecuenciaIndicador 	= $parametros['frecuencia-componente'];
					$componente->idTipoIndicador 		= $parametros['tipo-ind-componente'];
					$componente->idUnidadMedida 		= $parametros['unidad-medida-componente'];
					$componente->metaIndicador 			= $parametros['meta-componente'];
					$componente->numeroTrim1 			= ($parametros['trim1-componente'])?$parametros['trim1-componente']:NULL;
					$componente->numeroTrim2 			= ($parametros['trim2-componente'])?$parametros['trim2-componente']:NULL;
					$componente->numeroTrim3 			= ($parametros['trim3-componente'])?$parametros['trim3-componente']:NULL;
					$componente->numeroTrim4 			= ($parametros['trim4-componente'])?$parametros['trim4-componente']:NULL;
					$componente->valorNumerador 		= $parametros['numerador-componente'];
					if($componente->idFormula == 7){
						$componente->valorDenominador 	= NULL;
					}else{
						$componente->valorDenominador 	= $parametros['denominador-componente'];
					}
					$componente->lineaBase 				= ($parametros['linea-base-componente'])?$parametros['linea-base-componente']:NULL;
					$componente->anioBase 				= ($parametros['anio-base-componente'])?$parametros['anio-base-componente']:NULL;

					if($parametros['clasificacion'] == 2){
						$componente->idEntregable 		= $parametros['entregable'];
						if($parametros['tipo-entregable'] != 'NA'){
							$componente->idEntregableTipo	= $parametros['tipo-entregable'] ;
						}
						$componente->idEntregableAccion	= $parametros['accion-entregable'];
					}

					$respuesta['data'] = DB::transaction(function() use ($parametros, $proyecto, $componente){
						if($proyecto->componentes()->save($componente)){
							$componente->load('usuario');
							$proyecto->componentes[] = $componente;

							$jurisdicciones = $parametros['mes-componente']; //Arreglo que contiene los datos [jurisdiccion][mes] = valor

							$metasMes = array();

							foreach ($jurisdicciones as $clave => $meses) {
								foreach ($meses as $mes => $valor) {
									if($valor > 0){
										$meta = new ComponenteMetaMes;
										$meta->claveJurisdiccion = $clave;
										$meta->mes = $mes;
										$meta->meta = $valor;
										$meta->idProyecto = $componente->idProyecto;
										$metasMes[] = $meta;
									}
								}
							}
							
							$componente->metasMes()->saveMany($metasMes);
							$proyecto->componentes->load('unidadMedida');
							return array('data'=>$componente,'componentes'=>$proyecto->componentes,'metas'=>$metasMes);
						}else{
							throw new Exception("Ocurrió un error al guardar el componente.", 1);
						}
					});
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			//Guardar datos del componente
			}elseif($parametros['guardar'] == 'beneficiario'){
				$respuesta = $this->guardar_datos_beneficiario($parametros);	
			}elseif($parametros['guardar'] == 'proyecto'){ //Nuevo Proyecto
				$respuesta = $this->guardar_datos_proyecto($parametros);
				if($respuesta['http_status'] == 200){
					$recurso = $respuesta['data']['data'];
					if($recurso->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
						$jurisdicciones = Jurisdiccion::all();
					}elseif($recurso->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$jurisdicciones = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
					}elseif($recurso->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$jurisdicciones = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
					}
					$recurso = $recurso->toArray();
					$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
					$respuesta['data']['data'] = $recurso;
				}
			} //Guardar Datos del Proyecto
		

		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos.';
			}
			$respuesta['data']['ex'] = $ex->getMessage();
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
	public function update($id)
	{
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		$parametros = Input::all();
		try{
			if($parametros['guardar'] == 'validar-proyecto'){
				$proyecto = Proyecto::find($id);
				if($proyecto->idEstatusProyecto == 1 || $proyecto->idEstatusProyecto == 3){
					$proyecto->idEstatusProyecto = 2;
					$proyecto->save();
					$respuesta['data'] = array('data'=>'El Proyecto fue enviado a Revisión');
				}else{
					$respuesta['data'] = array('data'=>'El Proyecto ya se encuentra en proceso de Revisión');
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

			if($parametros['guardar'] == 'actividad'){
				$validacion = Validador::validar(Input::all(), $this->reglasActividad);

				if($validacion === TRUE){

					$recurso = Actividad::find($id);

					if(is_null($recurso)){
						$respuesta['data']['data'] = 'No se ha podido encontrar la actividad, por favor verifique que no haya sido eliminada.';
						throw new Exception("No se pudo encontrar la actividad que se intenta editar", 1);
					}

					//$componente->idProyecto = $parametros['id-proyecto'];
					$recurso->objetivo 					= 	$parametros['descripcion-obj-actividad'];
					$recurso->mediosVerificacion 		= 	$parametros['verificacion-actividad'];
					$recurso->supuestos 				= 	$parametros['supuestos-actividad'];
					$recurso->indicador 				= 	$parametros['descripcion-ind-actividad'];
					$recurso->numerador 				= 	$parametros['numerador-ind-actividad'];
					$recurso->denominador 				= 	$parametros['denominador-ind-actividad'];
					$recurso->interpretacion 			= 	$parametros['interpretacion-actividad'];
					$recurso->idFormula 				= 	$parametros['formula-actividad'];
					$recurso->idDimensionIndicador 		= 	$parametros['dimension-actividad'];
					$recurso->idFrecuenciaIndicador 	= 	$parametros['frecuencia-actividad'];
					$recurso->idTipoIndicador 			= 	$parametros['tipo-ind-actividad'];
					$recurso->idUnidadMedida 			= 	$parametros['unidad-medida-actividad'];
					$recurso->metaIndicador 			= 	$parametros['meta-actividad'];
					$recurso->numeroTrim1 				= 	($parametros['trim1-actividad'])?$parametros['trim1-actividad']:NULL;
					$recurso->numeroTrim2 				= 	($parametros['trim2-actividad'])?$parametros['trim2-actividad']:NULL;
					$recurso->numeroTrim3 				= 	($parametros['trim3-actividad'])?$parametros['trim3-actividad']:NULL;
					$recurso->numeroTrim4 				= 	($parametros['trim4-actividad'])?$parametros['trim4-actividad']:NULL;
					$recurso->valorNumerador 			= 	$parametros['numerador-actividad'];
					if($recurso->idFormula == 7){
						$recurso->valorDenominador 		= 	NULL;
					}else{
						$recurso->valorDenominador 		= 	$parametros['denominador-actividad'];
					}
					$recurso->lineaBase 				= 	($parametros['linea-base-actividad'])?$parametros['linea-base-actividad']:NULL;
					$recurso->anioBase 					= 	($parametros['anio-base-actividad'])?$parametros['anio-base-actividad']:NULL;
					
					$respuesta['data'] = DB::transaction(function() use ($parametros, $recurso){
						if($recurso->save()){
							$componente = Componente::with('actividades.unidadMedida')->find($recurso->idComponente);

							$jurisdicciones = $parametros['mes-actividad'];
							$ides = $parametros['mes-actividad-id'];

							$metasMes = array();

							foreach ($jurisdicciones as $clave => $meses) {
								foreach ($meses as $mes => $valor) {
									if(isset($ides[$clave][$mes])){
										$meta = ActividadMetaMes::find($ides[$clave][$mes]);
										$meta->meta = $valor;
										$metasMes[] = $meta;
									}elseif($valor > 0){
										$meta = new ActividadMetaMes;
										$meta->claveJurisdiccion = $clave;
										$meta->mes = $mes;
										$meta->meta = $valor;
										$meta->idProyecto = $componente->idProyecto;
										$metasMes[] = $meta;
									}
								}
							}

							$recurso->metasMes()->saveMany($metasMes);

							return array('data'=>$recurso,'actividades'=>$componente->actividades,'metas'=>$metasMes);
						}else{
							throw new Exception("Ocurrió un error al guardar la actividad.", 1);
						}
					});
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			//Guardar datos de la actividad
			}elseif($parametros['guardar'] == 'componente'){  //Editar componente
				if($parametros['clasificacion'] == 2){
					$this->reglasComponente['entregable'] = 'required';
					$this->reglasComponente['tipo-entregable'] = 'required';
					$this->reglasComponente['accion-entregable'] = 'required';
				}

				$validacion = Validador::validar(Input::all(), $this->reglasComponente);

				if($validacion === TRUE){
					$recurso = Componente::find($id);

					if(is_null($recurso)){
						$respuesta['data']['data'] = 'No se ha podido encontrar el componente, por favor verifique que no haya sido eliminado.';
						throw new Exception("No se pudo encontrar el componente que se intenta editar", 1);
					}

					//$componente->idProyecto = $parametros['id-proyecto'];
					$recurso->objetivo 					= 	$parametros['descripcion-obj-componente'];
					$recurso->mediosVerificacion 		= 	$parametros['verificacion-componente'];
					$recurso->supuestos 				= 	$parametros['supuestos-componente'];
					$recurso->indicador 				= 	$parametros['descripcion-ind-componente'];
					$recurso->numerador 				= 	$parametros['numerador-ind-componente'];
					$recurso->denominador 				= 	$parametros['denominador-ind-componente'];
					$recurso->interpretacion 			= 	$parametros['interpretacion-componente'];
					$recurso->idFormula 				= 	$parametros['formula-componente'];
					$recurso->idDimensionIndicador 		= 	$parametros['dimension-componente'];
					$recurso->idFrecuenciaIndicador 	= 	$parametros['frecuencia-componente'];
					$recurso->idTipoIndicador 			= 	$parametros['tipo-ind-componente'];
					$recurso->idUnidadMedida 			= 	$parametros['unidad-medida-componente'];
					$recurso->metaIndicador 			= 	$parametros['meta-componente'];
					$recurso->numeroTrim1 				= 	($parametros['trim1-componente'])?$parametros['trim1-componente']:NULL;
					$recurso->numeroTrim2 				= 	($parametros['trim2-componente'])?$parametros['trim2-componente']:NULL;
					$recurso->numeroTrim3 				= 	($parametros['trim3-componente'])?$parametros['trim3-componente']:NULL;
					$recurso->numeroTrim4 				= 	($parametros['trim4-componente'])?$parametros['trim4-componente']:NULL;
					$recurso->valorNumerador 			= 	$parametros['numerador-componente'];
					if($recurso->idFormula == 7){
						$recurso->valorDenominador 		= 	NULL;
					}else{
						$recurso->valorDenominador 		= 	$parametros['denominador-componente'];
					}
					$recurso->lineaBase 				= 	($parametros['linea-base-componente'])?$parametros['linea-base-componente']:NULL;
					$recurso->anioBase 					= 	($parametros['anio-base-componente'])?$parametros['anio-base-componente']:NULL;

					if($parametros['clasificacion'] == 2){
						$recurso->idEntregable 			= $parametros['entregable'];
						if($parametros['tipo-entregable'] != 'NA'){
							$recurso->idEntregableTipo	= $parametros['tipo-entregable'] ;
						}else{
							$recurso->idEntregableTipo	= NULL;
						}
						$recurso->idEntregableAccion	= $parametros['accion-entregable'];
					}

					$respuesta['data'] = DB::transaction(function() use ($parametros, $recurso){
						if($recurso->save()){
							$jurisdicciones = $parametros['mes-componente'];
							if(isset($parametros['mes-componente-id'])){
								$ides = $parametros['mes-componente-id'];
							}else{
								$ides = array();
							}

							$metasMes = array();

							foreach ($jurisdicciones as $clave => $meses) {
								foreach ($meses as $mes => $valor) {
									if(isset($ides[$clave][$mes])){
										$meta = ComponenteMetaMes::find($ides[$clave][$mes]);
										$meta->meta = $valor;
										$metasMes[] = $meta;
									}elseif($valor > 0){
										$meta = new ComponenteMetaMes;
										$meta->claveJurisdiccion = $clave;
										$meta->mes = $mes;
										$meta->meta = $valor;
										$meta->idProyecto = $recurso->idProyecto;
										$metasMes[] = $meta;
									}
								}	
							}

							$recurso->metasMes()->saveMany($metasMes);
							//$recurso->load('metasMes');

							$proyecto = Proyecto::with('componentes.unidadMedida')->find($recurso->idProyecto);

							return array('data'=>$recurso,'componentes'=>$proyecto->componentes,'metas'=>$metasMes);
						}else{
							throw new Exception("Ocurrió un error al guardar el componente.", 1);
						}
					});
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			//Guardar datos del componente
			}elseif($parametros['guardar'] == 'beneficiario'){ 
				$respuesta = $this->guardar_datos_beneficiario($parametros,$id);
			}elseif($parametros['guardar'] == 'proyecto'){
				$validacion = Validador::validar(Input::all(), $this->reglasProyecto);
				if($validacion === TRUE){

					$recurso = Proyecto::find($id);

					if(is_null($recurso)){
						$respuesta['data']['data'] = 'No se ha podido encontrar el proyecto, por favor verifique que no haya sido eliminado.';
						throw new Exception("No se pudo encontrar el proyecto que se intenta editar", 1);
					}

					$funcion_gasto = explode('.',$parametros['funciongasto']);

					$recurso->idClasificacionProyecto 		= $parametros['clasificacionproyecto'];
					$recurso->nombreTecnico 				= $parametros['nombretecnico'];
					$recurso->idTipoProyecto 				= $parametros['tipoproyecto'];
					$recurso->idTipoAccion 					= $parametros['tipoaccion'];
					$recurso->unidadResponsable 			= $parametros['unidadresponsable'];
					$recurso->finalidad 					= $funcion_gasto[0];
					$recurso->funcion 						= $funcion_gasto[1];
					$recurso->subFuncion 					= $funcion_gasto[2];
					$recurso->subSubFuncion 				= $funcion_gasto[3];
					$recurso->programaSectorial 			= $parametros['programasectorial'];
					$recurso->programaPresupuestario 		= $parametros['programapresupuestario'];
					$recurso->programaEspecial 				= $parametros['programaespecial'];
					$recurso->actividadInstitucional 		= $parametros['actividadinstitucional'];
					$recurso->proyectoEstrategico 			= $parametros['proyectoestrategico'];
					$recurso->idObjetivoPED 				= $parametros['vinculacionped'];
					
					//$recurso->totalBeneficiarios 			= $parametros['totalbeneficiariosf'] + $parametros['totalbeneficiariosm'];
					//$recurso->totalBeneficiariosF 			= $parametros['totalbeneficiariosf'];
					//$recurso->totalBeneficiariosM 			= $parametros['totalbeneficiariosm'];
					//$recurso->idEstatusProyecto 			= 1;
					$recurso->idCobertura 					= $parametros['cobertura'];

					if($recurso->idCobertura != $parametros['cobertura']){
						$recurso->idCobertura 				= $parametros['cobertura'];
					}
					$jurisdicciones = NULL;
					if($parametros['cobertura'] == 2){
						if($recurso->claveMunicipio != $parametros['municipio']){
							$recurso->claveRegion = NULL;
							$recurso->claveMunicipio = $parametros['municipio'];
							$jurisdicciones = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
						}
					}elseif($parametros['cobertura'] == 3){
						if($recurso->claveRegion != $parametros['region']){
							$recurso->claveMunicipio = NULL;
							$recurso->claveRegion = $parametros['region'];
							$jurisdicciones = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
						}
					}else{
						if(!is_null($recurso->claveRegion) || !is_null($recurso->claveMunicipio)){
							$recurso->claveMunicipio = NULL;
							$recurso->claveRegion = NULL;
							$jurisdicciones = Jurisdiccion::all();
						}
					}

					DB::transaction(function() use ($parametros, $recurso, $respuesta, $jurisdicciones){
						if($recurso->save()){
							if($jurisdicciones){
								$claves = $jurisdicciones->lists('clave');
								//throw new Exception(print_r($claves,false), 1);
								if(count($claves) > 0){
									$claves[] = 'OC';
									//throw new Exception(print_r($claves,false), 1);
									ComponenteMetaMes::where('idProyecto',$recurso->id)->whereNotIn('claveJurisdiccion',$claves)->delete();
									ActividadMetaMes::where('idProyecto',$recurso->id)->whereNotIn('claveJurisdiccion',$claves)->delete();
									//$queries = DB::getQueryLog();
									//$last_query = end($queries);
									//throw new Exception(print_r($last_query,false), 1);
								}
							}
						}else{
							//No se pudieron guardar los datos del proyecto
							$respuesta['data']['code'] = 'S01';
							throw new Exception("Error al intentar guardar los datos del proyecto", 1);
						}
					});
					
					$recurso = $recurso->toArray();
					if($jurisdicciones){
						$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
					}
					//Proyecto guardado con éxito
					$respuesta['data'] = array('data'=>$recurso);
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			//Guardar Datos del Proyecto
			}
		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos.';
			}
			$respuesta['data']['ex'] = $ex->getMessage();
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
				if($parametros['eliminar'] == 'componente'){
					$id_padre = $parametros['id-proyecto'];
					$rows = DB::transaction(function() use ($ids){
						//Obtenemos los ids de las actividades de los componentes seleccionados
						$actividades = Actividad::wherein('idComponente',$ids)->lists('id');
						if(count($actividades) > 0){
							//Eliminamos las metas de dichas actividades
							ActividadMetaMes::wherein('idActividad',$actividades)->delete();
							//Eliminamos las actividades de los componentes
							Actividad::wherein('idComponente',$ids)->delete();
						}
						//Eliminamos las metas de los componentes
						ComponenteMetaMes::wherein('idComponente',$ids)->delete();
						//Eliminamos los componenetes
						return Componente::wherein('id',$ids)->delete();
					});
				}elseif($parametros['eliminar'] == 'actividad'){
					$id_padre = $parametros['id-componente'];
					$rows = DB::transaction(function() use ($ids){
						//Eliminamos las metas de las actividades seleccionadas
						ActividadMetaMes::wherein('idActividad',$ids)->delete();
						//Eliminamos las actividades
						return Actividad::wherein('id',$ids)->delete();
					});
				}elseif($parametros['eliminar'] == 'beneficiario'){
					$id_padre = $parametros['id-proyecto'];
					$rows = DB::transaction(function() use ($ids,$id_padre){
						return Beneficiario::whereIn('idTipoBeneficiario',$ids)
									->where('idProyecto','=',$id_padre)
									->delete();
					});
				}
			}else{
				$rows = DB::transaction(function() use ($ids){
					//Eliminamos las metas de los componentes de los proyectos
					ComponenteMetaMes::wherein('idProyecto',$ids)->delete();
					//Eliminamos las metas de las actividades de los proyectos
					ActividadMetaMes::wherein('idProyecto',$ids)->delete();
					//Obtenemos los ids de los componentes de los proyectos
					$componentes = Componente::wherein('idProyecto',$ids)->lists('id');
					if(count($componentes) > 0){
						//Eliminamos las actividades de los componentes
						Actividad::wherein('idComponente',$componentes)->delete();
					}
					//Eliminamos los componentes de los proyectos
					Componente::wherein('idProyecto',$ids)->delete();
					//Eliminamos los beneficiarios de los proyectos
					Beneficiario::wherein('idProyecto',$ids)->delete();
					//Eliminamos los proyectos
					return Proyecto::wherein('id',$ids)->delete();
				});
			}

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
				if(isset($parametros['eliminar'])){
					if($parametros['eliminar'] == 'actividad'){
						$data['actividades'] = Actividad::with('usuario','unidadMedida')->where('idComponente',$id_padre)->get();
					}elseif($parametros['eliminar'] == 'componente'){
						$data['componentes'] = Componente::with('usuario','unidadMedida')->where('idProyecto',$id_padre)->get();
					}elseif($parametros['eliminar'] == 'beneficiario'){
						$data['beneficiarios'] = Beneficiario::with('tipoBeneficiario')->where('idProyecto',$id_padre)->get();
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

	public function guardar_datos_proyecto($parametros,$id = NULL){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array();
		$es_editar = FALSE;

		$validacion = Validador::validar(Input::all(), $this->reglasProyecto);
		
		if($validacion === TRUE){

			if($id){
				$recurso = Proyecto::find($id);
				$es_editar = TRUE;

				$respuesta['data']['datos-anteriores'] = array(
						'claveMunicipio' => $recurso->claveMunicipio,
						'claveRegion'	=> $recurso->claveRegion
					);
			}else{
				$recurso = new Proyecto;
			}
			
			$funcion_gasto = explode('.',$parametros['funciongasto']);

			$recurso->idClasificacionProyecto 		= $parametros['clasificacionproyecto'];
			$recurso->ejercicio						= $parametros['ejercicio'];
			$recurso->idTipoAccion 					= $parametros['tipoaccion'];
			$recurso->unidadResponsable 			= $parametros['unidadresponsable'];
			$recurso->finalidad 					= $funcion_gasto[0];
			$recurso->funcion 						= $funcion_gasto[1];
			$recurso->subFuncion 					= $funcion_gasto[2];
			$recurso->subSubFuncion 				= $funcion_gasto[3];
			$recurso->programaSectorial 			= $parametros['programasectorial'];
			$recurso->programaEspecial 				= $parametros['programaespecial'];
			$recurso->actividadInstitucional 		= $parametros['actividadinstitucional'];
			$recurso->proyectoEstrategico 			= $parametros['proyectoestrategico'];
			$recurso->idEstatusProyecto 			= 1;
			$recurso->idTipoProyecto 				= $parametros['tipoproyecto'];
			$recurso->nombreTecnico 				= $parametros['nombretecnico'];
			$recurso->idObjetivoPED 				= $parametros['vinculacionped'];
			$recurso->programaPresupuestario 		= $parametros['programapresupuestario'];
			
			$nuevas_jurisdicciones = NULL;

			if($es_editar){
				if($recurso->idCobertura != $parametros['cobertura']){
					$recurso->idCobertura 			= $parametros['cobertura'];
				}
				if($parametros['cobertura'] == 2){
					if($recurso->claveMunicipio != $parametros['municipio']){
						$recurso->claveRegion = NULL;
						$recurso->claveMunicipio = $parametros['municipio'];
						$nuevas_jurisdicciones = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
					}
				}elseif($parametros['cobertura'] == 3){
					if($recurso->claveRegion != $parametros['region']){
						$recurso->claveMunicipio = NULL;
						$recurso->claveRegion = $parametros['region'];
						$nuevas_jurisdicciones = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
					}
				}else{
					if(!is_null($recurso->claveRegion) || !is_null($recurso->claveMunicipio)){
						$recurso->claveMunicipio = NULL;
						$recurso->claveRegion = NULL;
						$nuevas_jurisdicciones = Jurisdiccion::all();
					}
				}
			}else{
				$recurso->idCobertura 				= $parametros['cobertura'];
				if($parametros['cobertura'] == 2){
					$recurso->claveMunicipio = $parametros['municipio'];
				}elseif($parametros['cobertura'] == 3){
					$recurso->claveRegion = $parametros['region'];
				}
			}

			if(!$es_editar){
				$titulares = Titular::whereIn('claveUnidad',array('00','01', Sentry::getUser()->claveUnidad))->get();
				foreach ($titulares as $titular) {
					if($titular->claveUnidad == '00'){ //Dirección General
						$recurso->idJefeInmediato 				= $titular->id;
					}elseif ($titular->claveUnidad == '01') { //Dirección de Planeación y Desarrollo
						$recurso->idJefePlaneacion 				= $titular->id;
			  			$recurso->idCoordinadorGrupoEstrategico = $titular->id;
						if($recurso->idLiderProyecto == NULL){
							$recurso->idLiderProyecto = $titular->id;
						}
					}else{
						$recurso->idLiderProyecto = $titular->id;
					}
				}
				$recurso->totalBeneficiarios = 0;
				$recurso->totalBeneficiariosF = 0;
				$recurso->totalBeneficiariosM = 0;
			}
			
			//, $componentes, $fibap, $beneficiarios,  
			DB::transaction(function() use ($recurso, $respuesta, $nuevas_jurisdicciones, $es_editar){
				if($recurso->save()){
					
					if($nuevas_jurisdicciones){
						$claves = $nuevas_jurisdicciones->lists('clave');
						if(count($claves) > 0){
							$claves[] = 'OC';
							ComponenteMetaMes::where('idProyecto',$recurso->id)->whereNotIn('claveJurisdiccion',$claves)->delete();
							ActividadMetaMes::where('idProyecto',$recurso->id)->whereNotIn('claveJurisdiccion',$claves)->delete();
						}
					}

					//Si el proyecto es de Inversión checas si hay fibap, para actualizar la cobertura
					if($es_editar && $recurso->idClasificacionProyecto == 2){
						$recurso->load('fibap');
						if($recurso->fibap){
							//Nos servira para borrar las metas por mes de los desgloses
							$desgloses = FALSE;
							if($recurso->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
								if($recurso->claveMunicipio != $respuesta['data']['datos-anteriores']['claveMunicipio']){
									//Obtenemos el municipio seleccionado
									$recurso->fibap->load('acciones');

									//Borrar todo lo que no sea de este municipio por municipio
									DistribucionPresupuesto::where('idFibap','=',$recurso->fibap->id)
															->where('claveMunicipio','!=',$recurso->claveMunicipio)
															->delete();
									$desgloses = ComponenteDesglose::whereIn('idAccion',$recurso->fibap->acciones->lists('id'))
														->where('claveMunicipio','!=',$recurso->claveMunicipio);
								}
							}elseif($recurso->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
								if($recurso->claveRegion != $respuesta['data']['datos-anteriores']['claveRegion']){
									$region = Region::with('municipios.localidades')->where('region','=',$recurso->claveRegion)->get();
									$recurso->fibap->load('acciones');

									DistribucionPresupuesto::where('idFibap','=',$recurso->fibap->id)
															->whereNotIn('claveMunicipio',$region[0]->municipios->lists('clave'))
															->delete();
									$desgloses = ComponenteDesglose::whereIn('idAccion',$recurso->fibap->acciones->lists('id'))
														->whereNotIn('claveMunicipio',$region[0]->municipios->lists('clave'));
								}
							}
							
							if($desgloses){
								$lista_ids = $desgloses->get()->lists('id');
								DesgloseMetasMes::whereIn('idComponenteDesglose',$lista_ids)->delete();
								DesgloseBeneficiario::whereIn('idComponenteDesglose',$lista_ids)->delete();
								//Actualizar desglose del componente
								$desgloses->delete();
							}
						}
					}
				}else{
					//No se pudieron guardar los datos del proyecto
					throw new Exception("Error al intentar guardar los datos del proyecto", 1);
				}
			});
			$respuesta['data']['data'] = $recurso;
		}else{
			//La Validación del Formulario encontro errores
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}
		
		return $respuesta;
	}

	public function guardar_datos_beneficiario($parametros, $id = NULL){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array();
		$es_editar = FALSE;

		$validacion = Validador::validar(Input::all(), $this->reglasBeneficiarios);

		if($validacion === TRUE){
			$recurso = array();
			$nuevo_beneficiario = 0;
			$viejo_beneficiario = 0;

			if($id){
				$es_editar = TRUE;
				$proyecto = Proyecto::with('beneficiarios')->find($parametros['id-proyecto']);
				$beneficiarios = Beneficiario::where('idProyecto','=',$parametros['id-proyecto'])->
								where('idTipoBeneficiario','=',$id)->get();
				if($parametros['tipobeneficiario'] != $id){
					$nuevo_beneficiario = $parametros['tipobeneficiario'];
					$viejo_beneficiario = $beneficiarios[0]->idTipoBeneficiario;
				}
			}else{
				$proyecto = Proyecto::find($parametros['id-proyecto']);
				$nuevo_beneficiario = $parametros['tipobeneficiario'];
			}

			if($nuevo_beneficiario > 0){
				$beneficiarios_capturados = $proyecto->beneficiarios->lists('idTipoBeneficiario','idTipoBeneficiario');
				if(isset($beneficiarios_capturados[$nuevo_beneficiario])){
					throw new Exception('{"field":"tipobeneficiario","error":"Este tipo de beneficiario ya fue capturado."}', 1);
				}
			}

			if($es_editar){
				foreach ($beneficiarios as $key => $item) {
					$beneficiarios[$key]->idTipoBeneficiario	= 	$parametros['tipobeneficiario'];
					$beneficiarios[$key]->total 				= 	$parametros['totalbeneficiarios'.$item->sexo];
					$beneficiarios[$key]->urbana 				= 	$parametros['urbana'.$item->sexo];
					$beneficiarios[$key]->rural 				= 	$parametros['rural'.$item->sexo];
					$beneficiarios[$key]->mestiza 				= 	$parametros['mestiza'.$item->sexo];
					$beneficiarios[$key]->indigena 				= 	$parametros['indigena'.$item->sexo];
					$beneficiarios[$key]->inmigrante 			=	$parametros['inmigrante'.$item->sexo];
					$beneficiarios[$key]->otros 				= 	$parametros['otros'.$item->sexo];
					$beneficiarios[$key]->muyAlta 				= 	$parametros['muyalta'.$item->sexo];
					$beneficiarios[$key]->alta 					= 	$parametros['alta'.$item->sexo];
					$beneficiarios[$key]->media 				= 	$parametros['media'.$item->sexo];
					$beneficiarios[$key]->baja 					= 	$parametros['baja'.$item->sexo];
					$beneficiarios[$key]->muyBaja 				= 	$parametros['muybaja'.$item->sexo];
					$recurso[] = $beneficiarios[$key];
				}
			}else{
				if($parametros['totalbeneficiariosf'] > 0){
					$beneficiarioF = new Beneficiario;
					$beneficiarioF->idTipoBeneficiario	= $parametros['tipobeneficiario'];
					$beneficiarioF->total 				= $parametros['totalbeneficiariosf'];
					$beneficiarioF->sexo 				= 'f';
					$beneficiarioF->urbana 				= $parametros['urbanaf'];
					$beneficiarioF->rural 				= $parametros['ruralf'];
					$beneficiarioF->mestiza 			= $parametros['mestizaf'];
					$beneficiarioF->indigena 			= $parametros['indigenaf'];
					$beneficiarioF->inmigrante 			= $parametros['inmigrantef'];
					$beneficiarioF->otros 				= $parametros['otrosf'];
					$beneficiarioF->muyAlta 			= $parametros['muyaltaf'];
					$beneficiarioF->alta 				= $parametros['altaf'];
					$beneficiarioF->media 				= $parametros['mediaf'];
					$beneficiarioF->baja 				= $parametros['bajaf'];
					$beneficiarioF->muyBaja 			= $parametros['muybajaf'];

					$recurso[] = $beneficiarioF;
				}
				if($parametros['totalbeneficiariosm'] > 0){
					$beneficiarioM = new Beneficiario;
					$beneficiarioM->idTipoBeneficiario	= $parametros['tipobeneficiario'];
					$beneficiarioM->total 				= $parametros['totalbeneficiariosm'];
					$beneficiarioM->sexo 				= 'm';
					$beneficiarioM->urbana 				= $parametros['urbanam'];
					$beneficiarioM->rural 				= $parametros['ruralm'];
					$beneficiarioM->mestiza 			= $parametros['mestizam'];
					$beneficiarioM->indigena 			= $parametros['indigenam'];
					$beneficiarioM->inmigrante 			= $parametros['inmigrantem'];
					$beneficiarioM->otros 				= $parametros['otrosm'];
					$beneficiarioM->muyAlta 			= $parametros['muyaltam'];
					$beneficiarioM->alta 				= $parametros['altam'];
					$beneficiarioM->media 				= $parametros['mediam'];
					$beneficiarioM->baja 				= $parametros['bajam'];
					$beneficiarioM->muyBaja 			= $parametros['muybajam'];

					$recurso[] = $beneficiarioM;
				}
			}

			$respuesta['data']['data'] = DB::transaction(function() use ($recurso, $proyecto, $es_editar, $viejo_beneficiario){
				if(!$proyecto->beneficiarios()->saveMany($recurso)){
					throw new Exception("Error al intentar guardar los beneficiarios del proyecto", 1);
				}
				$suma = 0;
				$suma_f = 0;
				$suma_m = 0;
				foreach ($proyecto->beneficiarios as $beneficiario) {
					$suma += $beneficiario->total;
					if($beneficiario->sexo == 'f'){
						$suma_f += $beneficiario->total;
					}else{
						$suma_m += $beneficiario->total;
					}
				}
				$proyecto->totalBeneficiarios = $suma;
				$proyecto->totalBeneficiariosF = $suma_f;
				$proyecto->totalBeneficiariosM = $suma_m;

				if(!$proyecto->save()){
					throw new Exception("Error al intentar actualizar los totales de los beneficiarios del proyecto", 1);
				}
				$proyecto->load('beneficiarios');
				
				if($proyecto->idClasificacionProyecto == 2 && $es_editar){
					$componentes_ids = $proyecto->componentes->lists('id');
					if(count($componentes_ids)){
						$desgloses_componente = ComponenteDesglose::whereIn('idComponente',$componentes_ids)->get()->lists('id');
						if(count($desgloses_componente)){
							DesgloseBeneficiario::whereIn('idComponenteDesglose',$desgloses_componente)
												->where('idTipoBeneficiario','=',$viejo_beneficiario)
												->update(['idTipoBeneficiario'=>$recurso[0]->idTipoBeneficiario]);
						}
					}
				}

				return $proyecto->beneficiarios;
			});
		}else{
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}
		return $respuesta;
	}

	public function guardar_datos_componente($selector,$parametros,$id = NULL){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array();
		$es_editar = FALSE;
		$proyecto = NULL;
		$componente = NULL;
		$reglasAccion = array(
			'denominador-ind-' . $selector 	=> 'required',
			'descripcion-ind-' . $selector 	=> 'required',
			'descripcion-obj-' . $selector 	=> 'required',
			'dimension-' . $selector 		=> 'required',
			'formula-' . $selector 			=> 'required',
			'frecuencia-' . $selector 		=> 'required',
			'interpretacion-' . $selector 	=> 'required',
			'numerador-ind-' . $selector 	=> 'required',
			'supuestos-' . $selector 		=> 'required',
			'tipo-ind-' . $selector 		=> 'required',
			'anio-base-' . $selector 		=> 'integer|min:0',
			'linea-base-' . $selector 		=> 'numeric|min:0',
			'unidad-medida-' . $selector 	=> 'required',
			'verificacion-' . $selector 	=> 'required'
			//'trim1-componente' 				=> 'numeric',
			//'trim2-componente' 				=> 'numeric',
			//'trim3-componente' 				=> 'numeric',
			//'trim4-componente' 				=> 'numeric',
			//'denominador-componente' 		=> 'required_if:formula-componente,1,2,3,4,5,6|numeric|min:0',
			//'numerador-componente' 			=> 'required|numeric|min:1',
			//'meta-componente' 				=> 'required|numeric|min:0',
		);

		if($selector == 'componente'){
			$proyecto = Proyecto::find($parametros['id-proyecto']);
			if(!$proyecto){
				throw new Exception("No se pudo encontrar el proyecto al que pertenece este componente", 1);
			}
		}else{
			$componente = Componente::find($parametros['id-componente']);
			if(!$componente){
				throw new Exception("No se pudo encontrar el componente al que pertenece esta actividad", 1);
			}
		}
		
		if($id){
			$es_editar = TRUE;
			if($selector == 'componente'){
				$recurso = Componente::find($id);
			}else{
				$recurso = Actividad::find($id);
			}
		}else{
			if($selector == 'componente'){
				$recurso = new Componente;
			}else{
				$recurso = new Actividad;
			}
			
		}
		
		if(!$es_editar){
			if($selector == 'componente'){
				$proyecto->load('componentes');

				if(count($proyecto->componentes) == 2){
					$respuesta['data']['data'] = 'El proyecto no puede tener mas de 2 componentes.';
					throw new Exception("No esta permitido guardar mas de 2 componentes por cada proyecto", 1);
				}
			}else{
				$componente->load('actividades');

				if(count($componente->actividades) == 5){
					$respuesta['data']['data'] = 'El componente no puede tener mas de 5 acciones.';
					throw new Exception("No esta permitido guardar mas de 5 acciones por cada componente", 1);
				}
			}
		}
		
		if($parametros['clasificacion'] == 2 && $selector == 'componente'){
			$reglasAccion['entregable'] 			= 'required';
			$reglasAccion['tipo-entregable'] 		= 'required';
			$reglasAccion['accion-entregable'] 	= 'required';
		}
		
		/*if(isset($parametros['datos_presupuesto'])){
			$this->reglasComponente['accion-presupuesto-requerido']	= 'required|numeric|min:1';
			$this->reglasComponente['objeto-gasto-presupuesto']		= 'required|array|min:1';
		}*/

		$validacion = Validador::validar(Input::all(), $reglasAccion);

		if($validacion === TRUE){

			$recurso->objetivo 				= $parametros['descripcion-obj-'.$selector];
			$recurso->mediosVerificacion 	= $parametros['verificacion-'.$selector];
			$recurso->supuestos 			= $parametros['supuestos-'.$selector];
			$recurso->indicador 			= $parametros['descripcion-ind-'.$selector];
			$recurso->numerador 			= $parametros['numerador-ind-'.$selector];
			$recurso->denominador 			= $parametros['denominador-ind-'.$selector];
			$recurso->interpretacion 		= $parametros['interpretacion-'.$selector];
			$recurso->idFormula 			= $parametros['formula-'.$selector];
			$recurso->idDimensionIndicador 	= $parametros['dimension-'.$selector];
			$recurso->idFrecuenciaIndicador = $parametros['frecuencia-'.$selector];
			$recurso->idTipoIndicador 		= $parametros['tipo-ind-'.$selector];
			$recurso->idUnidadMedida 		= $parametros['unidad-medida-'.$selector];
			$recurso->metaIndicador 		= ($parametros['meta-'.$selector])?$parametros['meta-'.$selector]:NULL;
			$recurso->numeroTrim1 			= ($parametros['trim1-'.$selector])?$parametros['trim1-'.$selector]:NULL;
			$recurso->numeroTrim2 			= ($parametros['trim2-'.$selector])?$parametros['trim2-'.$selector]:NULL;
			$recurso->numeroTrim3 			= ($parametros['trim3-'.$selector])?$parametros['trim3-'.$selector]:NULL;
			$recurso->numeroTrim4 			= ($parametros['trim4-'.$selector])?$parametros['trim4-'.$selector]:NULL;
			$recurso->valorNumerador 		= ($parametros['numerador-'.$selector])?$parametros['numerador-'.$selector]:NULL;
			$recurso->valorDenominador 		= ($parametros['denominador-'.$selector])?$parametros['denominador-'.$selector]:NULL;
			
			$recurso->lineaBase 			= ($parametros['linea-base-'.$selector])?$parametros['linea-base-'.$selector]:NULL;
			$recurso->anioBase 				= ($parametros['anio-base-'.$selector])?$parametros['anio-base-'.$selector]:NULL;

			if($parametros['clasificacion'] == 2 && $selector == 'componente'){
				$recurso->idEntregable 		= $parametros['entregable'];
				if($parametros['tipo-entregable'] != 'NA'){
					$recurso->idEntregableTipo	= $parametros['tipo-entregable'] ;
				}
				$recurso->idEntregableAccion	= $parametros['accion-entregable'];
			}

			if($selector == 'actividad'){
				$recurso->idProyecto = $componente->idProyecto;
			}

			$respuesta['data'] = DB::transaction(function() use ($parametros, $proyecto, $componente, $recurso, $selector, $es_editar){
				if($selector == 'componente'){
					$guardado = $proyecto->componentes()->save($recurso);
				}else{
					$guardado = $componente->actividades()->save($recurso);
				}

				if($guardado){
					if(isset($parametros['mes-'.$selector])){
						$jurisdicciones = $parametros['mes-'.$selector]; //Arreglo que contiene los datos [jurisdiccion][mes] = valor

						if(isset($parametros['mes-' . $selector . '-id'])){
							$ides = $parametros['mes-' . $selector . '-id'];
						}else{
							$ides = array();
						}

						$metasMes = array();

						if($es_editar){
							$recurso->load('metasMes');
						}

						foreach ($jurisdicciones as $clave => $meses) {
							foreach ($meses as $mes => $valor) {
								if(isset($ides[$clave][$mes])){
									$meta = $recurso->metasMes()->find($ides[$clave][$mes]);
									$meta->meta = $valor;
									$metasMes[] = $meta;
								}elseif($valor > 0){
									if($selector == 'componente'){
										$meta = new ComponenteMetaMes;
										$meta->idProyecto = $recurso->idProyecto;
									}else{
										$meta = new ActividadMetaMes;
										$meta->idProyecto = $componente->idProyecto;
									}
									$meta->claveJurisdiccion = $clave;
									$meta->mes = $mes;
									$meta->meta = $valor;
									$metasMes[] = $meta;
								}
							}
						}

						$recurso->metasMes()->saveMany($metasMes);
					}
					return array('data'=>$recurso);	
					//return array('data'=>$componente,'componentes'=>$proyecto->componentes,'metas'=>$metasMes);
				}else{
					throw new Exception("Ocurrió un error al guardar el componente.", 1);
				}
			});
		}else{
			//La Validación del Formulario encontro errores
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}
		return $respuesta;
	}

}

