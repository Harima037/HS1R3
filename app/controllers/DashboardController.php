<?php
use SSA\Utilerias\Util;

class DashboardController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$sys_sis_llave = 'DASHBOARD';
		$datos = array();
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey($sys_sis_llave);
		$uri = $datos['sys_activo']->uri;
		$datos['usuario'] = Sentry::getUser();
		$datos['sys_mod_activo'] = null;

		$unidad_responsable = UnidadResponsable::where('clave','=',$datos['usuario']->claveUnidad)->first();
		if($unidad_responsable){
			$datos['unidad_responsable'] = $unidad_responsable->clave . ' ' . $unidad_responsable->descripcion;
		}else{
			$datos['unidad_responsable'] = 'Unidad responsable no establecida';
		}

		$conteo_proyectos = Proyecto::select('idClasificacionProyecto','idEstatusProyecto',DB::raw('count(proyectos.id) AS conteoProyecto'),'estatusProyecto.descripcion AS estatusProyecto')
									->join('catalogoEstatusProyectos AS estatusProyecto','estatusProyecto.id','=','proyectos.idEstatusProyecto')
									->groupBy('idClasificacionProyecto','idEstatusProyecto');
		if($datos['usuario']->claveUnidad){
			$conteo_proyectos = $conteo_proyectos->where('unidadResponsable','=',$datos['usuario']->claveUnidad);
		}

		$conteo_proyectos = $conteo_proyectos->get();

		$lista_estatus = EstatusProyecto::all();

		$mes = Util::obtenerMesActual();
		$mes_trimestre = Util::obtenerMesTrimestre();

		$datos['mes'] = Util::obtenerDescripcionMes($mes);
		$datos['mes_activo'] = $mes;
		$datos['mes_trimestre'] = $mes_trimestre;
		$datos['clasificaciones'] = array(1=>'Institucionales',2=>'Inversión');
		$proyectos = array();
		$total_proyectos = array(1=>0, 2=>0);

		for ($i = 1; $i <= 2 ; $i++) { 
			foreach ($lista_estatus as $estatus) {
				$proyectos[$i][$estatus->id] = array('estatus' => $estatus->descripcion, 'conteo' => 0);
			}
		}

		foreach ($conteo_proyectos as $proyecto) {
			$proyectos[$proyecto->idClasificacionProyecto][$proyecto->idEstatusProyecto]['conteo'] = $proyecto->conteoProyecto;
			$total_proyectos[$proyecto->idClasificacionProyecto] += $proyecto->conteoProyecto;
		}

		$datos['proyectos'] = $proyectos;
		$datos['total_proyectos'] = $total_proyectos;

		return View::make($uri)->with($datos);
	}

}