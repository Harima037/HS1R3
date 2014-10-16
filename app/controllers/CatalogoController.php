<?php

class CatalogoController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return parent::loadIndex('DEMO','CATALOGO');
	}

}