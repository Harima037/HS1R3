<?php
use SSA\Utilerias\Util;

class ReporteSeguimientoProgramasController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n')-1;
		}
		$datos['trim_actual'] = Util::obtenerTrimestre();
		return parent::loadIndex('REPORTES','REPSEGPROG',$datos);
	}
}