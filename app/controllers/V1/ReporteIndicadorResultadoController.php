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
		if(!Sentry::hasAccess('REPORTES.INDIRESULT.R')){
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}

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

			$rows = Proyecto::reporteIndicadoresResultados($mes,$datos['ejercicio'])->get();

			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;
			//return Response::json($rows,200);

			$hojas = array();
			foreach ($rows as $row) {
				if(!isset($hojas[$row->subFuncionClave])){
					$hojas[$row->subFuncionClave] = array(
						'titulo' => $row->subFuncionDescripcion,
						'total_presup_aprobado' => 0,
						'total_presup_modificado' => 0,
						'total_presup_devengado' => 0,
						'conteo_items' => 0,
						'justificaciones'=>array(),
						'clase' => array()
					);
				}

				if(!isset($hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto])){
					$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto] = array('fuentes'=>array());
					$hojas[$row->subFuncionClave]['conteo_items']++;
				}

				$clave_fuentes = '';
				$titulo_fuentes = '';
				$total_fuentes = count($row->fuentesFinanciamiento) - 1;
				foreach ($row->fuentesFinanciamiento as $key => $fuente) {
					if($key == 0){
						$titulo_fuentes = $fuente->descripcion;
					}elseif($key < $total_fuentes){
						$titulo_fuentes .= ', ' . $fuente->descripcion;
					}else{
						$ultimo = ' y ';
						if(strtolower(substr($fuente->descripcion,0,1)) == 'i'){
							$ultimo = ' e ';
						}elseif(strtolower(substr($fuente->descripcion,0,1)) == 'h'){
							if(strtolower(substr($fuente->descripcion,1,1)) == 'i'){
								$ultimo = ' e ';
							}
						}
						$titulo_fuentes .= $ultimo . $fuente->descripcion;
					}
					$clave_fuentes .= $fuente->clave .'_';
					$row->totalPresupuestoAprobado += $fuente->presupuestoAprobado;
					$row->totalPresupuestoModificado += $fuente->presupuestoModificado;
					$row->totalPresupuestoDevengado += $fuente->presupuestoDevengado;
				}

				if(!isset($hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto]['fuentes'][$clave_fuentes])){
					$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto]['fuentes'][$clave_fuentes] = array(
						'titulo' => $titulo_fuentes,
						'proyectos'=>array()
					);
					$hojas[$row->subFuncionClave]['conteo_items']++;
				}

				$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto]['fuentes'][$clave_fuentes]['proyectos'][] = $row;
				$hojas[$row->subFuncionClave]['conteo_items']++;

				$row->desfaseActividades = count($row->componentes);

				$total_acciones = count($row->componentes) + count($row->actividades);
				$total_fuentes = count($row->fuentesFinanciamiento);

				if($total_acciones > $total_fuentes){
					$row->totalItems = $total_acciones;
				}else{
					$row->totalItems = $total_fuentes;
				}

				$hojas[$row->subFuncionClave]['conteo_items'] += $row->totalItems;
				
				$hojas[$row->subFuncionClave]['total_presup_aprobado'] += $row->totalPresupuestoAprobado;
				$hojas[$row->subFuncionClave]['total_presup_modificado'] += $row->totalPresupuestoModificado;
				$hojas[$row->subFuncionClave]['total_presup_devengado'] += $row->totalPresupuestoDevengado;
			}
			
			//return Response::json($hojas,200);

			$datos['hojas'] = $hojas;

			Excel::create('indicadores-resultados', function($excel) use ( $datos ){
				$datos_hoja = array();
				$datos_hoja['ejercicio'] = $datos['ejercicio'];
				$datos_hoja['trimestre'] = $datos['trimestre'];
				foreach ($datos['hojas'] as $clave => $hoja) {
					$excel->sheet($clave, function($sheet) use ( $datos_hoja, $hoja ){
						$sheet->setStyle(array(
						    'font' => array(
						        'name'      =>  'Arial',
						        'size'      =>  10
						    )
						));

						$datos_hoja['hoja'] = $hoja;

				    	$sheet->loadView('reportes.excel.indicadores-resultados',$datos_hoja);
				    	
				    	$sheet->mergeCells('A2:O2');
						$sheet->mergeCells('A4:O4');
						$sheet->cells('A2:O4',function($cells){ $cells->setAlignment('center'); });
						$sheet->mergeCells('A9:A10');
						$sheet->mergeCells('B9:B10');
						$sheet->mergeCells('D9:E10');
						$sheet->mergeCells('F9:J9');
						$sheet->mergeCells('H10:I10');
						$sheet->mergeCells('K9:K10');
						$sheet->mergeCells('L9:L10');
						$sheet->mergeCells('M9:M10');
						$sheet->mergeCells('N9:P9');
						$sheet->mergeCells('C11:P11');
						$sheet->cells('A9:P12',function($cells) { $cells->setAlignment('center'); });
						$sheet->getStyle('A9:P12')->getAlignment()->setWrapText(true);
						$sheet->getStyle('A9:P11')->applyFromArray(array(
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
						$sheet->getStyle('A11:P11')->applyFromArray(array(
						    'font' => array(
						        'size'      =>  13,
						        'bold'      =>  true
						    ),
						    'borders' => array(
						    	'top' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => 'FFFFFF')
						    	)
						    )
						));
						$sheet->getStyle('A12:P12')->applyFromArray(array(
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
						$total = $hoja['conteo_items'] + 13;
						for ($i='A'; $i < 'P' ; $i++) { 
							if($i != 'D' && $i != 'I'){
								$sheet->getStyle($i.'14:'.$i.$total)->applyFromArray(array(
								    'font' => array( 'size' => 10),
								    'borders' => array(
								    	'right' => array(
								    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
			            					'color' => array('argb' => '002060')
								    	)
								    )
								));
							}
						}
						$sheet->getStyle('A14:P'.$total)->getAlignment()->setWrapText(true);
						$sheet->setColumnFormat(array(
							'F14:H'.$total => '### ### ### ##0.00',
							'K12:M12' => '### ### ### ##0.00',
						    'J12:M'.$total => '### ### ### ##0.00',
						    'P14:P'.$total => '### ### ### ##0'
						));

						$imagen = $this->obtenerImagen('EscudoGobiernoChiapas.png','A1');
						$imagen->setWorksheet($sheet);
						$imagen = $this->obtenerImagen('LogoInstitucional.png','O1');
						$imagen->setWorksheet($sheet);
				    });
					
					$ultima_linea = $excel->getActiveSheet()->getHighestDataRow();
					$rows_sumar = explode(',',$excel->getActiveSheet()->getCell('A'.$ultima_linea)->getValue());

					
					$suma_k = '';
					$suma_l = '';
					$suma_m = '';
					foreach ($rows_sumar as $indice => $valor) {
						$suma_k .= 'K'.$valor.',';
						$suma_l .= 'L'.$valor.',';
						$suma_m .= 'M'.$valor.',';
					}
					
					$excel->getActiveSheet()->setCellValue('A'.$ultima_linea,null);
					$excel->getActiveSheet()->setCellValue('K12','=SUM('.rtrim($suma_k,',').')');
					$excel->getActiveSheet()->setCellValue('L12','=SUM('.rtrim($suma_l,',').')');
					$excel->getActiveSheet()->setCellValue('M12','=SUM('.rtrim($suma_m,',').')');

					$excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,10);

					$excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&RPágina &P / &N');
					$excel->getActiveSheet()->getHeaderFooter()->setEvenHeader('&RPágina &P / &N');

					$excel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
					$excel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

					$excel->getActiveSheet()->getPageSetup()->setFitToPage(true);
					$excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
					$excel->getActiveSheet()->getPageSetup()->setFitToHeight(0);

					$excel->getActiveSheet()->getPageMargins()->setTop(0.3543307);
					$excel->getActiveSheet()->getPageMargins()->setBottom(0.3543307);

					$excel->getActiveSheet()->getPageMargins()->setRight(0.1968504);
					$excel->getActiveSheet()->getPageMargins()->setLeft(0.2755906);
					
					$excel->getActiveSheet()->getPageMargins()->setHeader(0.9055118);
					$excel->getActiveSheet()->getPageMargins()->setFooter(0.5118110);
				}
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