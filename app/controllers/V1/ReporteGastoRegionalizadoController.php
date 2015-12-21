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

			$trimestre = Util::obtenerTrimestre($mes);
			$texto_trimestres = array(1=>'PRIMER',2=>'SEGUNDO',3=>'TERCER',4=>'CUARTO');
			$datos['trimestre'] = $texto_trimestres[$trimestre];

			if(!isset($parametros['ejercicio'])){
				$datos['ejercicio'] = Util::obtenerAnioCaptura();
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
			//$totalEstatal = 0;
			//$totalRegional = array_fill(1, 15, 0);
			//$datos['totalTotalDevengado'] = 0;
			//$datos['totalTotalAprobado'] = 0;
			//$datos['totalTotalModificado'] = 0;
													
			foreach ($rows as $row) {
				$importeEstatal = 0;
				$arrayRegion = array_fill(1, 15, 0);
				
				foreach($rowsCoberturaEstatal as $row2)
					if($row['PP']==$row2['PP'])
					{
						$importeEstatal = $row2['ImporteEstatal'];
						//$totalEstatal+=$importeEstatal;
					}
				for($i=1; $i<=15; $i++)
				{
					foreach($rowsRegiones[$i] as $region)
					{
						$auxiliar = get_object_vars($region);
						if($row['PP']==$auxiliar['PP'])
						{
							$arrayRegion[$i] = $auxiliar['importe'];
							//$totalRegional[$i]+=$arrayRegion[$i];
						}
					}
				}
				
				//$datos['totalTotalDevengado'] += $row['PresDevengado'];
				//$datos['totalTotalAprobado'] += $row['PresAprobado'];
				//$datos['totalTotalModificado'] += $row['PresModificado'];
				
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
			//$datos['totalEstatal'] = $totalEstatal;
			//$datos['totalRegional'] = $totalRegional;
			
			$datos['cuantos'] = $cuantos;
			
			$datos['fila'] = $arreglo;
			
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
					$imagen = $this->obtenerImagen('LogoInstitucional.png','T1',(-18));
					$imagen->setWorksheet($sheet);
					
					$sheet->mergeCells('A12:A13');
					$sheet->mergeCells('B12:Q12');
					$sheet->mergeCells('R12:R13');
					$sheet->mergeCells('S12:S13');
					$sheet->mergeCells('T12:T13');
					
					$sheet->mergeCells('A10:T10');
					$sheet->mergeCells('A11:T11');

					$sheet->cells('A10:T13',function($cells) {
						$cells->setAlignment('center');
					});
					
					$sheet->getStyle('A12:T13')->getAlignment()->setWrapText(true);
					$sheet->getStyle('A12:T13')->applyFromArray(array(
					    'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => 'C0C0C0')
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
						
					$sheet->getStyle('A8')->applyFromArray(array(						    
					    'font' => array(
					        'size'      =>  12,
					        'bold'      =>  true
					    )
					));
					
					$sheet->getStyle('B12:Q12')->applyFromArray(array(						    
					    'font' => array(
					        'size'      =>  14,
					        'bold'      =>  true
					    )
					));
						
					$sheet->getStyle('A10:T11')->applyFromArray(array(
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
					$sheet->getStyle('A15:T15')->applyFromArray(array(
						'font' => array(
							'size'      =>  11,
						    'bold'      =>  true,
						),
						'borders' => array(
							'bottom' => array(
									'style' => \PHPExcel_Style_Border::BORDER_THIN,
									'color' => array('argb' => '000000')
						    	)
						)
					));
					
					$total = 15+$datos['cuantos'];

					$sheet->getStyle('A16:A'.$total)->getAlignment()->setWrapText(true);
					
					
					$sheet->getStyle('A16:A'.$total)->applyFromArray(array(
						'font' => array(
							'size'      =>  11,
						    'bold'      =>  false,
						),
						'alignment' => array(
							'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
						)
					));
										
					for($i='A'; $i<='T'; $i++)
					{
						if($i != 'A'){
							$sheet->setCellValue($i.'15','=SUM('.$i.'16:'.$i.$total.')');
							$sheet->setCellValue($i.($total+2),'=SUM('.$i.'16:'.$i.$total.')');
						}
						$sheet->getStyle($i.'15')->applyFromArray(array(
							'font' => array( 'size' => 11),
							'borders' => array(
								'left' => array(
										'style' => \PHPExcel_Style_Border::BORDER_THIN,
										'color' => array('argb' => '000000')
							    ),
							    'right' => array(
										'style' => \PHPExcel_Style_Border::BORDER_THIN,
										'color' => array('argb' => '000000')
							    )
							)
						));
						$sheet->getStyle($i.'16:'.$i.$total)->applyFromArray(array(
								'font' => array( 'size' => 11),
							    'borders' => array(
							    	'allborders' => array(
							    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
			           					'color' => array('argb' => '000000')
							    	)
							    )
							));
					}
					$sheet->setColumnFormat(array(
						'A16:T'.$total => '### ### ### ##0.00'
					));
					
					$sheet->setColumnFormat(array(
						'B15:T15' => '### ### ### ##0.00'
					));
					
					$filatotales = $total+2;
					
					$sheet->setColumnFormat(array(
						'B'.$filatotales.':T'.$filatotales => '### ### ### ##0.00'
					));
					
					$sheet->getStyle('A'.$filatotales.':T'.$filatotales)->applyFromArray(array(
						'font' => array(
							'size'      =>  11,
						    'bold'      =>  true,
						),
						'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => 'C0C0C0')
					    ),
					));
					
					$sheet->getStyle('A'.($filatotales+2).':T'.($filatotales+2))->applyFromArray(array(
						'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => '28A659')
					    )
					));
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