<?php

class EstrategiaInstitucionalController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$catalogos = array(
				'clasificacion_proyectos'=>ClasificacionProyecto::all(),
				'tipos_proyectos'=>TipoProyecto::all(),
				'origenes_financiamiento' => OrigenFinanciamiento::all()
			);
		return parent::loadIndex('EXP','ESTINST',$catalogos);
    }

    public function loadIndex($sys_sis_llave,$sys_mod_llave = NULL,$datos_extra = array()){
		return parent::loadIndex($sys_sis_llave, $sys_mod_llave, $datos_extra);
	}


	public function editar($id = NULL){
		$datos = array(
			'formulas' 					=> Formula::all(),
			'dimensiones' 				=> Dimension::all(),
			'frecuencias' 				=> Frecuencia::all(),
			'tipos_indicador' 			=> TipoIndicador::all(),
			'unidades_medida' 			=> UnidadMedida::all(),
			'comportamientos_accion' 	=> ComportamientoAccion::select('id',DB::raw("concat(clave,' ',descripcion) as descripcion"))->get(),
			'tipos_valor_meta' 			=> TipoValorMeta::all()
		);
		//$datos['identificador'] = 'estrategia-institucional'; //El identificador se agrega al id de los elementos del formulario
		
		$datos['id'] = $id;
		//$datos['formulario_estrategia'] = View::make('expediente.formulario-componente',$datos_programa);
		//$datos['odm'] = ObjetivoDesarrolloMilenio::whereNull('idPadre')->with('hijos')->get();
		$datos['ods'] = ObjetivoDesarrolloSostenible::all();
		$datos['estrategias_nacionales'] = EstrategiaNacional::all();

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
		$datos['sys_activo'] = SysGrupoModulo::findByKey('EXP');
		$datos['sys_mod_activo'] = SysModulo::findByKey('ESTINST');
		$permiso = 'EXP.ESTINST.U';
		$uri = 'expediente.estrategia-institucional-formulario';
		
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
