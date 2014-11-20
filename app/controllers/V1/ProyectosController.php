<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use Proyecto, Componente, Actividad, Beneficiario,Hash, Exception;

class ProyectosController extends BaseController {
	private $reglasProyecto = array(
		'funciongasto'=>'required',
		'clasificacionproyecto'=>'required',
		'nombretecnico'=>'required',
		'tipoproyecto'=>'required',
		'cobertura'=>'required',
		'tipoaccion'=>'required',
		'unidadresponsable'=>'required',
		'programasectorial'=>'required',
		'programapresupuestario'=>'required',
		'programaespecial'=>'required',
		'actividadinstitucional'=>'required',
		'proyectoestrategico'=>'required',
		'vinculacionped'=>'required',
		'tipobeneficiario'=>'required',
		'totalbeneficiarios'=>'required',
		'totalbeneficiariosf'=>'required',
		'totalbeneficiariosm'=>'required',
		'altaf' => 'required',
		'altam' => 'required',
		'bajaf' => 'required',
		'bajam' => 'required',
		'indigenaf' => 'required',
		'indigenam' => 'required',
		'inmigrantef' => 'required',
		'inmigrantem' => 'required',
		'mediaf' => 'required',
		'mediam' => 'required',
		'mestizaf' => 'required',
		'mestizam' => 'required',
		'muyaltaf' => 'required',
		'muyaltam' => 'required',
		'muybajaf' => 'required',
		'muybajam' => 'required',
		'otrosf' => 'required',
		'otrosm' => 'required',
		'ruralf' => 'required',
		'ruralm' => 'required',
		'urbanaf' => 'required',
		'urbanam' => 'required'
	);

	private $reglasComponente = array(
		'anio-base-componente' => 'required',
		'denominador-componente' => 'required',
		'denominador-ind-componente' => 'required',
		'descripcion-ind-componente' => 'required',
		'descripcion-obj-componente' => 'required',
		'dimension-componente' => 'required',
		'formula-componente' => 'required',
		'frecuencia-componente' => 'required',
		'interpretacion-componente' => 'required',
		'linea-base-componente' => 'required',
		'meta-componente' => 'required',
		'numerador-componente' => 'required',
		'numerador-ind-componente' => 'required',
		'supuestos-componente' => 'required',
		'tipo-ind-componente' => 'required',
		'trim1-componente' => 'required',
		'trim2-componente' => 'required',
		'trim3-componente' => 'required',
		'trim4-componente' => 'required',
		'unidad-medida-componente' => 'required',
		'verificacion-componente' => 'required'
	);

	private $reglasActividad = array(
		'anio-base-actividad' => 'required',
		'denominador-actividad' => 'required',
		'denominador-ind-actividad' => 'required',
		'descripcion-ind-actividad' => 'required',
		'descripcion-obj-actividad' => 'required',
		'dimension-actividad' => 'required',
		'formula-actividad' => 'required',
		'frecuencia-actividad' => 'required',
		'interpretacion-actividad' => 'required',
		'linea-base-actividad' => 'required',
		'meta-actividad' => 'required',
		'numerador-actividad' => 'required',
		'numerador-ind-actividad' => 'required',
		'supuestos-actividad' => 'required',
		'tipo-ind-actividad' => 'required',
		'trim1-actividad' => 'required',
		'trim2-actividad' => 'required',
		'trim3-actividad' => 'required',
		'trim4-actividad' => 'required',
		'unidad-medida-actividad' => 'required',
		'verificacion-actividad' => 'required'
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
				$recurso = Componente::with('actividades')->find($id);
			}
			if($parametros['ver'] == 'actividad'){
				$recurso = Actividad::find($id);
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
					$actividad->objetivo = $parametros['descripcion-obj-actividad'];
					$actividad->mediosVerificacion = $parametros['verificacion-actividad'];
					$actividad->supuestos = $parametros['supuestos-actividad'];
					$actividad->indicador = $parametros['descripcion-ind-actividad'];
					$actividad->numerador = $parametros['numerador-ind-actividad'];
					$actividad->denominador = $parametros['denominador-ind-actividad'];
					$actividad->interpretacion = $parametros['interpretacion-actividad'];
					$actividad->idFormula = $parametros['formula-actividad'];
					$actividad->idDimensionIndicador = $parametros['dimension-actividad'];
					$actividad->idFrecuenciaIndicador = $parametros['frecuencia-actividad'];
					$actividad->idTipoIndicador = $parametros['tipo-ind-actividad'];
					$actividad->idUnidadMedida = $parametros['unidad-medida-actividad'];
					$actividad->metaIndicador = $parametros['meta-actividad'];
					$actividad->numeroTrim1 = $parametros['trim1-actividad'];
					$actividad->numeroTrim2 = $parametros['trim2-actividad'];
					$actividad->numeroTrim3 = $parametros['trim3-actividad'];
					$actividad->numeroTrim4 = $parametros['trim4-actividad'];
					$actividad->valorNumerador = $parametros['numerador-actividad'];
					$actividad->valorDenominador = $parametros['denominador-actividad'];
					$actividad->lineaBase = $parametros['linea-base-actividad'];
					$actividad->anioBase = $parametros['anio-base-actividad'];
					
					if($componente->actividades()->save($actividad)){
						$componente->actividades[] = $actividad;

						$respuesta['data']['data'] = $actividad;
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
				$proyecto = Proyecto::find(Input::get('id-proyecto'));

				if(!$proyecto){
					throw new Exception("No se pudo encontrar el proyecto al que pertenece este componente", 1);
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
					$componente->objetivo = $parametros['descripcion-obj-componente'];
					$componente->mediosVerificacion = $parametros['verificacion-componente'];
					$componente->supuestos = $parametros['supuestos-componente'];
					$componente->indicador = $parametros['descripcion-ind-componente'];
					$componente->numerador = $parametros['numerador-ind-componente'];
					$componente->denominador = $parametros['denominador-ind-componente'];
					$componente->interpretacion = $parametros['interpretacion-componente'];
					$componente->idFormula = $parametros['formula-componente'];
					$componente->idDimensionIndicador = $parametros['dimension-componente'];
					$componente->idFrecuenciaIndicador = $parametros['frecuencia-componente'];
					$componente->idTipoIndicador = $parametros['tipo-ind-componente'];
					$componente->idUnidadMedida = $parametros['unidad-medida-componente'];
					$componente->metaIndicador = $parametros['meta-componente'];
					$componente->numeroTrim1 = $parametros['trim1-componente'];
					$componente->numeroTrim2 = $parametros['trim2-componente'];
					$componente->numeroTrim3 = $parametros['trim3-componente'];
					$componente->numeroTrim4 = $parametros['trim4-componente'];
					$componente->valorNumerador = $parametros['numerador-componente'];
					$componente->valorDenominador = $parametros['denominador-componente'];
					$componente->lineaBase = $parametros['linea-base-componente'];
					$componente->anioBase = $parametros['anio-base-componente'];

					if($parametros['clasificacion'] == 2){
						$componente->idEntregable = $parametros['entregable-componente'];
						$componente->tipo = $parametros['tipo-obj-componente'];
						$componente->accion = $parametros['accion-componente'];
					}

					if($proyecto->componentes()->save($componente)){
						$proyecto->load('componentes');

						$respuesta['data']['data'] = $componente;
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
				$respuesta['data']['data'] = 'Ocurrio un error en el servidor, al guardar el componente.';
				$respuesta['data']['ex'] = $ex->getMessage();
				if(!isset($respuesta['data']['code'])){
					$respuesta['data']['code'] = 'S03';
				}
			}
		} //Guardar datos del componente

		if($parametros['guardar'] == 'proyecto'){

			if($parametros['cobertura'] != 1){
				//Si la cobertura es diferente a estatal, checamos que haya seleccionado un municipio
				$this->reglasProyecto['municipio'] = 'required';
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
					$recurso->totalBeneficiarios 			= $parametros['totalbeneficiarios'];
					$recurso->totalBeneficiariosF 			= $parametros['totalbeneficiariosf'];
					$recurso->totalBeneficiariosM 			= $parametros['totalbeneficiariosm'];
					$recurso->idEstatusProyecto 			= 1;

					if($parametros['cobertura'] != 1){
						$recurso->claveMunicipio = $parametros['municipio'];
					}

				  //$recurso->idLiderProyecto 				= $parametros[''];
				  //$recurso->idJefeInmediato 				= $parametros[''];
				  //$recurso->idJefePlaneacion 				= $parametros[''];
				  //$recurso->idCoordinadorGrupoEstrategico = $parametros[''];

					DB::transaction(function() use ($parametros, $recurso, $respuesta){
						if($recurso->save()){

							$beneficiarioF = new Beneficiario;
							$beneficiarioF->sexo = 'f';
							$beneficiarioF->urbana = $parametros['urbanaf'];
							$beneficiarioF->rural = $parametros['ruralf'];
							$beneficiarioF->mestiza = $parametros['mestizaf'];
							$beneficiarioF->indigena = $parametros['indigenaf'];
							$beneficiarioF->inmigrante = $parametros['inmigrantef'];
							$beneficiarioF->otros = $parametros['otrosf'];
							$beneficiarioF->muyAlta = $parametros['muyaltaf'];
							$beneficiarioF->alta = $parametros['altaf'];
							$beneficiarioF->media = $parametros['mediaf'];
							$beneficiarioF->baja = $parametros['bajaf'];
							$beneficiarioF->muyBaja = $parametros['muybajaf'];


							$beneficiarioM = new Beneficiario;
							$beneficiarioM->sexo = 'm';
							$beneficiarioM->urbana = $parametros['urbanam'];
							$beneficiarioM->rural = $parametros['ruralm'];
							$beneficiarioM->mestiza = $parametros['mestizam'];
							$beneficiarioM->indigena = $parametros['indigenam'];
							$beneficiarioM->inmigrante = $parametros['inmigrantem'];
							$beneficiarioM->otros = $parametros['otrosm'];
							$beneficiarioM->muyAlta = $parametros['muyaltam'];
							$beneficiarioM->alta = $parametros['altam'];
							$beneficiarioM->media = $parametros['mediam'];
							$beneficiarioM->baja = $parametros['bajam'];
							$beneficiarioM->muyBaja = $parametros['muybajam'];

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
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almancenar los datos';
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
		$respuesta = array();

		$recurso = Catalogo::find($id);

		if(is_null($recurso)){
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso->descripcion	= Input::get('descripcion');

			$respuesta = Validador::guardar(Input::all(), $this->reglas, $recurso);			
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
			$ids = Input::get('rows');
			
			$rows = Catalogo::wherein('id', $ids)->delete();

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
			}else{
				$http_status = 404;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}	
		}catch(Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');	
		}

		return Response::json($data,$http_status);
	}

}