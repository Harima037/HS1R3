<?php
use SSA\Utilerias\Util;

class CuentaPublicaController extends BaseController {
	
	public function index(){
		$variables = SysConfiguracionVariable::obtenerVariables(array('clave-institucional','mision','vision'))->lists('valor','variable');
		return parent::loadIndex('REVISION','CUENTPUB',array('datos'=>$variables));
	}
}