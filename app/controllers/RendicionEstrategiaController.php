<?php
use SSA\Utilerias\Util;

class RendicionEstrategiaController extends BaseController {
	
	public function index(){
		$datos = array();
		$datos['mes_avance'] = Util::obtenerMesActual();
		$datos['trimestre_avance'] = Util::obtenerTrimestre();
		$datos['anio_captura'] = Util::obtenerAnioCaptura();

		$mes_del_trimestre = Util::obtenerMesTrimestre();
		if($mes_del_trimestre == 3){
			$datos['trimestre_activo'] = TRUE;
		}else{
			$datos['trimestre_activo'] = FALSE;
		}

		return parent::loadIndex('RENDCUENTA','RENDEST',$datos);
	}

	public function editarAvance($id){
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

		$meses = array( 1=>'Enero',2=>'Febrero',3=>'Marzo',     4=>'Abril',   5=>'Mayo',      6=>'Junio',
						7=>'Julio',8=>'Agosto', 9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

		$datos['mes_clave'] = Util::obtenerMesActual();
		$datos['mes'] = $meses[$datos['mes_clave']];
		
		$mes_del_trimestre = Util::obtenerMesTrimestre();

		if($mes_del_trimestre == 3){
			$datos['trimestre_activo'] = Util::obtenerTrimestre();
		}else{
			//$datos['trimestre_activo'] = 0;
			return Response::view('errors.mes_no_disponible', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}

		$datos['id'] = $id;

		$datos['sys_activo'] = SysGrupoModulo::findByKey('RENDCUENTA');

		$datos['sys_mod_activo'] = SysModulo::findByKey('RENDEST');
		$permiso = 'RENDCUENTA.RENDEST.R';
		
		$uri = 'rendicion-cuentas.vista-captura-rendicion-estrategia';
		
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