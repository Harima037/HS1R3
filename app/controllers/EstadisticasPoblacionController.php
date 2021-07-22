<?php

class EstadisticasPoblacionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$catalogos = array(
				"ejercicio"=> \Date("Y")
			);
		return parent::loadIndex('ADMIN','ARCESTPOB',$catalogos);
	}
}