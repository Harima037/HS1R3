<?php

class SeguimientoController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexInstitucional(){
		return parent::loadIndex('RENDCUENTA','RENDINST');
	}
	public function indexInversion(){
		return parent::loadIndex('RENDCUENTA','RENDINV');
	}
}