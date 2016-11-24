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
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, ProyectosVariacionGastoRazones;

class ReporteVariacionesGastoController extends BaseController {

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
				$rows = Proyecto::variacionesGasto($mes,$ejercicio);

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
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
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}

				return Response::json($data,$http_status);
			}else{
				$rows = Proyecto::reporteVariacionesGasto($mes,$ejercicio);

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}

				$rows = $rows->get();
				
				$datos = array();
				$cuantosAprobados = 0;
				$cuantosDevengados = 0;
				
				$datos['fila'] = array();
				$arreglo = array();	
				$totalModificado = 0;
				$totalAprobado = 0;
				$totalDevengado = 0;
								
				foreach($rows as $row)
				{
					$data_row = array(					
						'nombre'=>$row['nombreTecnico'],
						'modificado' => $row['presupuestoModificado']/1000000,
						'aprobado' => $row['presupuestoAprobado']/1000000,
						'devengado' => $row['presupuestoDevengado']/1000000,
						'razonesAprobado' => $row['razonesAprobado'],
						'razonesDevengado' => $row['razonesDevengado'],
						'mostrarAprobado' => 0,
						'mostrarDevengado' => 0,
					);
					
					$totalModificado += $row['presupuestoModificado']/1000000;
					$totalAprobado += $row['presupuestoAprobado']/1000000;
					$totalDevengado += $row['presupuestoDevengado']/1000000;
					
					if(($row['presupuestoAprobado']-$row['presupuestoModificado'])!=0)
					{
						$cuantosAprobados++;
						$data_row['mostrarAprobado'] = 1;
					}
					if(($row['presupuestoDevengado']-$row['presupuestoModificado'])!=0)
					{
						$cuantosDevengados++;
						$data_row['mostrarDevengado'] = 1;
					}
					
					$arreglo[] = $data_row;
					
				}
				
				$datos['cuantosAprobados'] = $cuantosAprobados;
				$datos['cuantosDevengados'] = $cuantosDevengados;
				$datos['fila'] = $arreglo;				
				$datos['totalModificado'] = $totalModificado;
				$datos['totalAprobado'] = $totalAprobado;
				$datos['totalDevengado'] = $totalDevengado;
				
				Excel::create('variaciones-gasto', function($excel) use ( $datos ){
					$excel->sheet('MODIFICADO-APROBADO', function($sheet) use ( $datos ){
						$sheet->setStyle(array(
						    'font' => array(
						        'name'      =>  'Arial',
						        'size'      =>  9
						    )
						));
						$sheet->loadView('reportes.excel.variaciones-gasto',$datos);
						/*
						$imagen = $this->obtenerImagen('EscudoGobiernoChiapas.png','A1');
						$imagen->setWorksheet($sheet);
						$imagen = $this->obtenerImagen('LogoInstitucional.png','G1');
						$imagen->setWorksheet($sheet);	
						*/

						$sheet->mergeCells('C5:F5');
						$sheet->mergeCells('C7:F7');
						$sheet->mergeCells('E8:F8');
						$sheet->mergeCells('A13:G13');
						$sheet->mergeCells('C8:C9');
						$sheet->mergeCells('D8:D9');					
						$sheet->mergeCells('A14:C14');
						
						$sheet->cells('B5:F10',function($cells){ $cells->setAlignment('center'); });
						
						$sheet->cells('A11:F12',function($cells){ $cells->setAlignment('center'); });
						$sheet->cells('A13:G13',function($cells){ $cells->setAlignment('center'); });
						$sheet->cells('A14:G14',function($cells){ $cells->setAlignment('center'); });

						$total = 16 + $datos['cuantosAprobados'];
						$i = 16;
																	
						//$sheet->getStyle('A16:C'.$total)->getAlignment()->setWrapText(true);
						$sheet->getStyle('G16:G'.$total)->getAlignment()->setWrapText(true);
						$sheet->getStyle('G16:G'.$total)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
						
						$sheet->cells('G16:G'.$total,function($cells){ $cells->setAlignment('justify'); });
						
						$sheet->getStyle('C8:C9')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$sheet->getStyle('D8:D9')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
						
						for($i=14;$i<=$total;$i++)
						{
							$sheet->mergeCells('A'.$i.':C'.$i);
							//$sheet->mergeCells('D'.$i.':E'.$i);
						}
						$sheet->getStyle('A16:C'.$total)->getAlignment()->setWrapText(true);
						$sheet->cells('A16:C'.$total,function($cells){ $cells->setAlignment('justify'); });

						$sheet->cells('B14:F'.$total,function($cells){ $cells->setAlignment('center'); });

						$sheet->setColumnFormat(array(
							'C10:F10' => '### ### ### ##0.00'
						));
						
											
						$sheet->setColumnFormat(array(
							'D16:F'.$total => '### ### ### ##0.00'
						));
						$sheet->getStyle('D16:F'.$total)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
						
						$sheet->getStyle('B5:F5')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    )
						));
						$sheet->getStyle('A13:F13')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  10,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    )
						));
						$sheet->getStyle('C7:F7')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'FFFFFF')
						    ),
							'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '28A659')
						    ),
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						$sheet->getStyle('C8:F10')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    ),						
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						$sheet->getStyle('C8:F9')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => 'DDDDDD')
						    )
						));
						$sheet->getStyle('A13:G13')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    ),						
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						$sheet->getStyle('A14:G14')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  10,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'FFFFFF')
						    ),
							'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '28A659')
						    ),
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						
						$sheet->getStyle('A15')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true
						    )
						));
											
						$sheet->getStyle('A15:G'.$total)->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11
						    ),
							'borders' => array(
							  	/*'top' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
		            				'color' => array('argb' => '28A659')
							    ),*/
							    'bottom' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
									'color' => array('argb' => '28A659')
						    	),
								'left' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
									'color' => array('argb' => '28A659')
						    	),
								'right' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
									'color' => array('argb' => '28A659')
						    	)
							 )
						));
						
						for($i=16;$i<$total;$i++)
							$sheet->setCellValue('F'.$i,'=SUM(E'.$i.'-D'.$i.')');
							
						$totalMenosUno = $total-1;
											
						$sheet->setCellValue('D'.$total,'=SUM(D16:D'.$totalMenosUno.')');
						$sheet->setCellValue('E'.$total,'=SUM(E16:E'.$totalMenosUno.')');
						$sheet->setCellValue('F'.$total,'=SUM(F16:F'.$totalMenosUno.')');

						$sheet->setCellValue('C10','=D'.$total);
						$sheet->setCellValue('D10','=E'.$total);

						$sheet->setCellValue('E10','=D10-C10');
						$sheet->setCellValue('F10','=D10/C10*100-100');
						
						$totalMasDos = $total+2;					
						$sheet->setCellValue('F'.$totalMasDos,'=SUM(F'.$total.'/E10*100)');

											
						/*$excel->getActiveSheet()->setCellValue('A'.$ultima_linea,null);
						$excel->getActiveSheet()->setCellValue('J12','=SUM('.rtrim($suma_j,',').')');
						$excel->getActiveSheet()->setCellValue('K12','=SUM('.rtrim($suma_k,',').')');*/
						
						
						
						
						
				    });
					$excel->sheet('MODIFICADO-DEVENGADO', function($sheet) use ( $datos ){
						
						$sheet->setStyle(array(
						    'font' => array(
						        'name'      =>  'Arial',
						        'size'      =>  9
						    )
						));

						$sheet->loadView('reportes.excel.variaciones-gasto2',$datos);
						/*
						$imagen = $this->obtenerImagen('EscudoGobiernoChiapas.png','A1');
						$imagen->setWorksheet($sheet);
						$imagen = $this->obtenerImagen('LogoInstitucional.png','G1');
						$imagen->setWorksheet($sheet);	
						*/
						$sheet->mergeCells('C5:F5');
						$sheet->mergeCells('C7:F7');
						$sheet->mergeCells('E8:F8');
						$sheet->mergeCells('A13:G13');
						$sheet->mergeCells('C8:C9');
						$sheet->mergeCells('D8:D9');					
						$sheet->mergeCells('A14:C14');
						
						$sheet->cells('B5:F10',function($cells){ $cells->setAlignment('center'); });
						$sheet->cells('A11:F12',function($cells){ $cells->setAlignment('center'); });
						$sheet->cells('A13:G13',function($cells){ $cells->setAlignment('center'); });
						$sheet->cells('A14:G14',function($cells){ $cells->setAlignment('center'); });

						$total = 16 + $datos['cuantosDevengados'];
						$i = 16;
																	
						$sheet->getStyle('A16:C'.$total)->getAlignment()->setWrapText(true);
						$sheet->getStyle('G16:G'.$total)->getAlignment()->setWrapText(true);
						$sheet->getStyle('G16:G'.$total)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
						$sheet->cells('G16:G'.$total,function($cells){ $cells->setAlignment('justify'); });

						$sheet->getStyle('C8:C9')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$sheet->getStyle('D8:D9')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
						
						for($i=14;$i<=$total;$i++){
							$sheet->mergeCells('A'.$i.':C'.$i);
						}
						$sheet->getStyle('A16:C'.$total)->getAlignment()->setWrapText(true);
						//$sheet->getStyle('A16:C'.$total)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);					
						$sheet->cells('A16:C'.$total,function($cells){ $cells->setAlignment('justify'); });
							
						$sheet->cells('B14:F'.$total,function($cells){ $cells->setAlignment('center'); });

						$sheet->setColumnFormat(array(
							'C10:F10' => '### ### ### ##0.00'
						));
						
											
						$sheet->setColumnFormat(array(
							'D16:F'.$total => '### ### ### ##0.00'
						));
						$sheet->getStyle('D16:F'.$total)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
						
						$sheet->getStyle('B5:F5')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    )
						));
						$sheet->getStyle('A13:F13')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  10,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    )
						));
						$sheet->getStyle('C7:F7')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'FFFFFF')
						    ),
							'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '28A659')
						    ),
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						$sheet->getStyle('C8:F10')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    ),						
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						$sheet->getStyle('C8:F9')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => 'DDDDDD')
						    )
						));
						$sheet->getStyle('A13:G13')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    ),						
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						$sheet->getStyle('A14:G14')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  10,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'FFFFFF')
						    ),
							'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '28A659')
						    ),
							'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
		            				'color' => array('rgb' => '000000')
						    	)
						    )
						));
						
						$sheet->getStyle('A15')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11,
						        'bold'      =>  true
						    )
						));
											
						$sheet->getStyle('A15:G'.$total)->applyFromArray(array(
						    'font' => array(
						        'size'      =>  11
						    ),
							'borders' => array(
							  	/*'top' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
		            				'color' => array('argb' => '28A659')
							    ),*/
							    'bottom' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
									'color' => array('argb' => '28A659')
						    	),
								'left' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
									'color' => array('argb' => '28A659')
						    	),
								'right' => array(
									'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
									'color' => array('argb' => '28A659')
						    	)
							 )
						));
						
						for($i=16;$i<$total;$i++)
							$sheet->setCellValue('F'.$i,'=SUM(E'.$i.'-D'.$i.')');
							
						$totalMenosUno = $total-1;
											
						$sheet->setCellValue('D'.$total,'=SUM(D16:D'.$totalMenosUno.')');
						$sheet->setCellValue('E'.$total,'=SUM(E16:E'.$totalMenosUno.')');
						$sheet->setCellValue('F'.$total,'=SUM(F16:F'.$totalMenosUno.')');

						$sheet->setCellValue('C10','=D'.$total);
						$sheet->setCellValue('D10','=E'.$total);

						$sheet->setCellValue('E10','=D10-C10');
						$sheet->setCellValue('F10','=D10/C10*100-100');
						
						$totalMasDos = $total+2;					
						$sheet->setCellValue('F'.$totalMasDos,'=SUM(F'.$total.'/E10*100)');
						
						

				    });

				})->download('xlsx');

				/*//var_dump($rows->toArray());die;*/
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