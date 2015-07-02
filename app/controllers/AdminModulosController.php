<?php

class AdminModulosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//$sys_sis_llave = 'CONFIGURAR';
		$datos = array();
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_permisos'] = SysPermiso::all();
		//$datos['sys_activo'] = SysSistema::findByKey($sys_sis_llave);
		$datos['sys_activo'] = null;
		//$uri = $datos['sys_activo']->uri;
		$datos['usuario'] = Sentry::getUser();
		$datos['sys_mod_activo'] = null;
		
		//return View::make($uri)->with($datos);
		return View::make('root.modulos')->with($datos);
	}

}