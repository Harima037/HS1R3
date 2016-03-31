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
			if($mes_actual == 0){
				$mes_actual = 12;
			}
		}
		$datos['ejercicio'] = Util::obtenerAnioCaptura();
		$datos['trim_actual'] = Util::obtenerTrimestre();
		return parent::loadIndex('REPORTES','REPSEGPROG',$datos);
	}
}