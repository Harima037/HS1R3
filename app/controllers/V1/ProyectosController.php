<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use Proyecto, Beneficiario,Hash;

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
		'totalbenficiarios'=>'required',
		'totalbenficiariosf'=>'required',
		'totalbenficiariosm'=>'required',
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
		//'municipio' => 'required' //condicional

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

			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,numeroProyectoEstrategico) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS casificacionProyecto','catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl')
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

		$recurso = Catalogo::find($id);

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
					$recurso->subfuncion 					= $funcion_gasto[2];
					$recurso->subsubfuncion 				= $funcion_gasto[3];
					$recurso->programaSectorial 			= $parametros['programasectorial'];
					$recurso->programaPresupuestario 		= $parametros['programapresupuestario'];
					$recurso->programaEspecial 				= $parametros['programaespecial'];
					$recurso->actividadInstitucional 		= $parametros['actividadinstitucional'];
					$recurso->proyectoEstrategico 			= $parametros['proyectoestrategico'];
				  //$recurso->numeroProyectoEstrategico 	= $parametros['']; //Se genera automaticamente
					$recurso->idObjetivoPED 				= $parametros['vinculacionped'];
					$recurso->idTipoBeneficiario 			= $parametros['tipobeneficiario'];
					$recurso->totalBeneficiarios 			= $parametros['totalbenficiarios'];
					$recurso->totalBeneficiariosF 			= $parametros['totalbenficiariosf'];
					$recurso->totalBeneficiariosM 			= $parametros['totalbenficiariosm'];
					$recurso->idEstatusProyecto 			= 1;

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