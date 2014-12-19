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
		return parent::loadIndex('POA','FIBAP',$catalogos);
	}

	public function formulario()
	{
		$datos = array(
			'tipos_proyectos'=>TipoProyecto::all(),
			'programa_presupuestario'=>ProgramaPresupuestario::all(),
			'objetivos_ped'=>ObjetivoPED::whereNull('idPadre')->where('id','=',25)->with('hijos')->get(),
			'coberturas' => Cobertura::all(),
			'tipos_beneficiarios' => TipoBeneficiario::all(),
			'municipios' => Municipio::all(),
			'regiones' => Region::all(),
			'documentos_soporte' => DocumentoSoporte::all(),
			'origenes_financiamiento' => OrigenFinanciamiento::all()
		);

		//Si hay un id etonces es edición
		if(Input::get('id')){
			$datos['id'] = Input::get('id');
		}

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('POA');
		$datos['sys_mod_activo'] = SysModulo::findByKey('FIBAP');
		$uri = 'poa.formulario-fibap';
		$datos['usuario'] = Sentry::getUser();

		if(Sentry::hasAccess('POA.FIBAP.C')){
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