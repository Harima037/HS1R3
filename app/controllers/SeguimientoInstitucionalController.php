<?php
use SSA\Utilerias\Util;

class SeguimientoInstitucionalController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
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
		$mes_actual = Util::obtenerMesActual();
		$trimestre_actual = ceil(($mes_actual/3));
		$datos['mes_avance'] = $mes_actual;
		$datos['trimestre_avance'] = $trimestre_actual;
		return parent::loadIndex('REVISION','SEGUIINST',$datos);
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
		$datos['trimestre_avance'] = Util::obtenerTrimestre();
		return parent::loadIndex('REVISION','SEGUIINV',$datos);
	}

	public function rendicionCuentas($id){
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['usuario'] = Sentry::getUser();

		//$mes_actual = Util::obtenerMesActual();
		$mes_actual = date('n') - 1 ;
		
		/*if($mes_actual == 0){
			return Response::view('errors.mes_no_disponible', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}*/
		
		$proyecto = Proyecto::with(array('analisisFuncional'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual);
		},'evaluacionMeses'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->whereIn('idEstatus',array(2));
		}))->find($id);

		if(count($proyecto->evaluacionMeses) == 0){
			return Response::view('errors.avance_no_capturado', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}

		if($proyecto->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
			$jurisdicciones = Jurisdiccion::all();
		}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
			$jurisdicciones = Municipio::obtenerJurisdicciones($proyecto->claveMunicipio)->get();
		}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
			$jurisdicciones = Region::obtenerJurisdicciones($proyecto->claveRegion)->get();
		}
		$meses = array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
						7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

		$datos['mes_clave'] = $mes_actual; //Util::obtenerMesActual();
		$datos['mes'] = $meses[$datos['mes_clave']];
		$mes_del_trimestre = Util::obtenerMesTrimestre($mes_actual);
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

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('REVISION');
		
		
		if($proyecto->idClasificacionProyecto == 1){
			$datos['sys_mod_activo'] = SysModulo::findByKey('SEGUIINST');
			$permiso = 'REVISION.SEGUIINST.R';
		}
		else{
			$datos['sys_mod_activo'] = SysModulo::findByKey('SEGUIINV');
			$permiso = 'REVISION.SEGUIINV.R';
		}
		$uri = 'revision.segui-rendicion-institucional';
		
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