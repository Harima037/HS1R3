<?php

class ProyectosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$catalogos = array(
				'clasificacion_proyectos'=>ClasificacionProyecto::all(),
				'tipos_proyectos'=>TipoProyecto::all()
			);
		return parent::loadIndex('POA','PROYECTOS',$catalogos);
	}

	public function caratula()
	{
		
		$clasificacion = ClasificacionProyecto::all()->lists('descripcion','id');
		$tipo = TipoProyecto::all()->lists('descripcion','id');

		$funciones_gasto = FuncionGasto::whereNull('idPadre')->where('id','=',61)->with('hijos')->get();

		$objetivos_ped = ObjetivoPED::whereNull('idPadre')->where('id','=',25)->with('hijos')->get();

		$datos_componentes = array(
			'clasificacion_proyecto' => Input::get('clasificacion_proyecto'),
			'entregables' => Entregable::all(),
			'formulas' => Formula::all(),
			'dimensiones' => Dimension::all(),
			'frecuencias' => Frecuencia::all(),
			'tipos_indicador' => TipoIndicador::all(),
			'unidades_medida' => UnidadMedida::all()
		);

		$datos = array(
			'clasificacion_proyecto_id' => Input::get('clasificacion_proyecto'),
			'tipo_proyecto_id' => Input::get('tipo_proyecto'),
			'clasificacion_proyecto' => $clasificacion[Input::get('clasificacion_proyecto')],
			'tipo_proyecto' => $tipo[Input::get('tipo_proyecto')],
			'tipos_acciones' => TipoAccion::all(),
			'unidades_responsables' => UnidadResponsable::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'funciones_gasto' => $funciones_gasto,
			'programas_sectoriales' => ProgramaSectorial::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'programas_presupuestarios' => ProgramaPresupuestario::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'programas_especiales' => ProgramaEspecial::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'actividades_institucionales' => ActividadInstitucional::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'proyectos_estrategicos' => ProyectoEstrategico::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'objetivos_ped' => $objetivos_ped,
			'coberturas' => Cobertura::all(),
			'tipos_beneficiarios' => TipoBeneficiario::all(),
			'municipios' => Municipio::all()
		);
		
		//Si hay un id etonces es edición
		if(Input::get('id')){
			$datos['id'] = Input::get('id');
		}

		//Se Pre-carga formulario general de la caratula
		$datos['formulario'] = View::make('poa.formulario-caratula-captura',$datos);
		//Se Pre-carga el datagrid de los componentes
		$datos['grid_componentes'] = View::make('poa.listado-componentes');

		//Cargar el formulario para dar de alta actividades
		$datos_componentes['identificador'] = 'actividad'; //El identificador se agrega al id de los elementos del formulario
		$datos['formulario_actividades'] = View::make('poa.formulario-componente',$datos_componentes);

		//Cargar el formulario para dar de alta compoenentes
		$datos_componentes['lista_actividades'] = View::make('poa.listado-actividades'); //Se carga el datagrid de actividades
		$datos_componentes['identificador'] = 'componente';
		$datos['formulario_componente'] = View::make('poa.formulario-componente',$datos_componentes);

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('POA');
		$datos['sys_mod_activo'] = SysModulo::findByKey('PROYECTOS');
		$uri = 'poa.caratula';
		$permiso = 'POA.PROYECTOS.C';
		$datos['usuario'] = Sentry::getUser();

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