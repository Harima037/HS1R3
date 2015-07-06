<?php

class UsuariosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Sentry::getUser()->idDepartamento){
			$departamentos = SysDepartamento::where('id','=',Sentry::getUser()->idDepartamento)
											->orWhere('id','=',3)->get();
		}else{
			$departamentos = SysDepartamento::all();
		}
		$jurisdicciones =  array(''=>'Selecciona una jurisdicción') + Jurisdiccion::all()->lists('nombre','clave');
		$catalogos = array(
				'sys_roles'=>Sentry::findAllGroups(),
				'unidades_responsables'=>UnidadResponsable::all(),
				'departamentos'=>$departamentos,
				'jurisdicciones'=>$jurisdicciones
			);
		return parent::loadIndex('ADMIN','USUARIOS',$catalogos);
	}
}