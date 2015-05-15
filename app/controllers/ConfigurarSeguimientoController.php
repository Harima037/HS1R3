<?php

class ConfigurarSeguimientoController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$catalogos = array(
				'variables'=>SysConfiguracionVariable::obtenerVariables(array('mes-captura','dias-captura'))
			);
		return parent::loadIndex('ADMIN','CONFSEGMET',$catalogos);
	}
}