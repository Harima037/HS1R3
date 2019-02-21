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
use Excel, CargaDatosEP01, Directorio;

class ReporteEP20Controller extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();

			if(!isset($parametros['mes'])){
				$mes = Util::obtenerMesActual();
				if($mes == 0){ 
					if(date('n') == 1){
						$mes = 12;
					}else{
						$mes = date('n')-1; 
					}
				}
			}else{
				$mes = intval($parametros['mes']);
			}

			if(!isset($parametros['ejercicio'])){
				$ejercicio = Util::obtenerAnioCaptura();
			}else{
				$ejercicio = intval($parametros['ejercicio']);
			}

			if(isset($parametros['formatogrid'])){
				//$rows = Proyecto::cedulasAvances($mes,$ejercicio);

				$rows  = CargaDatosEP01::where('cargaDatosEP01.mes','=',$mes)
										->where('cargaDatosEP01.ejercicio','=',$ejercicio)
										->join('catalogoFuncionesGasto AS funcionesGasto','funcionesGasto.clave','=',DB::raw('concat_ws(".",cargaDatosEP01.FI,cargaDatosEP01.FU,cargaDatosEP01.SF,cargaDatosEP01.SSF)'))
										->groupBy('cargaDatosEP01.FI')
										->groupBy('cargaDatosEP01.FU')
										->groupBy('cargaDatosEP01.SF')
										->groupBy('cargaDatosEP01.SSF');

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where('funcionesGasto.descripcion','like','%'.$parametros['buscar'].'%');
					}
				}
				
				$total = count($rows->select('funcionesGasto.clave')->get());
				
				//var_dump($total);die;
				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;

				$rows = $rows->select('funcionesGasto.descripcion AS concepto',
								DB::raw('sum(cargaDatosEP01.presupuestoAprobado) AS presupuestoAprobado'),
								DB::raw('sum(cargaDatosEP01.modificacionNeta) AS modificacionNeta'),
								DB::raw('sum(cargaDatosEP01.presupuestoModificado) AS presupuestoModificado'))
							->skip(($parametros['pagina']-1)*10)->take(10)->get();

				$data = array('resultados'=>$total,'data'=>$rows);
				$http_status = 200;

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No se encontraron datos",'code'=>'W00');
				}

				return Response::json($data,$http_status);
			}else{

				$rows  = CargaDatosEP01::where('cargaDatosEP01.mes','=',$mes)
						->where('cargaDatosEP01.ejercicio','=',$ejercicio)
						->join('catalogoFuncionesGasto AS funcionesGasto','funcionesGasto.clave','=',DB::raw('concat_ws(".",cargaDatosEP01.FI,cargaDatosEP01.FU,cargaDatosEP01.SF,cargaDatosEP01.SSF)'))
						->groupBy('cargaDatosEP01.FI')
						->groupBy('cargaDatosEP01.FU')
						->groupBy('cargaDatosEP01.SF')
						->groupBy('cargaDatosEP01.SSF')
						->select('funcionesGasto.descripcion AS concepto',
								DB::raw('sum(cargaDatosEP01.presupuestoAprobado) AS presupuestoAprobado'),
								DB::raw('sum(cargaDatosEP01.modificacionNeta) AS modificacionNeta'),
								DB::raw('sum(cargaDatosEP01.presupuestoDevengadoModificado) AS presupuestoDevengadoModificado'),
								DB::raw('sum(cargaDatosEP01.presupuestoComprometidoModificado) AS presupuestoComprometidoModificado'),
								DB::raw('sum(cargaDatosEP01.presupuestoPorLiberar) AS presupuestoPorLiberar'),
								DB::raw('sum(cargaDatosEP01.presupuestoModificado) AS presupuestoModificado'),
								DB::raw('sum(cargaDatosEP01.disponiblePresupuestarioModificado) AS disponiblePresupuestarioModificado')
						);

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where('funcionesGasto.descripcion','like','%'.$parametros['buscar'].'%');
					}
				}

				$rows = $rows->get();
				//var_dump($rows);die;
				//print_r($rows);die;
				//$queries = DB::getQueryLog();
				//var_dump($queries);die;

				$datos = array('datos'=>$rows);
				$a_date = $ejercicio."-".$mes."-1";
				$fecha = date("t", strtotime($a_date));
				$datos['fecha_trimestre'] = $fecha.' de '.Util::obtenerDescripcionMes($mes).' del '.Util::obtenerAnioCaptura();
				$datos['firmas'] = array();
				$titulares = Directorio::titularesActivos(array('00','01'))->get();

				foreach ($titulares as $titular) {
					if($titular->claveUnidad == '00'){ //Dirección General
						$datos['firmas']['secretario'] = $titular;
					}elseif ($titular->claveUnidad == '01') { //Dirección de Planeación y Desarrollo
						$datos['firmas']['dir_planeacion'] = $titular;
					}
				}

				Excel::create('ep20', function($excel) use ( $datos ){
					$excel->sheet('EP 20', function($sheet) use ( $datos ){
						$sheet->loadView('reportes.excel.estado-programatico-funcional',$datos);

						$sheet->mergeCells('A1:I1');
						$sheet->mergeCells('A2:I2');
						$sheet->mergeCells('A3:I3');
						$sheet->mergeCells('A4:I4');
						$sheet->mergeCells('A5:I5');

						$sheet->getStyle('A6:I6')->applyFromArray(array(
						    'borders' => array(
						    	'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => 'FF6600')
						    	)
						    )
						));

						$sheet->mergeCells('A8:A10');
						$sheet->mergeCells('B8:D8');
						$sheet->mergeCells('B9:B10');
						$sheet->mergeCells('C9:C10');
						$sheet->mergeCells('D9:D10');
						$sheet->mergeCells('F8:I8');
						$sheet->mergeCells('F9:F10');
						$sheet->mergeCells('G9:G10');
						$sheet->mergeCells('H9:H10');
						$sheet->mergeCells('I9:I10');

						$sheet->cells('A1:I10',function($cells) { $cells->setAlignment('center'); });
						$sheet->getStyle('A1:I10')->getAlignment()->setWrapText(true);
						$sheet->getStyle('A8:I10')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '00B550')
						    ),
						    'font' => array(
						        'size'      =>  9,
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
						$total = count($datos['datos']);
						$sheet->getStyle('A12:I'.($total+11))->getAlignment()->setWrapText(true);
						$sheet->setColumnFormat(array( 'B12:I'.($total+15) => '### ### ### ##0.00' ));

						$sheet->getStyle('D'.($total+15))->applyFromArray(array(
						    'borders' => array(
						    	'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
	            					'color' => array('argb' => '000000')
						    	)
						    )
						));

						$sheet->getStyle('I'.($total+15))->applyFromArray(array(
						    'borders' => array(
						    	'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
	            					'color' => array('argb' => '000000')
						    	)
						    )
						));

						$sheet->getStyle('A'.($total+16).':I'.($total+16).'')->applyFromArray(array(
						    'borders' => array(
						    	'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            					'color' => array('argb' => '000000')
						    	)
						    )
						));

						$linea_firmas = $total+19;

						$sheet->getStyle('B'.($linea_firmas-1).':D'.($linea_firmas-1).'')->applyFromArray(array(
						    'borders' => array(
						    	'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')
						    	)
						    )
						));

						$sheet->getStyle('F'.($linea_firmas-1).':H'.($linea_firmas-1).'')->applyFromArray(array(
						    'borders' => array(
						    	'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')
						    	)
						    )
						));

						$sheet->mergeCells('B'.$linea_firmas.':D'.$linea_firmas.'');
						$sheet->mergeCells('B'.($linea_firmas+1).':D'.($linea_firmas+1).'');
						$sheet->mergeCells('F'.$linea_firmas.':H'.$linea_firmas.'');
						$sheet->mergeCells('F'.($linea_firmas+1).':H'.($linea_firmas+1).'');
						$sheet->cells('A'.$linea_firmas.':I'.($linea_firmas+1),function($cells) { 
							$cells->setAlignment('center'); 
						});

						$imagen = $this->obtenerImagen('LogoFederal.png','A1');
						$imagen->setWorksheet($sheet);
						$imagen = $this->obtenerImagen('LogoInstitucional.png','I1',(-15));
						$imagen->setWorksheet($sheet);

					});
					$ultima_linea = $excel->getActiveSheet()->getHighestDataRow();
					$excel->getActiveSheet()->getStyle('A1:I'.$ultima_linea)->applyFromArray(
						array( 'font' => array( 'name'=> 'Arial' ) )
					);

					$excel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
					$excel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

					$excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);

					$excel->getActiveSheet()->getPageMargins()->setTop(0.3543307);
					$excel->getActiveSheet()->getPageMargins()->setRight(0);
					$excel->getActiveSheet()->getPageMargins()->setLeft(0.1968504);
					$excel->getActiveSheet()->getPageMargins()->setBottom(0.3543307);
					$excel->getActiveSheet()->getPageMargins()->setHeader(0.3149606);
					$excel->getActiveSheet()->getPageMargins()->setFooter(0.3149606);
				})->export('xlsx');
			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	private function obtenerImagen($imagen,$celda,$offset = 10){
		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath('./img/'.$imagen);// filesystem reference for the image file
		$objDrawing->setHeight(90);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setWidth(180);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setCoordinates($celda);// pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX($offset);// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		return $objDrawing;
	}
}
?>