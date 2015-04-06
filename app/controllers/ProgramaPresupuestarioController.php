<?php

class ProgramaPresupuestarioController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		/*$datos_programa = array(
			'clasificacion_proyecto' => 1,
			'formulas' => Formula::all(),
			'dimensiones' => Dimension::all(),
			'frecuencias' => Frecuencia::all(),
			'tipos_indicador' => TipoIndicador::all(),
			'unidades_medida' => UnidadMedida::all()
		);
		$datos_programa['identificador'] = 'programa'; //El identificador se agrega al id de los elementos del formulario
				$datos_programa['jurisdicciones'] = array('OC'=>'O.C.');
				$datos_programa['meses'] = array( 
					'1'=>'ENE','2'=>'FEB','3'=>'MAR', '4'=>'ABR', '5'=>'MAY', '6'=>'JUN', 
					'7'=>'JUL','8'=>'AGO','9'=>'SEP','10'=>'OCT','11'=>'NOV','12'=>'DIC'
				);
		$datos['formulario_programa'] = View::make('expediente.formulario-componente',$datos_programa);*/
		return parent::loadIndex('EXP','PROGPRESUP');
	}

	public function editar($id = NULL){
		$datos_programa = array(
			'clasificacion_proyecto' => 1,
			'formulas' => Formula::all(),
			'dimensiones' => Dimension::all(),
			'frecuencias' => Frecuencia::all(),
			'tipos_indicador' => TipoIndicador::all(),
			'unidades_medida' => UnidadMedida::all()
		);
		$datos_programa['identificador'] = 'programa'; //El identificador se agrega al id de los elementos del formulario
				$datos_programa['jurisdicciones'] = array('OC'=>'O.C.');
				$datos_programa['meses'] = array( 
					'1'=>'ENE','2'=>'FEB','3'=>'MAR', '4'=>'ABR', '5'=>'MAY', '6'=>'JUN', 
					'7'=>'JUL','8'=>'AGO','9'=>'SEP','10'=>'OCT','11'=>'NOV','12'=>'DIC'
				);
		$datos['formulario_programa'] = View::make('expediente.formulario-componente',$datos_programa);
		$datos['odm'] = ObjetivoDesarrolloMilenio::whereNull('idPadre')->with('hijos')->get();
		$datos['modalidades'] = Modalidad::all();
		$datos['programas_presupuestarios'] = ProgramaPresupuestario::all();

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['usuario'] = Sentry::getUser();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('EXP');
		$datos['sys_mod_activo'] = SysModulo::findByKey('PROGPRESUP');
		$permiso = 'EXP.PROGPRESUP.U';
		$uri = 'expediente.programa-presupuestario-formulario';
		
		if(Sentry::hasAccess($permiso)){
			return View::make($uri)->with($datos);
		}else{
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}
	}
}