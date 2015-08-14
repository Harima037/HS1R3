<?php
use SSA\Utilerias\Util;

class BitacoraValidacionSeguimientoController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){ $mes_actual = date('n')-1; }
		$datos = array(
			'meses' => array(
							1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
							7=>'Julio',8=>'Agosto', 9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
						),
			'mes_actual' => $mes_actual
		);
		return parent::loadIndex('ADMIN','BITVALID',$datos);
	}
}