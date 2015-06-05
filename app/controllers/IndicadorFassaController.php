<?php

class IndicadorFassaController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$catalogos = array(
			'niveles' 			=> array('F'=>'Fin','P'=>'Propósito','C'=>'Componente','A'=>'Actividad'),
			'tipos_formulas' 	=> array('P'=>'Porcentaje','T'=>'Tasa'),
			'unidades'	 		=> UnidadResponsable::all(),
			'ejercicio'			=> date('Y')
		);
		return parent::loadIndex('EXP','INDFASSA',$catalogos);
	}
}