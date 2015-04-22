<?php
/* 
*	POA
*	Programa Operativo Anual
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2015, Instituto de Salud.
*	@author 			Manuel, Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

class RevisionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$catalogos = array(
				'clasificacion_proyectos'=>ClasificacionProyecto::all(),
				'tipos_proyectos'=>TipoProyecto::all(),
				'origenes_financiamiento' => OrigenFinanciamiento::all()
			);
		return parent::loadIndex('REVISION','REVIPROY',$catalogos);
	}

	public function caratula()
	{
		$clasificacion = ClasificacionProyecto::all()->lists('descripcion','id');
		$tipo = TipoProyecto::all()->lists('descripcion','id');

		$datos = array(
			'clasificacion_proyecto_id' => Input::get('clasificacion_proyecto'),
			'tipo_proyecto_id' => Input::get('tipo_proyecto'),
			'clasificacion_proyecto' => $clasificacion[Input::get('clasificacion_proyecto')],
			'tipo_proyecto' => $tipo[Input::get('tipo_proyecto')],
		);
		
		//Si hay un id etonces es edición
		if(Input::get('id')){
			$datos['id'] = Input::get('id');
		}

		if(Input::get('fibap-id')){
			$datos['fibap_id'] = Input::get('fibap-id');
		}

		//Se Pre-carga formulario general de la caratula
		$datos['formulario'] = View::make('revision.revision-formulario-caratula',$datos);
		
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey('REVISION');
		$datos['sys_mod_activo'] = SysModulo::findByKey('REVIPROY');
		$uri = 'revision.revision-caratula';
		$permiso = 'REVISION.REVIPROY.C';
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