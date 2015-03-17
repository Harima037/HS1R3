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
							array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'E'),
							array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'F'),
							array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'M')
						),
					'2' => array(
							array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'A'),
							array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'M'),
							array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'J')
						),
					'3' => array(
							array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'J'),
							array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'A'),
							array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'S')
						),
					'4' => array(
							array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'O'),
							array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'N'),
							array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'D')
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
		return parent::loadIndex('RENDCUENTA','RENDINV');
	}

	public function rendicionCuentas($id){
		$mes_actual = Util::obtenerMesActual();
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
						7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Dicembre');

		$datos['mes_clave'] = Util::obtenerMesActual();
		$datos['mes'] = $meses[Util::obtenerMesActual()];

		$datos['jurisdicciones'] = $jurisdicciones;

		$datos['id'] = $id;

		if(count($proyecto->analisisFuncional)){
			$datos['id_analisis'] = $proyecto->analisisFuncional[0]->id;
		}else{
			$datos['id_analisis'] = '';
		}

		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('RENDCUENTA');
		$datos['sys_mod_activo'] = SysModulo::findByKey('RENDINST');
		$uri = 'rendicion-cuentas.vista-captura-rendicion-institucional';
		$permiso = 'RENDCUENTA.RENDINST.C';
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