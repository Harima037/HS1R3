<?php

namespace V1;

use SSA\Utilerias\Validador, SSA\Utilerias\HelperSentry;
use BaseController, Input, Response, DB, Sentry;
use Role, Hash, Directorio, UnidadResponsable, CatalogoDirectorio, CatalogoCargo, Proyecto;

class LideresProyectosController extends \BaseController {
	private $reglas = array(
		'area'			=> 'required_without:terminar_cargo|numeric|min:1',
		'cargo'			=> 'required_without:terminar_cargo',
		'fecha_inicio'	=> 'required_without:terminar_cargo|date',
		'responsable'	=> 'required_without:terminar_cargo'
		//'fecha_fin'		=> 'date',
		//'extension'		=> 'required',
		//'telefono'		=> 'required'
	);

	private $reglas_responsable = array(
		'nombre'		=> 'required',
		'email'			=> 'email'
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
			
			$rows = Directorio::getModel();	
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){
				$rows = $rows->where(function($query)use($parametros){
					$query->where('nombre','like','%'.$parametros['buscar'].'%')->orWhere('cargo','like','%'.$parametros['buscar'].'%');
				});
			}

			if($parametros['filtro_activos'] === '1'){
				$rows = $rows->whereNull('fechaFin');
			}

			$total = $rows->count();

			$rows = $rows->select('id','nombre','cargo','fechaInicio','fechaFin')
								->orderBy('idArea', 'asc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			
			

			$data = array('resultados'=>$total,'data'=>$rows,'parametros'=>$parametros['filtro_activos']);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No se encontraron responsables",'code'=>'W00');
			}			
			
			return Response::json($data,$http_status);
		}	

		$rows = DB::table('groups')->all();
		
		
		return Response::json($data,$http_status);
	}

	public function responsableEnArea($id){
		$http_status = 200;
		$data = array();

		//$recurso = Directorio::whereNull('fechaFin')->where('idArea','=',$id)->get();
		$recurso = Directorio::where('idArea','=',$id)->orderBy('fechaInicio')->get();

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data = array("data"=>$recurso->toArray());
		}
		
		return Response::json($data,$http_status);
	}

	public function datosDelResponsable($id){
		$http_status = 200;
		$data = array();

		$inputs = Input::all();

		$recurso = CatalogoDirectorio::find($id);
		
		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data = array("data"=>$recurso->toArray());
		}
		
		return Response::json($data,$http_status);
	}

	public function areasDelResponsable($id){
		$http_status = 200;
		$data = array();
		
		$inputs = Input::all();

		$recurso = CatalogoCargo::where('idDirectorio','=',$id)->orderBy('fechaInicio');

		if(isset($inputs['solo_activos']) && $inputs['solo_activos'] == 1){
			$recurso = $recurso->whereNull('fechaFin');
		}

		$recurso = $recurso->get();

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data = array("data"=>$recurso->toArray());
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

		$recurso = Directorio::find($id);

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$historial_cargos = CatalogoCargo::where('idDirectorio','=',$recurso->idDirectorio)->orderBy('fechaInicio')->get();
			$proyectos_asignados = Proyecto::where(function($query)use($id){
				$query->where('idLiderProyecto','=',$id)->orWhere('idResponsable','=',$id);
			})->get();

			//Para prueba de scroll
			//$historial_cargos = CatalogoCargo::all();
			//$proyectos_asignados = Proyecto::all();

			$data = array("data"=>array('recurso'=>$recurso->toArray(),'historial'=>$historial_cargos,'proyectos'=>$proyectos_asignados));
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
		$inputs = Input::all();     
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		try {
			$validacion = Validador::validar(Input::all(), $this->reglas);

			if($validacion === TRUE){
				DB::beginTransaction();

				$unidades_responsables = UnidadResponsable::withTrashed()->get();
				$unidades_responsables = $unidades_responsables->lists('descripcion','idArea');
				
				$cargo = new CatalogoCargo();
				
				$cargo->idDirectorio = $inputs['responsable'];
				$cargo->descripcion = $inputs['cargo'];
				$cargo->idArea = $inputs['area'];
				$cargo->telefono = $inputs['telefono'];
				$cargo->extension = $inputs['extension'];
				$cargo->fechaInicio = $inputs['fecha_inicio'];

				if(isset($unidades_responsables[$cargo->idArea])){
					$cargo->nivel = 1;
				}else{
					$cargo->nivel = 0;
				}

				$cargo_ocupado = CatalogoCargo::where('idArea','=',$cargo->idArea)->whereNull('fechaFin')->first();
				if($cargo_ocupado){
					$cargo_ocupado->fechaFin = $cargo->fechaInicio;
					$cargo_ocupado->save();
				}

				$cargo_responsable = CatalogoCargo::where('idDirectorio','=',$cargo->idDirectorio)->whereNull('fechaFin')->first();
				if($cargo_responsable){
					$cargo_responsable->fechaFin = $cargo->fechaInicio;
					$cargo_responsable->save();
				}
				
				if($cargo->save()){
					$respuesta['data'] = array('data'=>$cargo);
					$this->reasignarDirectivos($cargo);
					DB::commit();
				}else{				
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'No se pudieron guardar los cambios.','code'=>'S03');
					DB::rollback();
				}
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
			}
        } catch (Exception $e) {
			DB::rollback();
        	return Response::json(array('data'=>'Ocurrio un error al guardar la información.','message'=>$e->getMessage(),'line'=>$e->getLine()),500);           
        }
        return Response::json($respuesta['data'],$respuesta['http_status']);
	}


	public function guardarResponsable(){
		$inputs = Input::all();     
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

        try {
			if($inputs['id_directorio'] != ''){
				$responsable = CatalogoDirectorio::find($inputs['id_directorio']);
			}else{
				$responsable = new CatalogoDirectorio();
			}

			$validacion = Validador::validar($inputs, $this->reglas_responsable);

			if($validacion === TRUE){
				DB::beginTransaction();

				$responsable->nombre = $inputs['nombre'];
				$responsable->email = $inputs['email'];
				$responsable->save();

				$respuesta['data'] = array('data'=>$responsable);
				//,'responsables'=>CatalogoDirectorio::all()

				DB::commit();
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
			}
		}catch (Exception $e) {
			DB::rollback();
        	return Response::json(array('data'=>'Ocurrio un error al guardar la información.','message'=>$e->getMessage(),'line'=>$e->getLine()),500);           
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
		
        $inputs = Input::all();     
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

        try {
			$lider = Directorio::find($id);

			if($lider->fechaFin || isset($inputs['terminar_cargo'])){
				$this->reglas['fecha_fin'] = 'required|date';
			}

			$validacion = Validador::validar(Input::all(), $this->reglas);

			if($validacion === TRUE){
				DB::beginTransaction();

				if(!isset($inputs['terminar_cargo'])){
					$unidades_responsables = UnidadResponsable::withTrashed()->get();
					$unidades_responsables = $unidades_responsables->lists('descripcion','idArea');
					
					if(!$inputs['id_cargo']){
						throw new \Exception("Error al buscar el cargo a editar", 1);
					}

					$cargo = CatalogoCargo::find($inputs['id_cargo']);
					
					/*if($cargo->idDirectorio != $inputs['responsable']){
						throw new \Exception("El cargo no esta asignado al personal seleccionado", 1);
					}*/

					$cargo->idDirectorio = $inputs['responsable'];
					$cargo->descripcion = $inputs['cargo'];
					$cargo->idArea = $inputs['area'];
					$cargo->telefono = $inputs['telefono'];
					$cargo->extension = $inputs['extension'];
					$cargo->fechaInicio = $inputs['fecha_inicio'];

					if(isset($unidades_responsables[$cargo->idArea])){
						$cargo->nivel = 1;
					}else{
						$cargo->nivel = 0;
					}

					if($lider->fechaFin){
						$cargo->fechaFin = $inputs['fecha_fin'];
					}else{
						if($lider->idArea != $cargo->idArea){
							$cargo_ocupado = CatalogoCargo::where('idArea','=',$cargo->idArea)->whereNull('fechaFin')->first();
							if($cargo_ocupado){
								$cargo_ocupado->fechaFin = $cargo->fechaInicio;
								$cargo_ocupado->save();
							}
						}
						if($lider->idDirectorio != $cargo->idDirectorio){
							$responsable_ocupado = CatalogoCargo::where('idDirectorio','=',$cargo->idDirectorio)->whereNull('fechaFin')->first();
							if($responsable_ocupado){
								$responsable_ocupado->fechaFin = $cargo->fechaInicio;
								$responsable_ocupado->save();
							}
						}
					}
				}else{
					$cargo = CatalogoCargo::find($id);
					$cargo->fechaFin = $inputs['fecha_fin'];
				}

				if($cargo->save()){
					$respuesta['data'] = array('data'=>$cargo);
					$this->reasignarDirectivos($cargo);
					DB::commit();
				}else{				
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'No se pudieron guardar los cambios.','code'=>'S03');
					DB::rollback();
				}
			}else{
				$respuesta['http_status'] = $validacion['http_status'];
				$respuesta['data'] = $validacion['data'];
			}
        } catch (Exception $e) {
			DB::rollback();
        	return Response::json(array('data'=>'Ocurrio un error al guardar la información.','message'=>$e->getMessage(),'line'=>$e->getLine()),500);           
        }
        return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	public function reasignarDirectivos($cargo){
		if($cargo->fechaFin == null && $cargo->nivel == 1){ //Solo si esta activo y es nivel 1 (Directivo)
			$unidades_responsables = UnidadResponsable::withTrashed()->get();
			$unidades_responsables = $unidades_responsables->lists('clave','idArea');

			//Si es director general
			if($unidades_responsables[$cargo->idArea] == '00'){
				//Actualizar proyectos en jefe inmediato
				//Proyecto::update(['idJefeInmediato'=>$cargo->id]);
				DB::table('proyectos')->update(array('idJefeInmediato'=>$cargo->id));
			}else{
				if($unidades_responsables[$cargo->idArea] == '01'){ //Si es director de planeacion
					//Actualizar proyectos en jefe planeacion y coordinador estrategico
					//Proyecto::update(['idJefePlaneacion'=>$cargo->id,'idCoordinadorGrupoEstrategico'=>$cargo->id]);
					DB::table('proyectos')->update(array('idJefePlaneacion'=>$cargo->id,'idCoordinadorGrupoEstrategico'=>$cargo->id));
				}
				
				//Actualizar proyectos en lider del proyecto en base a la unidad responsable
				//Proyecto::where('unidadResponsable','=',$unidades_responsables[$cargo->idArea])->update(['idLiderProyecto'=>$cargo->id]);
				DB::table('proyectos')->where('unidadResponsable','=',$unidades_responsables[$cargo->idArea])->update(array('idLiderProyecto'=>$cargo->id));
			}

			//Actualizar Estrategias en lider del programa
			DB::table('estrategia')->where('claveUnidadResponsable','=',$unidades_responsables[$cargo->idArea])->update(array('idLiderPrograma'=>$cargo->id));
		}
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

			foreach ($ids as $id) {
				$recurso = Sentry::findGroupById($id);
		    	$recurso->delete();
			}
			
			$data = array("data"=>"Se han eliminado los recursos.");
		}catch(\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
    		$http_status = 404;
			$data = array('data' => "Rol no encontrado",'code'=>'U06');	
		}catch(\Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');
		}
		return Response::json($data,$http_status);
	}
}