<?php

class AdminProyectosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$catalogos = array(
				'clasificacion_proyectos'=>ClasificacionProyecto::all(),
				'estatus_proyectos'=>EstatusProyecto::all()
			);
		return parent::loadIndex('ADMIN','ADMINPROYS',$catalogos);
	}
}