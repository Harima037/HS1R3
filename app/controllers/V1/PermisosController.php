<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use SysGrupoModulo, Hash;

class PermisosController extends \BaseController {
	private $reglas = array();

  	public function index()
	{
		//
		$http_status = 200;
		$data = array();
		$parametros = Input::all();

		$permisos = SysGrupoModulo::getPermisos();
		$permisos_usuario = array();
		$permisos_array = array();

		if(count($permisos) == 0){
			$http_status = 404;
			$data = array('resultados'=>0,"data"=>"No existen datos de Modulos capturados",'code'=>'W00');
			return Response::json($data,$http_status);
		}

		$permisos_roles = array();
		$permisos_usuario = array();

		if(isset($parametros['user_id'])){
			$permisos_usuario = Sentry::findUserById($parametros['user_id'])->getPermissions();

			$modulos = array();

			foreach ($permisos_usuario as $key =>$value) {
				$key_data = explode('.', $key);
				if(isset($key_data[2]))
					$modulos[$key_data[0]][$key_data[1]][$key_data[2]] = $value;
			}
			$permisos_usuario = $modulos;
		}

		if(isset($parametros['roles'])){
			if(count($parametros['roles']) > 0 && $parametros['roles'] !== ''){

				foreach ($parametros['roles'] as $rol) {
					$permisos_roles = array_merge($permisos_roles,Sentry::findGroupById($rol)->getPermissions());
				}


				$modulos = array();

				foreach ($permisos_roles as $key =>$value) {
					$key_data = explode('.', $key);
					if(isset($key_data[2]))
						$modulos[$key_data[0]][$key_data[1]][$key_data[2]] = $value;
				}
				$permisos_roles = $modulos;
			}
		}
		

		$modulos = array();

		foreach ($permisos as $key) {
			$key_data = explode('.', $key);
			if(isset($key_data[2]))
				$modulos[$key_data[0]][$key_data[1]][$key_data[2]] = 0;
		}
	
		
		$data = array("data"=>array('modulos'=>$modulos,'user'=>$permisos_usuario,'rol'=>$permisos_roles));
		
		return Response::json($data,$http_status);
	}

}