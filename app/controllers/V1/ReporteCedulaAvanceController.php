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
use BaseController, Input, Response, DB, Sentry, View, PDF;
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
			$mes = Util::obtenerMesActual();
			if($mes == 0){ $mes = date('n')-1; }
		}else{
			$mes = intval($parametros['mes']);
		}

		if(!isset($parametros['ejercicio'])){
			$ejercicio = date('Y');
		}else{
			$ejercicio = intval($parametros['ejercicio']);
		}

		if(isset($parametros['formatogrid'])){
			$rows = Proyecto::cedulasAvances($mes,$ejercicio);

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

			if(isset($parametros['buscar'])){		
				if($parametros['buscar']){
					$rows = $rows->where(function($query)use($parametros){
						$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
							->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
					});
				}
			}
			
			$total = $rows->get();
			$total = count($total);
			
			//var_dump($total);die;
			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;

			$rows = $rows->skip(($parametros['pagina']-1)*10)->take(10)->get();

			$data = array('resultados'=>$total,'data'=>$rows);
			$http_status = 200;

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No se encontraron variables",'code'=>'W00');
			}

			return Response::json($data,$http_status);
		}else{
			$rows = Proyecto::reporteCedulasAvances($mes,$ejercicio)->get();

			$datos = array('datos'=>$rows);
			$datos['total_programado'] = 0;
			$datos['total_avance'] = 0;
			$datos['indices'] = array();

			$pdf = PDF::setPaper('LETTER')->setOrientation('landscape')->setWarnings(false)->loadView('reportes.pdf.reporte-cedulas-avances',$datos);

			$pdf->output();
			$dom_pdf = $pdf->getDomPDF();
			$canvas = $dom_pdf ->get_canvas();
			$w = $canvas->get_width();
	  		$h = $canvas->get_height();
			$canvas->page_text(($w-75), ($h-16), "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

			return $pdf->stream('reporte_cedulas_avances.pdf');
		}

		
	}
}