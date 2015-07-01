<?php
use SSA\Utilerias\Util;

class PurgarSeguimientoController extends BaseController {
	
	public function index(){
		$datos = array();
		$datos['mes_activo'] = Util::obtenerMesActual();
		$datos['meses'] = array( 1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
						7=>'Julio',8=>'Agosto', 9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
		$datos['clasificacion'] = ClasificacionProyecto::all()->lists('descripcion','id');
		return parent::loadIndex('ADMIN','PURGARSEG',$datos);
	}
}