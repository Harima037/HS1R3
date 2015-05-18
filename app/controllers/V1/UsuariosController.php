<?php
/* 
*	PLATAFORMA BASE SSA
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2014, Instituto de Salud.
*	@author 			Mario Cabrera
*	@package 			plataforma-base
*	@version 			1.0 
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Validador;
use Illuminate\Database\QueryException, \Exception;
use BaseController, Input, Response, DB, Sentry;
use User, Hash;

class UsuariosController extends \BaseController {
	private $reglas = array(
			'nombres' => 'required',
			'apellido-paterno' => 'required',
			'apellido-materno' => 'required',
			'username' => 'required',
			'rol' => 'required',
			'email' => 'required|email',
			'password'=>'required',
			'password_confirm' =>'required|same:password'
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
			try{
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
				$rows = Sentry::getUserProvider()->createModel();

				if(!Sentry::getUser()->isSuperUser()){
					$rows = $rows->where(function($query){
						$query->where('permissions','!=','{"superuser":1}')
								->orWhereNull('permissions');
					});
				}

				if(Sentry::getUser()->idDepartamento){
					$rows = $rows->where(function($query){
								$query->where('idDepartamento','=',Sentry::getUser()->idDepartamento)
									  ->orWhere('idDepartamento','=',3);
							});
				}

				if(isset($parametros['buscar'])){
					if($parametros['buscar']){
						$rows = $rows->where(function($query) use ($parametros){
									$query->where('username','like','%'.$parametros['buscar'].'%')
										->orWhere(DB::raw('CONCAT_WS(" ",nombres,apellidoPaterno,apellidoMaterno)'),'like','%'.$parametros['buscar'].'%');
								});
					}
					$total = $rows->count();
				}else{
					$total = $rows->count();
				}

				$rows = $rows->select('id','username',DB::raw('CONCAT_WS(" ",nombres,apellidoPaterno,apellidoMaterno) AS name'),'activated','modificadoAl')
									->orderBy('id', 'desc')
									->skip(($parametros['pagina']-1)*10)->take(10)
									->get();

				$data_array = array();

				foreach ($rows as $row) {
					$user_throttle = Sentry::findThrottlerByUserId($row->id);
					$user_roles = Sentry::findUserById($row->id)->getGroups();

					$data_row = array(
						'id'=>$row->id,
						'username'=>$row->username,
						'name'=>$row->name,
						'status'=>$row->activated,
						'roles'=>'',						
						'modificadoAl'=> (string)$row->modificadoAl
					);

					if($user_throttle->isBanned()){
						$data_row['status'] = '<span class="label label-danger"><i class="fa fa-ban-circle"></i> Bloqueado</span>';
					}elseif($user_throttle->isSuspended()){
						$data_row['status'] = '<span class="label label-warning"><i class="fa fa-time"></i> Suspendido</span>';
					}elseif($row->activated == 1){
						$data_row['status'] = '<span class="label label-success"><i class="fa fa-ok"></i> Activo</span>';
					}else{
						$data_row['status'] = '<span class="label label-default"><i class="fa fa-minus"></i> No Activado</span>';
					}

					//$data_row['roles'] = '<div class="btn-group">';
					foreach ($user_roles as $item) {
						$data_row['roles'] .= '<span class="label label-info">'.$item->name.'</span> ';
					}
					//$data_row['roles'] .= '</div>';

					$data_array[] = $data_row;


				}

				$data = array('resultados'=>$total,'data'=>$data_array);

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
				$respuesta['data'] = $data;
				$respuesta['http_status'] = $http_status;
				
				
			}
			catch(\Illuminate\Database\QueryException $e){
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S01');
				
			}
			return Response::json($respuesta['data'],$respuesta['http_status']);
			
		}	

		$rows = Sentry::findAllUsers();
		
		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}else{
			$data = array("data"=>$rows);
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
			$recurso = Sentry::findUserById($id);
			if($recurso){
				$user_roles = array();
				$roles = $recurso->getGroups();
					
				if(count($roles) > 0){
					foreach ($roles as $rol) {
						$user_roles[] = $rol->id;
					}
				}
				$data = array("data"=>$recurso->toArray());
				$data['data']['roles'] = $user_roles;

				$permisos = $data['data']['permissions'];
			
				if(!isset($permisos['superuser'])){
					$modulos = array();

					foreach ($permisos as $key =>$value) {
						$key_data = explode('.', $key);
						if(isset($key_data[2]))
							$modulos[$key_data[0]][$key_data[1]][$key_data[2]] = $value;
					}
					$data['data']['permissions'] = $modulos;
				}
			}else{
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No se ha podido encontrar al usuario.",'code'=>'S01');
			}
		}catch(\Cartalyst\Sentry\Users\UserNotFoundException $e){
    		$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"El usuario no existe o ha sido eliminado.",'code'=>'U06');
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>'Error al obtener los datos','code'=>'S03');
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
		$respuesta = Validador::validar(Input::all(), $this->reglas);
		
		if($respuesta === true){
			try{

				$respuesta = array();
				$permissions = null;

				$usuario = Sentry::getUserProvider()->createModel()->where('email','=',Input::get('email'))->get();
				if(count($usuario) > 0){
					$respuesta['http_status'] = 500;
			    	$respuesta['data'] = array("data"=>'{"field":"email","error":"Este correo electronico ya esta registrado"}','code'=>'U00');
			    	return Response::json($respuesta['data'],$respuesta['http_status']);
				}
				
				if(Input::get('permissions')){
		    		$permissions = Input::get('permissions');
		    	}
		    	
		    	if(Input::get('unidad')){
		    		$unidades = implode('|',Input::get('unidad'));
		    	}else{
		    		$unidades = NULL;
		    	}

		    	$new_user = array(
		    		'username' => Input::get('username'),
					'nombres' => Input::get('nombres'),
					'apellidoPaterno' => Input::get('apellido-paterno'),
					'apellidoMaterno' => Input::get('apellido-materno'),
					'claveUnidad' => $unidades,
					'idDepartamento' => Input::get('departamento'),
					'cargo' => Input::get('cargo'),
					'telefono' => Input::get('telefono'),
					'email' => Input::get('email'),
					'password' => Input::get('password'),
					'activated' => 1
				);

				if($permissions){
					$new_user['permissions'] = $permissions;
				}

				$recurso = Sentry::createUser($new_user);

				$role_array = Input::get('rol');
				if(count($role_array) > 0 && $role_array !== ''){
					foreach ($role_array as $rol) {
						$user_group = Sentry::findGroupById($rol);
						$recurso->addGroup($user_group);
					}
				}
				
				$respuesta['http_status'] = 200;
				$respuesta['data'] = array("data"=>$recurso->toArray());
			}catch (\Cartalyst\Sentry\Users\LoginRequiredException $e){
			    $respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>'{"field":"username","error":"Este campo es requerido"}','code'=>'U00');
			}catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e){
			    $respuesta['http_status'] = 500;
			    $respuesta['data'] = array("data"=>'{"field":"password","error":"Este campo es requerido"}','code'=>'U00');
			}catch (\Cartalyst\Sentry\Users\UserExistsException $e){
			    $respuesta['http_status'] = 500;
			    $respuesta['data'] = array("data"=>'{"field":"username","error":"Este nombre de usuario ya existe"}','code'=>'U00');
			}catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
			    $respuesta['http_status'] = 404;
			    $respuesta['data'] = array("data"=>'El rol asignado no existe','code'=>'U06');
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
		$respuesta = array();

		try{

			if(Input::get('user-action-ban')){
				$ids = Input::get('rows');

				foreach ($ids as $id) {
					$user_throttle = Sentry::findThrottlerByUserId($id);
					if($user_throttle->isBanned()){
						$user_throttle->unBan();
					}else{
						$user_throttle->ban();
					}
				}

				$respuesta['http_status'] = 200;
				$respuesta['data'] = array("data"=>'Se ha actualizado el estatus a todos los usuarios.');

				return Response::json($respuesta['data'],$respuesta['http_status']);
			}
		
			$recurso = Sentry::findUserById($id);

			if(Input::get('password')!= ""){
				$recurso->password 		= Input::get('password');
			}else{
				$this->reglas['password'] ='';
				$this->reglas['password_confirm'] ='';
			}
			
			if(isset($recurso->permissions['superuser'])){
				$this->reglas['rol'] = '';
			}

			$valid_result = Validador::validar(Input::all(), $this->reglas);
			if($valid_result === true){
				if(Input::get('unidad')){
		    		$unidades = implode('|',Input::get('unidad'));
		    	}else{
		    		$unidades = NULL;
		    	}

				$recurso->nombres	= Input::get('nombres');
				$recurso->apellidoPaterno		= Input::get('apellido-paterno');
				$recurso->apellidoMaterno		= Input::get('apellido-materno');
				$recurso->claveUnidad			= $unidades;
				$recurso->idDepartamento		= Input::get('departamento');
				$recurso->cargo					= (Input::get('cargo'))?Input::get('cargo'):NULL;
				$recurso->telefono				= (Input::get('telefono'))?Input::get('telefono'):NULL;

				if($recurso->email != Input::get('email') || $recurso->username != Input::get('username')){
					$usuario = Sentry::getUserProvider()->createModel()
								->where('id','<>',$id)
								->where(function($query){
									$query->where('email','=',Input::get('email'))
										  ->orWhere('username','=',Input::get('username'));
								})->get();

					if(count($usuario) > 0){
						$respuesta['http_status'] = 500;
						$data = array();

						if(Input::get('email') == $usuario[0]->email){
							$data[] = '{"field":"email","error":"Este correo electronico ya esta registrado"}';
						}

						if(Input::get('username') == $usuario[0]->username){
							$data[] = '{"field":"username","error":"Este nombre de usuario ya esta registrado"}';
						}
						
				    	$respuesta['data'] = array("data"=>$data,'code'=>'U00');
				    	return Response::json($respuesta['data'],$respuesta['http_status']);
					}

					$recurso->email 	= Input::get('email');
					$recurso->username 	= Input::get('username');
				}
				if(!isset($recurso->permissions['superuser'])){
					$roles = Input::get('rol');
					$grupos = $recurso->getGroups();
					if(count($grupos)>0){
						foreach ($grupos as $grupo) {
							if(array_search($grupo->id, $roles) === FALSE){
								$recurso->removeGroup($grupo);
							}
						}
					}
					if(count($roles) > 0 && $roles !== ''){
						foreach ($roles as $rol) {
							$recurso->addGroup(Sentry::findGroupById($rol));
						}	
					}
					
					if(Input::get('permissions')){
				    	$user_permission = Input::get('permissions');
				    }else{
				    	$user_permission = array();
				    }
				    
				    foreach ($recurso->permissions as $key => $value) {
				    	if(!array_key_exists($key, $user_permission)){
				    		$user_permission[$key] = 0;
				    	}
				    }

				    $recurso->permissions = $user_permission;
				}

				if($recurso->save()){
					$respuesta['http_status'] = 200;
					$respuesta['data'] = array("data"=>$recurso->toArray());
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'No se pudieron guardar los cambios.','code'=>'S03');
				}
			}else{
					$respuesta['http_status'] = $valid_result['http_status'];
					$respuesta['data'] = $valid_result['data'];
			}
		}catch (\Cartalyst\Sentry\Users\UserExistsException $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>'{"field":"username","error":"Este nombre de usuario ya existe"}','code'=>'U00');
		}catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
    		$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No se encuentra el usuario que se quiere editar.",'code'=>'U06');
		}catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
    		$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"El rol que se quiere asignar no existe.",'code'=>'U06');
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
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
				if($id == Sentry::getUser()->id){
					return Response::json(array('data'=>'No se puede eliminar el usuario.','code'=>'S03'),500);
				}

				$user = Sentry::findUserById($id);
				$user->delete();
			}
			
			$data = array("data"=>"Se han eliminado los recursos.");
		}catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
    		$http_status = 404;	
			$data = array('data' => "Usuario no encontrado",'code'=>'U06');	
		}catch(\Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');	
		}

		return Response::json($data,$http_status);
	}

}