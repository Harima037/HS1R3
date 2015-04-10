<?php

class ProyectosController extends \BaseController {

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
		return parent::loadIndex('EXP','PROYECTOS',$catalogos);
	}

	public function caratula($id = NULL){
		if($id){
			$proyecto = Proyecto::find($id);
			if($proyecto){
				$parametros['clasificacion-proyecto'] = $proyecto->idClasificacionProyecto;
				$parametros['tipo-proyecto'] = $proyecto->idTipoProyecto;
				$parametros['id'] = $id;
			}
		}else{
			$parametros = Input::all();
			$parametros['clasificacion-proyecto'] = 1;
		}

		$datos = $this->catalogos_caratula($parametros);

		$datos_componentes = array(
			'clasificacion_proyecto' => $parametros['clasificacion-proyecto'],
			'formulas' => Formula::all(),
			'dimensiones' => Dimension::all(),
			'frecuencias' => Frecuencia::all(),
			'tipos_indicador' => TipoIndicador::all(),
			'unidades_medida' => UnidadMedida::all()
		);

		//Se Pre-carga formulario general de la caratula
		$datos['formulario'] = View::make('expediente.formulario-caratula-captura',$datos);
		//Se Pre-carga el datagrid de los componentes
		$datos['grid_componentes'] = View::make('expediente.listado-componentes');

		//$datos['grid_beneficiarios'] = View::make('expediente.formulario-caratula-beneficiarios');
		$datos_financiamiento = array(
			'fuentes_financiamiento'	=> FuenteFinanciamiento::where('nivel','=',4)->get(),
			'destino_gasto'				=> DestinoGasto::all(),
			'subfuentes_financiamiento' => SubFuenteFinanciamiento::all()
		);
		$datos['grid_fuentes_financiamiento'] = View::make('expediente.formulario-caratula-financiamiento',$datos_financiamiento);

		//Cargar el formulario para dar de alta actividades
		$datos_componentes['identificador'] = 'actividad'; //El identificador se agrega al id de los elementos del formulario
		$datos_componentes['jurisdicciones'] = array('OC'=>'O.C.');
		$datos_componentes['meses'] = array( 
			'1'=>'ENE','2'=>'FEB','3'=>'MAR', '4'=>'ABR', '5'=>'MAY', '6'=>'JUN', 
			'7'=>'JUL','8'=>'AGO','9'=>'SEP','10'=>'OCT','11'=>'NOV','12'=>'DIC'
		);
		
		$datos['formulario_actividades'] = View::make('expediente.formulario-componente',$datos_componentes);

		//Cargar el formulario para dar de alta compoenentes
		$datos_componentes['lista_actividades'] = View::make('expediente.listado-actividades'); //Se carga el datagrid de actividades
		$datos_componentes['identificador'] = 'componente';
		$datos['formulario_componente'] = View::make('expediente.formulario-componente',$datos_componentes);

		/*$datos_beneficiarios = array(
			'tipos_beneficiarios' => TipoBeneficiario::all()
		);*/
		//$datos['formulario_beneficiario'] = View::make('expediente.formulario-beneficiario',$datos_beneficiarios);

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

	public function loadIndex($sys_sis_llave,$sys_mod_llave = NULL,$datos_extra = array()){
		return parent::loadIndex($sys_sis_llave, $sys_mod_llave, $datos_extra);
	}
	
	public function catalogos_caratula($parametros){
		//
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

		$datos = array(
			'firmas' => $firmas,
			'clasificacion_proyecto_id' => $parametros['clasificacion-proyecto'],
			'tipo_proyecto_id' => $parametros['tipo-proyecto'],
			'clasificacion_proyecto' => $clasificacion[$parametros['clasificacion-proyecto']],
			'tipo_proyecto' => $tipo[$parametros['tipo-proyecto']],
			'tipos_acciones' => TipoAccion::where('id','=',7)->get(),
			'funciones_gasto' => $funciones_gasto,
			'programas_sectoriales' => ProgramaSectorial::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'programas_presupuestarios' => ProgramaPresupuestario::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'programas_especiales' => ProgramaEspecial::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'actividades_institucionales' => ActividadInstitucional::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'proyectos_estrategicos' => ProyectoEstrategico::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get(),
			'objetivos_ped' => $objetivos_ped,
			'coberturas' => Cobertura::all(),
			//'tipos_beneficiarios' => TipoBeneficiario::all(),
			'municipios' => Municipio::all(),
			'regiones' => Region::all()
		);
		
		if(Sentry::getUser()->claveUnidad){
			$datos['unidades_responsables'] = UnidadResponsable::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->where('clave','=',Sentry::getUser()->claveUnidad)->get();
		}else{
			$datos['unidades_responsables'] = UnidadResponsable::select('clave',DB::raw('concat(clave," ",descripcion) as descripcion'))->get();
		}

		/*$datos['clasificacion_proyecto_id'] = $parametros['clasificacion-proyecto'];
		$datos['clasificacion_proyecto'] = $clasificacion[$parametros['clasificacion-proyecto']];
		$datos['tipo_proyecto'] = $tipo[$parametros['tipo-proyecto']];*/
		
		if(isset($parametros['id'])){
			$datos['id'] = $parametros['id'];
		}

		$datos_beneficiarios = array(
			'tipos_beneficiarios' => TipoBeneficiario::all()
		);
		$datos_benef['formulario_beneficiarios'] = View::make('expediente.formulario-beneficiario',$datos_beneficiarios);
		$datos['grid_beneficiarios'] = View::make('expediente.formulario-caratula-beneficiarios',$datos_benef);

		//Se Pre-carga formulario general de la caratula
		$datos['formulario'] = View::make('expediente.formulario-caratula-captura',$datos);

		return $datos;
	}
}