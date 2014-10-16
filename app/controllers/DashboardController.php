<?php

class DashboardController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$sys_sis_llave = 'DASHBOARD';
		$datos = array();
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey($sys_sis_llave);
		$uri = $datos['sys_activo']->uri;
		$datos['usuario'] = Sentry::getUser();
		$datos['sys_mod_activo'] = null;
		
		return View::make($uri)->with($datos);
	}

}