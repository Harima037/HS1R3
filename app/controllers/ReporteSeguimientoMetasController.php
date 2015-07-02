<?php
use SSA\Utilerias\Util;

class ReporteSeguimientoMetasController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexInstitucional(){
		$datos = array(
			'meses' => array(
					array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'ENE'),
					array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'FEB'),
					array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'MAR'),
					array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'ABR'),
					array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'MAY'),
					array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'JUN'),
					array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'JUL'),
					array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'AGO'),
					array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'SEP'),
					array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'OCT'),
					array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'NOV'),
					array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'DIC')
				)
		);
		$datos['mes_avance'] = Util::obtenerMesActual();
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n')-1;
		}
		$datos['mes_actual'] = $mes_actual;
		return parent::loadIndex('REPORTES','REPSEGINST',$datos);
	}
	
	public function indexInversion(){
		$datos = array(
			'meses' => array(
					array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'ENE'),
					array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'FEB'),
					array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'MAR'),
					array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'ABR'),
					array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'MAY'),
					array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'JUN'),
					array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'JUL'),
					array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'AGO'),
					array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'SEP'),
					array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'OCT'),
					array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'NOV'),
					array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'DIC')
				)
		);
		$datos['mes_avance'] = Util::obtenerMesActual();
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n')-1;
		}
		$datos['mes_actual'] = $mes_actual;
		return parent::loadIndex('REPORTES','REPSEGINV',$datos);
	}
}