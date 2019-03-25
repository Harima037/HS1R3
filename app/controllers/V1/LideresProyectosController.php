<?php

namespace V1;

use SSA\Utilerias\Validador, SSA\Utilerias\HelperSentry;
use BaseController, Input, Response, DB, Sentry;
use Role, Hash,Directorio,Test;

class LideresProyectosController extends \BaseController {
	private $reglas = array(
		'name' => 'required',
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
				$data = array('resultados'=>$total,"data"=>"No se encontraron roles",'code'=>'W00');
			}			
			
			return Response::json($data,$http_status);
		}	

		$rows = DB::table('groups')->all();
		
		
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
		$respuesta = array();

		try{
			if(Input::get('permissions')){
				$permissions = Input::get('permissions');
			}else{
				$permissions = array();
			}
			$recurso = Sentry::createGroup(array(
				'name'	=> Input::get('name'),
				'permissions' => $permissions
			));

			$respuesta['http_status'] = 200;
			$respuesta['data'] = array("data"=>$recurso->toArray());
		}catch (\Cartalyst\Sentry\Groups\NameRequiredException $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array('data'=>'{"field":"name","error":"Este campo es requerido"}','code'=>'U00');
		}catch (\Cartalyst\Sentry\Groups\GroupExistsException $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array('data'=>'{"field":"name","error":"Este Rol ya existe"}','code'=>'U00');
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

           
            $lider->nombre = $inputs['name'];
            $lider->email = $inputs['email'];

            if($lider->save()){
            	return $inputs;
					$respuesta['data'] = array('data'=>$lider);
			}else{				
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'No se pudieron guardar los cambios.','code'=>'S03');
			}          
            
                        
        } catch (Exception $e) {        	
        	return Response::json(array('data'=>'Ocurrio un error al guardar la información.','message'=>$e->getMessage(),'line'=>$e->getLine()),500);           
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