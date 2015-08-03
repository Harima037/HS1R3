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
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, URL, CargaDatosEP01, CargaDatosEPRegion;

class ReporteGastoRegionalizadoController extends BaseController {

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
						
			$rows = CargaDatosEP01::reporteRegionalizado($mes,$datos['ejercicio'])->get();
			$rowsCoberturaEstatal = CargaDatosEPRegion::reporteEstatal($mes,$datos['ejercicio'])->get();
			$rowsRegiones = array();
			
			for($i=1; $i<=15; $i++)				
				$rowsRegiones[$i] = CargaDatosEPRegion::reporteRegional($mes,$i,$datos['ejercicio']);
			
				//var_dump($rowsRegiones[$i]);die;
			
			$datos['fila'] = array();
			$arreglo = array();	
			$cuantos = 0;	
			$totalEstatal = 0;
			$totalRegional = array_fill(1, 15, 0);
			$datos['totalTotalDevengado'] = 0;
			$datos['totalTotalAprobado'] = 0;
			$datos['totalTotalModificado'] = 0;
													
			foreach ($rows as $row) {
				$importeEstatal = 0;
				$arrayRegion = array_fill(1, 15, 0);
				
				foreach($rowsCoberturaEstatal as $row2)
					if($row['PP']==$row2['PP'])
					{
						$importeEstatal = $row2['ImporteEstatal'];
						$totalEstatal+=$importeEstatal;
					}
				for($i=1; $i<=15; $i++)
				{
					foreach($rowsRegiones[$i] as $region)
					{
						$auxiliar = get_object_vars($region);
						if($row['PP']==$auxiliar['PP'])
						{
							$arrayRegion[$i] = $auxiliar['importe'];
							$totalRegional[$i]+=$arrayRegion[$i];
						}
					}
				}
				
				$datos['totalTotalDevengado'] += $row['PresDevengado'];
				$datos['totalTotalAprobado'] += $row['PresAprobado'];
				$datos['totalTotalModificado'] += $row['PresModificado'];
				
				$data_row = array(					
					'programapre'=>$row['PP'].' '.$row['nombrePrograma'],
					'presupuestoDevengado' => $row['PresDevengado'],
					'presupuestoAprobado' => $row['PresAprobado'],
					'presupuestoModificado' => $row['PresModificado'],
					'importeEstatal' =>$importeEstatal,
					'regiones' =>$arrayRegion
				);
				$arreglo[] = $data_row;
				$cuantos++;
			}
			$datos['totalEstatal'] = $totalEstatal;
			$datos['totalRegional'] = $totalRegional;
			
			$datos['cuantos'] = $cuantos;
			
			$datos['fila'] = $arreglo;
						
			//var_dump($rows);die;
			//$datos['fila']=$rows;
							
			/*foreach ($rows as $row) {
				$datos['fila']=array(
					'' => 
					
				);
			}*/
			
			//var_dump($datos['fila']);die;

			Excel::create('gasto-regionalizado', function($excel) use ( $datos ){
				
				$excel->sheet('Reporte', function($sheet) use ( $datos ){
					$sheet->setStyle(array(
					    'font' => array(
					        'name'      =>  'Arial',
					        'size'      =>  10
					    )
					));

					$sheet->loadView('reportes.excel.gasto-regionalizado',$datos);
					$imagen = $this->obtenerImagen('EscudoGobiernoChiapas.png','A1');
					$imagen->setWorksheet($sheet);
					$imagen = $this->obtenerImagen('LogoInstitucional.png','S1');
					$imagen->setWorksheet($sheet);
					
					$sheet->mergeCells('A2:T2');
					$sheet->cells('A2:O4',function($cells){ $cells->setAlignment('center'); });
					$sheet->mergeCells('A9:A10');
					$sheet->mergeCells('B9:Q9');
					$sheet->mergeCells('R9:R10');
					$sheet->mergeCells('S9:S10');
					$sheet->mergeCells('T9:T10');
						
					$sheet->mergeCells('A7:T7');
					$sheet->mergeCells('A8:T8');
											
												
					$sheet->cells('A7:T10',function($cells) {
						$cells->setAlignment('center');
					});
					
					$sheet->getStyle('A9:T10')->getAlignment()->setWrapText(true);
					$sheet->getStyle('A9:T10')->applyFromArray(array(
					    'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => 'DDDDDD')
					    ),
					    'font' => array(
					        'size'      =>  12,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    ),
					    'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('argb' => 'FFFFFF')
					    	)
					    )
					));
						
					$sheet->getStyle('C5')->applyFromArray(array(						    
					    'font' => array(
					        'size'      =>  12,
					        'bold'      =>  true
					    )
					));
					
					$sheet->getStyle('B9:Q9')->applyFromArray(array(						    
					    'font' => array(
					        'size'      =>  14,
					        'bold'      =>  true
					    )
					));
						
					$sheet->getStyle('A7:T8')->applyFromArray(array(
					    'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => '28A659')
					    ),
					    'font' => array(
					        'size'      =>  12,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'FFFFFF')
					    )
					));
					$sheet->getStyle('A12:T12')->applyFromArray(array(
						'font' => array(
							'size'      =>  10,
						    'bold'      =>  true,
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
					
					$total = 12+$datos['cuantos'];
					
					$sheet->getStyle('A13:A'.$total)->getAlignment()->setWrapText(true);
					
					
					$sheet->getStyle('A13:A'.$total)->applyFromArray(array(
						'font' => array(
							'size'      =>  10,
						    'bold'      =>  true,
						),
						'alignment' => array(
							'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
						)
					));
					
					/*for($c = 13 ; $c<= $total; $c++)
					{
						$sheet->getStyle('A'.$c.':T'.$c)->applyFromArray(array(
							'borders' => array(
							    'bottom' => array(
									'style' => \PHPExcel_Style_Border::BORDER_THIN,
									'color' => array('argb' => 'FF808080')
						    	)
							 )
						));
					}*/
					
					for($i='A'; $i<'V'; $i++)
					{
						$sheet->getStyle($i.'13:'.$i.$total)->applyFromArray(array(
								'font' => array( 'size' => 10),
							    'borders' => array(
							    	'left' => array(
							    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
			           					'color' => array('argb' => 'FF808080')
							    	)
							    )
							));
					}
					$sheet->setColumnFormat(array(
						'A13:T'.$total => '### ### ### ##0.00'
					));
					
					$sheet->setColumnFormat(array(
						'B12:T12' => '### ### ### ##0.00'
					));
					
					$filatotales = $total+1;
					
					$sheet->setColumnFormat(array(
						'B'.$filatotales.':T'.$filatotales => '### ### ### ##0.00'
					));
					
					$sheet->getStyle('A'.$filatotales.':T'.$filatotales)->applyFromArray(array(
						'font' => array(
							'size'      =>  10,
						    'bold'      =>  true,
						)
					));
					
					/*$sheet->getStyle('A'.$filatotales.':T'.$filatotales)->applyFromArray(array(
						'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
				    	    'color' => array('rgb' => 'DDDDDD')
					    )
					));
					*/
					
					
					
					/*for ($i='A'; $i < 'O' ; $i++) { 
						if($i != 'H'){
							$sheet->getStyle($i.'14:'.$i.$total)->applyFromArray(array(
								'font' => array( 'size' => 8),
							    'borders' => array(
							    	'right' => array(
							    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
			           					'color' => array('argb' => '002060')
							    	)
							    )
							));
						}
					}*/
					$sheet->getStyle('A14:O'.$total)->getAlignment()->setWrapText(true);
			    });

			})->download('xlsx');
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