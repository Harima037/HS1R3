<?php

class AdminProyectosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$catalogos = array(
				'clasificacion_proyectos'=>ClasificacionProyecto::all(),
				'estatus_proyectos'=>EstatusProyecto::all(),
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
		return parent::loadIndex('ADMIN','ADMINPROYS',$catalogos);
	}
}