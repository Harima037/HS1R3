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
				'variables'=>SysConfiguracionVariable::obtenerVariables(array('mes-captura','dias-captura','anio-captura','poblacion-total','captura-cierre-fassa')),
				'mes_usuario'=>Sentry::getUser()->mesCaptura
			);
		return parent::loadIndex('ADMIN','CONFSEGMET',$catalogos);
	}
}