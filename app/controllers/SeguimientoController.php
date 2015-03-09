<?php

class SeguimientoController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexInstitucional(){
		$datos = array(
			'meses' => array(
					'1' => array('Enero','Febrero','Marzo'),
					'2' => array('Abril','Mayo','Junio'),
					'3' => array('Julio','Agosto','Septiembre'),
					'4' => array('Octubre','Noviembre','Dicembre')
				)
		);
		return parent::loadIndex('RENDCUENTA','RENDINST',$datos);
	}
	public function indexInversion(){
		return parent::loadIndex('RENDCUENTA','RENDINV');
	}
}