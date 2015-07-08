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
*	@author 			Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto;

class ReporteCedulaAvanceController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$parametros = Input::all();

		if(!isset($parametros['mes'])){
			$mes = Utl::obtenerMesActual();
			if($mes == 0){ $mes = date('n')-1; }
		}else{
			$mes = intval($parametros['mes']);
		}

		if(!isset($parametros['ejercicio'])){
			$ejercicio = date('Y');
		}else{
			$ejercicio = intval($parametros['ejercicio']);
		}

		if(!isset($parametros['buscar'])){
			$buscar = NULL;
		}else{
			$buscar = $parametros['buscar'];
		}

		$rows = Proyecto::reporteCedulasAvances($mes,$ejercicio,$buscar)->get();
		Excel::create('reporte', function($excel) use ($rows) {
		    $excel->sheet('Sheetname', function($sheet) use ($rows) {
		        $sheet->fromArray($rows);
		    });
		})->download('xls');
	}
}