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
				'tipos_proyectos'=>TipoProyecto::all(),
				'origenes_financiamiento' => OrigenFinanciamiento::all()
			);
		return parent::loadIndex('EXP','PROYECTOS',$catalogos);
	}

	public function caratula()
	{
		//Obtener los titulares para firmas: de las unidades de Planeacion y Desarrollo, Direccion General y Director de la Unidad Responsable
		$titulares = Titular::whereIn('claveUnidad',array('00','01', Sentry::getUser()->claveUnidad))->get();
		$firmas = array(
				'LiderProyecto' 	=> NULL,
				'JefeInmediato' 	=> NULL,
				'JefePlaneacion'	=> NULL,
				'CoordinadorGrupo' 	=> NULL
			);

		foreach ($titulares as $titular) {
			if($titular->claveUnidad == '00'){ //Dirección General
				$firmas['JefeInmediato'] = $titular;
			}elseif ($titular->claveUnidad == '01') { //Dirección de Planeación y Desarrollo
				$firmas['JefePlaneacion'] = $titular;
				$firmas['CoordinadorGrupo'] = $titular;
				if($firmas['LiderProyecto'] == NULL){
					$firmas['LiderProyecto'] = $titular;
				}
			}else{
				$firmas['LiderProyecto'] = $titular;
			}
		}
		
		$clasificacion = ClasificacionProyecto::all()->lists('descripcion','id');
		$tipo = TipoProyecto::all()->lists('descripcion','id');

		$funciones_gasto = FuncionGasto::whereNull('idPadre')->with('hijos')->get();

		$objetivos_ped = ObjetivoPED::whereNull('idPadre')->with('hijos')->get();

		$datos_componentes = array(
			'clasificacion_proyecto' => Input::get('clasificacion_proyecto'),
			'entregables' => Entregable::all(),
			'entregables_tipos' => EntregableTipo::all(),
			'entregables_acciones' => EntregableAccion::all(),
			'formulas' => Formula::all(),
			'dimensiones' => Dimension::all(),
			'frecuencias' => Frecuencia::all(),
			'tipos_indicador' => TipoIndicador::all(),
			'unidades_medida' => UnidadMedida::all()
		);

		$datos = array(
			'firmas' => $firmas,
			'clasificacion_proyecto_id' => Input::get('clasificacion_proyecto'),
			'tipo_proyecto_id' => Input::get('tipo_proyecto'),
			'clasificacion_proyecto' => $clasificacion[Input::get('clasificacion_proyecto')],
			'tipo_proyecto' => $tipo[Input::get('tipo_proyecto')],
			'tipos_acciones' => TipoAccion::all(),
			'unidades_responsables' => UnidadResponsable::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->where('clave','=',Sentry::getUser()->claveUnidad)->get(),
			'funciones_gasto' => $funciones_gasto,
			'programas_sectoriales' => ProgramaSectorial::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'programas_presupuestarios' => ProgramaPresupuestario::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'programas_especiales' => ProgramaEspecial::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'actividades_institucionales' => ActividadInstitucional::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'proyectos_estrategicos' => ProyectoEstrategico::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'objetivos_ped' => $objetivos_ped,
			'coberturas' => Cobertura::all(),
			'tipos_beneficiarios' => TipoBeneficiario::all(),
			'municipios' => Municipio::all(),
			'regiones' => Region::all()
		);
		
		//Si hay un id etonces es edición
		if(Input::get('id')){
			$datos['id'] = Input::get('id');
		}

		if(Input::get('fibap-id')){
			$datos['fibap_id'] = Input::get('fibap-id');
		}

		//Se Pre-carga formulario general de la caratula
		$datos['formulario'] = View::make('expediente.formulario-caratula-captura',$datos);
		//Se Pre-carga el datagrid de los componentes
		$datos['grid_componentes'] = View::make('expediente.listado-componentes');

		//Cargar el formulario para dar de alta actividades
		$datos_componentes['identificador'] = 'actividad'; //El identificador se agrega al id de los elementos del formulario
		$datos_componentes['jurisdicciones'] = array('OC'=>'O.C.','I'=>'I','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V','VI'=>'VI','VII'=>'VII','VIII'=>'VIII','IX'=>'IX','X'=>'X');
		$datos_componentes['meses'] = array(
				'ENE'=>'Enero',
				'FEB'=>'Febrero',
				'MAR'=>'Marzo',
				'ABR'=>'Abril',
				'MAY'=>'Mayo',
				'JUN'=>'Junio',
				'JUL'=>'Julio',
				'AGO'=>'Agosto',
				'SEP'=>'Septiembre',
				'OCT'=>'Octubre',
				'NOV'=>'Noviembre',
				'DIC'=>'Diciembre'
			);
		$datos['formulario_actividades'] = View::make('expediente.formulario-componente',$datos_componentes);

		//Cargar el formulario para dar de alta compoenentes
		$datos_componentes['lista_actividades'] = View::make('expediente.listado-actividades'); //Se carga el datagrid de actividades
		$datos_componentes['identificador'] = 'componente';
		$datos['formulario_componente'] = View::make('expediente.formulario-componente',$datos_componentes);

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('EXP');
		$datos['sys_mod_activo'] = SysModulo::findByKey('PROYECTOS');
		$uri = 'expediente.caratula';
		$permiso = 'EXP.PROYECTOS.C';
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