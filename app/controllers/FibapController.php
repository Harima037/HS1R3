<?php

class FibapController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$catalogos = array(
				'programa_presupuestal'=>ClasificacionProyecto::all(),
				'objetivos_ped'=>TipoProyecto::all()
			);
		return parent::loadIndex('EXP','FIBAP',$catalogos);
	}

	public function formulario()
	{
		$datos = array(
			'tipos_proyectos'=>TipoProyecto::all(),
			'programa_presupuestario'=>ProgramaPresupuestario::all(),
			'objetivos_ped'=>ObjetivoPED::whereNull('idPadre')->where('id','=',25)->with('hijos')->get(),
			'objetos_gasto'=>ObjetoGasto::whereNull('idPadre')->with('hijos')->get(),
			'coberturas' => Cobertura::all(),
			'tipos_beneficiarios' => TipoBeneficiario::all(),
			'municipios' => Municipio::all(),
			'regiones' => Region::all(),
			'documentos_soporte' => DocumentoSoporte::all(),
			'origenes_financiamiento' => OrigenFinanciamiento::all()
		);

		$datos['meses'] = array(
				'ENE'=>'Enero','FEB'=>'Febrero','MAR'=>'Marzo','ABR'=>'Abril','MAY'=>'Mayo','JUN'=>'Junio',
				'JUL'=>'Julio','AGO'=>'Agosto','SEP'=>'Septiembre','OCT'=>'Octubre','NOV'=>'Noviembre','DIC'=>'Diciembre'
			);

		//Si hay un id etonces es edición
		if(Input::get('id')){
			$datos['id'] = Input::get('id');
		}

		if(Input::get('proyecto-id')){ //Si hay un proyecto-id se bloquean algunos campos
			$datos['proyecto_id'] = Input::get('proyecto-id');
		}

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('EXP');
		$datos['sys_mod_activo'] = SysModulo::findByKey('FIBAP');
		$uri = 'expediente.formulario-fibap';
		$datos['usuario'] = Sentry::getUser();

		if(Sentry::hasAccess('EXP.FIBAP.C')){
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