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
use BaseController, Input, Response, DB, Sentry,SentryUser;
use User, Hash, UsuarioProyecto, Proyecto, Programa, IndicadorFASSA, Estrategia;

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

				/*if(Sentry::getUser()->idDepartamento){
					$rows = $rows->where(function($query){
								$query->where('idDepartamento','=',Sentry::getUser()->idDepartamento)
									  ->orWhere('idDepartamento','=',3)
									  ->orWhereNull('idDepartamento');
							});
				}*/

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

				$recurso->load('caratulas');
				$recurso->load('proyectos');
				$recurso->load('programas');
				$recurso->load('indicadores');
				$recurso->load('estrategias');
				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;

				/*if($recurso->proyectosAsignados){
					$ejercicio = date('Y');
					if($recurso->proyectosAsignados->ejercicio == $ejercicio){
						$id_proyectos = explode('|',$recurso->proyectosAsignados->proyectos);
						$proyectos = Proyecto::contenidoSuggester()->whereIn('proyectos.id',$id_proyectos)->get();
						$recurso['proyectos'] = $proyectos;
					}
				}*/

				$data = array("data"=>$recurso);
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
				$http_status = 404;
				$data = array("data"=>"No se ha podido encontrar al usuario.",'code'=>'S01');
			}
		}catch(\Cartalyst\Sentry\Users\UserNotFoundException $e){
    		$http_status = 404;
			$data = array("data"=>"El usuario no existe o ha sido eliminado.",'code'=>'U06');
		}catch(\Exception $e){
			$http_status = 500;
			$data = array("data"=>'Error al obtener los datos','code'=>'S03','ex'=>$e->getMessage());
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
					'claveJurisdiccion' => (Input::get('jurisdiccion'))?Input::get('jurisdiccion'):NULL,
					'idDepartamento' => (Input::get('departamento'))?Input::get('departamento'):NULL,
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

				if(count(Input::get('caratulas'))){
					$caratulas = Input::get('caratulas');
					Proyecto::whereIn('id',$caratulas)
							->whereNull('idUsuarioCaptura')
							->update(array('idUsuarioCaptura'=>$recurso->id));
				}
				
				if(count(Input::get('proyectos'))){
					$proyectos = Input::get('proyectos');
					if($recurso->idDepartamento == 2){
						Proyecto::whereIn('id',$proyectos)
								->whereNull('idUsuarioValidacionSeg')
								->update(array('idUsuarioValidacionSeg'=>$recurso->id));
					}else{
						Proyecto::whereIn('id',$proyectos)
								->whereNull('idUsuarioRendCuenta')
								->update(array('idUsuarioRendCuenta'=>$recurso->id));
					}
				}
				
				if(count(Input::get('programas'))){
					$programas = Input::get('programas');
					if($recurso->idDepartamento == 2){
						Programa::whereIn('id',$programas)
								->whereNull('idUsuarioValidacionSeg')
								->update(array('idUsuarioValidacionSeg'=>$recurso->id));
					}else{
						Programa::whereIn('id',$programas)
								->whereNull('idUsuarioRendCuenta')
								->update(array('idUsuarioRendCuenta'=>$recurso->id));
					}
				}
				
				if(count(Input::get('indicadores'))){
					$indicadores = Input::get('indicadores');
					if($recurso->idDepartamento == 2){
						IndicadorFASSA::whereIn('id',$indicadores)
								->whereNull('idUsuarioValidacionSeg')
								->update(array('idUsuarioValidacionSeg'=>$recurso->id));
					}else{
						IndicadorFASSA::whereIn('id',$indicadores)
								->whereNull('idUsuarioRendCuenta')
								->update(array('idUsuarioRendCuenta'=>$recurso->id));
					}
				}

				if(count(Input::get('estrategias'))){
					$estrategias = Input::get('estrategias');
					if($recurso->idDepartamento == 2){
						Estrategia::whereIn('id',$estrategias)
								->whereNull('idUsuarioValidacionSeg')
								->update(array('idUsuarioValidacionSeg'=>$recurso->id));
					}else{
						Estrategia::whereIn('id',$estrategias)
								->whereNull('idUsuarioRendCuenta')
								->update(array('idUsuarioRendCuenta'=>$recurso->id));
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
				$recurso->claveJurisdiccion		= (Input::get('jurisdiccion'))?Input::get('jurisdiccion'):NULL;
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

				if($recurso->idDepartamento != Input::get('departamento')){
					if($recurso->idDepartamento == 2){
						Proyecto::where('idUsuarioValidacionSeg','=',$recurso->id)
							->update(array('idUsuarioValidacionSeg'=>NULL));
					}else{
						Proyecto::where('idUsuarioRendCuenta','=',$recurso->id)
							->update(array('idUsuarioRendCuenta'=>NULL));
					}
				}

				$recurso->idDepartamento = (Input::get('departamento'))?Input::get('departamento'):NULL;

				if(Input::get('filtrar-caratulas')){
					$recurso->filtrarCaratulas = 1;
				}else{
					$recurso->filtrarCaratulas = NULL;
				}

				if(count(Input::get('proyectos'))){
					$recurso->filtrarProyectos = 1;
				}else{
					$recurso->filtrarProyectos = NULL;
				}
				
				if(count(Input::get('programas'))){
					$recurso->filtrarProgramas = 1;
				}else{
					$recurso->filtrarProgramas = NULL;
				}
				
				if(count(Input::get('indicadores'))){
					$recurso->filtrarIndicadores = 1;
				}else{
					$recurso->filtrarIndicadores = NULL;
				}

				if(count(Input::get('estrategias'))){
					$recurso->filtrarEstrategias = 1;
				}else{
					$recurso->filtrarEstrategias = NULL;
				}

				$caratulas = $recurso->caratulas()->lists('id');
				$proyectos = $recurso->proyectos()->lists('id');
				$programas = $recurso->programas()->lists('id');
				$indicadores = $recurso->indicadores()->lists('id');
				$estrategias = $recurso->estrategias()->lists('id');
				
				if(Input::get('caratulas')){ $nuevas_caratulas = Input::get('caratulas'); }
				else{ $nuevas_caratulas = array(); }
				$array_caratulas['nuevas'] = array_diff($nuevas_caratulas, $caratulas);
				$array_caratulas['borrar'] = array_diff($caratulas, $nuevas_caratulas);
				
				if(Input::get('proyectos')){ $nuevos_proyectos = Input::get('proyectos'); }
				else{ $nuevos_proyectos = array(); }
				$array_proyectos['nuevos'] = array_diff($nuevos_proyectos, $proyectos);
				$array_proyectos['borrar'] = array_diff($proyectos, $nuevos_proyectos);
				
				if(Input::get('programas')){ $nuevos_programas = Input::get('programas'); }
				else{ $nuevos_programas = array(); }
				$array_programas['nuevos'] = array_diff($nuevos_programas, $programas);
				$array_programas['borrar'] = array_diff($programas, $nuevos_programas);
				
				if(Input::get('indicadores')){ $nuevos_indicadores = Input::get('indicadores'); }
				else{ $nuevos_indicadores = array(); }
				$array_indicadores['nuevos'] = array_diff($nuevos_indicadores, $indicadores);
				$array_indicadores['borrar'] = array_diff($indicadores, $nuevos_indicadores);

				if(Input::get('estrategias')){ $nuevas_estrategias = Input::get('estrategias'); }
				else{ $nuevas_estrategias = array(); }
				$array_estrategias['nuevos'] = array_diff($nuevas_estrategias, $estrategias);
				$array_estrategias['borrar'] = array_diff($estrategias, $nuevas_estrategias);
				
				/*$recurso->load('proyectosAsignados');
				$proyectos_asignados = NULL;
				if($recurso->proyectosAsignados){
					$proyectos_asignados = $recurso->proyectosAsignados;
					if(count(Input::get('proyectos'))){
						$proyectos_asignados->proyectos = implode('|',Input::get('proyectos'));
					}else{
						$proyectos_asignados->proyectos = NULL;
					}
				}else{
					if(count(Input::get('proyectos'))){
						$proyectos_asignados = new UsuarioProyecto;
						$proyectos_asignados->ejercicio = date('Y');
						$proyectos_asignados->proyectos = implode('|',Input::get('proyectos'));
					}
				}
				if($proyectos_asignados){
					$recurso->proyectosAsignados()->save($proyectos_asignados);
				}*/

				$respuesta = DB::transaction(function() use ($recurso,$array_proyectos,$array_caratulas,$array_programas,$array_indicadores,$array_estrategias){
					$respuesta_transac = array();
					if($recurso->save()){
						if(count($array_caratulas['nuevas'])){
							Proyecto::whereIn('id',$array_caratulas['nuevas'])
									->whereNull('idUsuarioCaptura')
									->update(array('idUsuarioCaptura'=>$recurso->id));
						}
						if(count($array_caratulas['borrar'])){
							Proyecto::whereIn('id',$array_caratulas['borrar'])
									->where('idUsuarioCaptura','=',$recurso->id)
									->update(array('idUsuarioCaptura'=>NULL));
						}
						
						if(count($array_proyectos['nuevos'])){
							if($recurso->idDepartamento == 2){
								Proyecto::whereIn('id',$array_proyectos['nuevos'])
										->whereNull('idUsuarioValidacionSeg')
										->update(array('idUsuarioValidacionSeg'=>$recurso->id));
							}else{
								Proyecto::whereIn('id',$array_proyectos['nuevos'])
										->whereNull('idUsuarioRendCuenta')
										->update(array('idUsuarioRendCuenta'=>$recurso->id));
							}
						}
						if(count($array_proyectos['borrar'])){
							if($recurso->idDepartamento == 2){
								Proyecto::whereIn('id',$array_proyectos['borrar'])
										->where('idUsuarioValidacionSeg','=',$recurso->id)
										->update(array('idUsuarioValidacionSeg'=>NULL));
							}else{
								Proyecto::whereIn('id',$array_proyectos['borrar'])
										->where('idUsuarioRendCuenta','=',$recurso->id)
										->update(array('idUsuarioRendCuenta'=>NULL));
							}
						}
						
						if(count($array_programas['nuevos'])){
							if($recurso->idDepartamento == 2){
								Programa::whereIn('id',$array_programas['nuevos'])
										->whereNull('idUsuarioValidacionSeg')
										->update(array('idUsuarioValidacionSeg'=>$recurso->id));
							}else{
								Programa::whereIn('id',$array_programas['nuevos'])
										->whereNull('idUsuarioRendCuenta')
										->update(array('idUsuarioRendCuenta'=>$recurso->id));
							}
						}
						if(count($array_programas['borrar'])){
							if($recurso->idDepartamento == 2){
								Programa::whereIn('id',$array_programas['borrar'])
										->where('idUsuarioValidacionSeg','=',$recurso->id)
										->update(array('idUsuarioValidacionSeg'=>NULL));
							}else{
								Programa::whereIn('id',$array_programas['borrar'])
										->where('idUsuarioRendCuenta','=',$recurso->id)
										->update(array('idUsuarioRendCuenta'=>NULL));
							}
						}
						
						if(count($array_indicadores['nuevos'])){
							if($recurso->idDepartamento == 2){
								IndicadorFASSA::whereIn('id',$array_indicadores['nuevos'])
										->whereNull('idUsuarioValidacionSeg')
										->update(array('idUsuarioValidacionSeg'=>$recurso->id));
							}else{
								IndicadorFASSA::whereIn('id',$array_indicadores['nuevos'])
										->whereNull('idUsuarioRendCuenta')
										->update(array('idUsuarioRendCuenta'=>$recurso->id));
							}
						}
						if(count($array_indicadores['borrar'])){
							if($recurso->idDepartamento == 2){
								IndicadorFASSA::whereIn('id',$array_indicadores['borrar'])
										->where('idUsuarioValidacionSeg','=',$recurso->id)
										->update(array('idUsuarioValidacionSeg'=>NULL));
							}else{
								IndicadorFASSA::whereIn('id',$array_indicadores['borrar'])
										->where('idUsuarioRendCuenta','=',$recurso->id)
										->update(array('idUsuarioRendCuenta'=>NULL));
							}
						}

						if(count($array_estrategias['nuevos'])){
							if($recurso->idDepartamento == 2){
								Estrategia::whereIn('id',$array_estrategias['nuevos'])
										->whereNull('idUsuarioValidacionSeg')
										->update(array('idUsuarioValidacionSeg'=>$recurso->id));
							}else{
								Estrategia::whereIn('id',$array_estrategias['nuevos'])
										->whereNull('idUsuarioRendCuenta')
										->update(array('idUsuarioRendCuenta'=>$recurso->id));
							}
						}
						if(count($array_estrategias['borrar'])){
							if($recurso->idDepartamento == 2){
								Estrategia::whereIn('id',$array_estrategias['borrar'])
										->where('idUsuarioValidacionSeg','=',$recurso->id)
										->update(array('idUsuarioValidacionSeg'=>NULL));
							}else{
								Estrategia::whereIn('id',$array_estrategias['borrar'])
										->where('idUsuarioRendCuenta','=',$recurso->id)
										->update(array('idUsuarioRendCuenta'=>NULL));
							}
						}
						
						$respuesta_transac['http_status'] = 200;
						$respuesta_transac['data'] = array("data"=>$recurso->toArray());
					}else{
						$respuesta_transac['http_status'] = 500;
						$respuesta_transac['data'] = array("data"=>'No se pudieron guardar los cambios.','code'=>'S03');
					}
					return $respuesta_transac;
				});
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
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03','line'=>$e->getLine());
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

			$respuesta = DB::transaction(function()use($ids){
				$idUsuario = Sentry::getUser()->id;

				$ids_borrar = array();
				foreach ($ids as $id) {
					if($id != $idUsuario){
						$ids_borrar[] = $id;
					}
				}

				Proyecto::whereIn('idUsuarioValidacionSeg',$ids_borrar)->update(array('idUsuarioValidacionSeg'=>NULL));
				Proyecto::whereIn('idUsuarioRendCuenta',$ids_borrar)->update(array('idUsuarioRendCuenta'=>NULL));
				Proyecto::whereIn('idUsuarioCaptura',$ids_borrar)->update(array('idUsuarioCaptura'=>NULL));
				
				IndicadorFASSA::whereIn('idUsuarioValidacionSeg',$ids_borrar)->update(array('idUsuarioValidacionSeg'=>NULL));
				IndicadorFASSA::whereIn('idUsuarioRendCuenta',$ids_borrar)->update(array('idUsuarioRendCuenta'=>NULL));
				
				Programa::whereIn('idUsuarioValidacionSeg',$ids_borrar)->update(array('idUsuarioValidacionSeg'=>NULL));
				Programa::whereIn('idUsuarioRendCuenta',$ids_borrar)->update(array('idUsuarioRendCuenta'=>NULL));

				Estrategia::whereIn('idUsuarioValidacionSeg',$ids_borrar)->update(array('idUsuarioValidacionSeg'=>NULL));
				Estrategia::whereIn('idUsuarioRendCuenta',$ids_borrar)->update(array('idUsuarioRendCuenta'=>NULL));

				//SentryUser::whereIn('id',$ids_borrar)->update(array('email'=>'concat_ws("-",email,"borrado@borrado.com")'));
				if(SentryUser::whereIn('id',$ids_borrar)->delete()){
					return array('data'=>array("data"=>"Se han eliminado los recursos."),'http_status'=>200);
				}else{
					return array('data'=>array("data"=>"No Se han eliminado los recursos.",'code'=>'S01'),'http_status'=>500);
				}
			});
			$data = $respuesta['data'];
			$http_status = $respuesta['http_status'];
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