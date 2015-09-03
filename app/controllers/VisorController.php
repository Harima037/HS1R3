<?php
use SSA\Utilerias\Util;

class VisorController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexInstitucional(){
		$datos = $this->obtenerDatosAvances();
		return parent::loadIndex('VISORGEN','VIPROYINST',$datos);
	}
	
	public function indexInversion(){
		$datos = $this->obtenerDatosAvances();
		return parent::loadIndex('VISORGEN','VIPROYINV',$datos);
	}

	public function obtenerDatosAvances(){
		$datos = array(
			'meses' => array(
				array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'ENE'),
				array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'FEB'),
				array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'MAR'),
				array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'ABR'),
				array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'MAY'),
				array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'JUN'),
				array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'JUL'),
				array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'AGO'),
				array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'SEP'),
				array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'OCT'),
				array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'NOV'),
				array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'DIC')
			)
		);
		$datos['mes_avance'] = Util::obtenerMesActual();
		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n')-1;
		}
		$datos['mes_actual'] = $mes_actual;
		$datos['trimestre_avance'] = Util::obtenerTrimestre(date('n')-1);

		$usuario = Sentry::getUser();
		if(!$usuario->claveJurisdiccion && !$usuario->claveUnidad){
			$datos['mostrar_filtrado'] = true;
			$datos['jurisdicciones'] = array('OC'=>'OFICINA CENTRAL') + Jurisdiccion::all()->lists('nombre','clave');
			$datos['unidades_responsables'] = UnidadResponsable::all()->lists('descripcion','clave');
			$datos['jurisdiccion_select'] = '';
			$datos['unidad_select'] = '';
			$parametros = Input::all();
			if(isset($parametros['j'])){
				if($parametros['j']){
					$datos['jurisdiccion_select'] = $parametros['j'];
				}
			}
			if(isset($parametros['u'])){
				if($parametros['u']){
					$datos['unidad_select'] = $parametros['u'];
				}
			}

		}
		return $datos;
	}

	public function indexDesempenioGeneral(){
		return parent::loadIndex('VISORGEN','VIDESEMGEN');
	}

	public function indexPresupuesto(){
		$datos['usuario'] = Sentry::getUser();
		if($datos['usuario']->claveJurisdiccion){
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}
		return parent::loadIndex('VISORGEN','VIPRESUP');
	}

	public function indexPresupuestoMeta(){
		$datos['usuario'] = Sentry::getUser();
		if($datos['usuario']->claveJurisdiccion){
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}
		return parent::loadIndex('VISORGEN','VIPRESUPME');
	}

	public function indexEstatal(){
		$datos['usuario'] = Sentry::getUser();
		if($datos['usuario']->claveJurisdiccion || $datos['usuario']->claveUnidad){
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}
		$catalogos = array( 
			'jurisdicciones' => array('OC'=>'OFICINA CENTRAL') + Jurisdiccion::all()->lists('nombre','clave'),
			'unidades_responsables' => UnidadResponsable::all()->lists('descripcion','clave')
		);
		return parent::loadIndex('VISORGEN','VIESTATAL',$catalogos);
	}

	public function indexDirecciones(){
		$datos['usuario'] = Sentry::getUser();
		if($datos['usuario']->claveJurisdiccion || $datos['usuario']->claveUnidad){
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}
		return parent::loadIndex('VISORGEN','VIDIRECS');
	}

	public function indexJurisdicciones(){
		$datos['usuario'] = Sentry::getUser();
		if($datos['usuario']->claveJurisdiccion || $datos['usuario']->claveUnidad){
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}
		$catalogos = array( 'jurisdicciones' => array('OC'=>'OFICINA CENTRAL') + Jurisdiccion::all()->lists('nombre','clave') );
		return parent::loadIndex('VISORGEN','VIJURIS',$catalogos);
	}

	public function avanceIndicadores($id){
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['usuario'] = Sentry::getUser();

		$parametros = Input::all();

		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){ $mes_actual = date('n')-1; }

		$meses = array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
						7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

		$proyecto = Proyecto::find($id);

		$es_jurisdiccion = false;

		if($datos['usuario']->claveJurisdiccion){
			$es_jurisdiccion = true;
		}

		$datos['unidad'] = '';
		if(isset($parametros['u'])){
			if($parametros['u']){
				$datos['unidad'] = $parametros['u'];
			}
		}

		$datos['jurisdiccion'] = '';
		if(isset($parametros['j'])){
			if($parametros['j']){
				$es_jurisdiccion = true;
				$datos['jurisdiccion'] = $parametros['j'];
			}
		}

		if($es_jurisdiccion){
			$datos['meses'] = $meses;
		}else{
			if($proyecto->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
				$jurisdicciones = Jurisdiccion::all();
			}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
				$jurisdicciones = Municipio::obtenerJurisdicciones($proyecto->claveMunicipio)->get();
			}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
				$jurisdicciones = Region::obtenerJurisdicciones($proyecto->claveRegion)->get();
			}
			$datos['jurisdicciones'] = array('OC'=>'OFICINA CENTRAL') + $jurisdicciones->lists('nombre','clave');
		}


		$datos['mes_clave'] = $mes_actual;
		$datos['mes'] = $meses[$datos['mes_clave']];
		$mes_del_trimestre = Util::obtenerMesTrimestre();
		if($mes_del_trimestre == 3){
			$datos['trimestre_activo'] = TRUE;
		}else{
			$datos['trimestre_activo'] = FALSE;
		}
		
		$datos['id'] = $id;
		$datos['id_clasificacion'] = $proyecto->idClasificacionProyecto;
		
		$datos['sys_activo'] = SysGrupoModulo::findByKey('VISORGEN');

		if($proyecto->idClasificacionProyecto == 1){
			$datos['sys_mod_activo'] = SysModulo::findByKey('VIPROYINST');
			$permiso = 'VISORGEN.VIPROYINST.R';
		}else{
			$datos['sys_mod_activo'] = SysModulo::findByKey('VIPROYINV');
			$permiso = 'VISORGEN.VIPROYINV.R';
		}
		
		$uri = 'visor.vista-avance-indicadores';
		
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

	public function imprimirGrafica(){
		$parametros = Input::all();

		if(isset($parametros['mostrar'])){
			$request = Request::create('v1/visor/'.$parametros['id-indicador'], 'GET', $parametros);
			$data = json_decode(Route::dispatch($request)->getContent());

			$meses_capturados = array();
			foreach ($data->data->meses as $clave => $datos_mes) {
				$meses_capturados[$clave] = $datos_mes;
			}
			$data->data->meses = $meses_capturados;

			$lista_jurisdicciones = array('0'=>'OFICINA CENTRAL') + Jurisdiccion::all()->lists('nombre','clave');
			$jurisdicciones = array();
			foreach ($data->data->jurisdicciones as $clave => $datos_juris) {
				$jurisdicciones[$clave] = json_decode(json_encode($datos_juris),true);
			}
			foreach ($lista_jurisdicciones as $clave => $datos_juris) {
				if(!isset($jurisdicciones[intval($clave)])){
					$jurisdicciones[intval($clave)] = array(
						'nombre'=>$datos_juris,
						'clave'=>($clave=='0')?'OC':$clave
					);
				}
			}
			ksort($jurisdicciones);
			$data->data->jurisdicciones = $jurisdicciones;

			$mes_actual = Util::obtenerMesActual();
			if($mes_actual == 0){ $mes_actual = date('n')-1; }
			$datos = array(
				'data' => $data->data,
				'meses' => array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
						7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'),
				'mes_clave'=>$mes_actual,
				'tomar'	=> $data->data->tomar,
				'ultimo_avance' => array('acumulado'=>0,'porcentaje'=>0,'estatus'=>0,'activo'=>0),
				'srcGraficaMensual' => 'src="'.$parametros['grafica-mensual'].'"',
				'srcGraficaJurisdiccional' => 'src="'.$parametros['grafica-jurisdiccional'].'"'
			);
			
			$pdf = PDF::setPaper('LETTER')
						->setOrientation('portrait')
						->setWarnings(false)
						->loadView('visor.pdf.reporte-graficas-indicador',$datos);
		}else{
			$datos = array('soloGraficas'=>1);
			
			if(isset($parametros['tipo'])){
				if($parametros['tipo'] == 'tabla'){
					$mes_actual = Util::obtenerMesActual();
					if($mes_actual == 0){ 
						$mes_actual = date('n')-1; 
					}else{
						$mes_actual = $mes_actual-1;
					}

					$rows = CargaDatosEP01::where('mes','=',$mes_actual);

					$usuario = Sentry::getUser();
					if($usuario->claveUnidad){
						$unidades = explode('|',$usuario->claveUnidad);
						$rows = $rows->whereIn('UR',$unidades);
					}

					$rows = $rows->select('unidades.descripcion AS unidadResponsable',
									DB::raw('sum(presupuestoModificado) AS presupuestoModificado'),
									DB::raw('sum(presupuestoLiberado) AS presupuestoLiberado'),
									DB::raw('sum(presupuestoMinistrado) AS presupuestoMinistrado'),
									DB::raw('sum(presupuestoComprometidoModificado) AS presupuestoComprometidoModificado'),
									DB::raw('sum(presupuestoDevengadoModificado) AS presupuestoDevengadoModificado'),
									DB::raw('sum(presupuestoEjercidoModificado) AS presupuestoEjercidoModificado'),
									DB::raw('sum(presupuestoPagadoModificado) AS presupuestoPagadoModificado'),
									DB::raw('sum(disponiblePresupuestarioModificado) AS disponiblePresupuestarioModificado')
								)
								->leftjoin('catalogoUnidadesResponsables AS unidades','unidades.clave','=','UR')
								->groupBy('UR')
								->get();
					$total['presupuestoModificado'] = $rows->sum('presupuestoModificado');
					$total['presupuestoLiberado'] = $rows->sum('presupuestoLiberado');
					$total['presupuestoMinistrado'] = $rows->sum('presupuestoMinistrado');
					$total['presupuestoComprometidoModificado'] = $rows->sum('presupuestoComprometidoModificado');
					$total['presupuestoDevengadoModificado'] = $rows->sum('presupuestoDevengadoModificado');
					$total['presupuestoEjercidoModificado'] = $rows->sum('presupuestoEjercidoModificado');
					$total['presupuestoPagadoModificado'] = $rows->sum('presupuestoPagadoModificado');
					$total['disponiblePresupuestarioModificado'] = $rows->sum('disponiblePresupuestarioModificado');

					$datos['datos'] = $rows;
					$datos['total'] = $total;
					/*$pdf = PDF::setPaper('LETTER')
								->setOrientation('landscape')
								->setWarnings(false)
								->loadView('visor.pdf.reporte-resumen-presupuesto',$datos);
								*/
				}
			}

			if(isset($parametros['titulo'])){
				$datos['titulo'] = $parametros['titulo'];
			}else{
				$datos['titulo'] = 'Gráfica';
			}

			if($parametros['imagen']){
				if($parametros['imagen'] != 'lol'){
					$datos['grafica'] = 'src="'.$parametros['imagen'].'"';
				}
			}

			if(isset($parametros['imagen2'])){
				if($parametros['imagen2']){
					$datos['grafica2'] = 'src="'.$parametros['imagen2'].'"';
				}
			}

			$pdf = PDF::setPaper('LETTER')
						->setOrientation('landscape')
						->setWarnings(false)
						->loadView('visor.pdf.reporte-graficas-indicador',$datos);
		}
		return $pdf->stream('Grafica.pdf');
	}
}
?>