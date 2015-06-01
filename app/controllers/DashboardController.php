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

		if($datos['usuario']->claveUnidad){
			$unidades = explode('|',$datos['usuario']->claveUnidad);
			$unidad_responsable = UnidadResponsable::whereIn('clave',$unidades)->get();
			$unidades_seleccionadas = array();
			foreach ($unidad_responsable as $unidad) {
				 $unidades_seleccionadas[] = $unidad->clave . ' ' . $unidad->descripcion;
			}
			$datos['unidad_responsable'] = implode('<br>',$unidades_seleccionadas);
		}else{
			$datos['unidad_responsable'] = 'Asignado a todas las Unidades';
		}

		$conteo_proyectos = Proyecto::select('idClasificacionProyecto',DB::raw('count(proyectos.id) AS conteoProyecto'))
									->where('idEstatusProyecto','=',5)
									->groupBy('idClasificacionProyecto');
		//
		if($datos['usuario']->proyectosAsignados){
			if($datos['usuario']->proyectosAsignados->proyectos){
				$proyectos = explode('|',$datos['usuario']->proyectosAsignados->proyectos);
				$conteo_proyectos = $conteo_proyectos->whereIn('proyectos.id',$proyectos);
			}
		}

		if($datos['usuario']->claveUnidad){
			$unidades = explode('|',$datos['usuario']->claveUnidad);
			$conteo_proyectos = $conteo_proyectos->whereIn('unidadResponsable',$unidades);
		}

		$conteo_proyectos = $conteo_proyectos->get();

		$lista_estatus = EstatusProyecto::all();

		$mes = Util::obtenerMesActual();
		$mes_trimestre = Util::obtenerMesTrimestre();

		if($mes){
			$datos['mes'] = Util::obtenerDescripcionMes($mes);
		}else{
			$datos['mes'] = 'No disponible';
		}
		$datos['mes_activo'] = $mes;
		$datos['mes_trimestre'] = $mes_trimestre;
		$datos['clasificaciones'] = array(1=>'Institucionales',2=>'Inversión');
		
		$total_proyectos = array(1=>0, 2=>0);
		
		foreach ($conteo_proyectos as $proyecto) {
			$total_proyectos[$proyecto->idClasificacionProyecto] += $proyecto->conteoProyecto;
		}

		$datos['total_proyectos'] = $total_proyectos;

		$permisos = array();
		$grupos = array(
				'RENDCUENTA.RENDINST.R','RENDCUENTA.RENDINV.R','RENDCUENTA.RENDPROG.R','RENDCUENTA.RENDFASSA.R'
			);
		if(Sentry::hasAnyAccess($grupos)){
			foreach ($grupos as $grupo) {
				if(Sentry::hasAccess($grupo)){
					$grupo = explode('.',$grupo);
					$permisos[$grupo[1]] = 1;
				}
			}
		}

		$datos['permisos'] = $permisos;

		return View::make($uri)->with($datos);
	}

}