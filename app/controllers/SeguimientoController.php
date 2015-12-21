<?php
use SSA\Utilerias\Util;

class SeguimientoController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexInstitucional(){
		$datos = array(
			'meses' => array(
					'1' => array(
							array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'ENE'),
							array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'FEB'),
							array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'MAR')
						),
					'2' => array(
							array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'ABR'),
							array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'MAY'),
							array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'JUN')
						),
					'3' => array(
							array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'JUL'),
							array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'AGO'),
							array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'SEP')
						),
					'4' => array(
							array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'OCT'),
							array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'NOV'),
							array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'DIC')
						)
				)
		);
		$datos['mes_avance'] = Util::obtenerMesActual();
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n')-1;
			if($mes_actual == 0){ $mes_actual = 12; }
		}
		$datos['mes_actual'] = $mes_actual;

		$mes_trimestre = date('n')-1;
		if($mes_trimestre == 0){ $mes_trimestre = 12; }

		$datos['trimestre_avance'] = Util::obtenerTrimestre($mes_trimestre);

		$datos['anio_captura'] = Util::obtenerAnioCaptura();
		return parent::loadIndex('RENDCUENTA','RENDINST',$datos);
	}
	public function indexInversion(){
		$datos = array(
			'meses' => array(
					'1' => array(
							array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'ENE'),
							array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'FEB'),
							array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'MAR')
						),
					'2' => array(
							array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'ABR'),
							array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'MAY'),
							array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'JUN')
						),
					'3' => array(
							array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'JUL'),
							array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'AGO'),
							array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'SEP')
						),
					'4' => array(
							array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'OCT'),
							array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'NOV'),
							array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'DIC')
						)
				)
		);
		$datos['mes_avance'] = Util::obtenerMesActual();
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n')-1;
			if($mes_actual == 0){ $mes_actual = 12; }
		}
		$datos['mes_actual'] = $mes_actual;

		$mes_trimestre = date('n')-1;
		if($mes_trimestre == 0){ $mes_trimestre = 12; }

		$datos['trimestre_avance'] = Util::obtenerTrimestre($mes_trimestre);

		$datos['anio_captura'] = Util::obtenerAnioCaptura();
		return parent::loadIndex('RENDCUENTA','RENDINV',$datos);
	}
	
	public function archivoMetas($id){
		//El id es idAccion
		$mes_actual = Util::obtenerMesActual();
		$parametros = Input::all();
		
		if($parametros['nivel'] == 'componente'){
			$idElemento = 'idComponente';
			$tablaMetasMes = 'desgloseMetasMes';
			$tablaDesglose = 'componenteDesglose';
			$recurso = Componente::find($id);
		}elseif($parametros['nivel'] == 'actividad'){
			$idElemento = 'idActividad';
			$tablaMetasMes = 'actividadDesgloseMetasMes';
			$tablaDesglose = 'actividadDesglose';
			$recurso = Actividad::find($id);
		}
		
		$recurso->load(array(
		'desglose'=>function($query)use($mes_actual,$idElemento,$tablaDesglose,$tablaMetasMes){
			$query->join('vistaMunicipios AS municipios',$tablaDesglose.'.claveMunicipio','=','municipios.clave')
				->join('vistaLocalidades AS localidades',function($join)use($tablaDesglose){
					$join->on($tablaDesglose.'.claveLocalidad','=','localidades.clave')
						->on('municipios.id','=','localidades.idMunicipio');
				})
				->join('vistaJurisdicciones AS jurisdicciones','claveJurisdiccion','=','jurisdicciones.clave')
				->leftjoin($tablaMetasMes,function($join)use($mes_actual,$idElemento,$tablaDesglose){
					$join->on($idElemento.'Desglose','=',$tablaDesglose.'.id')
						->where('mes','=',$mes_actual);
				})
				->select($tablaDesglose.'.'.$idElemento,'idAccion','jurisdicciones.nombre AS jurisdiccion',
						'municipios.nombre AS municipio','localidades.nombre AS localidad',
						DB::raw('concat_ws("_",'.$tablaDesglose.'.claveMunicipio,'.$tablaDesglose.'.claveLocalidad) AS claveElemento'),
						$tablaMetasMes.'.meta',$tablaMetasMes.'.avance')
				->orderBy('claveJurisdiccion')->orderBy('municipio')->orderBy('localidad');
		}));
		
		try{
			$headers = [
					'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
				,   'Content-type'        => 'text/csv'
				,   'Content-Disposition' => 'attachment; filename=ArchivoProgramacionMetas.csv'
				,   'Expires'             => '0'
				,   'Pragma'              => 'public'
			];
			
			# add headers for each column in the CSV download
			$callback = function() use ($recurso){
				$FH = fopen('php://output', 'w');
				fputcsv($FH, array('clave','jurisdiccion','municipio','localidad','meta','avance'));
				foreach ($recurso->desglose->toArray() as $key => $row) {
					if($key == 0){ unset($recurso->desglose); }//Se elimina el array original, dado que genera mucha memoria ocupada
					
					$item = array();
					$item['clave'] = $row['claveElemento'];
					$item['jurisdiccion'] = $row['jurisdiccion'];
					$item['municipio'] = $row['municipio'];
					$item['localidad'] = $row['localidad'];
					$item['meta'] = ($row['meta'])?$row['meta']:'0';
					$item['avance'] = $row['avance'];
					fputcsv($FH, $item);
				}
				fclose($FH);
			};
		}catch(Exception $ex){
			return Response::json(array('data'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
		return Response::stream($callback, 200, $headers);
	}

	public function rendicionCuentas($id){
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['usuario'] = Sentry::getUser();

		$mes_actual = Util::obtenerMesActual();

		if($mes_actual == 0){
			return Response::view('errors.mes_no_disponible', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}

		$proyecto = Proyecto::with(array('analisisFuncional'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual);
		}))->find($id);
		if($proyecto->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
			$jurisdicciones = Jurisdiccion::all();
		}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
			$jurisdicciones = Municipio::obtenerJurisdicciones($proyecto->claveMunicipio)->get();
		}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
			$jurisdicciones = Region::obtenerJurisdicciones($proyecto->claveRegion)->get();
		}
		$meses = array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
						7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

		$datos['mes_clave'] = Util::obtenerMesActual();
		$datos['mes'] = $meses[$datos['mes_clave']];
		$mes_del_trimestre = Util::obtenerMesTrimestre();
		if($mes_del_trimestre == 3){
			$datos['trimestre_activo'] = TRUE;
		}else{
			$datos['trimestre_activo'] = FALSE;
		}

		$datos['jurisdicciones'] = array('OC'=>'Oficina Central') + $jurisdicciones->lists('nombre','clave');
		
		$datos['id'] = $id;
		$datos['id_clasificacion'] = $proyecto->idClasificacionProyecto;

		if(count($proyecto->analisisFuncional)){
			$datos['id_analisis'] = $proyecto->analisisFuncional[0]->id;
		}else{
			$datos['id_analisis'] = '';
		}

		$datos['sys_activo'] = SysGrupoModulo::findByKey('RENDCUENTA');

		if($proyecto->idClasificacionProyecto == 1){
			$datos['sys_mod_activo'] = SysModulo::findByKey('RENDINST');
			$permiso = 'RENDCUENTA.RENDINST.R';
		}else{
			$datos['sys_mod_activo'] = SysModulo::findByKey('RENDINV');
			$permiso = 'RENDCUENTA.RENDINV.R';
		}
		
		$uri = 'rendicion-cuentas.vista-captura-rendicion-institucional';
		
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