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
*	@author 			Donaldo Ríos
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View;
use Excel, Proyecto, FIBAP, ComponenteMetaMes, ActividadMetaMes;

class ReporteEvaluacionController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		$recurso = Proyecto::with('componentes.actividades.registroAvance')->find($id);
		$recurso->componentes->load('registroAvance');

		$nombreArchivo = ($recurso->idClasificacionProyecto== 2) ? 'Rendición de Cuentas Inversión' : 'Rendición de Cuentas Institucional';
		$nombreArchivo.=' - '.$recurso->ClavePresupuestaria;
		$data['data'] = $recurso;
		$data['mes_actual'] = Util::obtenerMesActual();
		$data['meses'] = array(
			1 => array('mes'=>'Enero',			'abrev'=>'ENE'),
			2 => array('mes'=>'Febrero',		'abrev'=>'FEB'),
			3 => array('mes'=>'Marzo',			'abrev'=>'MAR'),
			4 => array('mes'=>'Abril',			'abrev'=>'ABR'),
			5 => array('mes'=>'Mayo',			'abrev'=>'MAy'),
			6 => array('mes'=>'Junio',			'abrev'=>'JUN'),
			7 => array('mes'=>'Julio',			'abrev'=>'JUL'),
			8 => array('mes'=>'Agosto',			'abrev'=>'AGO'),
			9 => array('mes'=>'Septiembre',		'abrev'=>'SEP'),
			10 => array('mes'=>'Octubre',		'abrev'=>'OCT'),
			11 => array('mes'=>'Noviembre',		'abrev'=>'NOV'),
			12 => array('mes'=>'Dicembre',		'abrev'=>'DIC')
		);
		$data['recurso'] = $recurso;

		Excel::create($nombreArchivo, function($excel) use ($data){
			$mes_actual = $data['mes_actual'];
			for ($i=1; $i < $mes_actual ; $i++) { 
				$datos['mes'] = $data['meses'][$i];
				$datos['proyecto'] = $data['recurso'];
				$excel->sheet('SM '.$datos['mes']['abrev'], function($sheet)  use ($datos){
			        $sheet->loadView('rendicion-cuentas.excel.seguimiento-metas-mes', $datos);
			    });
			}
		})->export('xls');

	}

}