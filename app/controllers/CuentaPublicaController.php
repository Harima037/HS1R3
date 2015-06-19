<?php
use SSA\Utilerias\Util;

class CuentaPublicaController extends BaseController {
	
	public function index(){
		return parent::loadIndex('REVISION','CUENTPUB');
	}
}