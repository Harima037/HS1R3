<?php

class RevisionEstrategiaInstitucionalController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return parent::loadIndex('REVISION','REVEST');
	}

	public function editar($id = NULL){
		
		$datos = array(
			'formulas' 					=> Formula::all(),
			'dimensiones' 				=> Dimension::all(),
			'frecuencias' 				=> Frecuencia::all(),
			'tipos_indicador' 			=> TipoIndicador::all(),
			'unidades_medida' 			=> UnidadMedida::all(),
		);
		
		$datos['id'] = $id;
		$datos['unidades_responsables'] = UnidadResponsable::all();

		$datos['odm'] = ObjetivoDesarrolloMilenio::whereNull('idPadre')->with('hijos')->get();
		$datos['programas_presupuestarios'] = ProgramaPresupuestario::all();

		if(Sentry::getUser()->claveUnidad){
			$unidades = explode('|',Sentry::getUser()->claveUnidad);
			$datos['unidades_responsables'] = UnidadResponsable::whereIn('clave',$unidades)->get();
		}else{
			$datos['unidades_responsables'] = UnidadResponsable::all();
		}

		$datos['programas_sectoriales'] = ProgramaSectorial::all();
		$datos['objetivos_ped'] = ObjetivoPED::whereNull('idPadre')->with('hijos')->get();
		

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['usuario'] = Sentry::getUser();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('REVISION');
		$datos['sys_mod_activo'] = SysModulo::findByKey('REVEST');
		$permiso = 'REVISION.REVEST.U';
		$uri = 'revision.estrategia-institucional-formulario'; //ojo
		
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