<?php

//class InversionController extends \BaseController {
class InversionController extends ProyectosController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$catalogos = array( 
			'origenes_financiamiento' => OrigenFinanciamiento::all() 
		);
		$datos['tipos_proyectos'] = TipoProyecto::all();
		$datos['modal_detalle'] = View::make('expediente.detalles-proyecto',$catalogos);
		return parent::loadIndex('EXP','INVERSION',$datos);
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
			$parametros['clasificacion-proyecto'] = 2;
		}

		$datos = parent::catalogos_caratula($parametros);

		$catalogos_fibap =  array(
			'objetos_gasto'=>ObjetoGasto::whereNull('idPadre')->with('hijos')->get(),
			'documentos_soporte' => DocumentoSoporte::all()
		);

		$datos_financiamiento = array(
			'fuentes_financiamiento'	=> FuenteFinanciamiento::where('nivel','=',4)->get(),
			'destino_gasto'				=> DestinoGasto::all(),
			'subfuentes_financiamiento' => SubFuenteFinanciamiento::all()
		);
		$datos['grid_fuentes_financiamiento'] = View::make('expediente.formulario-caratula-financiamiento',$datos_financiamiento);

		$datos['formulario_fibap'] = View::make('expediente.formulario-caratula-fibap',$catalogos_fibap);
		$datos['formulario_antecedentes'] = View::make('expediente.formulario-caratula-antecedente');

		$origenes_financiamiento = OrigenFinanciamiento::all();
		$meses = array( 
					'1'=>'ENE','2'=>'FEB','3'=>'MAR', '4'=>'ABR', '5'=>'MAY', '6'=>'JUN', 
					'7'=>'JUL','8'=>'AGO','9'=>'SEP','10'=>'OCT','11'=>'NOV','12'=>'DIC'
				);
		//
		$datos_acciones = array(
			'origenes_financiamiento' => $origenes_financiamiento,
			'meses' => $meses
		);

		$datos_acciones_formulario = array(
			'clasificacion_proyecto' 	=> $parametros['clasificacion-proyecto'],
			'entregables'				=> Entregable::all(),
			'entregables_tipos' 		=> EntregableTipo::all(),
			'entregables_acciones' 		=> EntregableAccion::all(),
			'formulas' 					=> Formula::all(),
			'dimensiones' 				=> Dimension::all(),
			'frecuencias'	 			=> Frecuencia::all(),
			'tipos_indicador' 			=> TipoIndicador::all(),
			'unidades_medida' 			=> UnidadMedida::all(),
			'objetos_gasto'				=> ObjetoGasto::whereNull('idPadre')->with('hijos')->get(),
			'origenes_financiamiento' 	=> $origenes_financiamiento,
			'jurisdicciones' 			=> array('OC'=>'O.C.'),
			'meses'						=> $meses
		);

		$datos_acciones_formulario['identificador'] = 'actividad'; //El identificador se agrega al id de los elementos del formulario
		$datos_acciones['formulario_actividad'] = View::make('expediente.formulario-inversion-componente',$datos_acciones_formulario);

		//Cargar el formulario para dar de alta compoenentes
		$datos_acciones_formulario['lista_actividades'] = View::make('expediente.listado-actividades');
		$datos_acciones_formulario['identificador'] = 'componente';
		$datos_acciones['formulario_componente'] = View::make('expediente.formulario-inversion-componente',$datos_acciones_formulario);
		
		$datos['formulario_acciones'] = View::make('expediente.formulario-caratula-acciones',$datos_acciones);
		
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('EXP');
		$datos['sys_mod_activo'] = SysModulo::findByKey('INVERSION');
		$uri = 'expediente.caratula-inversion';
		$permiso = 'EXP.INVERSION.C';
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