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
*	@author 			Donaldo Ríos, Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception;
use Proyecto, Componente, Actividad, Beneficiario, FIBAP, ComponenteMetaMes, ActividadMetaMes, Region, Municipio, Jurisdiccion, 
	FibapDatosProyecto, Directorio, ComponenteDesglose, Accion, PropuestaFinanciamiento, DistribucionPresupuesto, DesgloseMetasMes, 
	DesgloseBeneficiario, ProyectoFinanciamiento, ProyectoFinanciamientoSubFuente,FuenteFinanciamiento,SentryUser;

class ProyectosController extends BaseController {
	private $reglasProyecto = array(
		'funciongasto'				=> 'required',
		'clasificacionproyecto'		=> 'required',
		'nombretecnico'				=> 'sometimes|required',
		'ejercicio'					=> 'required',
		'tipoproyecto'				=> 'required',
		'fechainicio'				=> 'required',
		'finalidadproyecto'			=> 'required',
		'cobertura'					=> 'sometimes|required',
		'municipio'					=> 'sometimes|required_if:cobertura,2|digits_between:1,3',
		'region'					=> 'sometimes|required_if:cobertura,3|alpha',
		'tipoaccion'				=> 'required',
		'unidadresponsable'			=> 'required|digits:2',
		'programasectorial'			=> 'required|alpha_num|size:1',
		'programapresupuestario'	=> 'sometimes|required|alpha_num|size:3',
		'origenasignacion'			=> 'required|alpha_num|size:2',
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
		'mediaf' 					=> 'required|integer|min:0',
		'mediam' 					=> 'required|integer|min:0',
		'mestizaf' 					=> 'required|integer|min:0',
		'mestizam'					=> 'required|integer|min:0',
		'muyaltaf' 					=> 'required|integer|min:0',
		'muyaltam' 					=> 'required|integer|min:0',
		'muybajaf' 					=> 'required|integer|min:0',
		'muybajam' 					=> 'required|integer|min:0',
		'ruralf' 					=> 'required|integer|min:0',
		'ruralm' 					=> 'required|integer|min:0',
		'urbanaf' 					=> 'required|integer|min:0',
		'urbanam' 					=> 'required|integer|min:0'
	);
	
	private $reglasFuenteInformacion = array(
		'fuente-informacion'	=> 'required',
		'responsable'			=> 'required'
	);

	private $reglasComponente = array(
		//'denominador-ind-componente' 	=> 'required',
		'descripcion-ind-componente' 	=> 'required',
		//'descripcion-obj-componente' 	=> 'required',
		//'dimension-componente' 			=> 'required',
		//'formula-componente' 			=> 'required',
		//'frecuencia-componente' 		=> 'required',
		//'interpretacion-componente' 	=> 'required',
		//'numerador-ind-componente' 		=> 'required',
		//'supuestos-componente' 			=> 'required',
		//'tipo-ind-componente' 			=> 'required',
		'anio-base-componente' 			=> 'integer|min:0',
		'linea-base-componente' 		=> 'numeric|min:0',
		'unidad-medida-componente' 		=> 'required',
		//'verificacion-componente' 		=> 'required',
		'trim1-componente' 				=> 'numeric',
		'trim2-componente' 				=> 'numeric',
		'trim3-componente' 				=> 'numeric',
		'trim4-componente' 				=> 'numeric',
		'denominador-componente' 		=> 'required_if:formula-componente,1,2,3,4,5,6|numeric|min:0',
		'numerador-componente' 			=> 'required|numeric|min:1',
		//'meta-componente' 				=> 'required|numeric|min:0'
	);

	private $reglasActividad = array(
		//'denominador-ind-actividad' 	=> 'required',
		'descripcion-ind-actividad' 	=> 'required',
		//'descripcion-obj-actividad' 	=> 'required',
		//'dimension-actividad' 			=> 'required',
		//'formula-actividad' 			=> 'required',
		//'frecuencia-actividad' 			=> 'required',
		//'interpretacion-actividad' 		=> 'required',
		//'meta-actividad' 				=> 'required|numeric|min:0',
		//'numerador-actividad' 			=> 'required|numeric|min:1',
		//'numerador-ind-actividad' 		=> 'required',
		//'supuestos-actividad' 			=> 'required',
		//'tipo-ind-actividad' 			=> 'required',
		'anio-base-actividad' 			=> 'integer|min:0',
		'denominador-actividad' 		=> 'required_if:formula-actividad,1,2,3,4,5,6|numeric|min:0',
		'linea-base-actividad' 			=> 'numeric|min:0',
		'trim1-actividad' 				=> 'numeric',
		'trim2-actividad' 				=> 'numeric',
		'trim3-actividad' 				=> 'numeric',
		'trim4-actividad' 				=> 'numeric',
		'unidad-medida-actividad' 		=> 'required',
		//'verificacion-actividad' 		=> 'required'
	);

	private $reglasFinanciamiento = array(
		'fondo-financiamiento'			=> 'required',
		//'destino-gasto'					=> 'required',
		'subfuente'						=> 'required|array|min:1'
	);
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){

			$rows = Proyecto::getModel();
			$rows = $rows->where('idClasificacionProyecto','=',1)
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
				$rows = $rows->where(function($query)use($parametros){
					$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
						->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
				});
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}
			
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoCoberturas.descripcion AS coberturaDescripcion','proyectos.idEstatusProyecto',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl','proyectos.cancelado')
								->join('sentryUsers','sentryUsers.id','=','proyectos.actualizadoPor')
								->join('catalogoCoberturas','catalogoCoberturas.id','=','proyectos.idCobertura')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->orderBy('modificadoAl', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
			return Response::json($data,$http_status);
		}elseif(isset($parametros['typeahead'])){
			$rows = Proyecto::getModel();

			if(isset($parametros['buscar'])){				
				$rows = $rows->where(function($query)use($parametros){
						$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
							->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
					});
			}

			if(isset($parametros['unidades'])){
				$unidades = explode(',',$parametros['unidades']);
				$rows = $rows->whereIn('proyectos.unidadResponsable',$unidades);
			}

			if($parametros['tipo'] == 'proyecto'){
				$rows = $rows->where('proyectos.idEstatusProyecto','=',5);
			}

			if(isset($parametros['departamento'])){
				if(isset($parametros['usuario'])){
					$id_usuario = $parametros['usuario'];
				}else{
					$id_usuario = 0;
				}

				if($parametros['tipo'] == 'proyecto'){
					if($parametros['departamento'] == 2){
						$rows = $rows->where(function($query)use($id_usuario){
							$query->whereNull('proyectos.idUsuarioValidacionSeg')
								->orWhere('proyectos.idUsuarioValidacionSeg','=',$id_usuario);
						});
					}else{
						$rows = $rows->where(function($query)use($id_usuario){
							$query->whereNull('proyectos.idUsuarioRendCuenta')
								->orWhere('proyectos.idUsuarioRendCuenta','=',$id_usuario);
						});
					}
				}else{
					$rows = $rows->where(function($query)use($id_usuario){
						$query->whereNull('proyectos.idUsuarioCaptura')
							->orWhere('proyectos.idUsuarioCaptura','=',$id_usuario);
					});
				}

				/*
				$usuarios = SentryUser::usuariosProyectos()->where('idDepartamento','=',$parametros['departamento'])
										->where('ejercicio','=',intval(date('Y')))->where('sentryUsers.id','<>',$id_usuario)->get();
				$proyectos_asignados = array();
				foreach ($usuarios as $usuario) {
					if($usuario->proyectos){
						$proyectos_asignados[] = $usuario->proyectos;
					}
				}
				$proyectos_asignados = explode('|',implode('|',$proyectos_asignados));

				$rows = $rows->whereNotIn('proyectos.id',$proyectos_asignados);*/
			}
			//var_dump($proyectos_asignados);die;
			//throw new Exception("Error:: " + print_r($proyectos_asignados,true), 1);
			
			$rows = $rows->contenidoSuggester()->get();

			if(count($rows)<=0){
	          $data = array('resultados'=>0,"data"=>array());
	        }else{
	          $data = array('resultados'=>count($rows),'data'=>$rows);
	        }
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
	public function show($id)
	{
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if($parametros){
			if($parametros['ver'] == 'componente'){
				$recurso = Componente::with('actividades.unidadMedida','metasMes')->find($id);
			}elseif($parametros['ver'] == 'financiamiento'){
				$recurso = ProyectoFinanciamiento::with('subFuentesFinanciamiento')->find($id);
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
			$recurso = Proyecto::with('componentes','beneficiarios','clasificacionProyecto','tipoProyecto','estatusProyecto',
									'jefeInmediato','liderProyecto','jefePlaneacion','coordinadorGrupoEstrategico',
									'fuentesFinanciamiento.fondoFinanciamiento','fuentesFinanciamiento.fuenteFinanciamiento',
									'fuentesFinanciamiento.subFuentesFinanciamiento')
								->find($id);

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
			$responsables = Directorio::responsablesActivos($recurso->unidadResponsable)->get();
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();
			if(!$parametros){
				$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
				$recurso['responsables'] = $responsables;
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
				$parametros['clasificacion'] = 1;
				$respuesta = $this->guardar_datos_componente('actividad',$parametros);

				if($respuesta['http_status'] == 200){
					$componente = Componente::find($parametros['id-componente']);
					$componente->actividades->load('unidadMedida');
					$actividad = $respuesta['data']['data'];
					$metasMes = $actividad->metasMes;
					$respuesta['data'] = array('data'=>$actividad,'actividades'=>$componente->actividades,'metas'=>$metasMes);
				}
			//Guardar datos de la actividad
			}elseif($parametros['guardar'] == 'componente'){
				$respuesta = $this->guardar_datos_componente('componente',$parametros);

				if($respuesta['http_status'] == 200){
					$proyecto = Proyecto::find($parametros['id-proyecto']);
					$proyecto->componentes->load('unidadMedida');
					$componente = $respuesta['data']['data'];
					$metasMes = $componente->metasMes;
					$respuesta['data'] = array('data'=>$componente,'componentes'=>$proyecto->componentes,'metas'=>$metasMes);
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
					$responsables = Directorio::responsablesActivos($recurso->unidadResponsable)->get();
					$recurso = $recurso->toArray();
					$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
					$recurso['responsables'] = $responsables;
					$recurso['liderProyecto'] = $respuesta['data']['nombre-lider-proyecto'];
					$recurso['liderProyectoCargo'] = $respuesta['data']['cargo-lider-proyecto'];
					$respuesta['data']['data'] = $recurso;
				}
				//Guardar Datos del Proyecto
			}elseif($parametros['guardar'] == 'financiamiento'){
				$respuesta = $this->guardar_datos_financiamiento($parametros);
			}
		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos.';
			}
			if(strpos($ex->getMessage(), '{"field":') !== FALSE){
				$respuesta['data']['code'] = 'U00';
				$respuesta['data']['data'] = $ex->getMessage();
			}else{
				$respuesta['data']['ex'] = $ex->getMessage();
				$respuesta['data']['linea'] = $ex->getLine();
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
	public function update($id)
	{
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		$parametros = Input::all();
		try{
			if($parametros['guardar'] == 'validar-proyecto'){
				$proyecto = Proyecto::find($id);

				if($proyecto->cancelado){
					$respuesta['data'] = array('data'=>'El Proyecto se encuentra cancelado');
					throw new Exception("Proyecto cancelado", 1);
				}elseif($proyecto->idEstatusProyecto == 1 || $proyecto->idEstatusProyecto == 3){
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
				}else{
					$respuesta['data'] = array('data'=>'El Proyecto ya se encuentra en proceso de Revisión');
				}
			}elseif($parametros['guardar'] == 'cancelacionproyecto'){
				$proyecto = Proyecto::find($id);
				if($proyecto){
					if($proyecto->cancelado){
						$respuesta['data'] = array('data'=>'El Proyecto ya se encuentra cancelado');
						throw new Exception("Proyecto cancelado", 1);
					}else{
						$respuesta = $this->cancelar_proyecto($parametros,$proyecto);
					}
				}else{
					throw new Exception("El proyecto no se encuentra disponible", 1);
				}
			}else{
				if($parametros['guardar'] != 'proyecto'){
					$proyecto = Proyecto::find($parametros['id-proyecto']);
				}else{
					$proyecto = Proyecto::find($id);
				}
				
				if($proyecto->cancelado){
					$respuesta['data'] = array('data'=>'El Proyecto se encuentra cancelado');
					throw new Exception("Proyecto cancelado", 1);
				}elseif($proyecto->idEstatusProyecto != 1 && $proyecto->idEstatusProyecto != 3){
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
				$parametros['clasificacion'] = 1;
				$respuesta = $this->guardar_datos_componente('actividad',$parametros,$id);

				if($respuesta['http_status'] == 200){
					$componente = Componente::find($parametros['id-componente']);
					$componente->actividades->load('unidadMedida');
					$actividad = $respuesta['data']['data'];
					$metasMes = $actividad->metasMes;
					$respuesta['data'] = array('data'=>$actividad,'actividades'=>$componente->actividades,'metas'=>$metasMes);
				}
			//Guardar datos de la actividad
			}elseif($parametros['guardar'] == 'componente'){  //Editar componente
				$respuesta = $this->guardar_datos_componente('componente',$parametros,$id);

				if($respuesta['http_status'] == 200){
					$proyecto = Proyecto::find($parametros['id-proyecto']);
					$proyecto->componentes->load('unidadMedida');
					$componente = $respuesta['data']['data'];
					$metasMes = $componente->metasMes;
					$respuesta['data'] = array('data'=>$componente,'componentes'=>$proyecto->componentes,'metas'=>$metasMes);
				}
			//Guardar datos del componente
			}elseif($parametros['guardar'] == 'beneficiario'){ 
				$respuesta = $this->guardar_datos_beneficiario($parametros,$id);
			}elseif($parametros['guardar'] == 'proyecto'){
				$respuesta = $this->guardar_datos_proyecto($parametros,$id);
				if($respuesta['http_status'] == 200){
					$recurso = $respuesta['data']['data'];
					$datos_anteriores = $respuesta['data']['datos-anteriores'];

					$jurisdicciones = NULL;
					if($recurso->idCobertura == 1 && ($datos_anteriores['claveMunicipio'] != NULL || $datos_anteriores['claveRegion'] != NULL)){
					//Cobertura Estado => Todos las Jurisdicciones
						$jurisdicciones = Jurisdiccion::all();
					}elseif($recurso->idCobertura == 2 && $recurso->claveMunicipio != $datos_anteriores['claveMunicipio']){ 
					//Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
						$jurisdicciones = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
					}elseif($recurso->idCobertura == 3 && $recurso->claveRegion != $datos_anteriores['claveRegion']){ 
					//Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
						$jurisdicciones = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
					}
					if(isset($respuesta['data']['nombre-lider-proyecto'])){
						$responsables = Directorio::responsablesActivos($recurso->unidadResponsable)->get();
					}

					$recurso = $recurso->toArray();
					if($jurisdicciones){
						$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
					}
					if(isset($respuesta['data']['nombre-lider-proyecto'])){
						$recurso['liderProyecto'] = $respuesta['data']['nombre-lider-proyecto'];
						$recurso['liderProyectoCargo'] = $respuesta['data']['cargo-lider-proyecto'];
						$recurso['responsables'] = $responsables;
					}
					$respuesta['data']['data'] = $recurso;
				}
			//Guardar Datos del Proyecto
			}elseif($parametros['guardar'] == 'financiamiento'){
				$respuesta = $this->guardar_datos_financiamiento($parametros,$id);
			}elseif($parametros['guardar'] == 'fuenteinformacion'){
				$respuesta = $this->guardar_fuente_informacion($parametros,$id);
			}
		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos.';
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

					$fuentes_financiamiento = ProyectoFinanciamiento::whereIn('idProyecto',$ids)->get();
					foreach ($fuentes_financiamiento as $fuente) {
						$fuente->subFuentesFinanciamiento()->detach();
					}
					ProyectoFinanciamiento::whereIn('idProyecto',$ids)->delete();
					
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
					}elseif($parametros['eliminar'] == 'financiamiento'){
						$data['financiamiento'] = ProyectoFinanciamiento::with('fondoFinanciamiento','fuenteFinanciamiento','subFuentesFinanciamiento')
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
			if($data['data'] == ''){
				$data['data'] = "No se pueden borrar los registros";
			}
		}

		return Response::json($data,$http_status);
	}

	public function guardar_fuente_informacion($parametros, $id){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array();

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
		return $respuesta;
	}

	public function guardar_datos_financiamiento($parametros, $id = NULL){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array();
		$es_editar = FALSE;
		
		$validacion = Validador::validar(Input::all(), $this->reglasFinanciamiento);
		//$validacion = TRUE;

		if($validacion === TRUE){
			if($id){
				$recurso = ProyectoFinanciamiento::find($id);
				$es_editar = TRUE;
			}else{
				$recurso = new ProyectoFinanciamiento;
				$recurso->idProyecto = $parametros['id-proyecto'];
			}

			$fondo = FuenteFinanciamiento::find($parametros['fondo-financiamiento']);
			$fuente = FuenteFinanciamiento::find($fondo->idPadre);
			//aquie es

			$recurso->idFuenteFinanciamiento 	= $fuente->idPadre; //Aqui es
			$recurso->idFondoFinanciamiento		= $parametros['fondo-financiamiento'];

			if($es_editar){
				$subfuentes_anteriores = $recurso->subFuentesFinanciamiento->lists('id');
			}else{
				$subfuentes_anteriores = array();
			}

			$subfuentes['nuevos'] = array_diff($parametros['subfuente'], $subfuentes_anteriores);
			$subfuentes['borrar'] = array_diff($subfuentes_anteriores, $parametros['subfuente']);
			
			$respuesta['data'] = DB::transaction(function() use ($recurso,$parametros,$subfuentes){
				$respuesta_transaction = array();
				if($recurso->save()){
					if(count($subfuentes['borrar'])){
						$recurso->subFuentesFinanciamiento()->detach($subfuentes['borrar']);
					}
					if(count($subfuentes['nuevos'])){
						$recurso->subFuentesFinanciamiento()->attach($subfuentes['nuevos']);
					}
					$respuesta_transaction['data'] = ProyectoFinanciamiento::where('idProyecto','=',$recurso->idProyecto)
																			->with('fuenteFinanciamiento','fondoFinanciamiento','subFuentesFinanciamiento')
																			->get();
				}else{
					throw new Exception("Ocurrio un error al intentar guardar los datos", 1);
				}
				return $respuesta_transaction;
			});
		}else{
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}
		return $respuesta;
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
			
			if(!$es_editar){
				$usuario = Sentry::getUser();
				$recurso->idUsuarioCaptura = $usuario->id;
			}

			$funcion_gasto = explode('.',$parametros['funciongasto']);

			if(Sentry::hasAccess('EXP.PROYECTOS.S')){
				if($parametros['numeroproyectoestrategico'] > 0){
					$recurso->numeroProyectoEstrategico = $parametros['numeroproyectoestrategico'];
				}else{
					$recurso->numeroProyectoEstrategico = 0;
				}
			}

			if($es_editar && $recurso->numeroProyectoEstrategico > 0){
				if($recurso->unidadResponsable != $parametros['unidadresponsable'] || $recurso->finalidad != $funcion_gasto[0] || $recurso->funcion != $funcion_gasto[1] || $recurso->subFuncion != $funcion_gasto[2] || $recurso->subSubFuncion != $funcion_gasto[3] || $recurso->programaSectorial != $parametros['programasectorial'] || $recurso->origenAsignacion != $parametros['origenasignacion'] || $recurso->actividadInstitucional != $parametros['actividadinstitucional'] || $recurso->proyectoEstrategico != $parametros['proyectoestrategico'] || $recurso->programaPresupuestario != $parametros['programapresupuestario']){
					$recurso->numeroProyectoEstrategico = 0;
				}
			}

			if($es_editar && ($recurso->unidadResponsable != $parametros['unidadresponsable'])){
				$recurso->idResponsable = NULL;
			}

			//$recurso->unidadResponsable 			= $parametros['unidadresponsable'];
			$recurso->idClasificacionProyecto 		= $parametros['clasificacionproyecto'];
			$recurso->ejercicio						= $parametros['ejercicio'];
			$recurso->idTipoAccion 					= $parametros['tipoaccion'];
			$recurso->finalidad 					= $funcion_gasto[0];
			$recurso->funcion 						= $funcion_gasto[1];
			$recurso->subFuncion 					= $funcion_gasto[2];
			$recurso->subSubFuncion 				= $funcion_gasto[3];
			$recurso->programaSectorial 			= $parametros['programasectorial'];
			$recurso->origenAsignacion 				= $parametros['origenasignacion'];
			$recurso->actividadInstitucional 		= $parametros['actividadinstitucional'];
			$recurso->proyectoEstrategico 			= $parametros['proyectoestrategico'];
			$recurso->idTipoProyecto 				= $parametros['tipoproyecto'];
			$recurso->fechaInicio					= $parametros['fechainicio'];
			$recurso->fechaTermino					= ($parametros['fechatermino'])?$parametros['fechatermino']:NULL;
			$recurso->finalidadProyecto				= $parametros['finalidadproyecto'];
			$recurso->nombreTecnico 				= $parametros['nombretecnico'];
			$recurso->idObjetivoPED 				= $parametros['vinculacionped'];
			$recurso->programaPresupuestario 		= $parametros['programapresupuestario'];
			
			if($recurso->idEstatusProyecto == NULL){
				$recurso->idEstatusProyecto 		= 1;
			}

			$nuevas_jurisdicciones = NULL;

			if($es_editar){
				if($recurso->idCobertura != $parametros['cobertura']){
					$recurso->idCobertura = $parametros['cobertura'];
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
				$recurso->idCobertura = $parametros['cobertura'];
				if($parametros['cobertura'] == 2){
					$recurso->claveMunicipio = $parametros['municipio'];
				}elseif($parametros['cobertura'] == 3){
					$recurso->claveRegion = $parametros['region'];
				}
			}

			if($recurso->unidadResponsable != $parametros['unidadresponsable'] || !$recurso->idLiderProyecto){
				$titulares = Directorio::titularesActivos(array('00','01', $parametros['unidadresponsable']))->get();
				foreach ($titulares as $titular) {
					if($titular->claveUnidad == '00'){ //Dirección General
						$recurso->idJefeInmediato 				= $titular->id;
					}elseif ($titular->claveUnidad == '01') { //Dirección de Planeación y Desarrollo
						$recurso->idJefePlaneacion 				= $titular->id;
						$recurso->idCoordinadorGrupoEstrategico = $titular->id;
						//if($recurso->idLiderProyecto == NULL){
						if(count($titulares) == 2){
							$recurso->idLiderProyecto = $titular->id;
							$respuesta['data']['nombre-lider-proyecto'] = $titular->nombre;
							$respuesta['data']['cargo-lider-proyecto'] = $titular->cargo;
						}
					}else{
						$recurso->idLiderProyecto = $titular->id;
						$respuesta['data']['nombre-lider-proyecto'] = $titular->nombre;
						$respuesta['data']['cargo-lider-proyecto'] = $titular->cargo;
					}
				}
			}
			$recurso->unidadResponsable = $parametros['unidadresponsable'];

			if(!$es_editar){
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

			$suma_zona_f = $parametros['urbanaf'] + $parametros['ruralf'];
			$suma_zona_m = $parametros['urbanam'] + $parametros['ruralm'];
			$suma_poblacion_f = $parametros['mestizaf'] + $parametros['indigenaf'];
			$suma_poblacion_m = $parametros['mestizam'] + $parametros['indigenam'];
			$suma_marginacion_f = $parametros['muyaltaf'] + $parametros['altaf'] + $parametros['mediaf'] + $parametros['bajaf'] + $parametros['muybajaf'];
			$suma_marginacion_m = $parametros['muyaltam'] + $parametros['altam'] + $parametros['mediam'] + $parametros['bajam'] + $parametros['muybajam'];

			if($parametros['totalbeneficiariosf'] != $suma_zona_f || $parametros['totalbeneficiariosf'] != $suma_poblacion_f || $parametros['totalbeneficiariosf'] != $suma_marginacion_f){
				throw new Exception('{"field":"totalbeneficiariosf","error":"Hay totales capturados en el desglose para este beneficiario que no concuerda con este total."}', 1);
			}

			if($parametros['totalbeneficiariosm'] != $suma_zona_m || $parametros['totalbeneficiariosm'] != $suma_poblacion_m || $parametros['totalbeneficiariosm'] != $suma_marginacion_m){
				throw new Exception('{"field":"totalbeneficiariosm","error":"Hay totales capturados en el desglose para este beneficiario que no concuerda con este total."}', 1);
			}			

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
		
		if(isset($parametros['nivel'])){
			$selector_nivel = $parametros['nivel'];
		}else{
			$selector_nivel = $selector;
		}

		$reglasAccion = array(
			//'denominador-ind-' . $selector 	=> 'required',
			'descripcion-ind-' . $selector 	=> 'required',
			//'descripcion-obj-' . $selector 	=> 'required',
			//'dimension-' . $selector 		=> 'required',
			//'formula-' . $selector 			=> 'required',
			//'frecuencia-' . $selector 		=> 'required',
			//'interpretacion-' . $selector 	=> 'required',
			//'numerador-ind-' . $selector 	=> 'required',
			//'supuestos-' . $selector 		=> 'required',
			//'tipo-ind-' . $selector 		=> 'required',
			'anio-base-' . $selector 		=> 'integer|min:0',
			'linea-base-' . $selector 		=> 'numeric|min:0',
			'unidad-medida-' . $selector 	=> 'required',
			//'verificacion-' . $selector 	=> 'required'
			//'trim1-componente' 				=> 'numeric',
			//'trim2-componente' 				=> 'numeric',
			//'trim3-componente' 				=> 'numeric',
			//'trim4-componente' 				=> 'numeric',
			//'denominador-componente' 		=> 'required_if:formula-componente,1,2,3,4,5,6|numeric|min:0',
			//'numerador-componente' 			=> 'required|numeric|min:1',
			//'meta-componente' 				=> 'required|numeric|min:0',
		);

		if($selector_nivel == 'componente'){
			$proyecto = Proyecto::find($parametros['id-proyecto']);
			if(!$proyecto){
				throw new Exception("No se pudo encontrar el proyecto al que pertenece este componente", 1);
			}
		}else{
			if(isset($parametros['id-nuevo-componente'])){
				$componente_id = $parametros['id-nuevo-componente'];
			}else{
				$componente_id = $parametros['id-componente'];
			}
			
			$componente = Componente::find($componente_id);
			if(!$componente){
				//componente-seleccionado
				throw new Exception('{"field":"componente-seleccionado","error":"No se pudo encontrar el componente al que pertenece esta actividad."}', 1);
				//throw new Exception("No se pudo encontrar el componente al que pertenece esta actividad", 1);
			}
		}
		
		if($id){
			$es_editar = TRUE;
			if($selector_nivel == 'componente'){
				$recurso = Componente::find($id);
			}else{
				$recurso = Actividad::find($id);
			}
		}else{
			if($selector_nivel == 'componente'){
				$recurso = new Componente;
			}else{
				$recurso = new Actividad;
			}
			
		}
		
		/*if(!$es_editar){
			if($selector_nivel == 'componente'){
				$proyecto->load('componentes');
				if(count($proyecto->componentes) == 2){  //Por proyecto con tres compoenentes
					$respuesta['data']['data'] = 'El proyecto no puede tener mas de 2 componentes.';
					throw new Exception("No esta permitido guardar mas de 2 componentes por cada proyecto", 1);
				}
			}//else{
				//$componente->load('actividades');
				//Que siempre no, que si se puede tener mas de 5 actividades por componente
				if(count($componente->actividades) == 5){
					$respuesta['data']['data'] = 'El componente no puede tener mas de 5 acciones.';
					throw new Exception("No esta permitido guardar mas de 5 acciones por cada componente", 1);
				}
				
			//}
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

			if(!$recurso->idFormula){ $recurso->idFormula = NULL; }
			if(!$recurso->idDimensionIndicador){ $recurso->idDimensionIndicador = NULL; }
			if(!$recurso->idFrecuenciaIndicador){ $recurso->idFrecuenciaIndicador = NULL; }
			if(!$recurso->idTipoIndicador){ $recurso->idTipoIndicador = NULL; }

			if($parametros['clasificacion'] == 2 && $selector_nivel == 'componente'){
				if($parametros['entregable']){
					$recurso->idEntregable 		= $parametros['entregable'];
				}
				if($parametros['tipo-entregable'] != 'NA' && $parametros['tipo-entregable'] != ''){
					$recurso->idEntregableTipo	= $parametros['tipo-entregable'] ;
				}
				if($parametros['accion-entregable']){
					$recurso->idEntregableAccion	= $parametros['accion-entregable'];
				}
			}

			if($selector_nivel == 'actividad'){
				$recurso->idProyecto = $componente->idProyecto;
			}

			$respuesta['data'] = DB::transaction(function() use ($parametros, $proyecto, $componente, $recurso, $selector, $selector_nivel, $es_editar){
				if($selector_nivel == 'componente'){
					$guardado = $proyecto->componentes()->save($recurso);
				}else{
					$guardado = $componente->actividades()->save($recurso);
				}

				if($guardado){
					if($parametros['clasificacion'] == 2 && !$es_editar){
						$accion = new Accion;
						if($selector_nivel == 'componente'){
							$accion->idComponente = $recurso->id;
						}else{
							$accion->idActividad = $recurso->id;
						}
						$accion->idFibap = $parametros['id-fibap'];
						$accion->presupuestoRequerido = 0;
						$accion->save();
					}

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
									$meta->meta = ($valor)?$valor:0;
									$metasMes[] = $meta;
								}elseif($valor > 0){
									if($selector_nivel == 'componente'){
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
					$recurso->load('metasMes');
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

	public function cancelar_proyecto($parametros,$proyecto){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array();

		$reglasCancelacion = array(
			'fecha-cancelacion' 	=> 'date|required',
			'motivos-cancelacion' 	=> 'required'
		);

		$validacion = Validador::validar(Input::all(), $reglasCancelacion);

		if($validacion === TRUE){
			$proyecto->cancelado = 1;
			$proyecto->fechaCancelacion = $parametros['fecha-cancelacion'];
			$proyecto->motivoCancelacion = $parametros['motivos-cancelacion'];
			if(!$proyecto->save()){
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array('data'=>'Ocurrió un error al intentar guardar los datos','code'=>'S01');
			}
		}else{
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}

		return $respuesta;
	}

}

