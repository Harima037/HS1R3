<?php

class LideresProyectoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return parent::loadIndex('ADMIN','CATRESPO');
	}

}