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
use Excel, SentryUser,UnidadResponsable; 

class ReporteUsuarioController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		//
		$parametros = Input::all();

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

		$rows = $rows->select('sentryUsers.id','username',DB::raw('CONCAT_WS(" ",nombres,apellidoPaterno,apellidoMaterno) AS nombre'),'activated','claveUnidad','telefono','email','cargo','sysDepartamentos.descripcion AS departamento')
						->leftjoin('sysDepartamentos','sysDepartamentos.id','=','sentryUsers.idDepartamento')
							->orderBy('id', 'desc')
							->get();
		//
		$unidades = UnidadResponsable::lists('descripcion','clave');

		//return View::make('administrador.excel.reporte-usuarios')->with();
		//var_dump($rows->toArray());die;
		$datos = array('datos'=>$rows,'unidades'=>$unidades);
		
		Excel::create('ListaUsuarios', function($excel) use ($datos){
			$excel->sheet('Usuarios', function($sheet)  use ($datos){
		        $sheet->loadView('administrador.excel.reporte-usuarios', $datos);
		    });
		})->download('xls');
	}

}