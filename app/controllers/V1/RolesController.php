<?php

namespace V1;

use SSA\Utilerias\Validador, SSA\Utilerias\HelperSentry;
use BaseController, Input, Response, DB, Sentry;
use Role, Hash;

class RolesController extends \BaseController {
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
			
			$rows = DB::table("sentryGroups");	
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('name','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{
				$total = $rows->count();						
			}

			$rows = $rows->select('id','name','permissions','modificadoAl')
								->orderBy('id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			
			$data_array = array();

			foreach ($rows as $row) {
				$permisos = json_decode($row->permissions);
				$row->permissions = '';
				$permisos_array = array();
				if(count($permisos) > 0){
					foreach ($permisos as $key => $value) {
						$grupo = explode('.', $key)[0];
						if(!isset($permisos_array[$grupo])){
							$permisos_array[$grupo] = $grupo;
						}
					}
					foreach ($permisos_array as $value) {
						$row->permissions .= '<label class="label label-info">'.$value.'</label> ';
					}
					$row->modificadoAl = (string)$row->modificadoAl;
				}

				$group = Sentry::findGroupById($row->id);
				$users = Sentry::findAllUsersInGroup($group);

				$data_array[] = array(
						'id'=>$row->id,
						'name'=>$row->name,
						'permissions'=>$row->permissions,
						'users'=>count($users),
						'modificadoAl'=>$row->modificadoAl
					);
			}

			$data = array('resultados'=>$total,'data'=>$data_array);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No se encontraron roles",'code'=>'W00');
			}			
			
			return Response::json($data,$http_status);
		}	

		$rows = DB::table('groups')->all();
		
		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No se encontraron roles",'code'=>'W00');
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

		try{
			$recurso = Sentry::findGroupById($id);
			$permisos_array = $recurso->getPermissions();


			$permisos = array();

			foreach ($permisos_array as $key =>$value) {
				$key_data = explode('.', $key);
				if(isset($key_data[2]))
					$permisos[$key_data[0]][$key_data[1]][$key_data[2]] = $value;
			}
			$usuarios = Sentry::findAllUsersInGroup($recurso);
			$recurso = $recurso->toArray();
			$recurso['permisos_array'] = $permisos;
			$recurso['usuarios_array'] = $usuarios->toArray();
			$data = array("data"=>$recurso);
		}catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
			$http_status = 404;
			$data = array("data"=>"Rol no encontrado.",'code'=>'U06');
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
		//
		$respuesta = array();

		try{
			$recurso = Sentry::findGroupById($id);

			$recurso->name = Input::get('name');

			if(Input::get('permissions')){
				$permissions = Input::get('permissions');
			}else{
				$permissions = array();
			}

			foreach ($recurso->getPermissions() as $key => $value) {
				if(!array_key_exists($key, $permissions)){
					$permissions[$key] = 0;
				}
			}

			$recurso->permissions = $permissions;

			if($recurso->save()){
				$respuesta['http_status'] = 200;
				$respuesta['data'] = array("data"=>$recurso->toArray());
			}else{
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>"El grupo no pudo ser actualizado.",'code'=>'S03');
			}

		}catch (\Cartalyst\Sentry\Groups\NameRequiredException $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array('data'=>'{"field":"name","error":"Este campo es requerido"}','code'=>'U00');
		}catch (\Cartalyst\Sentry\Groups\GroupExistsException $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>"El grupo ya existe.",'code'=>'S01');
		}catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No existe el recurso que quiere actualizar.",'code'=>'U06');
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