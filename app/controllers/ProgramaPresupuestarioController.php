<?php

class ProgramaPresupuestarioController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return parent::loadIndex('EXP','PROGPRESUP');
	}

	public function editar($id = NULL){
		$datos_programa = array(
			'clasificacion_proyecto' 	=> 1,
			'formulas' 					=> Formula::all(),
			'dimensiones' 				=> Dimension::all(),
			'frecuencias' 				=> Frecuencia::all(),
			'tipos_indicador' 			=> TipoIndicador::all(),
			'unidades_medida' 			=> UnidadMedida::all(),
			'comportamientos_accion' 	=> ComportamientoAccion::select('id',DB::raw("concat(clave,' ',descripcion) as descripcion"))->get(),
			'ambitos'					=> array(
											array('clave'=>'E','descripcion'=>'Estatal'),
											array('clave'=>'F','descripcion'=>'Federal')
										)
		);
		$datos_programa['identificador'] = 'programa'; //El identificador se agrega al id de los elementos del formulario
		
		$datos['id'] = $id;
		$datos['formulario_programa'] = View::make('expediente.formulario-componente',$datos_programa);
		$datos['odm'] = ObjetivoDesarrolloMilenio::whereNull('idPadre')->with('hijos')->get();
		$datos['modalidades'] = Modalidad::all();
		$datos['programas_presupuestarios'] = ProgramaPresupuestario::all();

		if(Sentry::getUser()->claveUnidad){
			$unidades = explode('|',Sentry::getUser()->claveUnidad);
			$datos['unidades_responsables'] = UnidadResponsable::whereIn('clave',$unidades)->get();
		}else{
			$datos['unidades_responsables'] = UnidadResponsable::all();
		}

		$datos['programas_sectoriales'] = ProgramaSectorial::all();
		$datos['objetivos_ped'] = ObjetivoPED::whereNull('idPadre')->with('hijos')->get();
		$datos['objetivos_pnd'] = ObjetivoPND::whereNull('idPadre')->with('hijos')->get();

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