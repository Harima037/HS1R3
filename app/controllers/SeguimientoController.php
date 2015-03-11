<?php

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
							array('mes'=>'Enero',		'clave'=>1),
							array('mes'=>'Febrero',		'clave'=>2),
							array('mes'=>'Marzo',		'clave'=>3)
						),
					'2' => array(
							array('mes'=>'Abril',		'clave'=>4),
							array('mes'=>'Mayo',		'clave'=>5),
							array('mes'=>'Junio',		'clave'=>6)
						),
					'3' => array(
							array('mes'=>'Julio',		'clave'=>7),
							array('mes'=>'Agosto',		'clave'=>8),
							array('mes'=>'Septiembre',	'clave'=>9)
						),
					'4' => array(
							array('mes'=>'Octubre',		'clave'=>10),
							array('mes'=>'Noviembre',	'clave'=>11),
							array('mes'=>'Dicembre',	'clave'=>12)
						)
				)
		);
		$mes_actual = date("n");
		$trimestre_actual = ceil(($mes_actual/3));
		$datos['mes_avance'] = $mes_actual;
		$datos['trimestre_avance'] = $trimestre_actual;
		return parent::loadIndex('RENDCUENTA','RENDINST',$datos);
	}
	public function indexInversion(){
		return parent::loadIndex('RENDCUENTA','RENDINV');
	}

	public function rendicionCuentas($id){
		$proyecto = Proyecto::find($id);
		if($proyecto->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
			$jurisdicciones = Jurisdiccion::all();
		}elseif($proyecto->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
			$jurisdicciones = Municipio::obtenerJurisdicciones($proyecto->claveMunicipio)->get();
		}elseif($proyecto->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
			$jurisdicciones = Region::obtenerJurisdicciones($proyecto->claveRegion)->get();
		}
		$datos['jurisdicciones'] = $jurisdicciones;
		$datos['id'] = $id;
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