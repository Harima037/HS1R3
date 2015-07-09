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
use BaseController, Input, Response, DB, Sentry, View, PDF, Exception;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto;

class ReporteIndicadorResultadoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();
			$datos = array();

			if(!isset($parametros['mes'])){
				$mes = Util::obtenerMesActual();
				if($mes == 0){ $mes = date('n')-1; }
			}else{
				$mes = intval($parametros['mes']);
			}

			$trimestre = Util::obtenerTrimestre($mes);
			$texto_trimestres = array(1=>'PRIMER',2=>'SEGUNDO',3=>'TERCER',4=>'CUARTO');
			$datos['trimestre'] = $texto_trimestres[$trimestre];

			if(!isset($parametros['ejercicio'])){
				$datos['ejercicio'] = date('Y');
			}else{
				$datos['ejercicio'] = intval($parametros['ejercicio']);
			}

			$datos['total_presup_aprobado'] = '1 001 260 223.02';
			$datos['total_presup_modificado'] = '949 910 992.55';
			$datos['total_presup_devengado'] = '805 003 657.69 ';

			Excel::create('indicadores-resultados', function($excel) use ( $datos ){
				$excel->sheet('PP', function($sheet) use ( $datos ){
					$sheet->setStyle(array(
					    'font' => array(
					        'name'      =>  'Arial',
					        'size'      =>  10
					    )
					));

			    	$sheet->loadView('reportes.excel.indicadores-resultados',$datos);
			    	
			    	$sheet->mergeCells('A2:O2');
					$sheet->mergeCells('A4:O4');
					$sheet->cells('A2:O4',function($cells){ $cells->setAlignment('center'); });
					$sheet->mergeCells('A9:A10');
					$sheet->mergeCells('B9:B10');
					$sheet->mergeCells('D9:D10');
					$sheet->mergeCells('E9:I9');
					$sheet->mergeCells('H10:I10');
					$sheet->mergeCells('J9:J10');
					$sheet->mergeCells('K9:K10');
					$sheet->mergeCells('L9:L10');
					$sheet->mergeCells('M9:O9');
					$sheet->mergeCells('A11:O11');
					$sheet->cells('A9:O12',function($cells) {
						$cells->setAlignment('center');
					});
					$sheet->getStyle('A9:O12')->getAlignment()->setWrapText(true);
					$sheet->getStyle('A9:O11')->applyFromArray(array(
					    'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => '28A659')
					    ),
					    'font' => array(
					        'size'      =>  8,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'FFFFFF')
					    ),
					    'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
            					'color' => array('argb' => 'FFFFFF')
					    	)
					    )
					));
					$sheet->getStyle('A11:O11')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  12,
					        'bold'      =>  true
					    ),
					    'borders' => array(
					    	'top' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
            					'color' => array('argb' => 'FFFFFF')
					    	)
					    )
					));
					$sheet->getStyle('A12:O12')->applyFromArray(array(
					    'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => 'DDDDDD')
					    ),
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    ),
					    'borders' => array(
					    	'top' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
            					'color' => array('argb' => '28A659')
					    	),
					    	'bottom' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
            					'color' => array('argb' => '28A659')
					    	)
					    )
					));
			    });
			})->download('xlsx');
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}
}
?>