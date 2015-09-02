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

	public function archivoMunicipios($id){
		$parametros = Input::all();
		$proyecto = Proyecto::select('id','claveMunicipio','claveRegion','idCobertura')->where('id',$id)->first();
		
		$recurso = Municipio::join('vistaLocalidades AS localidad',function($join){
									$join->on('localidad.idMunicipio','=','vistaMunicipios.id')
										->whereNull('localidad.borradoAl');
								});
		
		$nombre_archivo = '';
		if($parametros['tipo-carga'] == 'meta'){
			$encabezados = array('1','2','3','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
			$campos = 'null AS enero';
			$nombre_archivo = 'Metas';
		}elseif($parametros['tipo-carga'] == 'presupuesto'){
			$encabezados = array('1','2','3','4','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
			$campos = 'objetosGasto.clave AS partida';
			$nombre_archivo = 'Presupuesto';
			$recurso = $recurso->join('relAccionesPartidas AS partidas','partidas.idAccion','=',DB::raw($parametros['id-accion']))
							->leftjoin('catalogoObjetosGasto as objetosGasto','objetosGasto.id','=','partidas.idObjetoGasto')
							->groupBy('clave','partidas.id');
		}else{
			$encabezados = array('1','2','3','4','5','totalMujeres','totalHombres');
			$campos = 'tiposBenef.id AS idBeneficiario, tiposBenef.descripcion AS tipoBeneficiario';
			$nombre_archivo = 'Beneficiarios';
			$recurso = $recurso->join('proyectoBeneficiarios AS proyectoBenef',function($join)use($id){
									$join->whereNull('proyectoBenef.borradoAl')
										->where('proyectoBenef.idProyecto','=',$id);
								})
								->leftjoin('catalogoTiposBeneficiarios AS tiposBenef','tiposBenef.id','=','proyectoBenef.idTipoBeneficiario')
								->groupBy('clave','tiposBenef.id');
		}
		$recurso = $recurso->select(DB::raw('CONCAT_WS("_",vistaMunicipios.clave,localidad.clave) AS clave'),
									'vistaMunicipios.nombre AS nombreMunicipio','localidad.nombre AS nombreLocalidad',DB::raw($campos))
								->orderBy('clave','ASC');

		if($proyecto->idCobertura == 1){ 
		//Cobertura Estado
			$recurso = $recurso->get();
		}elseif($proyecto->idCobertura == 2){ 
		//Cobertura Municipio
			$recurso = $recurso->where('vistaMunicipios.clave','=',$proyecto->claveMunicipio)->get();
		}elseif($proyecto->idCobertura == 3){ 
		//Cobertura Region
			$recurso = $recurso->join('vistaRegiones AS region',function($join)use($proyecto){
									$join->on('vistaMunicipios.idRegion','=','region.id')
										->where('region.region','=',$proyecto->claveRegion)
										->whereNull('region.borradoAl');
								})->get();
		}
		//$queries = DB::getQueryLog();
		//var_dump(end($queries));die;
		if(count($recurso) == 0){
			$recurso = array(0=>array('nombre'=>'Faltan los recursos necesarios para generar esta lista.'));
		}else{
			$recurso = $recurso->toArray();
		}
		
		$headers = [
				'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
			,   'Content-type'        => 'text/csv'
			,   'Content-Disposition' => 'attachment; filename=ArchivoMunicipios'.$nombre_archivo.'.csv'
			,   'Expires'             => '0'
			,   'Pragma'              => 'public'
		];
		
		# add headers for each column in the CSV download
		$cabeceras = array_keys($recurso[0]) + $encabezados;
		array_unshift($recurso, $cabeceras);
		$callback = function() use ($recurso){
			$FH = fopen('php://output', 'w');
			foreach ($recurso as $row) { 
				fputcsv($FH, $row);
			}
			fclose($FH);
		};
	
		return Response::stream($callback, 200, $headers);
		/*
		Excel::create('ArchivoMunicipios'.$nombre_archivo, function($excel) use ($recurso) {
		    $excel->sheet('Municipios', function($sheet) use ($recurso) {
		        $sheet->fromArray($recurso);
		    });
		})->download('csv');
		*/
		//return Response::download($recurso, 'output.csv', ['Content-Type: text/cvs']);
		//return Response::json($recurso,200);
	}
}