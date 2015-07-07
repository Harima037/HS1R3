<?php
use SSA\Utilerias\Util;

class CargarArchivoController extends BaseController {
	
	public function cargarEP01(){
		return parent::loadIndex('CARGAR','REP-EP01');
	}

	public function cargarRegionalizado(){
		return parent::loadIndex('CARGAR','REP-REGI');
	}
}