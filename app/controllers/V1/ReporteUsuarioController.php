<?php
/* 
*	SIRE
*	Sistema de Integración, Rendición de cuentas y Evaluación
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2015, Instituto de Salud.
*	@author 			Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View;
use Excel, SentryUser, SentryGroup, UnidadResponsable; 

class ReporteUsuarioController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		//
		$parametros = Input::all();
		$datos = array();

		$rows = Sentry::getUserProvider()->createModel();

		$rows = $rows->where(function($query){
			$query->where('permissions','!=','{"superuser":1}')
					->orWhereNull('permissions');
		});

		if($parametros['reporte-departamento']){
			$rows = $rows->where('idDepartamento','=',$parametros['reporte-departamento']);
		}elseif(Sentry::getUser()->idDepartamento){
			$rows = $rows->where(function($query){
						$query->where('idDepartamento','=',Sentry::getUser()->idDepartamento)
							  ->orWhere('idDepartamento','=',3);
					});
		}

		if(isset($parametros['reporte-unidad'])){
			$unidades = $parametros['reporte-unidad'];
			$rows = $rows->where(function($query)use($unidades){
					foreach ($unidades as $unidad) {
						$query = $query->orWhere('claveUnidad','like',$unidad.'|%')
										->orWhere('claveUnidad','like','%|'.$unidad.'|%')
										->orWhere('claveUnidad','like','%|'.$unidad)
										->orWhere('claveUnidad','like',$unidad);
					}
				});
		}
		$unidades = UnidadResponsable::lists('descripcion','clave');
		$datos['unidades'] = $unidades;

		if(isset($parametros['reporte-rol'])){
			$roles = $parametros['reporte-rol'];
			$user_ids = DB::table('sentryUsersGroups')->whereIn('sentry_group_id',$roles)->lists('sentry_user_id');
			$rows = $rows->whereIn('sentryUsers.id',$user_ids);
		}

		$rows = $rows->select('sentryUsers.id','username',DB::raw('CONCAT_WS(" ",nombres,apellidoPaterno,apellidoMaterno) AS nombre'),
							'activated','claveUnidad','telefono','email','cargo','sysDepartamentos.descripcion AS departamento')
						->leftjoin('sysDepartamentos','sysDepartamentos.id','=','sentryUsers.idDepartamento')
						->orderBy('id', 'desc')
						->get();
		//

		if(isset($parametros['reporte-rol'])){
			$roles = $parametros['reporte-rol'];
		}else{
			$roles = DB::table('sentryUsersGroups')->whereIn('sentry_user_id',$rows->lists('id'))->lists('sentry_group_id');
		}
		$roles_raw = SentryGroup::whereIn('sentryGroups.id',$roles)
								->leftjoin('sentryUsersGroups','sentryUsersGroups.sentry_group_id','=','sentryGroups.id')
								->select('sentryGroups.name','sentryUsersGroups.sentry_user_id')
								->get();
		$datos['roles'] = array();
		foreach ($roles_raw as $rol) {
			if(!isset($datos['roles'][$rol->sentry_user_id])){
				$datos['roles'][$rol->sentry_user_id] = array();
			}
			$datos['roles'][$rol->sentry_user_id][] = $rol->name;
		}

		//return View::make('administrador.excel.reporte-usuarios')->with();
		//var_dump($rows->toArray());die;
		$datos['datos'] = $rows;
		//var_dump($datos);die;

		Excel::create('ListaUsuarios', function($excel) use ($datos){
			$excel->sheet('Usuarios', function($sheet)  use ($datos){
		        $sheet->loadView('administrador.excel.reporte-usuarios', $datos);
		    });
		    $excel->getActiveSheet()->getStyle('A2:I'.(count($datos['datos'])+1))->getAlignment()->setWrapText(true);
		})->download('xls');
	}

}