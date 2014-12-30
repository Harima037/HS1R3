<?php

class UsuariosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$catalogos = array(
				'sys_roles'=>Sentry::findAllGroups(),
				'unidades_responsables'=>UnidadResponsable::all()
			);
		return parent::loadIndex('ADMIN','USUARIOS',$catalogos);
	}
}