<?php
use SSA\Utilerias\Util;

class RevisionRendicionFassaController extends BaseController {
	
	public function index(){
		$datos = array();
		$datos['mes_avance'] = Util::obtenerMesActual();
		$datos['trimestre_avance'] = Util::obtenerTrimestre();

		$mes_del_trimestre = Util::obtenerMesTrimestre();
		if($mes_del_trimestre == 3){
			$datos['trimestre_activo'] = TRUE;
		}else{
			$datos['trimestre_activo'] = FALSE;
		}

		return parent::loadIndex('RENDCUENTA','RENDFASSA',$datos);
	}
}