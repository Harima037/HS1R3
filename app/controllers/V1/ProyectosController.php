<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use Proyecto, Componente, Actividad, Beneficiario,ComponenteMetaMes,ActividadMetaMes, Hash, Exception;

class ProyectosController extends BaseController {
	private $reglasProyecto = array(
		'funciongasto'				=> 'required',
		'clasificacionproyecto'		=> 'required',
		'nombretecnico'				=> 'required',
		'tipoproyecto'				=> 'required',
		'cobertura'					=> 'required',
		'tipoaccion'				=> 'required',
		'unidadresponsable'			=> 'required|digits:2',
		'programasectorial'			=> 'required|alpha_num|size:1',
		'programapresupuestario'	=> 'required|alpha_num|size:3',
		'programaespecial'			=> 'required|alpha_num|size:3',
		'actividadinstitucional'	=> 'required|alpha_num|size:3',
		'proyectoestrategico'		=> 'required|alpha_num|size:1',
		'vinculacionped'			=> 'required',
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

	private $reglasComponente = array(
		'denominador-ind-componente' 	=> 'required',
		'descripcion-ind-componente' 	=> 'required',
		'descripcion-obj-componente' 	=> 'required',
		'dimension-componente' 			=> 'required',
		'formula-componente' 			=> 'required',
		'frecuencia-componente' 		=> 'required',
		'interpretacion-componente' 	=> 'required',
		'meta-componente' 				=> 'required|numeric|min:1',
		'numerador-componente' 			=> 'required',
		'numerador-ind-componente' 		=> 'required',
		'supuestos-componente' 			=> 'required',
		'tipo-ind-componente' 			=> 'required',
		'anio-base-componente' 			=> 'integer|min:0',
		'denominador-componente' 		=> 'required_if:formula-componente,1,2,3,4,5,6|numeric|min:0',
		'linea-base-componente' 		=> 'numeric|min:0',
		'trim1-componente' 				=> 'numeric',
		'trim2-componente' 				=> 'numeric',
		'trim3-componente' 				=> 'numeric',
		'trim4-componente' 				=> 'numeric',
		'unidad-medida-componente' 		=> 'required',
		'verificacion-componente' 		=> 'required'
	);

	private $reglasActividad = array(
		'denominador-ind-actividad' 	=> 'required',
		'descripcion-ind-actividad' 	=> 'required',
		'descripcion-obj-actividad' 	=> 'required',
		'dimension-actividad' 			=> 'required',
		'formula-actividad' 			=> 'required',
		'frecuencia-actividad' 			=> 'required',
		'interpretacion-actividad' 		=> 'required',
		'meta-actividad' 				=> 'required|numeric|min:1',
		'numerador-actividad' 			=> 'required',
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

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}

			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS casificacionProyecto',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl')
								->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
								->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
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
				$recurso = Componente::with('actividades','metasMes')->find($id);
			}
			if($parametros['ver'] == 'actividad'){
				$recurso = Actividad::with('metasMes')->find($id);
			}
			if($parametros['ver'] == 'proyecto'){
				$recurso = Proyecto::contenidoCompleto()->find($id);
				if($recurso){
					foreach ($recurso->componentes as $key => $componente) {
						$recurso->componentes[$key]->load(array('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable'));
						foreach ($recurso->componentes[$key]->actividades as $llave => $actividad) {
							$recurso->componentes[$key]->actividades[$llave]->load(array('formula','dimension','frecuencia','tipoIndicador','unidadMedida'));
						}
					}
				}
			}
		}else{
			$recurso = Proyecto::contenidoCompleto()->find($id);
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data = array("data"=>$recurso->toArray());
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

		if($parametros['guardar'] == 'actividad'){
			try{
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
					$actividad->numeroTrim1 			= $parametros['trim1-actividad'];
					$actividad->numeroTrim2 			= $parametros['trim2-actividad'];
					$actividad->numeroTrim3 			= $parametros['trim3-actividad'];
					$actividad->numeroTrim4 			= $parametros['trim4-actividad'];
					$actividad->valorNumerador 			= $parametros['numerador-actividad'];
					$actividad->valorDenominador 		= $parametros['denominador-actividad'];
					$actividad->lineaBase 				= $parametros['linea-base-actividad'];
					$actividad->anioBase 				= $parametros['anio-base-actividad'];
					
					$respuesta['data'] = DB::transaction(function() use ($parametros, $componente, $actividad){
						if($componente->actividades()->save($actividad)){
							$actividad->load('usuario');
							$componente->actividades[] = $actividad;

							$meses = $parametros['mes-actividad'];

							$metasMes = array();

							foreach ($meses as $mes => $valor) {
								if($valor > 0){
									$meta = new ActividadMetaMes;
									$meta->mes = $mes;
									$meta->meta = $valor;
									$meta->idProyecto = $componente->idProyecto;
									$metasMes[] = $meta;
								}
							}
							
							$actividad->metasMes()->saveMany($metasMes);

							return array('data'=>$actividad,'actividades'=>$componente->actividades);
						}else{
							throw new Exception("Ocurrió un error al guardar la actividad.", 1);
						}
					});

				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
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
		} //Guardar datos de la actividad

		if($parametros['guardar'] == 'componente'){
			try{
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
					$this->reglasComponente['entregable-componente'] = 'required';
					$this->reglasComponente['tipo-obj-componente'] = 'required';
					$this->reglasComponente['accion-componente'] = 'required';
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
					$componente->numeroTrim1 			= $parametros['trim1-componente'];
					$componente->numeroTrim2 			= $parametros['trim2-componente'];
					$componente->numeroTrim3 			= $parametros['trim3-componente'];
					$componente->numeroTrim4 			= $parametros['trim4-componente'];
					$componente->valorNumerador 		= $parametros['numerador-componente'];
					$componente->valorDenominador 		= $parametros['denominador-componente'];
					$componente->lineaBase 				= $parametros['linea-base-componente'];
					$componente->anioBase 				= $parametros['anio-base-componente'];

					if($parametros['clasificacion'] == 2){
						$componente->idEntregable = $parametros['entregable-componente'];
						$componente->tipo = $parametros['tipo-obj-componente'];
						$componente->accion = $parametros['accion-componente'];
					}

					$respuesta['data'] = DB::transaction(function() use ($parametros, $proyecto, $componente){
						if($proyecto->componentes()->save($componente)){
							$componente->load('usuario');
							$proyecto->componentes[] = $componente;

							$meses = $parametros['mes-componente'];

							$metasMes = array();

							foreach ($meses as $mes => $valor) {
								if($valor > 0){
									$meta = new ComponenteMetaMes;
									$meta->mes = $mes;
									$meta->meta = $valor;
									$meta->idProyecto = $componente->idProyecto;
									$metasMes[] = $meta;
								}
							}
							
							$componente->metasMes()->saveMany($metasMes);

							return array('data'=>$componente,'componentes'=>$proyecto->componentes);
						}else{
							throw new Exception("Ocurrió un error al guardar el componente.", 1);
						}
					});
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}catch(\Exception $ex){
				$respuesta['http_status'] = 500;
				if($respuesta['data']['data'] == ''){
					$respuesta['data']['data'] = 'Ocurrio un error en el servidor, al guardar el componente.';
				}
				$respuesta['data']['ex'] = $ex->getMessage();
				if(!isset($respuesta['data']['code'])){
					$respuesta['data']['code'] = 'S03';
				}
			}
		} //Guardar datos del componente

		if($parametros['guardar'] == 'proyecto'){

			if($parametros['cobertura'] == 2){
				//Si la cobertura es diferente a estatal, checamos que haya seleccionado un municipio
				$this->reglasProyecto['municipio'] = 'required|digits_between:1,3';
			}elseif($parametros['cobertura'] == 3){
				$this->reglasProyecto['region'] = 'required|alpha';
			}

			$validacion = Validador::validar(Input::all(), $this->reglasProyecto);

			try{
				if($validacion === TRUE){

					$recurso = new Proyecto;

					$funcion_gasto = explode('.',$parametros['funciongasto']);

					$recurso->idClasificacionProyecto 		= $parametros['clasificacionproyecto'];
					$recurso->nombreTecnico 				= $parametros['nombretecnico'];
					$recurso->idTipoProyecto 				= $parametros['tipoproyecto'];
					$recurso->idCobertura 					= $parametros['cobertura'];
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
					$recurso->idTipoBeneficiario 			= $parametros['tipobeneficiario'];
					$recurso->totalBeneficiarios 			= $parametros['totalbeneficiariosf'] + $parametros['totalbeneficiariosm'];
					$recurso->totalBeneficiariosF 			= $parametros['totalbeneficiariosf'];
					$recurso->totalBeneficiariosM 			= $parametros['totalbeneficiariosm'];
					$recurso->idEstatusProyecto 			= 1;

					if($parametros['cobertura'] == 2){
						$recurso->claveMunicipio = $parametros['municipio'];
					}elseif($parametros['cobertura'] == 3){
						$recurso->claveRegion = $parametros['region'];
					}

				  //$recurso->idLiderProyecto 				= $parametros[''];
				  //$recurso->idJefeInmediato 				= $parametros[''];
				  //$recurso->idJefePlaneacion 				= $parametros[''];
				  //$recurso->idCoordinadorGrupoEstrategico = $parametros[''];

					DB::transaction(function() use ($parametros, $recurso, $respuesta){
						if($recurso->save()){

							$beneficiarioF = new Beneficiario;
							$beneficiarioF->sexo 		= 	'f';
							$beneficiarioF->urbana 		= 	$parametros['urbanaf'];
							$beneficiarioF->rural 		= 	$parametros['ruralf'];
							$beneficiarioF->mestiza 	= 	$parametros['mestizaf'];
							$beneficiarioF->indigena 	= 	$parametros['indigenaf'];
							$beneficiarioF->inmigrante 	= 	$parametros['inmigrantef'];
							$beneficiarioF->otros 		= 	$parametros['otrosf'];
							$beneficiarioF->muyAlta 	= 	$parametros['muyaltaf'];
							$beneficiarioF->alta 		= 	$parametros['altaf'];
							$beneficiarioF->media 		= 	$parametros['mediaf'];
							$beneficiarioF->baja 		= 	$parametros['bajaf'];
							$beneficiarioF->muyBaja 	= 	$parametros['muybajaf'];


							$beneficiarioM = new Beneficiario;
							$beneficiarioM->sexo 		= 	'm';
							$beneficiarioM->urbana 		= 	$parametros['urbanam'];
							$beneficiarioM->rural 		= 	$parametros['ruralm'];
							$beneficiarioM->mestiza 	= 	$parametros['mestizam'];
							$beneficiarioM->indigena 	= 	$parametros['indigenam'];
							$beneficiarioM->inmigrante 	= 	$parametros['inmigrantem'];
							$beneficiarioM->otros 		= 	$parametros['otrosm'];
							$beneficiarioM->muyAlta 	= 	$parametros['muyaltam'];
							$beneficiarioM->alta 		= 	$parametros['altam'];
							$beneficiarioM->media 		= 	$parametros['mediam'];
							$beneficiarioM->baja 		= 	$parametros['bajam'];
							$beneficiarioM->muyBaja 	= 	$parametros['muybajam'];

							$beneficiarios = array($beneficiarioF,$beneficiarioM);

							if(!$recurso->beneficiarios()->saveMany($beneficiarios)){
								$respuesta['data']['code'] = 'S01';
								throw new Exception("Error al intentar guardar los beneficiarios del proyecto", 1);
							}
						}else{
							//No se pudieron guardar los datos del proyecto
							$respuesta['data']['code'] = 'S01';
							throw new Exception("Error al intentar guardar los datos del proyecto", 1);
						}
					});
					//Proyecto guardado con éxito
					$respuesta['data'] = array('data'=>$recurso->toArray());
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}catch(\Exception $ex){
				$respuesta['http_status'] = 500;	
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos';
				$respuesta['data']['ex'] = $ex->getMessage();
				if(!isset($respuesta['data']['code'])){
					$respuesta['data']['code'] = 'S03';
				}
			}
		} //Guardar Datos del Proyecto
		
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

		if($parametros['guardar'] == 'actividad'){
			try{
				
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
					$recurso->numeroTrim1 				= 	$parametros['trim1-actividad'];
					$recurso->numeroTrim2 				= 	$parametros['trim2-actividad'];
					$recurso->numeroTrim3 				= 	$parametros['trim3-actividad'];
					$recurso->numeroTrim4 				= 	$parametros['trim4-actividad'];
					$recurso->valorNumerador 			= 	$parametros['numerador-actividad'];
					$recurso->valorDenominador 			= 	$parametros['denominador-actividad'];
					$recurso->lineaBase 				= 	$parametros['linea-base-actividad'];
					$recurso->anioBase 					= 	$parametros['anio-base-actividad'];
					
					if($recurso->save()){
						$componente = Componente::with('actividades')->find($recurso->idComponente);

						$respuesta['data']['data'] = $recurso;
						$respuesta['data']['actividades'] = $componente->actividades;
					}else{
						throw new Exception("Ocurrió un error al guardar la actividad.", 1);
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
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
		} //Guardar datos de la actividad

		if($parametros['guardar'] == 'componente'){
			try{
				
				if($parametros['clasificacion'] == 2){
					$this->reglasComponente['entregable-componente'] = 'required';
					$this->reglasComponente['tipo-obj-componente'] = 'required';
					$this->reglasComponente['accion-componente'] = 'required';
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
					$recurso->numeroTrim1 				= 	$parametros['trim1-componente'];
					$recurso->numeroTrim2 				= 	$parametros['trim2-componente'];
					$recurso->numeroTrim3 				= 	$parametros['trim3-componente'];
					$recurso->numeroTrim4 				= 	$parametros['trim4-componente'];
					$recurso->valorNumerador 			= 	$parametros['numerador-componente'];
					$recurso->valorDenominador 			= 	$parametros['denominador-componente'];
					$recurso->lineaBase 				= 	$parametros['linea-base-componente'];
					$recurso->anioBase 					= 	$parametros['anio-base-componente'];

					if($parametros['clasificacion'] == 2){
						$recurso->idEntregable 	= 	$parametros['entregable-componente'];
						$recurso->tipo 			= 	$parametros['tipo-obj-componente'];
						$recurso->accion 		= 	$parametros['accion-componente'];
					}

					if($recurso->save()){
						$proyecto = Proyecto::with('componentes')->find($recurso->idProyecto);

						$respuesta['data']['data'] = $recurso;
						$respuesta['data']['componentes'] = $proyecto->componentes;
					}else{
						throw new Exception("Ocurrió un error al guardar el componente.", 1);
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}catch(\Exception $ex){
				$respuesta['http_status'] = 500;	
				if($respuesta['data']['data'] == ''){
					$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos';
				}
				$respuesta['data']['ex'] = $ex->getMessage();
				if(!isset($respuesta['data']['code'])){
					$respuesta['data']['code'] = 'S03';
				}
			}
		} //Guardar datos del componente

		if($parametros['guardar'] == 'proyecto'){

			if($parametros['cobertura'] == 2){
				//Si la cobertura es diferente a estatal, checamos que haya seleccionado un municipio
				$this->reglasProyecto['municipio'] = 'required|digits_between:1,3';
			}elseif($parametros['cobertura'] == 3){
				$this->reglasProyecto['region'] = 'required|alpha';
			}

			$validacion = Validador::validar(Input::all(), $this->reglasProyecto);

			try{
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
					$recurso->idCobertura 					= $parametros['cobertura'];
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
					$recurso->idTipoBeneficiario 			= $parametros['tipobeneficiario'];
					$recurso->totalBeneficiarios 			= $parametros['totalbeneficiariosf'] + $parametros['totalbeneficiariosm'];
					$recurso->totalBeneficiariosF 			= $parametros['totalbeneficiariosf'];
					$recurso->totalBeneficiariosM 			= $parametros['totalbeneficiariosm'];
					$recurso->idEstatusProyecto 			= 1;

					if($parametros['cobertura'] == 2){
						$recurso->claveMunicipio = $parametros['municipio'];
						$recurso->claveRegion = NULL;
					}elseif($parametros['cobertura'] == 3){
						$recurso->claveMunicipio = NULL;
						$recurso->claveRegion = $parametros['region'];
					}else{
						$recurso->claveMunicipio = NULL;
						$recurso->claveRegion = NULL;
					}

				  //$recurso->idLiderProyecto 				= $parametros[''];
				  //$recurso->idJefeInmediato 				= $parametros[''];
				  //$recurso->idJefePlaneacion 				= $parametros[''];
				  //$recurso->idCoordinadorGrupoEstrategico = $parametros[''];

					DB::transaction(function() use ($parametros, $recurso, $respuesta){
						if($recurso->save()){
							$recurso->load('beneficiarios');

							foreach ($recurso->beneficiarios as $key => $item) {
								$recurso->beneficiarios[$key]->urbana 		= 	$parametros['urbana'.$item->sexo];
								$recurso->beneficiarios[$key]->rural 		= 	$parametros['rural'.$item->sexo];
								$recurso->beneficiarios[$key]->mestiza 		= 	$parametros['mestiza'.$item->sexo];
								$recurso->beneficiarios[$key]->indigena 	= 	$parametros['indigena'.$item->sexo];
								$recurso->beneficiarios[$key]->inmigrante 	=	$parametros['inmigrante'.$item->sexo];
								$recurso->beneficiarios[$key]->otros 		= 	$parametros['otros'.$item->sexo];
								$recurso->beneficiarios[$key]->muyAlta 		= 	$parametros['muyalta'.$item->sexo];
								$recurso->beneficiarios[$key]->alta 		= 	$parametros['alta'.$item->sexo];
								$recurso->beneficiarios[$key]->media 		= 	$parametros['media'.$item->sexo];
								$recurso->beneficiarios[$key]->baja 		= 	$parametros['baja'.$item->sexo];
								$recurso->beneficiarios[$key]->muyBaja 		= 	$parametros['muybaja'.$item->sexo];

								$recurso->beneficiarios[$key]->save();
							}
						}else{
							//No se pudieron guardar los datos del proyecto
							$respuesta['data']['code'] = 'S01';
							throw new Exception("Error al intentar guardar los datos del proyecto", 1);
						}
					});
					//Proyecto guardado con éxito
					$respuesta['data'] = array('data'=>$recurso->toArray());
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}catch(\Exception $ex){
				$respuesta['http_status'] = 500;	
				if($respuesta['data']['data'] == ''){
					$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos';
				}
				$respuesta['data']['ex'] = $ex->getMessage();
				if(!isset($respuesta['data']['code'])){
					$respuesta['data']['code'] = 'S03';
				}
			}
		} //Guardar Datos del Proyecto
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
				if($parametros['eliminar'] == 'componente'){
					$id_padre = $parametros['id-proyecto'];
					$rows = DB::transaction(function() use ($ids){
						Actividad::wherein('idComponente',$ids)->delete();
						return Componente::wherein('id',$ids)->delete();
					});
				}
				if($parametros['eliminar'] == 'actividad'){
					$id_padre = $parametros['id-componente'];
					$rows = Actividad::wherein('id',$ids)->delete();
				}
			}else{
				$rows = DB::transaction(function() use ($ids){
					$componentes = Componente::wherein('idProyecto',$ids)->get();
					foreach ($componentes as $componente) {
						$componente->actividades()->delete();
					}
					Componente::wherein('idProyecto',$ids)->delete();
					Beneficiario::wherein('idProyecto',$ids)->delete();
					return Proyecto::wherein('id',$ids)->delete();
				});
			}

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
				if(isset($parametros['eliminar'])){
					if($parametros['eliminar'] == 'actividad'){
						$data['actividades'] = Actividad::with('usuario')->where('idComponente',$id_padre)->get();
					}
					if($parametros['eliminar'] == 'componente'){
						$data['componentes'] = Componente::with('usuario')->where('idProyecto',$id_padre)->get();
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